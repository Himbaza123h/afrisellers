@extends('layouts.home')
@section('page-content')

{{-- ── Header ─────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Documents</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">My Documents</h1>
        <p class="text-xs text-gray-500 mt-0.5">Your uploaded files and certificates</p>
    </div>
    <a href="{{ route('partner.documents.upload') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
        <i class="fas fa-upload"></i> Upload Document
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
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    {{-- Total --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-folder text-gray-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Total</p>
            <p class="text-xl font-black text-gray-900 leading-tight">{{ $stats['total'] }}</p>
        </div>
    </div>

    {{-- Verified --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Verified</p>
            <p class="text-xl font-black text-green-600 leading-tight">{{ $stats['verified'] }}</p>
        </div>
    </div>

    {{-- Pending --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-clock text-amber-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Pending</p>
            <p class="text-xl font-black text-amber-600 leading-tight">{{ $stats['pending'] }}</p>
        </div>
    </div>

    {{-- Total size --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-hdd text-blue-500 text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Storage</p>
            @php
                $bytes = $stats['total_size'];
                $label = $bytes >= 1048576
                    ? number_format($bytes / 1048576, 1) . ' MB'
                    : ($bytes >= 1024 ? number_format($bytes / 1024, 1) . ' KB' : $bytes . ' B');
            @endphp
            <p class="text-xl font-black text-blue-600 leading-tight">{{ $label }}</p>
        </div>
    </div>
</div>

{{-- ── Filters bar ─────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('partner.documents.index') }}"
          class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">

        {{-- Search --}}
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by title or notes…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
        </div>

        {{-- Type filter --}}
        <select name="type"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent bg-white min-w-[160px]">
            <option value="">All types</option>
            @foreach(\App\Models\PartnerDocument::$types as $val => $label)
                <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        {{-- Status filter --}}
        <select name="status"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent bg-white min-w-[140px]">
            <option value="">All statuses</option>
            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
        </select>

        {{-- Actions --}}
        <div class="flex gap-2 shrink-0">
            <button type="submit"
                    class="px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search','type','status']))
                <a href="{{ route('partner.documents.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </div>
    </form>
</div>

{{-- ── Result meta + layout toggle ────────────────────────── --}}
@if($documents->isNotEmpty())
<div class="flex items-center justify-between mb-3 px-0.5">
    <p class="text-xs text-gray-400">
        Showing <span class="font-bold text-gray-700">{{ $documents->count() }}</span>
        {{ Str::plural('document', $documents->count()) }}
        @if(request()->hasAny(['search','type','status']))
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
@if($documents->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col items-center justify-center py-16 text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
            <i class="fas {{ request()->hasAny(['search','type','status']) ? 'fa-search' : 'fa-folder-open' }} text-gray-300 text-2xl"></i>
        </div>
        @if(request()->hasAny(['search','type','status']))
            <p class="text-sm font-semibold text-gray-500">No documents match your filters</p>
            <p class="text-xs text-gray-400 mt-1 mb-4">Try adjusting your search or filters</p>
            <a href="{{ route('partner.documents.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
                <i class="fas fa-times"></i> Clear filters
            </a>
        @else
            <p class="text-sm font-semibold text-gray-500">No documents yet</p>
            <p class="text-xs text-gray-400 mt-1 mb-4">Upload certificates, contracts, or IDs</p>
            <a href="{{ route('partner.documents.upload') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
                <i class="fas fa-upload"></i> Upload Document
            </a>
        @endif
    </div>

@else

    {{-- ══ GRID VIEW ════════════════════════════════════════ --}}
    <div id="view-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($documents as $doc)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col gap-3 hover:shadow-md transition-shadow">
            {{-- Icon + type + status --}}
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $doc->isPdf() ? 'bg-red-50' : ($doc->isImage() ? 'bg-blue-50' : 'bg-gray-50') }}">
                        <i class="fas {{ $doc->isPdf() ? 'fa-file-pdf text-red-500' : ($doc->isImage() ? 'fa-file-image text-blue-500' : 'fa-file text-gray-400') }}"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 leading-tight">{{ $doc->title }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $doc->getTypeLabel() }}</p>
                    </div>
                </div>
                @if($doc->is_verified)
                    <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-md flex items-center gap-1 shrink-0">
                        <i class="fas fa-check-circle text-[8px]"></i> Verified
                    </span>
                @else
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-md shrink-0">Pending</span>
                @endif
            </div>

            {{-- Meta --}}
            <div class="text-xs text-gray-400 flex items-center gap-3">
                <span><i class="fas fa-hdd mr-1"></i>{{ $doc->file_size_formatted }}</span>
                <span><i class="fas fa-clock mr-1"></i>{{ $doc->created_at->format('d M Y') }}</span>
            </div>

            @if($doc->notes)
                <p class="text-xs text-gray-500 italic border-t border-gray-100 pt-3">{{ Str::limit($doc->notes, 80) }}</p>
            @endif

            {{-- Actions --}}
            <div class="flex items-center gap-2 pt-1 border-t border-gray-100 mt-auto">
                <a href="{{ route('partner.documents.download', $doc) }}"
                   class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition-all">
                    <i class="fas fa-download text-[10px]"></i> Download
                </a>
                <form action="{{ route('partner.documents.destroy', $doc) }}" method="POST"
                      onsubmit="return confirm('Delete this document?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-3 py-2 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100 transition-all">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══ LIST VIEW ════════════════════════════════════════ --}}
    <div id="view-list" class="hidden flex-col gap-2">
        @foreach($documents as $doc)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 flex items-center gap-4 hover:shadow-md transition-shadow">
            {{-- File icon --}}
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                {{ $doc->isPdf() ? 'bg-red-50' : ($doc->isImage() ? 'bg-blue-50' : 'bg-gray-50') }}">
                <i class="fas {{ $doc->isPdf() ? 'fa-file-pdf text-red-500' : ($doc->isImage() ? 'fa-file-image text-blue-500' : 'fa-file text-gray-400') }} text-sm"></i>
            </div>

            {{-- Title + type --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 truncate">{{ $doc->title }}</p>
                <p class="text-[10px] text-gray-400">{{ $doc->getTypeLabel() }}</p>
            </div>

            {{-- Meta --}}
            <div class="hidden sm:flex items-center gap-4 text-xs text-gray-400 shrink-0">
                <span><i class="fas fa-hdd mr-1"></i>{{ $doc->file_size_formatted }}</span>
                <span><i class="fas fa-clock mr-1"></i>{{ $doc->created_at->format('d M Y') }}</span>
            </div>

            {{-- Status badge --}}
            <div class="shrink-0">
                @if($doc->is_verified)
                    <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-md flex items-center gap-1">
                        <i class="fas fa-check-circle text-[8px]"></i> Verified
                    </span>
                @else
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-md">Pending</span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('partner.documents.download', $doc) }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition-all">
                    <i class="fas fa-download text-[10px]"></i>
                    <span class="hidden sm:inline">Download</span>
                </a>
                <form action="{{ route('partner.documents.destroy', $doc) }}" method="POST"
                      onsubmit="return confirm('Delete this document?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100 transition-all">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

@endif

{{-- ── Layout toggle JS ────────────────────────────────────── --}}
<script>
const STORAGE_KEY = 'docs_layout';

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
        list.classList.add('flex');
        grid.classList.add('hidden');
        btnList.classList.add(...activeClass);
        btnList.classList.remove(...inactiveClass);
        btnGrid.classList.remove(...activeClass);
        btnGrid.classList.add(...inactiveClass);
    }

    localStorage.setItem(STORAGE_KEY, mode);
}

// Restore saved preference on load
document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem(STORAGE_KEY) || 'grid';
    setLayout(saved);
});
</script>

@endsection
