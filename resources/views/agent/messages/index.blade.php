@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Messages</h1>
            <p class="mt-1 text-xs text-gray-500">
                Conversations with your vendors and support team
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(($totalUnread + $groupUnread) > 0)
                <form action="{{ route('agent.messages.mark-all-read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                        <i class="fas fa-check-double"></i> Mark All Read
                    </button>
                </form>
            @endif
            <a href="{{ route('agent.messages.compose') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm">
                <i class="fas fa-pen"></i> New Message
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-comments text-blue-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Groups</p>
                <p class="text-xl font-bold text-gray-900">{{ $groups->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-envelope text-purple-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Direct Threads</p>
                <p class="text-xl font-bold text-gray-900">{{ $directThreads->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-envelope-open text-red-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Unread</p>
                <p class="text-xl font-bold text-red-600">{{ $totalUnread + $groupUnread }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-gray-200">
        <button onclick="switchTab('direct')" id="tab-direct"
            class="tab-btn px-4 py-2.5 text-sm font-semibold text-blue-600 border-b-2 border-blue-600">
            Direct Messages
            @if($totalUnread > 0)
                <span class="ml-1 px-1.5 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">{{ $totalUnread }}</span>
            @endif
        </button>
        <button onclick="switchTab('groups')" id="tab-groups"
            class="tab-btn px-4 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-800">
            Group Chats
            @if($groupUnread > 0)
                <span class="ml-1 px-1.5 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">{{ $groupUnread }}</span>
            @endif
        </button>
    </div>

    {{-- ── DIRECT MESSAGES ─────────────────────────────────────────── --}}
    <div id="section-direct">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            @forelse($directThreads as $otherUserId => $lastMsg)
                @php
                    $other = $lastMsg->sender_id == auth()->id()
                        ? $lastMsg->receiver
                        : $lastMsg->sender;
                    $isUnread = !$lastMsg->is_read && $lastMsg->receiver_id == auth()->id();
                @endphp
                <a href="{{ route('agent.messages.show', $otherUserId) }}"
                   class="flex items-center gap-4 px-5 py-4 border-b border-gray-50 hover:bg-blue-50/40 transition-colors last:border-0 {{ $isUnread ? 'bg-blue-50' : '' }}">

                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 rounded-full bg-blue-400 to-blue-600 flex items-center justify-center">
                            <span class="text-white font-bold text-sm">
                                {{ strtoupper(substr($other?->name ?? '?', 0, 1)) }}
                            </span>
                        </div>
                        @if($isUnread)
                            <span class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-red-500 rounded-full border-2 border-white"></span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-0.5">
                            <p class="text-sm font-semibold text-gray-900 {{ $isUnread ? 'font-bold' : '' }}">
                                {{ $other?->name ?? 'Unknown User' }}
                            </p>
                            <span class="text-xs text-gray-400 flex-shrink-0 ml-2">
                                {{ $lastMsg->created_at->diffForHumans(null, true) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 truncate {{ $isUnread ? 'text-gray-800 font-medium' : '' }}">
                            @if($lastMsg->sender_id == auth()->id())
                                <span class="text-gray-400">You: </span>
                            @endif
                            {{ Str::limit($lastMsg->message, 60) }}
                        </p>
                    </div>

                    {{-- Unread badge --}}
                    @if($isUnread)
                        <div class="flex-shrink-0">
                            <span class="w-2 h-2 bg-blue-600 rounded-full block"></span>
                        </div>
                    @endif
                </a>
            @empty
                <div class="flex flex-col items-center py-14">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-envelope text-3xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No direct messages yet</p>
                    <p class="text-xs text-gray-400 mt-1 mb-4">Send a message to one of your vendors</p>
                    <a href="{{ route('agent.messages.compose') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700">
                        <i class="fas fa-pen"></i> New Message
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── GROUP CHATS ──────────────────────────────────────────────── --}}
    <div id="section-groups" class="hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            @forelse($groups as $group)
                @php $hasUnread = $group->unread > 0; @endphp
                <a href="{{ route('agent.messages.show', $group->id) }}"
                   class="flex items-center gap-4 px-5 py-4 border-b border-gray-50 hover:bg-blue-50/40 transition-colors last:border-0 {{ $hasUnread ? 'bg-blue-50' : '' }}">

                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 rounded-full bg-purple-400 to-purple-600 flex items-center justify-center">
                            @if($group->avatar)
                                <img src="{{ $group->avatar }}" class="w-11 h-11 rounded-full object-cover" alt="">
                            @else
                                <i class="fas fa-users text-white text-sm"></i>
                            @endif
                        </div>
                        @if($hasUnread)
                            <span class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-red-500 rounded-full border-2 border-white"></span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-0.5">
                            <p class="text-sm font-semibold text-gray-900">{{ $group->name }}</p>
                            <span class="text-xs text-gray-400 flex-shrink-0 ml-2">
                                {{ $group->lastMessage?->created_at?->diffForHumans(null, true) ?? '' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <p class="text-xs text-gray-500 truncate flex-1">
                                @if($group->lastMessage)
                                    <span class="text-gray-400">{{ $group->lastMessage->sender?->name }}: </span>
                                    {{ Str::limit($group->lastMessage->message, 50) }}
                                @else
                                    <span class="italic">No messages yet</span>
                                @endif
                            </p>
                            @if($hasUnread)
                                <span class="flex-shrink-0 px-1.5 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">
                                    {{ $group->unread }}
                                </span>
                            @endif
                        </div>
                        <p class="text-[10px] text-gray-400 mt-0.5">
                            {{ $group->members->count() }} {{ Str::plural('member', $group->members->count()) }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="flex flex-col items-center py-14">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-comments text-3xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No group chats</p>
                    <p class="text-xs text-gray-400 mt-1">Group conversations will appear here</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function switchTab(name) {
    ['direct', 'groups'].forEach(t => {
        document.getElementById('section-' + t).classList.add('hidden');
        const btn = document.getElementById('tab-' + t);
        btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        btn.classList.add('text-gray-500');
    });
    document.getElementById('section-' + name).classList.remove('hidden');
    const active = document.getElementById('tab-' + name);
    active.classList.remove('text-gray-500');
    active.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
}
document.addEventListener('DOMContentLoaded', () => {
    const tab = new URLSearchParams(window.location.search).get('tab') || 'direct';
    switchTab(tab);
});
</script>
@endpush
