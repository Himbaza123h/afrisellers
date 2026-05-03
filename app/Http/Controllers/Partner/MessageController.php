<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /** List all conversations (grouped by the other party). */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $messages = Message::where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->whereNull('group_id')
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();

        // Group by the "other" user in each thread
        $conversations = $messages
            ->groupBy(fn($msg) => $msg->sender_id == $userId
                ? $msg->receiver_id
                : $msg->sender_id
            )
            ->map(function ($msgs, $otherId) use ($userId) {
                $last = $msgs->first();
                return [
                    'other'        => $last->sender_id == $userId ? $last->receiver : $last->sender,
                    'last_message' => $last,
                    'unread_count' => $msgs->where('receiver_id', $userId)->where('is_read', false)->count(),
                ];
            })
            ->values();

        // ── Statistics (always from full unfiltered set) ──────────────────
        $stats = [
            'total'       => $conversations->count(),
            'unread'      => $conversations->where('unread_count', '>', 0)->count(),
            'read'        => $conversations->where('unread_count', 0)->count(),
            'sent'        => $messages->where('sender_id', $userId)->count(),
        ];

        // ── Search ────────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $term = strtolower($request->search);
            $conversations = $conversations->filter(function ($conv) use ($term) {
                $nameMatch    = str_contains(strtolower($conv['other']->name ?? ''), $term);
                $messageMatch = str_contains(strtolower($conv['last_message']->message ?? ''), $term);
                return $nameMatch || $messageMatch;
            })->values();
        }

        // ── Status filter ─────────────────────────────────────────────────
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $conversations = $conversations->where('unread_count', '>', 0)->values();
            } elseif ($request->status === 'read') {
                $conversations = $conversations->where('unread_count', 0)->values();
            }
        }

        return view('partner.messages.index', compact('conversations', 'stats'));
    }

    /** Compose form — partners can only message admins. */
    public function compose()
    {
        $admins = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->get();
        return view('partner.messages.compose', compact('admins'));
    }

    /** Send a new message. */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string|max:5000',
        ]);

        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'type'        => 'text',
        ]);

        return redirect()->route('partner.messages.index')
                         ->with('success', 'Message sent successfully.');
    }

    /** Show a conversation thread with another user. */
    public function show(int $conversation)
    {
        $userId  = auth()->id();
        $otherId = $conversation;
        $other   = User::findOrFail($otherId);

        $messages = Message::where(function ($q) use ($userId, $otherId) {
                $q->where('sender_id', $userId)->where('receiver_id', $otherId);
            })
            ->orWhere(function ($q) use ($userId, $otherId) {
                $q->where('sender_id', $otherId)->where('receiver_id', $userId);
            })
            ->whereNull('group_id')
            ->with(['sender', 'receiver'])
            ->oldest()
            ->get();

        // Mark incoming messages as read
        $messages->where('receiver_id', $userId)
                 ->where('is_read', false)
                 ->each(fn($msg) => $msg->markAsRead($userId));

        return view('partner.messages.show', compact('messages', 'other'));
    }

    /** Reply inside an existing conversation. */
    public function reply(Request $request, int $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $conversation,
            'message'     => $request->message,
            'type'        => 'text',
        ]);

        return back()->with('success', 'Reply sent.');
    }

    /** Soft-delete a message the partner sent. */
    public function destroy(int $message)
    {
        $msg = Message::where('sender_id', auth()->id())->findOrFail($message);
        $msg->delete();
        return back()->with('success', 'Message deleted.');
    }

    public function markAsRead(int $message)
    {
        $msg = Message::findOrFail($message);
        $msg->markAsRead(auth()->id());
        return back();
    }

    public function markAllAsRead()
    {
        Message::where('receiver_id', auth()->id())
               ->where('is_read', false)
               ->whereNull('group_id')
               ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'All messages marked as read.');
    }
}
