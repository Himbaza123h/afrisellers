@extends('layouts.home')

@push('styles')
<style>
    .drop-zone {
        border: 2px dashed #d1d5db;
        transition: border-color 0.2s, background-color 0.2s;
    }
    .drop-zone.dragover {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
    .preview-wrap { position: relative; }
    .preview-wrap video,
    .preview-wrap img { border-radius: 12px; max-height: 220px; width: 100%; object-fit: contain; }
</style>
@endpush

@section('page-content')
@php $editing = isset($ad); @endphp

<div class="max-w-2xl mx-auto space-y-6">

    {{-- ── Breadcrumb ──────────────────────────────────────────── --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('vendor.ads.index') }}" class="hover:text-blue-600 transition-colors">Ads & Promotions</a>
        <i class="fas fa-chevron-right text-xs text-gray-300"></i>
        <span class="text-gray-900 font-semibold">{{ $editing ? 'Edit Ad' : 'Create New Ad' }}</span>
    </nav>

    {{-- ── Header ───────────────────────────────────────────────── --}}
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-md">
            <i class="fas fa-{{ $editing ? 'edit' : 'bullhorn' }} text-white text-lg"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $editing ? 'Edit Ad' : 'Create New Ad' }}</h1>
            <p class="text-sm text-gray-500">{{ $editing ? 'Update your advertisement details' : 'Set up an image or video promotion' }}</p>
        </div>
    </div>

    {{-- ── Errors ──────────────────────────────────────────────── --}}
    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
        @foreach($errors->all() as $e)
        <div class="flex items-start gap-2"><i class="fas fa-circle text-[6px] mt-1.5 text-red-400 flex-shrink-0"></i> {{ $e }}</div>
        @endforeach
    </div>
    @endif

    {{-- ── Form ────────────────────────────────────────────────── --}}
    <form
        action="{{ $editing ? route('vendor.ads.update', $ad) : route('vendor.ads.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-5">
        @csrf
        @if($editing) @method('PUT') @endif

        {{-- Title & Description --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Ad Details</h2>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ad Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $ad->title ?? '') }}"
                       placeholder="e.g. Summer Sale — 30% Off All Items"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                       required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          placeholder="Short description shown below your ad..."
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none">{{ old('description', $ad->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Click-Through URL</label>
                <div class="relative">
                    <i class="fas fa-link absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="url" name="target_url" value="{{ old('target_url', $ad->target_url ?? '') }}"
                           placeholder="https://yourstore.com/promo"
                           class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>
                <p class="text-xs text-gray-400 mt-1">Where users go when they click your ad</p>
            </div>
        </div>

        {{-- Media Upload --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Media</h2>

            @if(!$editing)
            {{-- Media type selector --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Media Type <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3" id="typeSelector">
                    @foreach(['image' => ['fas fa-image','Image','Max 2 MB — JPG, PNG, GIF, WEBP'],
                               'video' => ['fas fa-video','Video','Max 4 MB — MP4, MOV, AVI, WEBM']] as $type => [$icon, $label, $hint])
                    <label class="type-option cursor-pointer">
                        <input type="radio" name="media_type" value="{{ $type }}"
                               class="sr-only type-radio"
                               {{ old('media_type', 'image') === $type ? 'checked' : '' }}>
                        <div class="type-card flex flex-col items-center gap-2 p-4 border-2 rounded-xl text-center transition-all
                            {{ old('media_type', 'image') === $type ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <i class="{{ $icon }} text-2xl {{ old('media_type', 'image') === $type ? 'text-blue-600' : 'text-gray-400' }}"></i>
                            <span class="text-sm font-bold {{ old('media_type', 'image') === $type ? 'text-blue-700' : 'text-gray-700' }}">{{ $label }}</span>
                            <span class="text-xs text-gray-400">{{ $hint }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @else
            <input type="hidden" name="media_type" value="{{ $ad->media_type }}">
            <p class="text-xs text-gray-500 bg-gray-50 rounded-lg px-3 py-2">
                <i class="fas fa-info-circle text-blue-400 mr-1"></i>
                Media type is <strong>{{ ucfirst($ad->media_type) }}</strong> — leave upload empty to keep existing file.
            </p>
            @endif

            {{-- Drop zone --}}
            <div id="dropZone" class="drop-zone rounded-2xl p-8 flex flex-col items-center justify-center cursor-pointer text-center min-h-[160px]"
                 onclick="document.getElementById('mediaInput').click()">
                <div id="dropPlaceholder">
                    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Click to upload or drag & drop</p>
                    <p id="dropHint" class="text-xs text-gray-400 mt-1">Images: JPG, PNG, GIF, WEBP (max 2 MB) · Videos: MP4, MOV (max 4 MB)</p>
                </div>
                <div id="previewWrap" class="preview-wrap w-full hidden">
                    <img id="imgPreview" src="" alt="Preview" class="hidden mx-auto">
                    <video id="vidPreview" controls class="hidden mx-auto"></video>
                    <p id="previewName" class="text-xs text-gray-500 mt-2 text-center"></p>
                    <button type="button" id="clearMedia"
                            class="mt-2 text-xs text-red-500 underline hover:text-red-700">Remove</button>
                </div>
            </div>
            <input type="file" id="mediaInput" name="media" class="sr-only" accept="image/*,video/*">

            @if($editing && $ad->media_path)
            <div class="p-3 bg-gray-50 rounded-xl flex items-center gap-3">
                @if($ad->media_type === 'image')
                    <img src="{{ asset('storage/' . $ad->media_path) }}" class="w-12 h-12 rounded-lg object-cover">
                @else
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-video text-purple-500"></i>
                    </div>
                @endif
                <div>
                    <p class="text-xs font-semibold text-gray-700">Current file</p>
                    <p class="text-xs text-gray-400">{{ $ad->media_original_name ?? basename($ad->media_path) }} · {{ $ad->media_size_formatted }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Settings --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Settings</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Placement <span class="text-red-500">*</span></label>
                    <select name="placement"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(['homepage'=>'Homepage','sidebar'=>'Sidebar','banner'=>'Banner','popup'=>'Popup','feed'=>'Feed'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('placement', $ad->placement ?? 'feed') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                    <select name="status"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(['draft'=>'Save as Draft','active'=>'Publish Active'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('status', $ad->status ?? 'draft') === $val)>{{ $lbl }}</option>
                        @endforeach
                        @if($editing)
                            <option value="paused" @selected($ad->status === 'paused')>Paused</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Start Date</label>
                    <input type="datetime-local" name="starts_at"
                           value="{{ old('starts_at', isset($ad->starts_at) ? $ad->starts_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">End Date</label>
                    <input type="datetime-local" name="ends_at"
                           value="{{ old('ends_at', isset($ad->ends_at) ? $ad->ends_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <p class="text-xs text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                Ads are only displayed while your subscription is active. If your plan expires, ads automatically stop showing.
            </p>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3 pb-6">
            <button type="submit"
                    class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-{{ $editing ? 'save' : 'paper-plane' }}"></i>
                {{ $editing ? 'Save Changes' : 'Create Ad' }}
            </button>
            <a href="{{ route('vendor.ads.index') }}"
               class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold text-sm transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // ── Media type radio styling ────────────────────────────────
    document.querySelectorAll('.type-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.type-card').forEach(c => {
                c.className = c.className
                    .replace('border-blue-500 bg-blue-50', 'border-gray-200 hover:border-gray-300')
                    .replace('text-blue-600', 'text-gray-400')
                    .replace('text-blue-700', 'text-gray-700');
            });
            const card = radio.nextElementSibling;
            card.className = card.className
                .replace('border-gray-200 hover:border-gray-300', 'border-blue-500 bg-blue-50')
                .replace('text-gray-400', 'text-blue-600')
                .replace('text-gray-700', 'text-blue-700');

            // Update accepted types
            const input = document.getElementById('mediaInput');
            input.accept = radio.value === 'image' ? 'image/*' : 'video/*';
            clearPreview();
        });
    });

    // ── Drag & Drop ────────────────────────────────────────────
    const dropZone  = document.getElementById('dropZone');
    const mediaInput = document.getElementById('mediaInput');

    dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length) handleFile(files[0]);
    });

    mediaInput.addEventListener('change', () => {
        if (mediaInput.files.length) handleFile(mediaInput.files[0]);
    });

    function handleFile(file) {
        const maxImage = 2 * 1024 * 1024;
        const maxVideo = 4 * 1024 * 1024;
        const isImage = file.type.startsWith('image/');
        const isVideo = file.type.startsWith('video/');
        const max = isImage ? maxImage : maxVideo;

        if (!isImage && !isVideo) { alert('Please upload an image or video file.'); return; }
        if (file.size > max) {
            alert((isImage ? 'Images' : 'Videos') + ' must be under ' + (isImage ? '2 MB' : '4 MB') + '.');
            return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('dropPlaceholder').classList.add('hidden');
            document.getElementById('previewWrap').classList.remove('hidden');
            document.getElementById('previewName').textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';

            if (isImage) {
                const img = document.getElementById('imgPreview');
                img.src = e.target.result;
                img.classList.remove('hidden');
                document.getElementById('vidPreview').classList.add('hidden');
            } else {
                const vid = document.getElementById('vidPreview');
                vid.src = e.target.result;
                vid.classList.remove('hidden');
                document.getElementById('imgPreview').classList.add('hidden');
            }
        };
        reader.readAsDataURL(file);
    }

    document.getElementById('clearMedia').addEventListener('click', clearPreview);

    function clearPreview() {
        mediaInput.value = '';
        document.getElementById('dropPlaceholder').classList.remove('hidden');
        document.getElementById('previewWrap').classList.add('hidden');
        document.getElementById('imgPreview').src = '';
        document.getElementById('vidPreview').src = '';
    }
</script>
@endpush
@endsection
