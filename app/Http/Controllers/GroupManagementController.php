<?php

namespace App\Http\Controllers;

use App\Models\MessageGroup;
use App\Models\User;
use Illuminate\Http\Request;

class GroupManagementController extends Controller
{
    public function show($id)
    {
        $user = auth()->user();
        $group = MessageGroup::with(['members', 'admins', 'creator'])->findOrFail($id);

        if (!$group->isMember($user->id)) {
            abort(403, 'Unauthorized');
        }

        $isAdmin = $group->isAdmin($user->id);

        return view('messages.group-settings', compact('group', 'isAdmin'));
    }

    public function update(Request $request, $id)
    {
        $group = MessageGroup::findOrFail($id);
        $user = auth()->user();

        if (!$group->isAdmin($user->id)) {
            return back()->with('error', 'Only admins can update group settings');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Group updated successfully');
    }

    public function addMember(Request $request, $id)
    {
        $group = MessageGroup::findOrFail($id);
        $user = auth()->user();

        if (!$group->isAdmin($user->id)) {
            return back()->with('error', 'Only admins can add members');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($group->isMember($request->user_id)) {
            return back()->with('error', 'User is already a member');
        }

        $group->members()->attach($request->user_id, ['role' => 'member']);

        return back()->with('success', 'Member added successfully');
    }

    public function removeMember($id, $userId)
    {
        $group = MessageGroup::findOrFail($id);
        $user = auth()->user();

        if (!$group->isAdmin($user->id)) {
            return back()->with('error', 'Only admins can remove members');
        }

        if ($userId == $group->created_by) {
            return back()->with('error', 'Cannot remove group creator');
        }

        $group->members()->detach($userId);

        return back()->with('success', 'Member removed successfully');
    }

    public function makeAdmin($id, $userId)
    {
        $group = MessageGroup::findOrFail($id);
        $user = auth()->user();

        if (!$group->isAdmin($user->id)) {
            return back()->with('error', 'Only admins can promote members');
        }

        $group->members()->updateExistingPivot($userId, ['role' => 'admin']);

        return back()->with('success', 'Member promoted to admin');
    }

    public function removeAdmin($id, $userId)
    {
        $group = MessageGroup::findOrFail($id);
        $user = auth()->user();

        if (!$group->isAdmin($user->id)) {
            return back()->with('error', 'Only admins can demote admins');
        }

        if ($userId == $group->created_by) {
            return back()->with('error', 'Cannot demote group creator');
        }

        $group->members()->updateExistingPivot($userId, ['role' => 'member']);

        return back()->with('success', 'Admin demoted to member');
    }

    public function leave($id)
    {
        $group = MessageGroup::findOrFail($id);
        $user = auth()->user();

        if ($user->id == $group->created_by) {
            return back()->with('error', 'Group creator cannot leave. Delete the group instead.');
        }

        $group->members()->detach($user->id);

        return redirect()->route('messages.index')->with('success', 'You have left the group');
    }

    public function toggleLock($id)
    {
        $group = MessageGroup::findOrFail($id);
        $user = auth()->user();

        if (!$group->isAdmin($user->id)) {
            return back()->with('error', 'Only admins can lock/unlock group');
        }

        $group->update(['is_locked' => !$group->is_locked]);

        $status = $group->is_locked ? 'locked' : 'unlocked';
        return back()->with('success', "Group {$status} successfully");
    }

    public function generateInvite($id)
    {
        $group = MessageGroup::findOrFail($id);
        $user = auth()->user();

        if (!$group->isAdmin($user->id)) {
            return back()->with('error', 'Only admins can generate invite links');
        }

        $group->generateInviteCode(7);

        return back()->with('success', 'Invite link generated successfully');
    }

    public function joinViaInvite(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string',
        ]);

        $group = MessageGroup::where('invite_code', strtoupper($request->invite_code))->first();

        if (!$group) {
            return back()->with('error', 'Invalid invite code');
        }

        if (!$group->isInviteCodeValid()) {
            return back()->with('error', 'Invite code has expired');
        }

        if ($group->isMember(auth()->id())) {
            return redirect()->route('messages.show', $group->id)->with('info', 'You are already a member');
        }

        $group->members()->attach(auth()->id(), ['role' => 'member']);

        return redirect()->route('messages.show', $group->id)->with('success', 'You have joined the group');
    }

    public function searchUsers(Request $request, $id)
    {
        $group = MessageGroup::findOrFail($id);
        $query = $request->get('q');

        $existingMemberIds = $group->members()->pluck('users.id')->toArray();

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->whereNotIn('id', $existingMemberIds)
            ->limit(10)
            ->get(['id', 'name', 'email', 'avatar']);

        return response()->json($users);
    }
}
