<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $groups = $user->messageGroups()
            ->with(['lastMessage.sender', 'members'])
            ->withCount('messages')
            ->get();

        $privateChats = DB::table('messages')
            ->select([
                DB::raw('MAX(id) as id'),
                DB::raw('GREATEST(sender_id, receiver_id) as user1'),
                DB::raw('LEAST(sender_id, receiver_id) as user2'),
                DB::raw('MAX(created_at) as created_at')
            ])
            ->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })
            ->whereNull('group_id')
            ->whereNull('deleted_at')
            ->groupBy('user1', 'user2')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($chat) {
                return Message::with(['sender', 'receiver'])->find($chat->id);
            })
            ->filter();

        $unreadCount = $user->unreadMessagesCount();

        return view('messages.index', compact('groups', 'privateChats', 'unreadCount'));
    }

    public function show($id)
    {
        $user = auth()->user();
        $group = MessageGroup::with(['messages.sender', 'members', 'admins'])->findOrFail($id);

        if (!$group->isMember($user->id)) {
            abort(403, 'Unauthorized');
        }

        $group->members()->updateExistingPivot($user->id, [
            'last_read_at' => now()
        ]);

        $isAdmin = $group->isAdmin($user->id);
        $canSendMessage = $group->canSendMessage($user->id);

        return view('messages.show', compact('group', 'isAdmin', 'canSendMessage'));
    }

    public function private($userId)
    {
        $user = auth()->user();
        $otherUser = User::findOrFail($userId);

        $messages = Message::where(function($q) use ($user, $userId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $userId);
        })->orWhere(function($q) use ($user, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $user->id);
        })
        ->with('sender')
        ->orderBy('created_at', 'asc')
        ->get();

        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('messages.private', compact('otherUser', 'messages'));
    }

public function store(Request $request)
{
    $request->validate([
        'message' => 'required|string',
        'group_id' => 'nullable|exists:message_groups,id',
        'receiver_id' => 'nullable|exists:users,id',
        'reply_to' => 'nullable|exists:messages,id',
    ]);

    // Check message limit from subscription
        $currentSubscription = \App\Models\Subscription::where('seller_id', auth()->user()->id)
            ->where('status', 'active')
            ->with('plan.features')
            ->first();

        if ($currentSubscription) {
            $maxMessages = $currentSubscription->plan->getFeature('max_messages');
            if ($maxMessages !== null) {
                $currentCount = Message::where('sender_id', auth()->id())
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();
                if ($currentCount >= (int) $maxMessages) {
                    return response()->json([
                        'success' => false,
                        'error' => 'You have reached your monthly message limit of ' . $maxMessages . '. Please upgrade your plan.'
                    ], 403);
                }
            }
        }

    // Check if user can send message to group
    if ($request->group_id) {
        $group = MessageGroup::find($request->group_id);
        if (!$group->canSendMessage(auth()->id())) {
            return response()->json([
                'success' => false,
                'error' => 'You do not have permission to send messages in this group'
            ], 403);
        }
    }

    $message = Message::create([
        'sender_id' => auth()->id(),
        'group_id' => $request->group_id,
        'receiver_id' => $request->receiver_id,
        'message' => $request->message,
        'reply_to' => $request->reply_to,
        'type' => 'text',
    ]);

    // Load the sender relationship
    $message->load('sender');

    return response()->json([
        'success' => true,
        'message' => [
            'id' => $message->id,
            'message' => $message->message,
            'created_at' => $message->created_at,
            'sender' => [
                'id' => $message->sender->id,
                'name' => $message->sender->name
            ]
        ]
    ]);
}

    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
            'type' => 'required|in:private,vendor,public',
        ]);

        // Check message limit from subscription
$currentSubscription = \App\Models\Subscription::where('seller_id', auth()->user()->id)
    ->where('status', 'active')
    ->with('plan.features')
    ->first();

if ($currentSubscription) {
    $maxMessages = $currentSubscription->plan->getFeature('max_messages');
    if ($maxMessages !== null) {
        $currentCount = Message::where('sender_id', auth()->id())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        if ($currentCount >= (int) $maxMessages) {
            return response()->json([
                'success' => false,
                'error' => 'You have reached your monthly chatting limit of ' . $maxMessages . '. Please upgrade your plan.'
            ], 403);
        }
    }
}

        $group = MessageGroup::create([
            'name' => $request->name,
            'description' => $request->description, // Already HTML from Quill
            'type' => $request->type,
            'created_by' => auth()->id(),
        ]);

        $group->members()->attach(auth()->id(), ['role' => 'admin']);

        $members = $request->members ?? [];

        if ($request->type === 'vendor') {
            $vendorIds = User::whereHas('vendor')->pluck('id')->toArray();
            $members = array_unique(array_merge($members, $vendorIds));
        } elseif ($request->type === 'public') {
            $allUserIds = User::pluck('id')->toArray();
            $members = array_unique(array_merge($members, $allUserIds));
        }

        foreach ($members as $memberId) {
            $memberId = (int) $memberId;
            if ($memberId > 0 && $memberId != auth()->id()) {
                $group->members()->attach($memberId, ['role' => 'member']);
            }
        }

        return redirect()->route('messages.show', $group->id)
            ->with('success', 'Group created successfully with ' . count($members) . ' members');
    }

    public function searchUsers(Request $request)
    {
        $query = $request->get('q');

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->where('id', '!=', auth()->id())
            ->limit(10)
            ->get(['id', 'name', 'email', 'avatar']);

        return response()->json($users);
    }

    public function getMembersByType(Request $request)
    {
        $type = $request->get('type');

        switch($type) {
            case 'vendor':
                $users = User::whereHas('vendor')
                    ->where('id', '!=', auth()->id())
                    ->get(['id', 'name', 'email']);
                break;
            case 'public':
                $users = User::where('id', '!=', auth()->id())
                    ->get(['id', 'name', 'email']);
                break;
            default:
                $users = [];
        }

        return response()->json($users);
    }

    public function loadPrivateChat($userId)
{
    $user = auth()->user();
    $otherUser = User::findOrFail($userId);

    $messages = Message::where(function($q) use ($user, $userId) {
        $q->where('sender_id', $user->id)->where('receiver_id', $userId);
    })->orWhere(function($q) use ($user, $userId) {
        $q->where('sender_id', $userId)->where('receiver_id', $user->id);
    })
    ->with('sender')
    ->orderBy('created_at', 'asc')
    ->get();

    // Mark messages as read
    Message::where('sender_id', $userId)
        ->where('receiver_id', $user->id)
        ->where('is_read', false)
        ->update(['is_read' => true, 'read_at' => now()]);

    if (request()->wantsJson()) {
        $html = view('messages.partials.private-chat-content', compact('otherUser', 'messages'))->render();
        return response()->json(['html' => $html]);
    }

    return view('messages.private', compact('otherUser', 'messages'));
}

public function loadGroupChat($id)
{
    $user = auth()->user();
    $group = MessageGroup::with(['messages.sender', 'members', 'admins'])->findOrFail($id);

    if (!$group->isMember($user->id)) {
        abort(403);
    }

    $group->members()->updateExistingPivot($user->id, [
        'last_read_at' => now()
    ]);

    $canSendMessage = $group->canSendMessage($user->id);
    $messages = $group->messages()->with('sender')->orderBy('created_at', 'asc')->get();

    if (request()->wantsJson()) {
        $html = view('messages.partials.group-chat-content', compact('group', 'messages', 'canSendMessage'))->render();
        return response()->json(['html' => $html]);
    }

    return view('messages.show', compact('group', 'messages', 'canSendMessage'));
}
}
