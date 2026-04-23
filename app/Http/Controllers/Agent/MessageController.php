<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageGroup;
use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────────
    public function index()
    {
        $userId = auth()->id();

        // Group conversations the agent belongs to
        $groups = MessageGroup::whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->with(['lastMessage.sender', 'members'])
            ->latest('updated_at')
            ->get()
            ->map(function ($group) use ($userId) {
                $group->unread = $group->unreadCount($userId);
                return $group;
            });

        // Direct message threads (group by the other user)
        $directThreads = Message::where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->whereNull('group_id')
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function ($msg) use ($userId) {
                return $msg->sender_id == $userId
                    ? $msg->receiver_id
                    : $msg->sender_id;
            })
            ->map(fn($msgs) => $msgs->first()); // latest message per thread

        $totalUnread = Message::where('receiver_id', $userId)
            ->whereNull('group_id')
            ->where('is_read', false)
            ->count();

        $groupUnread = $groups->sum('unread');

        return view('agent.messages.index', compact(
            'groups', 'directThreads', 'totalUnread', 'groupUnread'
        ));
    }

    // ─── COMPOSE ──────────────────────────────────────────────────────
    public function compose()
    {
        $agentId = auth()->id();

        // Vendors the agent manages + admins
        $myVendorUserIds = Vendor::where('agent_id', $agentId)->pluck('user_id');

        $recipients = User::where('id', '!=', $agentId)
            ->where(function ($q) use ($myVendorUserIds) {
                $q->whereIn('id', $myVendorUserIds)
                  ->orWhereHas('roles', fn($r) => $r->whereIn('slug', ['admin', 'super-admin']));
            })
            ->orderBy('name')
            ->get();

        return view('agent.messages.compose', compact('recipients'));
    }

    // ─── STORE (new direct message) ───────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id|different:' . auth()->id(),
            'message'      => 'required|string|max:5000',
        ]);

        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->recipient_id,
            'message'     => $request->message,
            'type'        => 'text',
            'is_read'     => false,
        ]);

        return redirect()
            ->route('agent.messages.show', $request->recipient_id)
            ->with('success', 'Message sent.');
    }

    // ─── SHOW (conversation) ──────────────────────────────────────────
    // $conversation = MessageGroup ID  OR  the other user's ID (direct)
    public function show(Request $request, $conversation)
    {
        $userId = auth()->id();

        // 1. Try as a MessageGroup
        $group = MessageGroup::whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->find($conversation);

        if ($group) {
            $messages = Message::where('group_id', $group->id)
                ->with(['sender', 'replyTo.sender'])
                ->oldest()
                ->get();

            // Mark all as read & update pivot
            $messages->each(fn($msg) => $msg->markAsRead($userId));
            $group->members()->updateExistingPivot($userId, ['last_read_at' => now()]);

            $otherUser = null;
            return view('agent.messages.show', compact('group', 'messages', 'otherUser'));
        }

        // 2. Direct message thread with another user
        $otherUser = User::findOrFail($conversation);

        $messages = Message::where(function ($q) use ($userId, $otherUser) {
                $q->where('sender_id', $userId)->where('receiver_id', $otherUser->id);
            })
            ->orWhere(function ($q) use ($userId, $otherUser) {
                $q->where('sender_id', $otherUser->id)->where('receiver_id', $userId);
            })
            ->whereNull('group_id')
            ->with(['sender', 'replyTo.sender'])
            ->oldest()
            ->get();

        // Mark incoming as read
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $userId)
            ->whereNull('group_id')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $group = null;
        return view('agent.messages.show', compact('group', 'messages', 'otherUser'));
    }

    // ─── REPLY ────────────────────────────────────────────────────────
    public function reply(Request $request, $conversation)
    {
        $request->validate([
            'message'  => 'required|string|max:5000',
            'reply_to' => 'nullable|exists:messages,id',
        ]);

        $userId = auth()->id();

        // Group reply
        $group = MessageGroup::whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->find($conversation);

        if ($group) {
            if (!$group->canSendMessage($userId)) {
                return back()->with('error', 'You cannot send messages in this group.');
            }

            Message::create([
                'group_id'  => $group->id,
                'sender_id' => $userId,
                'message'   => $request->message,
                'type'      => 'text',
                'reply_to'  => $request->reply_to,
            ]);

            $group->touch();

            return back()->with('success', 'Message sent.');
        }

        // Direct reply
        $otherUser = User::findOrFail($conversation);

        Message::create([
            'sender_id'   => $userId,
            'receiver_id' => $otherUser->id,
            'message'     => $request->message,
            'type'        => 'text',
            'reply_to'    => $request->reply_to,
            'is_read'     => false,
        ]);

        return back();
    }

    // ─── DESTROY ──────────────────────────────────────────────────────
    public function destroy($message)
    {
        $msg = Message::where('sender_id', auth()->id())->findOrFail($message);
        $msg->delete();

        return back()->with('success', 'Message deleted.');
    }

    // ─── MARK AS READ ─────────────────────────────────────────────────
    public function markAsRead($message)
    {
        $msg = Message::findOrFail($message);
        $msg->markAsRead(auth()->id());

        return back();
    }

    // ─── MARK ALL AS READ ─────────────────────────────────────────────
    public function markAllAsRead()
    {
        Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'All messages marked as read.');
    }
}
