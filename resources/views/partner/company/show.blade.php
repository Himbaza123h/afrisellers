@extends('layouts.home')
@section('page-content')

{{-- ── Header ─────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Company Info</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Company Information</h1>
        <p class="text-xs text-gray-500 mt-0.5">Your basic company details</p>
    </div>
    <a href="{{ route('partner.company.edit') }}"
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

{{-- ── Statistics cards ─────────────────────────────────────── --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-building text-gray-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Fields</p>
            <p class="text-xl font-black text-gray-900 leading-tight">{{ $stats['total'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Filled</p>
            <p class="text-xl font-black text-green-600 leading-tight">{{ $stats['filled'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-circle text-amber-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Empty</p>
            <p class="text-xl font-black text-amber-500 leading-tight">{{ $stats['missing'] }}</p>
        </div>
    </div>
</div>

{{-- ── Fields card ──────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">

    {{-- Toolbar: search + filter + layout toggle --}}
    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">

        {{-- Search --}}
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-xs mt-2"></i>
            <input id="company-search"
                   type="text"
                   placeholder="Search fields…"
                   oninput="applyFilters()"
                   class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300 transition-all">
        </div>

        {{-- Filter pills --}}
        <div class="flex items-center gap-1.5">
            <button onclick="setFilter('all')"    class="filter-pill px-3 py-1.5 rounded-full text-[11px] font-bold transition-all">All</button>
            <button onclick="setFilter('filled')" class="filter-pill px-3 py-1.5 rounded-full text-[11px] font-bold transition-all">Filled</button>
            <button onclick="setFilter('empty')"  class="filter-pill px-3 py-1.5 rounded-full text-[11px] font-bold transition-all">Empty</button>
        </div>

        {{-- Layout toggle --}}
        <div class="flex items-center gap-1 bg-gray-100 p-0.5 rounded-lg self-end sm:self-auto">
            <button id="btn-grid" onclick="setLayout('grid')"
                    class="layout-btn px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all" title="Grid view">
                <i class="fas fa-th-large"></i>
            </button>
            <button id="btn-list" onclick="setLayout('list')"
                    class="layout-btn px-2.5 py-1.5 rounded-md text-xs font-semibold transition-all" title="List view">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>

    @php
    $fields = [
        ['field' => 'company_name',        'icon' => 'fas fa-building',        'color' => 'text-blue-600',   'bg' => 'bg-blue-50',    'label' => 'Company Name'],
        ['field' => 'trading_name',        'icon' => 'fas fa-store',           'color' => 'text-purple-600', 'bg' => 'bg-purple-50',  'label' => 'Trading Name'],
        ['field' => 'registration_number', 'icon' => 'fas fa-id-badge',        'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50',  'label' => 'Registration Number'],
        ['field' => 'partner_type',        'icon' => 'fas fa-handshake',       'color' => 'text-red-500',    'bg' => 'bg-red-50',     'label' => 'Partnership Type'],
        ['field' => 'established',         'icon' => 'fas fa-calendar-alt',    'color' => 'text-amber-600',  'bg' => 'bg-amber-50',   'label' => 'Year Established'],
        ['field' => 'country',             'icon' => 'fas fa-globe',           'color' => 'text-teal-600',   'bg' => 'bg-teal-50',    'label' => 'Country of Registration'],
        ['field' => 'physical_address',    'icon' => 'fas fa-map-marker-alt',  'color' => 'text-orange-600', 'bg' => 'bg-orange-50',  'label' => 'Physical Address'],
        ['field' => 'website_url',         'icon' => 'fas fa-globe',           'color' => 'text-cyan-600',   'bg' => 'bg-cyan-50',    'label' => 'Website'],
    ];
    @endphp

    {{-- ══ GRID VIEW ════════════════════════════════════════ --}}
    <div id="view-grid" class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach($fields as $f)
        @php
            $val = $partner?->{$f['field']};
            $display = $f['field'] === 'website_url' && $val
                ? Str::limit($val, 40)
                : ($val ?? null);
        @endphp
        <div class="company-field-item flex items-center gap-3 p-4 rounded-lg border
                    {{ $val ? 'border-gray-200' : 'border-dashed border-gray-200 opacity-60' }}"
             data-filled="{{ $val ? '1' : '0' }}"
             data-label="{{ strtolower($f['label']) }}">
            <div class="w-9 h-9 {{ $f['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="{{ $f['icon'] }} {{ $f['color'] }}"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-400 mb-0.5">{{ $f['label'] }}</p>
                @if($val)
                    @if($f['field'] === 'partner_type')
                        <span class="px-2 py-0.5 bg-red-50 text-[#ff0808] rounded-md text-xs font-bold">{{ $val }}</span>
                    @elseif($f['field'] === 'website_url')
                        <a href="{{ $val }}" target="_blank"
                           class="text-xs font-semibold text-[#ff0808] hover:underline flex items-center gap-1 truncate">
                            {{ $display }} <i class="fas fa-external-link-alt text-[9px]"></i>
                        </a>
                    @else
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $display }}</p>
                    @endif
                @else
                    <p class="text-xs text-gray-400 italic">Not added</p>
                @endif
            </div>
            @if($val)
                <span class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-[8px] text-green-600"></i>
                </span>
            @else
                <span class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-minus text-[8px] text-gray-300"></i>
                </span>
            @endif
        </div>
        @endforeach
    </div>

    {{-- ══ LIST VIEW ════════════════════════════════════════ --}}
    <div id="view-list" class="hidden divide-y divide-gray-100">
        @foreach($fields as $f)
        @php
            $val = $partner?->{$f['field']};
            $display = $f['field'] === 'website_url' && $val
                ? Str::limit($val, 40)
                : ($val ?? null);
        @endphp
        <div class="company-field-item flex items-center gap-4 px-5 py-3.5 {{ $val ? 'hover:bg-gray-50' : 'opacity-60' }} transition-all"
             data-filled="{{ $val ? '1' : '0' }}"
             data-label="{{ strtolower($f['label']) }}">
            <div class="w-8 h-8 {{ $f['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="{{ $f['icon'] }} {{ $f['color'] }} text-sm"></i>
            </div>
            <p class="text-sm font-semibold text-gray-700 w-44 flex-shrink-0">{{ $f['label'] }}</p>
            <div class="flex-1 min-w-0">
                @if($val)
                    @if($f['field'] === 'partner_type')
                        <span class="px-2 py-0.5 bg-red-50 text-[#ff0808] rounded-md text-xs font-bold">{{ $val }}</span>
                    @elseif($f['field'] === 'website_url')
                        <a href="{{ $val }}" target="_blank"
                           class="text-xs font-semibold text-[#ff0808] hover:underline flex items-center gap-1 truncate">
                            {{ $display }} <i class="fas fa-external-link-alt text-[9px]"></i>
                        </a>
                    @else
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $display }}</p>
                    @endif
                @else
                    <p class="text-xs text-gray-400 italic">Not added</p>
                @endif
            </div>
            @if($val)
                <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-md flex-shrink-0 flex items-center gap-1">
                    <i class="fas fa-check-circle text-[8px]"></i> Filled
                </span>
            @else
                <span class="px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-semibold rounded-md flex-shrink-0">Empty</span>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Empty state --}}
    <div id="no-results" class="hidden py-12 text-center">
        <i class="fas fa-search text-gray-200 text-3xl mb-3"></i>
        <p class="text-sm text-gray-400">No fields match your search.</p>
    </div>
</div>

{{-- ── Prompt to complete missing fields ──────────────────────── --}}
@if($stats['missing'] > 0)
<div class="mt-4 bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <i class="fas fa-lightbulb text-amber-500"></i>
        <p class="text-xs text-amber-700 font-semibold">
            You have {{ $stats['missing'] }} field{{ $stats['missing'] > 1 ? 's' : '' }} not yet filled. Complete your company profile.
        </p>
    </div>
    <a href="{{ route('partner.company.edit') }}"
       class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 transition-all">
        <i class="fas fa-pencil-alt text-[10px]"></i> Add Now
    </a>
</div>
@endif

{{-- ── JS ───────────────────────────────────────────────────── --}}
<script>
const COMPANY_LAYOUT_KEY = 'company_layout';
let currentFilter = 'all';

function setLayout(mode) {
    const grid    = document.getElementById('view-grid');
    const list    = document.getElementById('view-list');
    const btnGrid = document.getElementById('btn-grid');
    const btnList = document.getElementById('btn-list');
    if (!grid || !list) return;

    const active   = ['bg-white', 'text-gray-800', 'shadow-sm'];
    const inactive = ['text-gray-400'];

    if (mode === 'grid') {
        grid.classList.remove('hidden');
        list.classList.add('hidden');
        btnGrid.classList.add(...active);    btnGrid.classList.remove(...inactive);
        btnList.classList.remove(...active); btnList.classList.add(...inactive);
    } else {
        list.classList.remove('hidden');
        grid.classList.add('hidden');
        btnList.classList.add(...active);    btnList.classList.remove(...inactive);
        btnGrid.classList.remove(...active); btnGrid.classList.add(...inactive);
    }
    localStorage.setItem(COMPANY_LAYOUT_KEY, mode);
}

function setFilter(mode) {
    currentFilter = mode;
    document.querySelectorAll('.filter-pill').forEach(btn => {
        const label = btn.textContent.trim().toLowerCase();
        const isActive = label === mode || (mode === 'all' && label === 'all');
        btn.classList.toggle('bg-[#ff0808]', isActive);
        btn.classList.toggle('text-white',    isActive);
        btn.classList.toggle('bg-gray-100',  !isActive);
        btn.classList.toggle('text-gray-500',!isActive);
    });
    applyFilters();
}

function applyFilters() {
    const query = (document.getElementById('company-search')?.value || '').toLowerCase().trim();
    const items = document.querySelectorAll('.company-field-item');
    let visible = 0;

    items.forEach(el => {
        const label  = el.dataset.label || '';
        const filled = el.dataset.filled === '1';

        const matchSearch = !query || label.includes(query);
        const matchFilter = currentFilter === 'all'
                         || (currentFilter === 'filled' && filled)
                         || (currentFilter === 'empty'  && !filled);

        const show = matchSearch && matchFilter;
        el.classList.toggle('hidden', !show);
        if (show) visible++;
    });

    document.getElementById('no-results')?.classList.toggle('hidden', visible > 0);
}

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem(COMPANY_LAYOUT_KEY) || 'grid';
    setLayout(saved);
    setFilter('all');
});
</script>

@endsection
