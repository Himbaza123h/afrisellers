@extends('layouts.home')
@section('page-content')

{{-- ── Header ─────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-lg font-black text-gray-900">Profile Overview</h1>
        <p class="text-xs text-gray-500 mt-0.5">Your partner profile and completion status</p>
    </div>
    <a href="{{ route('partner.company.edit') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
        <i class="fas fa-pencil-alt"></i> Edit Profile
    </a>
</div>

{{-- ── Profile Card ─────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">

    {{-- Cover --}}
    <div class="h-28 bg-gradient-to-r from-red-50 to-gray-100 relative">
        @if($partner?->cover_image)
            <img src="{{ Storage::url($partner->cover_image) }}" class="w-full h-full object-cover">
        @endif
        <a href="{{ route('partner.branding.edit') }}"
           class="absolute top-3 right-3 px-2 py-1 bg-white text-xs text-gray-600 font-semibold rounded-md border border-gray-200 hover:bg-gray-50 transition-all">
            <i class="fas fa-camera mr-1"></i> Cover
        </a>
    </div>

    {{-- Avatar + Info --}}
    <div class="px-6 pb-6">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-8 mb-4">
            <div class="relative">
                @if($partner?->logo)
                    <img src="{{ Storage::url($partner->logo) }}" alt="Logo"
                         class="w-16 h-16 rounded-lg object-cover border-2 border-white shadow-sm">
                @else
                    <div class="w-16 h-16 rounded-lg bg-red-50 border-2 border-white shadow-sm flex items-center justify-center">
                        <i class="fas fa-handshake text-[#ff0808] text-xl"></i>
                    </div>
                @endif
                <a href="{{ route('partner.branding.edit') }}"
                   class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#ff0808] rounded-full flex items-center justify-center">
                    <i class="fas fa-camera text-white text-[8px]"></i>
                </a>
            </div>

            <div class="flex-1 pt-2">
                <h2 class="text-base font-black text-gray-900">
                    {{ $partner?->company_name ?? $user->name }}
                </h2>
                <p class="text-xs text-gray-500">
                    {{ $partner?->partner_type ?? 'Partner' }}
                    @if($partner?->country) &mdash; {{ $partner->country }} @endif
                </p>
            </div>

            <div class="sm:ml-auto">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold
                    {{ $overallPercent === 100 ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse
                        {{ $overallPercent === 100 ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                    {{ $overallPercent }}% Complete
                </span>
            </div>
        </div>

        <div class="mb-4">
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full transition-all {{ $overallPercent === 100 ? 'bg-green-500' : 'bg-[#ff0808]' }}"
                     style="width: {{ $overallPercent }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">{{ $filledFields }} of {{ $totalFields }} fields filled</p>
        </div>

        @if($partner?->short_description)
            <p class="text-sm text-gray-600">{{ $partner->short_description }}</p>
        @else
            <p class="text-xs text-gray-400 italic">No description added yet.
                <a href="{{ route('partner.branding.edit') }}" class="text-[#ff0808] font-semibold not-italic">Add one →</a>
            </p>
        @endif
    </div>
</div>

{{-- ── Stats cards ──────────────────────────────────────────── --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-layer-group text-gray-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Sections</p>
            <p class="text-xl font-black text-gray-900 leading-tight">{{ $stats['sections'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Complete</p>
            <p class="text-xl font-black text-green-600 leading-tight">{{ $stats['complete'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-circle text-amber-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Incomplete</p>
            <p class="text-xl font-black text-amber-500 leading-tight">{{ $stats['incomplete'] }}</p>
        </div>
    </div>
</div>

{{-- ── Sections card ────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">

    {{-- Toolbar --}}
    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">

        {{-- Search --}}
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-xs mt-2"></i>
            <input id="profile-search"
                   type="text"
                   placeholder="Search sections…"
                   oninput="applyFilters()"
                   class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300 transition-all">
        </div>

        {{-- Filter pills --}}
        <div class="flex items-center gap-1.5">
            <button onclick="setFilter('all')"        class="filter-pill px-3 py-1.5 rounded-full text-[11px] font-bold transition-all">All</button>
            <button onclick="setFilter('complete')"   class="filter-pill px-3 py-1.5 rounded-full text-[11px] font-bold transition-all">Complete</button>
            <button onclick="setFilter('incomplete')" class="filter-pill px-3 py-1.5 rounded-full text-[11px] font-bold transition-all">Incomplete</button>
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

    {{-- ══ GRID VIEW ════════════════════════════════════════ --}}
    <div id="view-grid" class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($sections as $section)
        <div class="profile-section-item flex flex-col gap-3 p-4 rounded-lg border
                    {{ $section['complete'] ? 'border-gray-200' : 'border-dashed border-gray-200' }}"
             data-complete="{{ $section['complete'] ? '1' : '0' }}"
             data-label="{{ strtolower($section['label']) }}">

            {{-- Icon + Label + Badge --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 {{ $section['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="{{ $section['icon'] }} {{ $section['color'] }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-800 truncate">{{ $section['label'] }}</p>
                    <p class="text-[10px] text-gray-400">{{ $section['filled'] }}/{{ $section['total'] }} fields</p>
                </div>
                @if($section['complete'])
                    <span class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-[8px] text-green-600"></i>
                    </span>
                @else
                    <span class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-minus text-[8px] text-gray-300"></i>
                    </span>
                @endif
            </div>

            {{-- Progress bar --}}
            <div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full {{ $section['complete'] ? 'bg-green-500' : 'bg-[#ff0808]' }}"
                         style="width: {{ $section['percent'] }}%"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $section['percent'] }}% complete</p>
            </div>

            {{-- Action --}}
            <a href="{{ route($section['complete'] ? $section['route'] : $section['edit']) }}"
               class="text-[10px] font-bold {{ $section['complete'] ? 'text-gray-500 hover:text-gray-700' : 'text-[#ff0808] hover:underline' }}">
                {{ $section['complete'] ? 'View →' : 'Complete →' }}
            </a>
        </div>
        @endforeach
    </div>

    {{-- ══ LIST VIEW ════════════════════════════════════════ --}}
    <div id="view-list" class="hidden divide-y divide-gray-100">
        @foreach($sections as $section)
        <div class="profile-section-item flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-all"
             data-complete="{{ $section['complete'] ? '1' : '0' }}"
             data-label="{{ strtolower($section['label']) }}">

            <div class="w-8 h-8 {{ $section['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="{{ $section['icon'] }} {{ $section['color'] }} text-sm"></i>
            </div>

            <p class="text-sm font-semibold text-gray-700 w-36 flex-shrink-0">{{ $section['label'] }}</p>

            {{-- Progress --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $section['complete'] ? 'bg-green-500' : 'bg-[#ff0808]' }}"
                             style="width: {{ $section['percent'] }}%"></div>
                    </div>
                    <span class="text-[10px] text-gray-400 flex-shrink-0">{{ $section['filled'] }}/{{ $section['total'] }}</span>
                </div>
            </div>

            @if($section['complete'])
                <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-md flex-shrink-0 flex items-center gap-1">
                    <i class="fas fa-check-circle text-[8px]"></i> Complete
                </span>
            @else
                <span class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[10px] font-semibold rounded-md flex-shrink-0">
                    {{ $section['missing'] }} left
                </span>
            @endif

            <a href="{{ route($section['complete'] ? $section['route'] : $section['edit']) }}"
               class="text-[10px] font-bold {{ $section['complete'] ? 'text-gray-500 hover:text-gray-700' : 'text-[#ff0808] hover:underline' }} flex-shrink-0">
                {{ $section['complete'] ? 'View' : 'Complete' }}
            </a>
        </div>
        @endforeach
    </div>

    {{-- Empty state --}}
    <div id="no-results" class="hidden py-12 text-center">
        <i class="fas fa-search text-gray-200 text-3xl mb-3"></i>
        <p class="text-sm text-gray-400">No sections match your search.</p>
    </div>
</div>

{{-- ── Social Media ─────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-gray-900">Social Media</h3>
        <a href="{{ route('partner.social.edit') }}" class="text-xs text-[#ff0808] font-semibold hover:underline">Edit</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach([
            ['field' => 'facebook_url',  'icon' => 'fab fa-facebook',  'color' => 'text-blue-600',  'bg' => 'bg-blue-50',  'label' => 'Facebook'],
            ['field' => 'instagram_url', 'icon' => 'fab fa-instagram', 'color' => 'text-pink-600',  'bg' => 'bg-pink-50',  'label' => 'Instagram'],
            ['field' => 'twitter_url',   'icon' => 'fab fa-twitter',   'color' => 'text-sky-500',   'bg' => 'bg-sky-50',   'label' => 'Twitter / X'],
            ['field' => 'linkedin_url',  'icon' => 'fab fa-linkedin',  'color' => 'text-blue-700',  'bg' => 'bg-blue-50',  'label' => 'LinkedIn'],
            ['field' => 'youtube_url',   'icon' => 'fab fa-youtube',   'color' => 'text-red-600',   'bg' => 'bg-red-50',   'label' => 'YouTube'],
            ['field' => 'tiktok_url',    'icon' => 'fab fa-tiktok',    'color' => 'text-gray-900',  'bg' => 'bg-gray-100', 'label' => 'TikTok'],
        ] as $s)
            @if($partner?->{$s['field']})
                <a href="{{ $partner->{$s['field']} }}" target="_blank"
                   class="flex items-center gap-2 px-3 py-2 {{ $s['bg'] }} rounded-lg text-xs font-semibold {{ $s['color'] }} hover:opacity-80 transition-all">
                    <i class="{{ $s['icon'] }}"></i>
                    <span>{{ $s['label'] }}</span>
                </a>
            @else
                <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg text-xs text-gray-300 font-semibold">
                    <i class="{{ $s['icon'] }}"></i>
                    <span>{{ $s['label'] }}</span>
                </div>
            @endif
        @endforeach
    </div>
</div>

{{-- ── JS ───────────────────────────────────────────────────── --}}
<script>
const PROFILE_LAYOUT_KEY = 'profile_layout';
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
    localStorage.setItem(PROFILE_LAYOUT_KEY, mode);
}

function setFilter(mode) {
    currentFilter = mode;
    document.querySelectorAll('.filter-pill').forEach(btn => {
        const label    = btn.textContent.trim().toLowerCase();
        const isActive = label === mode || (mode === 'all' && label === 'all');
        btn.classList.toggle('bg-[#ff0808]', isActive);
        btn.classList.toggle('text-white',    isActive);
        btn.classList.toggle('bg-gray-100',  !isActive);
        btn.classList.toggle('text-gray-500',!isActive);
    });
    applyFilters();
}

function applyFilters() {
    const query = (document.getElementById('profile-search')?.value || '').toLowerCase().trim();
    const items = document.querySelectorAll('.profile-section-item');
    let visible = 0;

    items.forEach(el => {
        const label    = el.dataset.label    || '';
        const complete = el.dataset.complete === '1';

        const matchSearch = !query || label.includes(query);
        const matchFilter = currentFilter === 'all'
                         || (currentFilter === 'complete'   && complete)
                         || (currentFilter === 'incomplete' && !complete);

        const show = matchSearch && matchFilter;
        el.classList.toggle('hidden', !show);
        if (show) visible++;
    });

    document.getElementById('no-results')?.classList.toggle('hidden', visible > 0);
}

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem(PROFILE_LAYOUT_KEY) || 'grid';
    setLayout(saved);
    setFilter('all');
});
</script>

@endsection
