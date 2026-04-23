@extends('layouts.home')

@section('page-content')
@php $editing = isset($adPlacement); @endphp

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.ad-placements.index') }}"
           class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $editing ? 'Edit Placement' : 'New Placement' }}</h1>
            <p class="mt-0.5 text-xs text-gray-500">Pick a file from your library and choose where it appears</p>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
            <ul class="text-sm text-red-800 space-y-1">
                @foreach($errors->all() as $e)
                    <li class="flex items-start gap-2"><i class="fas fa-exclamation-circle mt-0.5 text-red-500 text-xs"></i>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $editing ? route('admin.ad-placements.update', $adPlacement) : route('admin.ad-placements.store') }}"
          method="POST">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

            {{-- ══ LEFT: Library Picker (2/3 width) ══ --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-gray-800">
                            <i class="fas fa-photo-video text-[#ff0808] mr-2"></i>Pick from Library
                        </h2>
                        <a href="{{ route('admin.ad-library.index') }}" target="_blank"
                           class="text-xs text-[#ff0808] font-semibold hover:underline">
                            <i class="fas fa-plus mr-1"></i> Upload New
                        </a>
                    </div>

                    {{-- Type Filter tabs --}}
                    <div class="flex gap-1 p-3 border-b border-gray-100 bg-gray-50">
                        @foreach(['all'=>'All', 'image'=>'Images', 'gif'=>'GIFs', 'video'=>'Video', 'document'=>'Docs'] as $t => $tl)
                        <button type="button"
                                onclick="filterMedia('{{ $t }}')"
                                data-filter="{{ $t }}"
                                class="filter-btn px-3 py-1 rounded-lg text-xs font-semibold transition-colors
                                       {{ $t === 'all' ? 'bg-[#ff0808] text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                            {{ $tl }}
                        </button>
                        @endforeach
                    </div>

                    {{-- Media grid --}}
                    <div class="p-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 max-h-[420px] overflow-y-auto" id="media-grid">
                        @forelse($media as $item)
                        @php
                            $selected = $editing
                                ? ($adPlacement->ad_media_id == $item->id)
                                : (old('ad_media_id', request('media_id')) == $item->id);
                        @endphp
                        <label data-type="{{ $item->type }}"
                               class="media-item relative cursor-pointer group block">
                            <input type="radio" name="ad_media_id" value="{{ $item->id }}"
                                   class="sr-only peer" {{ $selected ? 'checked' : '' }}>

                            <div class="aspect-square rounded-xl overflow-hidden border-2 transition-all
                                        peer-checked:border-[#ff0808] peer-checked:shadow-lg border-gray-200 group-hover:border-gray-300
                                        bg-gray-100 flex items-center justify-center">
                                @if($item->is_image)
                                    <img src="{{ $item->url }}" alt="{{ $item->name }}"
                                         class="w-full h-full object-cover">
                                @elseif($item->is_video)
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-gray-800 text-white gap-1">
                                        <i class="fas fa-play-circle text-xl"></i>
                                        <span class="text-[9px] uppercase font-bold text-gray-300">Video</span>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center gap-1">
                                        <i class="fas fa-file-pdf text-amber-500 text-2xl"></i>
                                        <span class="text-[9px] uppercase font-bold text-gray-400">PDF</span>
                                    </div>
                                @endif

                                {{-- Selected check --}}
                                <div class="absolute top-1.5 right-1.5 w-5 h-5 bg-[#ff0808] rounded-full
                                            items-center justify-center hidden peer-checked:flex shadow">
                                    <i class="fas fa-check text-white text-[8px]"></i>
                                </div>
                            </div>

                            <p class="mt-1 text-[10px] text-gray-600 truncate text-center" title="{{ $item->name }}">
                                {{ Str::limit($item->name, 18) }}
                            </p>
                        </label>
                        @empty
                        <div class="col-span-5 flex flex-col items-center py-10">
                            <i class="fas fa-photo-video text-3xl text-gray-200 mb-2"></i>
                            <p class="text-sm text-gray-400">No files in library.</p>
                            <a href="{{ route('admin.ad-library.index') }}"
                               class="mt-2 text-xs text-[#ff0808] font-semibold hover:underline">Upload files →</a>
                        </div>
                        @endforelse
                    </div>

                    {{-- Pagination if lots of files --}}
                    @if($media->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">{{ $media->links() }}</div>
                    @endif
                </div>
            </div>

            {{-- ══ RIGHT: Settings (1/3 width) ══ --}}
            <div class="space-y-4">

                {{-- Position selector --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
                    <h2 class="text-sm font-bold text-gray-800">
                        <i class="fas fa-map-marker-alt text-[#ff0808] mr-2"></i>Placement Settings
                    </h2>

                    {{-- Position --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Position <span class="text-red-500">*</span></label>
                        <select name="position"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                            <option value="">— Select position —</option>
                            @foreach($positions as $posKey => $posLabel)
                                <option value="{{ $posKey }}"
                                        {{ old('position', $prePosition ?? ($editing ? $adPlacement->position : '')) === $posKey ? 'selected' : '' }}>
                                    {{ $posLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CTA URL --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Click URL</label>
                        <input type="url" name="cta_url"
                               value="{{ old('cta_url', $editing ? $adPlacement->cta_url : '') }}"
                               placeholder="https://…"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    </div>

                    {{-- Headline --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Headline <span class="text-gray-400">(optional)</span></label>
                        <input type="text" name="headline"
                               value="{{ old('headline', $editing ? $adPlacement->headline : '') }}"
                               placeholder="Optional overlay text"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    </div>

                    {{-- Sub text --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sub Text <span class="text-gray-400">(optional)</span></label>
                        <input type="text" name="sub_text"
                               value="{{ old('sub_text', $editing ? $adPlacement->sub_text : '') }}"
                               placeholder="Supporting line"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
                    <h2 class="text-sm font-bold text-gray-800">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Schedule <span class="text-xs text-gray-400 font-normal">(optional)</span>
                    </h2>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Start Date</label>
                        <input type="datetime-local" name="starts_at"
                               value="{{ old('starts_at', $editing && $adPlacement->starts_at ? $adPlacement->starts_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">End Date</label>
                        <input type="datetime-local" name="ends_at"
                               value="{{ old('ends_at', $editing && $adPlacement->ends_at ? $adPlacement->ends_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    </div>
                </div>

                {{-- Status + Submit --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Active</p>
                            <p class="text-xs text-gray-400">Make this placement go live immediately</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                   {{ old('is_active', $editing ? $adPlacement->is_active : true) ? 'checked' : '' }}>
                            <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-[#ff0808] transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-bold hover:bg-red-700 transition-colors">
                        <i class="fas {{ $editing ? 'fa-save' : 'fa-map-marker-alt' }} mr-1"></i>
                        {{ $editing ? 'Save Changes' : 'Create Placement' }}
                    </button>

                    <a href="{{ route('admin.ad-placements.index') }}"
                       class="block text-center text-xs text-gray-500 hover:text-gray-700">Cancel</a>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
function filterMedia(type) {
    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        const isActive = btn.dataset.filter === type;
        btn.className = btn.className
            .replace('bg-[#ff0808] text-white', 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200')
            .replace('bg-white text-gray-600 hover:bg-gray-100 border border-gray-200', 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200');
        if (isActive) {
            btn.classList.remove('bg-white', 'text-gray-600', 'hover:bg-gray-100', 'border', 'border-gray-200');
            btn.classList.add('bg-[#ff0808]', 'text-white');
        } else {
            btn.classList.remove('bg-[#ff0808]', 'text-white');
            btn.classList.add('bg-white', 'text-gray-600', 'border', 'border-gray-200');
        }
    });

    // Show/hide items
    document.querySelectorAll('.media-item').forEach(el => {
        el.style.display = (type === 'all' || el.dataset.type === type) ? '' : 'none';
    });
}
</script>
@endsection
