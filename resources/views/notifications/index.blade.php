@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Notifications</h1>
            <p class="mt-1 text-xs text-gray-500">All your system and account notifications</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($stats['unread'] > 0)
            <button onclick="markAllRead()"
                class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-check-double"></i>
                <span>Mark All Read</span>
            </button>
            @endif
            @if($stats['total'] > 0)
            <form method="POST" action="{{ route('notifications.destroy-all') }}" onsubmit="return confirm('Clear all notifications?')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                    <i class="fas fa-trash"></i>
                    <span>Clear All</span>
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex gap-2 border-b border-gray-200">
        <button onclick="switchTab('all')" id="tab-all"
            class="tab-button px-4 py-2 text-sm font-semibold {{ $filter === 'all' ? 'text-[#ff0808] border-b-2 border-[#ff0808]' : 'text-gray-600 hover:text-gray-900' }} transition-colors">
            All
            <span class="ml-1 px-1.5 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">{{ $stats['total'] }}</span>
        </button>
        <button onclick="switchTab('unread')" id="tab-unread"
            class="tab-button px-4 py-2 text-sm font-semibold {{ $filter === 'unread' ? 'text-[#ff0808] border-b-2 border-[#ff0808]' : 'text-gray-600 hover:text-gray-900' }} transition-colors">
            Unread
            @if($stats['unread'] > 0)
            <span class="ml-1 px-1.5 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">{{ $stats['unread'] }}</span>
            @endif
        </button>
        <button onclick="switchTab('read')" id="tab-read"
            class="tab-button px-4 py-2 text-sm font-semibold {{ $filter === 'read' ? 'text-[#ff0808] border-b-2 border-[#ff0808]' : 'text-gray-600 hover:text-gray-900' }} transition-colors">
            Read
        </button>
        <button onclick="switchTab('stats')" id="tab-stats"
            class="tab-button px-4 py-2 text-sm font-semibold {{ $filter === 'stats' ? 'text-[#ff0808] border-b-2 border-[#ff0808]' : 'text-gray-600 hover:text-gray-900' }} transition-colors">
            Stats
        </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats Section --}}
    <div id="stats-section" class="{{ $filter === 'stats' ? '' : ($filter === 'all' ? '' : 'hidden') }}">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</span>
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bell text-gray-600 text-xs"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 mt-1">All notifications</p>
            </div>
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Unread</span>
                    <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-red-500 text-xs"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-[#ff0808]">{{ $stats['unread'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Need your attention</p>
            </div>
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Read</span>
                    <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope-open text-green-500 text-xs"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['read'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Already seen</p>
            </div>
            <div class="stat-card bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Today</span>
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-blue-500 text-xs"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['today'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Received today</p>
            </div>
        </div>
    </div>

    {{-- Notifications List --}}
    <div id="list-section" class="{{ $filter === 'stats' ? 'hidden' : '' }}">

        {{-- Search Bar --}}
        <form method="GET" action="{{ route('notifications.index') }}" class="flex gap-2 mb-4">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <div class="relative flex-1 max-w-sm">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Search notifications..."
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-red-400 focus:outline-none">
            </div>
            <button type="submit"
                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                Search
            </button>
            @if($search)
            <a href="{{ route('notifications.index', ['filter' => $filter]) }}"
                class="px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                Clear
            </a>
            @endif
        </form>

        {{-- Notification Items --}}
        @if($notifications->count() > 0)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            @foreach($notifications as $notification)
            <div class="flex items-start gap-4 p-4 border-b border-gray-50 last:border-b-0 hover:bg-gray-50 transition-colors {{ !$notification->is_read ? 'bg-red-50/30 border-l-4 border-l-[#ff0808]' : '' }}">

                {{-- Icon --}}
                <div class="flex-shrink-0 mt-0.5">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center
                        {{ !$notification->is_read ? 'bg-red-100' : 'bg-gray-100' }}">
                        @php
                            $icon = 'fa-bell';
                            $iconColor = 'text-gray-500';
                            $title = strtolower($notification->title ?? '');
                            if (str_contains($title, 'order')) { $icon = 'fa-shopping-cart'; $iconColor = 'text-blue-500'; }
                            elseif (str_contains($title, 'product')) { $icon = 'fa-box'; $iconColor = 'text-purple-500'; }
                            elseif (str_contains($title, 'verified') || str_contains($title, 'approved')) { $icon = 'fa-check-circle'; $iconColor = 'text-green-500'; }
                            elseif (str_contains($title, 'reject')) { $icon = 'fa-ban'; $iconColor = 'text-red-500'; }
                            elseif (str_contains($title, 'message')) { $icon = 'fa-envelope'; $iconColor = 'text-indigo-500'; }
                            elseif (str_contains($title, 'payment') || str_contains($title, 'plan') || str_contains($title, 'trial')) { $icon = 'fa-credit-card'; $iconColor = 'text-yellow-500'; }
                            elseif (str_contains($title, 'suspended') || str_contains($title, 'alert')) { $icon = 'fa-exclamation-triangle'; $iconColor = 'text-orange-500'; }
                        @endphp
                        <i class="fas {{ $icon }} {{ $iconColor }} text-sm"></i>
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-900 {{ !$notification->is_read ? 'text-gray-900' : 'text-gray-700' }}">
                            {{ $notification->title }}
                            @if(!$notification->is_read)
                                <span class="ml-1.5 inline-block w-2 h-2 bg-[#ff0808] rounded-full align-middle"></span>
                            @endif
                        </p>
                        <span class="flex-shrink-0 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $notification->content }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        @if($notification->link_url)
                        <a href="{{ route('notifications.show', $notification->id) }}"
                            class="text-xs text-[#ff0808] font-medium hover:underline">
                            View details →
                        </a>
                        @else
                        <a href="{{ route('notifications.show', $notification->id) }}"
                            class="text-xs text-gray-500 font-medium hover:text-gray-700">
                            View →
                        </a>
                        @endif
                        @if(!$notification->is_read)
                        <button onclick="markRead({{ $notification->id }}, this)"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Mark read
                        </button>
                        @endif
                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="inline" onsubmit="return confirm('Delete this notification?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-500 font-medium">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>

        @else
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-bell-slash text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No notifications</h3>
            <p class="text-sm text-gray-500">
                {{ $search ? 'No notifications match your search.' : 'You\'re all caught up!' }}
            </p>
        </div>
        @endif
    </div>

</div>

<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<script>
function switchTab(tab) {
    const filterMap = { all: 'all', unread: 'unread', read: 'read', stats: 'stats' };
    const url = new URL(window.location.href);
    url.searchParams.set('filter', filterMap[tab]);
    window.location.href = url.toString();
}

function markRead(id, btn) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) location.reload();
    });
}

function markAllRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) location.reload();
    });
}
</script>
@endsection
