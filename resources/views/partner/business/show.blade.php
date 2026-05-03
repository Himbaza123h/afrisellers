@extends('layouts.home')
@section('page-content')

{{-- ── Header ─────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Business Type</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Business Type & Category</h1>
        <p class="text-xs text-gray-500 mt-0.5">Your industry, type and services</p>
    </div>
    <a href="{{ route('partner.business.edit') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
        <i class="fas fa-pencil-alt"></i> Edit
    </a>
</div>

{{-- ── Success flash ──────────────────────────────────────── --}}
@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-700 font-semibold">{{ session('success') }}</p>
    </div>
@endif

{{-- ── Statistics cards ────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    {{-- Industry --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-industry text-amber-500 text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Industry</p>
            <p class="text-sm font-black text-gray-900 leading-tight truncate">
                {{ $stats['industry'] ?? '—' }}
            </p>
        </div>
    </div>

    {{-- Business Type --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-briefcase text-indigo-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Business Type</p>
            @if($stats['business_type'])
                <span class="inline-block mt-0.5 px-2 py-0.5 bg-indigo-50 text-indigo-700 text-xs font-black rounded-md">
                    {{ $stats['business_type'] }}
                </span>
            @else
                <p class="text-sm font-black text-gray-900 leading-tight">—</p>
            @endif
        </div>
    </div>

    {{-- Services count --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-concierge-bell text-[#ff0808] text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Services Offered</p>
            <p class="text-xl font-black text-[#ff0808] leading-tight">{{ $stats['services_count'] }}</p>
        </div>
    </div>
</div>

{{-- ── Classification detail card ──────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-4">
    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Classification</h2>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <dt class="text-xs text-gray-400 mb-0.5">Industry</dt>
            <dd class="text-sm font-semibold text-gray-900">{{ $partner?->industry ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs text-gray-400 mb-0.5">Business Type</dt>
            <dd>
                @if($partner?->business_type)
                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-md">
                        {{ $partner->business_type }}
                    </span>
                @else
                    <span class="text-sm font-semibold text-gray-900">—</span>
                @endif
            </dd>
        </div>
    </dl>
</div>

{{-- ── Services section ─────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">

    {{-- Section header --}}
    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex-1">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Services Offered</h2>
        </div>

        @if(count($allServices))
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('partner.business.show') }}" class="flex items-center gap-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                    <input type="text" name="search" value="{{ $serviceSearch }}"
                           placeholder="Search service…"
                           class="pl-7 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent w-40">
                </div>
                <button type="submit"
                        class="px-3 py-1.5 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
                    <i class="fas fa-filter"></i>
                </button>
                @if($serviceSearch)
                    <a href="{{ route('partner.business.show') }}"
                       class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            {{-- Grid / List toggle --}}
            <div class="flex items-center gap-1 bg-gray-100 p-0.5 rounded-lg shrink-0">
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
    </div>

    {{-- Result count --}}
    @if(count($allServices))
    <div class="px-5 pt-3 pb-0">
        <p class="text-xs text-gray-400">
            Showing <span class="font-bold text-gray-700">{{ count($services) }}</span>
            of <span class="font-bold text-gray-700">{{ count($allServices) }}</span>
            {{ Str::plural('service', count($allServices)) }}
            @if($serviceSearch)
                for <span class="italic text-gray-500">"{{ $serviceSearch }}"</span>
            @endif
        </p>
    </div>
    @endif

    {{-- ── Empty: nothing added ─────────────────────────────── --}}
    @if(count($allServices) === 0)
        <div class="flex flex-col items-center justify-center py-14 text-center px-5">
            <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100">
                <i class="fas fa-concierge-bell text-gray-300 text-xl"></i>
            </div>
            <p class="text-sm font-semibold text-gray-500">No services added yet</p>
            <p class="text-xs text-gray-400 mt-1 mb-4">Add the services your business offers</p>
            <a href="{{ route('partner.business.edit') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
                <i class="fas fa-pencil-alt"></i> Edit Business Info
            </a>
        </div>

    {{-- ── Empty: no search match ────────────────────────────── --}}
    @elseif(count($services) === 0)
        <div class="flex flex-col items-center justify-center py-12 text-center px-5">
            <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100">
                <i class="fas fa-search text-gray-300 text-xl"></i>
            </div>
            <p class="text-sm font-semibold text-gray-500">No services match "{{ $serviceSearch }}"</p>
            <a href="{{ route('partner.business.show') }}"
               class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
                <i class="fas fa-times"></i> Clear search
            </a>
        </div>

    @else

        {{-- ══ GRID VIEW ════════════════════════════════════ --}}
        <div id="view-grid" class="p-5 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
            @foreach($services as $service)
                <div class="flex items-center gap-2 px-3 py-2.5 bg-red-50 border border-red-100 rounded-lg">
                    <i class="fas fa-check-circle text-[#ff0808] text-[10px] flex-shrink-0"></i>
                    <span class="text-xs font-semibold text-[#ff0808] truncate">{{ $service }}</span>
                </div>
            @endforeach
        </div>

        {{-- ══ LIST VIEW ════════════════════════════════════ --}}
        <div id="view-list" class="hidden divide-y divide-gray-100">
            @foreach($services as $index => $service)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-all">
                    <span class="text-[10px] font-bold text-gray-300 w-5 text-right flex-shrink-0">{{ $index + 1 }}</span>
                    <div class="w-7 h-7 rounded-md bg-red-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-[#ff0808] text-[10px]"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">{{ $service }}</span>
                </div>
            @endforeach
        </div>

    @endif
</div>

{{-- ── Layout toggle JS ────────────────────────────────────── --}}
<script>
const BIZ_LAYOUT_KEY = 'biz_layout';

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

    localStorage.setItem(BIZ_LAYOUT_KEY, mode);
}

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem(BIZ_LAYOUT_KEY) || 'grid';
    setLayout(saved);
});
</script>

@endsection
