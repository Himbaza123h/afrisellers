@extends('layouts.home')
@section('page-content')

{{-- ── Header ─────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Social Media</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Social Media Profiles</h1>
        <p class="text-xs text-gray-500 mt-0.5">Your social media presence</p>
    </div>
    <a href="{{ route('partner.social.edit') }}"
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
<div class="grid grid-cols-3 gap-3 mb-6">
    {{-- Total platforms --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-share-alt text-gray-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Platforms</p>
            <p class="text-xl font-black text-gray-900 leading-tight">{{ $stats['total'] }}</p>
        </div>
    </div>

    {{-- Connected --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Connected</p>
            <p class="text-xl font-black text-green-600 leading-tight">{{ $stats['connected'] }}</p>
        </div>
    </div>

    {{-- Missing --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-circle text-amber-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Not Added</p>
            <p class="text-xl font-black text-amber-500 leading-tight">{{ $stats['missing'] }}</p>
        </div>
    </div>
</div>

{{-- ── Platforms card ───────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">

    {{-- Section header + layout toggle --}}
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">All Platforms</h2>

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

    @php
    $platforms = [
        ['field' => 'facebook_url',  'icon' => 'fab fa-facebook',  'color' => 'text-blue-600',  'bg' => 'bg-blue-50',   'label' => 'Facebook'],
        ['field' => 'instagram_url', 'icon' => 'fab fa-instagram', 'color' => 'text-pink-600',  'bg' => 'bg-pink-50',   'label' => 'Instagram'],
        ['field' => 'twitter_url',   'icon' => 'fab fa-twitter',   'color' => 'text-sky-500',   'bg' => 'bg-sky-50',    'label' => 'Twitter / X'],
        ['field' => 'linkedin_url',  'icon' => 'fab fa-linkedin',  'color' => 'text-blue-700',  'bg' => 'bg-blue-50',   'label' => 'LinkedIn'],
        ['field' => 'youtube_url',   'icon' => 'fab fa-youtube',   'color' => 'text-red-600',   'bg' => 'bg-red-50',    'label' => 'YouTube'],
        ['field' => 'tiktok_url',    'icon' => 'fab fa-tiktok',    'color' => 'text-gray-900',  'bg' => 'bg-gray-100',  'label' => 'TikTok'],
    ];
    @endphp

    {{-- ══ GRID VIEW ════════════════════════════════════════ --}}
    <div id="view-grid" class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach($platforms as $s)
        @php $url = $partner?->{$s['field']}; @endphp
        <div class="flex items-center gap-3 p-4 rounded-lg border
                    {{ $url ? 'border-gray-200' : 'border-dashed border-gray-200 opacity-60' }}">
            <div class="w-9 h-9 {{ $s['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="{{ $s['icon'] }} {{ $s['color'] }}"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-400 mb-0.5">{{ $s['label'] }}</p>
                @if($url)
                    <a href="{{ $url }}" target="_blank"
                       class="text-xs font-semibold text-[#ff0808] hover:underline truncate block">
                        {{ Str::limit($url, 35) }}
                    </a>
                @else
                    <p class="text-xs text-gray-400 italic">Not added</p>
                @endif
            </div>
            @if($url)
                <a href="{{ $url }}" target="_blank"
                   class="text-gray-300 hover:text-gray-500 flex-shrink-0 transition-colors">
                    <i class="fas fa-external-link-alt text-xs"></i>
                </a>
            @else
                <span class="w-4 h-4 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-minus text-[8px] text-gray-300"></i>
                </span>
            @endif
        </div>
        @endforeach
    </div>

    {{-- ══ LIST VIEW ════════════════════════════════════════ --}}
    <div id="view-list" class="hidden divide-y divide-gray-100">
        @foreach($platforms as $s)
        @php $url = $partner?->{$s['field']}; @endphp
        <div class="flex items-center gap-4 px-5 py-3.5 {{ $url ? 'hover:bg-gray-50' : 'opacity-60' }} transition-all">
            {{-- Icon --}}
            <div class="w-8 h-8 {{ $s['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="{{ $s['icon'] }} {{ $s['color'] }} text-sm"></i>
            </div>

            {{-- Label --}}
            <p class="text-sm font-semibold text-gray-700 w-28 flex-shrink-0">{{ $s['label'] }}</p>

            {{-- URL --}}
            <div class="flex-1 min-w-0">
                @if($url)
                    <a href="{{ $url }}" target="_blank"
                       class="text-xs font-semibold text-[#ff0808] hover:underline truncate block">
                        {{ Str::limit($url, 50) }}
                    </a>
                @else
                    <p class="text-xs text-gray-400 italic">Not added</p>
                @endif
            </div>

            {{-- Status badge --}}
            @if($url)
                <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-md flex-shrink-0 flex items-center gap-1">
                    <i class="fas fa-check-circle text-[8px]"></i> Connected
                </span>
                <a href="{{ $url }}" target="_blank"
                   class="text-gray-300 hover:text-gray-500 flex-shrink-0 transition-colors">
                    <i class="fas fa-external-link-alt text-xs"></i>
                </a>
            @else
                <span class="px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-semibold rounded-md flex-shrink-0">Not added</span>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- ── Prompt to fill missing platforms ────────────────────── --}}
@if($stats['missing'] > 0)
<div class="mt-4 bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <i class="fas fa-lightbulb text-amber-500"></i>
        <p class="text-xs text-amber-700 font-semibold">
            You have {{ $stats['missing'] }} platform{{ $stats['missing'] > 1 ? 's' : '' }} not yet connected. Boost your visibility by adding them.
        </p>
    </div>
    <a href="{{ route('partner.social.edit') }}"
       class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 transition-all">
        <i class="fas fa-pencil-alt text-[10px]"></i> Add Now
    </a>
</div>
@endif

{{-- ── Layout toggle JS ────────────────────────────────────── --}}
<script>
const SOCIAL_LAYOUT_KEY = 'social_layout';

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

    localStorage.setItem(SOCIAL_LAYOUT_KEY, mode);
}

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem(SOCIAL_LAYOUT_KEY) || 'grid';
    setLayout(saved);
});
</script>

@endsection
