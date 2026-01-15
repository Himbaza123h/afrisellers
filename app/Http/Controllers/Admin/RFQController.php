<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RFQs;
use App\Models\RFQMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RFQController extends Controller
{
    /**
     * Display a listing of all RFQs (Admin has no limits).
     */
/**
 * Display a listing of all RFQs (Admin has no limits).
 */
public function index(Request $request)
{
    try {
        // Calculate statistics
        $stats = [
            'total' => RFQs::count(),
            'pending' => RFQs::where('status', 'pending')->count(),
            'accepted' => RFQs::where('status', 'accepted')->count(),
            'rejected' => RFQs::where('status', 'rejected')->count(),
            'closed' => RFQs::where('status', 'closed')->count(),
        ];

        // Calculate percentages
        if ($stats['total'] > 0) {
            $stats['pending_percentage'] = round(($stats['pending'] / $stats['total']) * 100, 1);
            $stats['accepted_percentage'] = round(($stats['accepted'] / $stats['total']) * 100, 1);
            $stats['rejected_percentage'] = round(($stats['rejected'] / $stats['total']) * 100, 1);
            $stats['closed_percentage'] = round(($stats['closed'] / $stats['total']) * 100, 1);
        } else {
            $stats['pending_percentage'] = 0;
            $stats['accepted_percentage'] = 0;
            $stats['rejected_percentage'] = 0;
            $stats['closed_percentage'] = 0;
        }

        // Query builder
        $query = RFQs::with(['product', 'country', 'user'])
            ->withCount('messages');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    \Carbon\Carbon::parse($dates[0])->startOfDay(),
                    \Carbon\Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $rfqs = $query->paginate(20)->withQueryString();

        return view('admin.rfq.index', compact('rfqs', 'stats'));
    } catch (\Exception $e) {
        Log::error('Admin RFQ Index Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.dashboard.home')->with('error', 'An error occurred while loading RFQs.');
    }
}

    /**
     * Show vendors who responded to a specific RFQ.
     */
    public function showVendors(RFQs $rfq)
    {
        try {
            // Admin can view all RFQs, no restriction needed

            $rfq->load(['product', 'country', 'messages.user']);

            // Get all messages as collection
            $allMessages = $rfq->messages()->with('user')->get();

            // Get unique vendors who responded (users with vendor messages)
            $vendors = $allMessages
                ->where('sender_type', 'vendor')
                ->pluck('user')
                ->filter()
                ->unique('id')
                ->map(function ($vendor) use ($allMessages) {
                    $vendor->messages_count = $allMessages->where('user_id', $vendor->id)->count();
                    return $vendor;
                })
                ->values();

            return view('admin.rfq.vendors', compact('rfq', 'vendors', 'allMessages'));
        } catch (\Exception $e) {
            Log::error('Admin RFQ Vendors Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'rfq_id' => $rfq->id ?? null,
            ]);

            return redirect()->route('admin.rfq.index')
                ->with('error', 'An error occurred while loading vendors.');
        }
    }

    /**
     * Show messages from a specific vendor for an RFQ.
     */
    public function showMessages(RFQs $rfq, $vendorId)
    {
        try {
            // Admin can view all RFQs, no restriction needed

            $rfq->load(['product', 'country', 'messages.user']);

            // Get all messages as collection
            $allMessages = $rfq->messages()->with('user')->get();

            // Get the vendor
            $vendor = \App\Models\User::find($vendorId);
            if (!$vendor) {
                return redirect()->route('admin.rfq.vendors', $rfq)
                    ->with('error', 'Vendor not found.');
            }

            // Filter messages: show messages from vendor AND messages from admin (conversation thread)
            $adminId = auth()->id();
            $messages = $allMessages->filter(function ($message) use ($vendorId, $adminId) {
                // Include messages from the vendor
                if ($message->user_id == $vendorId && $message->sender_type === 'vendor') {
                    return true;
                }
                // Include messages from the admin (in this conversation thread)
                if ($message->user_id == $adminId && $message->sender_type === 'admin') {
                    return true;
                }
                // Also include buyer messages for context
                if ($message->sender_type === 'buyer') {
                    return true;
                }
                return false;
            })->sortBy('created_at')->values();

            // Ensure messages is a collection
            if (!$messages instanceof \Illuminate\Support\Collection) {
                $messages = collect($messages);
            }

            return view('admin.rfq.messages', compact('rfq', 'vendor', 'messages', 'allMessages'));
        } catch (\Exception $e) {
            Log::error('Admin RFQ Messages Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'rfq_id' => $rfq->id ?? null,
                'vendor_id' => $vendorId,
            ]);

            return redirect()->route('admin.rfq.vendors', $rfq)
                ->with('error', 'An error occurred while loading messages.');
        }
    }

    /**
     * Store a reply message for an RFQ.
     */
    public function storeMessage(Request $request, RFQs $rfq)
    {
        try {
            // Check if RFQ is closed
            if ($rfq->isClosed()) {
                return back()->with('error', 'This RFQ is closed. No further messages can be sent.');
            }

            $validated = $request->validate([
                'message' => 'required|string|min:1|max:5000',
            ]);

            // Create message
            $message = RFQMessage::create([
                'rfq_id' => $rfq->id,
                'user_id' => auth()->id(),
                'message' => $validated['message'],
                'sender_type' => 'admin',
            ]);

            Log::info('Admin RFQ message sent', [
                'message_id' => $message->id,
                'rfq_id' => $rfq->id,
                'admin_id' => auth()->id(),
            ]);

            // Get vendor_id from request if present (to redirect back to vendor messages)
            $vendorId = $request->get('vendor_id');
            if ($vendorId) {
                return redirect()->route('admin.rfq.messages', ['rfq' => $rfq, 'vendor' => $vendorId])
                    ->with('success', 'Message sent successfully.');
            }

            // Otherwise redirect to vendors list
            return redirect()->route('admin.rfq.vendors', $rfq)
                ->with('success', 'Message sent successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to send admin RFQ message', [
                'error' => $e->getMessage(),
                'rfq_id' => $rfq->id ?? null,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to send message. Please try again.');
        }
    }
}
