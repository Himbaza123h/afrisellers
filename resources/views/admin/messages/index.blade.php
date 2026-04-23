@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Messages Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor and manage platform communications</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.messages.broadcast') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium text-sm">
                <i class="fas fa-bullhorn"></i>
                <span>Broadcast Message</span>
            </a>
            <form action="{{ route('admin.messages.vendor-group.create') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                    <i class="fas fa-users"></i>
                    <span>Create Vendor Group</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Messages</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($totalMessages) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comment text-lg text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Groups</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($totalGroups) }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-lg text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Active Users (7d)</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($activeUsers) }}</p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-lg text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Groups List -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b">
            <h3 class="text-base font-semibold text-gray-900">All Message Groups</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Group</th>
                        <th class="px-5 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Type</th>
                        <th class="px-5 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Members</th>
                        <th class="px-5 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Messages</th>
                        <th class="px-5 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Last Activity</th>
                        <th class="px-5 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($groups as $group)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $group->name }}</p>
                                        @if($group->description)
                                            <p class="text-xs text-gray-500">{{ Str::limit($group->description, 40) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $group->type === 'admin_broadcast' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $group->type === 'vendor' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $group->type === 'private' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $group->type === 'public' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $group->type)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-sm text-gray-900">{{ $group->members->count() }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-sm text-gray-900">{{ $group->messages_count }}</span>
                            </td>
                            <td class="px-5 py-3">
                                @if($group->lastMessage)
                                    <p class="text-sm text-gray-900">{{ $group->lastMessage->created_at->diffForHumans() }}</p>
                                @else
                                    <span class="text-sm text-gray-500">No messages</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <a href="{{ route('messages.show', $group->id) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-500 text-sm">
                                No message groups found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($groups->hasPages())
            <div class="px-5 py-3 border-t">
                {{ $groups->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
