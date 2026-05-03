@extends('layouts.home')
@section('page-content')

{{-- ── Header ─────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Messages</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Messages</h1>
        <p class="text-xs text-gray-500 mt-0.5">Your conversations</p>
    </div>
    <div class="flex items-center gap-2">
        @if(auth()->user()->unreadMessagesCount() > 0)
        <form action="{{ route('partner.messages.mark-all-read') }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
                <i class="fas fa-check-double"></i> Mark all read
            </button>
        </form>
        @endif
        <a href="{{ route('partner.messages.compose') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
            <i class="fas fa-pen"></i> New Message
        </a>
    </div>
</div>

{{-- ── Success flash ──────────────────────────────────────── --}}
@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-700 font-semibold">{{ session('success') }}</p>
    </div>
@endif

{{-- ── Statistics cards ────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    {{-- Total conversations --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-comments text-gray-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Conversations</p>
            <p class="text-xl font-black text-gray-900 leading-tight">{{ $stats['total'] }}</p>
        </div>
    </div>

    {{-- Unread --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-envelope text-[#ff0808] text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Unread</p>
            <p class="text-xl font-black text-[#ff0808] leading-tight">{{ $stats['unread'] }}</p>
        </div>
    </div>

    {{-- Read --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-envelope-open text-green-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Read</p>
            <p class="text-xl font-black text-green-600 leading-tight">{{ $stats['read'] }}</p>
        </div>
    </div>

    {{-- Sent --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-paper-plane text-blue-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Sent</p>
            <p class="text-xl font-black text-blue-600 leading-tight">{{ $stats['sent'] }}</p>
        </div>
    </div>
</div>

{{-- ── Filters bar ─────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('partner.messages.index') }}"
          class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">

        {{-- Search --}}
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name or message…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
        </div>

        {{-- Status filter --}}
        <select name="status"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent bg-white min-w-[150px]">
            <option value="">All conversations</option>
            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread only</option>
            <option value="read"   {{ request('status') === 'read'   ? 'selected' : '' }}>Read only</option>
        </select>

        {{-- Actions --}}
        <div class="flex gap-2 shrink-0">
            <button type="submit"
                    class="px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('partner.messages.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </div>
    </form>
</div>

{{-- ── Result meta + layout toggle ────────────────────────── --}}
@if($conversations->isNotEmpty())
<div class="flex items-center justify-between mb-3 px-0.5">
    <p class="text-xs text-gray-400">
        Showing <span class="font-bold text-gray-700">{{ $conversations->count() }}</span>
        {{ Str::plural('conversation', $conversations->count()) }}
        @if(request()->hasAny(['search','status']))
            <span class="italic">(filtered)</span>
        @endif
    </p>

    {{-- Grid / List toggle --}}
    <div class="flex items-center gap-1 bg-gray-100 p-0.5 rounded-lg">
        <button id="btn-grid" onclick="setLayout('grid')"
                class="layout-btn px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all"
                title="Grid view">
            <i class="fas fa-th-large"></i>
        </button>
        <button id="btn-list" onclick="setLayout('list')"
                class="layout-btn px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all"
                title="List view">
            <i class="fas fa-list"></i>
        </button>
    </div>
</div>
@endif

{{-- ── Empty state ─────────────────────────────────────────── --}}
@if($conversations->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col items-center justify-center py-16 text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
            <i class="fas {{ request()->hasAny(['search','status']) ? 'fa-search' : 'fa-comments' }} text-gray-300 text-2xl"></i>
        </div>
        @if(request()->hasAny(['search','status']))
            <p class="text-sm font-semibold text-gray-500">No conversations match your filters</p>
            <p class="text-xs text-gray-400 mt-1 mb-4">Try adjusting your search or filters</p>
            <a href="{{ route('partner.messages.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
                <i class="fas fa-times"></i> Clear filters
            </a>
        @else
            <p class="text-sm font-semibold text-gray-500">No conversations yet</p>
            <p class="text-xs text-gray-400 mt-1 mb-4">Start a conversation with the admin team</p>
            <a href="{{ route('partner.messages.compose') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
                <i class="fas fa-pen"></i> Compose Message
            </a>
        @endif
    </div>

@else

    {{-- ══ GRID VIEW ════════════════════════════════════════ --}}
    <div id="view-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($conversations as $conv)
        <a href="{{ route('partner.messages.show', $conv['other']->id) }}"
           class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col gap-3 hover:shadow-md transition-all group
                  {{ $conv['unread_count'] > 0 ? 'border-l-4 border-l-[#ff0808]' : '' }}">

            {{-- Avatar + name + time --}}
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center">
                            <span class="text-sm font-black text-[#ff0808]">
                                {{ strtoupper(substr($conv['other']->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                        @if($conv['unread_count'] > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#ff0808] rounded-full flex items-center justify-center">
                                <span class="text-[9px] font-black text-white">{{ $conv['unread_count'] }}</span>
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-{{ $conv['unread_count'] > 0 ? 'black' : 'semibold' }} text-gray-900 leading-tight">
                            {{ $conv['other']->name ?? 'Unknown' }}
                        </p>
                        <p class="text-[10px] text-gray-400 mt-0.5">
                            {{ $conv['last_message']->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                {{-- Read/Unread badge --}}
                @if($conv['unread_count'] > 0)
                    <span class="px-2 py-0.5 bg-red-50 text-[#ff0808] text-[10px] font-bold rounded-md shrink-0">Unread</span>
                @else
                    <span class="px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-semibold rounded-md shrink-0">Read</span>
                @endif
            </div>

            {{-- Last message preview --}}
            <p class="text-xs text-gray-{{ $conv['unread_count'] > 0 ? '700' : '400' }} truncate border-t border-gray-100 pt-3">
                @if($conv['last_message']->sender_id == auth()->id())
                    <span class="text-gray-400">You: </span>
                @endif
                {{ Str::limit($conv['last_message']->message, 70) }}
            </p>

            {{-- Footer --}}
            <div class="flex items-center justify-between pt-1 border-t border-gray-100">
                <span class="text-[10px] text-gray-400">
                    <i class="fas fa-{{ $conv['last_message']->sender_id == auth()->id() ? 'paper-plane' : 'inbox' }} mr-1"></i>
                    {{ $conv['last_message']->sender_id == auth()->id() ? 'Sent' : 'Received' }}
                </span>
                <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-gray-500 transition-all"></i>
            </div>
        </a>
        @endforeach
    </div>

    {{-- ══ LIST VIEW ════════════════════════════════════════ --}}
    <div id="view-list" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <ul class="divide-y divide-gray-100">
            @foreach($conversations as $conv)
            <li>
                <a href="{{ route('partner.messages.show', $conv['other']->id) }}"
                   class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-all group
                          {{ $conv['unread_count'] > 0 ? 'bg-red-50/30' : '' }}">

                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center">
                            <span class="text-sm font-black text-[#ff0808]">
                                {{ strtoupper(substr($conv['other']->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                        @if($conv['unread_count'] > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#ff0808] rounded-full flex items-center justify-center">
                                <span class="text-[9px] font-black text-white">{{ $conv['unread_count'] }}</span>
                            </span>
                        @endif
                    </div>

                    {{-- Name + preview --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-0.5">
                            <p class="text-sm font-{{ $conv['unread_count'] > 0 ? 'black' : 'semibold' }} text-gray-900 truncate">
                                {{ $conv['other']->name ?? 'Unknown' }}
                            </p>
                            <span class="text-[10px] text-gray-400 flex-shrink-0 ml-2">
                                {{ $conv['last_message']->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-{{ $conv['unread_count'] > 0 ? '700' : '400' }} truncate">
                            @if($conv['last_message']->sender_id == auth()->id())
                                <span class="text-gray-400">You: </span>
                            @endif
                            {{ Str::limit($conv['last_message']->message, 60) }}
                        </p>
                    </div>

                    {{-- Status badge --}}
                    <div class="shrink-0 hidden sm:block">
                        @if($conv['unread_count'] > 0)
                            <span class="px-2 py-0.5 bg-red-50 text-[#ff0808] text-[10px] font-bold rounded-md">Unread</span>
                        @else
                            <span class="px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-semibold rounded-md">Read</span>
                        @endif
                    </div>

                    <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-gray-500 transition-all flex-shrink-0"></i>
                </a>
            </li>
            @endforeach
        </ul>
    </div>

@endif

{{-- ── Layout toggle JS ────────────────────────────────────── --}}
<script>
const MSG_LAYOUT_KEY = 'msgs_layout';

function setLayout(mode) {
    const grid    = document.getElementById('view-grid');
    const list    = document.getElementById('view-list');
    const btnGrid = document.getElementById('btn-grid');
    const btnList = document.getElementById('btn-list');

    if (!grid || !list) return;

    const activeClass   = ['bg-white', 'text-gray-800', 'shadow-sm'];
    const inactiveClass = ['text-gray-400'];

    if (mode === 'grid') {
        grid.classList.remove('hidden');
        list.classList.add('hidden');
        btnGrid.classList.add(...activeClass);
        btnGrid.classList.remove(...inactiveClass);
        btnList.classList.remove(...activeClass);
        btnList.classList.add(...inactiveClass);
    } else {
        list.classList.remove('hidden');
        grid.classList.add('hidden');
        btnList.classList.add(...activeClass);
        btnList.classList.remove(...inactiveClass);
        btnGrid.classList.remove(...activeClass);
        btnGrid.classList.add(...inactiveClass);
    }

    localStorage.setItem(MSG_LAYOUT_KEY, mode);
}

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem(MSG_LAYOUT_KEY) || 'list';
    setLayout(saved);
});
</script>

@endsection
