<?php
// app/Http/Controllers/Admin/AdminMessageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageGroup;
use App\Models\User;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function index()
    {
        $groups = MessageGroup::with(['lastMessage.sender', 'members'])
            ->withCount('messages')
            ->latest()
            ->paginate(20);

        $totalMessages = Message::count();
        $totalGroups = MessageGroup::count();
        $activeUsers = User::whereHas('sentMessages', function($q) {
            $q->where('created_at', '>=', now()->subDays(7));
        })->count();

        return view('admin.messages.index', compact('groups', 'totalMessages', 'totalGroups', 'activeUsers'));
    }

    public function broadcast()
    {
        $users = User::select('id', 'name', 'email')->get();
        $vendors = User::whereHas('vendor')->select('id', 'name', 'email')->get();
        $buyers = User::whereHas('buyer')->select('id', 'name', 'email')->get();

        return view('admin.messages.broadcast', compact('users', 'vendors', 'buyers'));
    }

    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'recipient_type' => 'required|in:all,vendors,buyers,specific',
            'recipients' => 'required_if:recipient_type,specific|array',
            'recipients.*' => 'exists:users,id',
        ]);

        // Create broadcast group
        $group = MessageGroup::create([
            'name' => 'Admin Broadcast - ' . now()->format('Y-m-d H:i'),
            'description' => 'Admin broadcast message',
            'type' => 'admin_broadcast',
            'created_by' => auth()->id(),
        ]);

        // Determine recipients
        $recipients = [];
        switch ($request->recipient_type) {
            case 'all':
                $recipients = User::pluck('id')->toArray();
                break;
            case 'vendors':
                $recipients = User::whereHas('vendor')->pluck('id')->toArray();
                break;
            case 'buyers':
                $recipients = User::whereHas('buyer')->pluck('id')->toArray();
                break;
            case 'specific':
                $recipients = $request->recipients;
                break;
        }

        // Add members to group
        foreach ($recipients as $recipientId) {
            $group->members()->attach($recipientId, ['role' => 'member']);
        }

        // Send message
        Message::create([
            'group_id' => $group->id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'type' => 'text',
        ]);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Broadcast sent to ' . count($recipients) . ' users');
    }

    public function createVendorGroup()
    {
        // Check if vendor group exists
        $group = MessageGroup::where('type', 'vendor')
            ->where('name', 'All Vendors')
            ->first();

        if (!$group) {
            $group = MessageGroup::create([
                'name' => 'All Vendors',
                'description' => 'Communication group for all vendors',
                'type' => 'vendor',
                'created_by' => auth()->id(),
            ]);

            // Add all vendors
            $vendors = User::whereHas('vendor')->pluck('id');
            foreach ($vendors as $vendorId) {
                $group->members()->attach($vendorId, ['role' => 'member']);
            }
        }

        return redirect()->route('admin.messages.index')
            ->with('success', 'Vendor group created/updated with ' . $group->members()->count() . ' members');
    }
}
