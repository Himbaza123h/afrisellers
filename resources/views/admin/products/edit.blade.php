@extends('layouts.home')

@section('page-content')
{{-- Quill CSS --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="mb-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-black text-gray-900 uppercase sm:text-2xl lg:text-lg">Edit Product</h1>
            <p class="mt-1 text-xs text-gray-600 sm:text-sm">Update product info and manage images</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.vendor.product.show', $product) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-eye"></i> View
            </a>
            @php $businessProfile = $product->user->businessProfile ?? null; @endphp
            @if($businessProfile)
                <a href="{{ route('admin.business-profile.show', $businessProfile) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i> Back to Business Profile
                </a>
            @else
                <a href="{{ route('admin.vendor.product.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
    <div class="p-4 mb-4 bg-green-50 rounded-lg border border-green-300">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif

@if($errors->any())
    <div class="p-4 mb-6 bg-red-50 rounded-lg border border-red-300">
        <ul class="space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.vendor.product.update', $product) }}" method="POST" enctype="multipart/form-data" id="editForm">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Left: Main Fields ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ── Image Gallery Manager ── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-900">Image Gallery</h2>
                    <div class="flex items-center gap-3">
                        <span id="totalImageCount" class="text-xs font-semibold text-gray-400">
                            {{ $product->images->count() }} / 4
                        </span>
                        <span class="text-xs text-gray-400 hidden sm:inline">Drag to reorder · ★ to set primary</span>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Existing Images --}}
                    @if($product->images->isNotEmpty())
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Current Images</p>
                            <div id="existingImages" class="grid grid-cols-4 sm:grid-cols-5 lg:grid-cols-6 gap-3">
                                @foreach($product->images->sortBy('sort_order') as $image)
                                    @php
                                        $imgSrc = str_starts_with($image->image_url, 'http') ? $image->image_url : asset($image->image_url);
                                    @endphp
                                    <div class="relative group cursor-grab active:cursor-grabbing existing-image-wrap"
                                         draggable="true"
                                         data-id="{{ $image->id }}"
                                         id="img-{{ $image->id }}">
                                        <div class="aspect-square rounded-xl overflow-hidden border-2 transition-all
                                                    {{ $image->is_primary ? 'border-[#ff0808]' : 'border-gray-200 hover:border-gray-400' }}">
                                            <img src="{{ $imgSrc }}"
                                                 alt="Product image"
                                                 class="w-full h-full object-cover pointer-events-none">
                                        </div>
                                        @if($image->is_primary)
                                            <div class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-[#ff0808] text-white text-[9px] font-bold rounded shadow">
                                                PRIMARY
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-black/40 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                            <button type="button" title="Set as primary"
                                                    onclick="setPrimary({{ $product->id }}, {{ $image->id }})"
                                                    class="w-7 h-7 bg-yellow-400 text-white rounded-full flex items-center justify-center text-xs hover:bg-yellow-500 shadow">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            <button type="button" title="Delete image"
                                                    onclick="deleteImage({{ $product->id }}, {{ $image->id }}, this)"
                                                    class="w-7 h-7 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700 shadow">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Upload New Images --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-3">
                            Add New Images
                            <span class="normal-case font-normal text-gray-400 ml-1">(max 4 total including existing)</span>
                        </p>
                        <div id="dropZone"
                             class="flex flex-col items-center justify-center w-full min-h-[120px] border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#ff0808] hover:bg-red-50 transition-colors"
                             onclick="document.getElementById('newImages').click()"
                             ondragover="event.preventDefault(); this.classList.add('border-[#ff0808]','bg-red-50')"
                             ondragleave="this.classList.remove('border-[#ff0808]','bg-red-50')"
                             ondrop="handleImageDrop(event)">
                            <i class="fas fa-plus-circle text-gray-400 text-3xl mb-2"></i>
                            <p class="text-sm text-gray-500 font-medium">Click or drag to add images</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — max 5 MB each</p>
                            <input type="file" id="newImages" name="images[]" multiple accept="image/*" class="hidden"
                                   onchange="addNewImages(this.files)">
                        </div>

                        <p id="imageLimitWarn" class="hidden mt-2 text-xs text-red-500 font-medium">
                            <i class="fas fa-exclamation-circle mr-1"></i>Maximum 4 images total. Extra files were ignored.
                        </p>

                        <div id="newPreviewGrid" class="grid grid-cols-4 sm:grid-cols-5 lg:grid-cols-6 gap-3 mt-3"></div>
                    </div>
                </div>
            </div>

            {{-- ── Video ── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Product Video</h2>
                        <p class="text-xs text-gray-400 mt-0.5">One video only — MP4, WebM, MOV — max 50 MB</p>
                    </div>
                    <i class="fas fa-video text-gray-300 text-xl"></i>
                </div>
                <div class="p-6 space-y-4">

                    {{-- Existing video --}}
                    @if($product->video_url)
                        @php
                            $videoSrc = str_starts_with($product->video_url, 'http') ? $product->video_url : Storage::url($product->video_url);
                        @endphp
                        <div id="existingVideoWrap" class="rounded-xl overflow-hidden border border-gray-200 bg-black relative">
                            <video controls class="w-full max-h-64 object-contain bg-black">
                                <source src="{{ $videoSrc }}" type="video/mp4">
                            </video>
                            <div class="px-3 py-2 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                                <span class="text-xs text-gray-500">Current video</span>
                                <button type="button" onclick="removeExistingVideo()"
                                        class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1">
                                    <i class="fas fa-trash text-[10px]"></i> Remove video
                                </button>
                            </div>
                            <input type="hidden" name="remove_video" id="removeVideoFlag" value="0">
                        </div>
                        <p id="existingVideoRemovedNote" class="hidden text-xs text-red-500 font-medium">
                            <i class="fas fa-exclamation-circle mr-1"></i>Existing video will be removed on save.
                            <button type="button" onclick="undoRemoveVideo()" class="underline ml-1">Undo</button>
                        </p>
                        <p class="text-xs text-gray-400">To replace the video, upload a new one below.</p>
                    @endif

                    {{-- Upload / Drop Zone --}}
                    <div id="videoDropZone"
                         class="flex flex-col items-center justify-center w-full min-h-[110px] border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors"
                         onclick="document.getElementById('videoInput').click()"
                         ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50')"
                         ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
                         ondrop="handleVideoDrop(event)">
                        <i class="fas fa-film text-gray-400 text-3xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">
                            {{ $product->video_url ? 'Upload replacement video' : 'Drag & drop or click to upload video' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">MP4, WebM, MOV — max 50 MB</p>
                        <input type="file" id="videoInput" name="video" accept="video/mp4,video/webm,video/quicktime" class="hidden"
                               onchange="previewNewVideo(this)">
                    </div>

                    {{-- New video preview --}}
                    <div id="newVideoPreview" class="hidden rounded-xl overflow-hidden border border-gray-200 bg-black relative">
                        <video id="newVideoEl" controls class="w-full max-h-64 object-contain bg-black"></video>
                        <button type="button" onclick="removeNewVideo()"
                                class="absolute top-2 right-2 w-7 h-7 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700 shadow">
                            <i class="fas fa-times"></i>
                        </button>
                        <p id="newVideoName" class="px-3 py-2 text-xs text-gray-400 bg-gray-50 border-t border-gray-200"></p>
                    </div>

                </div>
            </div>

            {{-- ── Basic Info ── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-bold text-gray-900">Basic Information</h2>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Name --}}
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                    </div>

                    {{-- Short Description --}}
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Short Description</label>
                        <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" maxlength="500"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                    </div>

                    {{-- Description (Quill) --}}
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="descriptionInput" class="hidden">{{ old('description', $product->description) }}</textarea>
                        <div id="descriptionEditor"
                             class="border border-gray-300 rounded-lg overflow-hidden"
                             style="min-height:200px;">
                        </div>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Min Qty / Negotiable --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700">Min Order Quantity</label>
                            <input type="number" name="min_order_quantity"
                                   value="{{ old('min_order_quantity', $product->min_order_quantity ?? 1) }}" min="1"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                        </div>
                        <div class="flex items-end pb-1">
                            <label class="inline-flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="is_negotiable" value="0">
                                <input type="checkbox" name="is_negotiable" value="1"
                                       {{ old('is_negotiable', $product->is_negotiable) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808]">
                                <span class="text-sm font-medium text-gray-700">Price is Negotiable</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- end left col --}}

        {{-- ── Sidebar Settings ── --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-bold text-gray-900">Settings</h2>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Vendor --}}
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Vendor <span class="text-red-500">*</span></label>
                        <select name="user_id" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('user_id', $product->user_id) == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                        <select name="product_category_id" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('product_category_id', $product->product_category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Country --}}
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Country <span class="text-red-500">*</span></label>
                        <select name="country_id" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ old('country_id', $product->country_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Status</label>
                        <select name="status"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                            <option value="draft"    {{ old('status', $product->status) === 'draft'    ? 'selected' : '' }}>Draft</option>
                            <option value="active"   {{ old('status', $product->status) === 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Admin Verified --}}
                    <div>
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_admin_verified" value="0">
                            <input type="checkbox" name="is_admin_verified" value="1"
                                   {{ old('is_admin_verified', $product->is_admin_verified) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808]">
                            <span class="text-sm font-medium text-gray-700">Admin Verified</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('admin.vendor.product.show', $product) }}"
                   class="flex-1 inline-flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>{{-- end sidebar --}}

    </div>
</form>

{{-- Quill JS --}}
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
// ── Quill ─────────────────────────────────────────────────────────────────
const quill = new Quill('#descriptionEditor', {
    theme: 'snow',
    placeholder: 'Enter full product description…',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'header': [1, 2, 3, false] }],
            ['link', 'blockquote'],
            ['clean']
        ]
    }
});

const existingDesc = document.getElementById('descriptionInput').value.trim();
if (existingDesc) quill.root.innerHTML = existingDesc;

document.getElementById('editForm').addEventListener('submit', function () {
    document.getElementById('descriptionInput').value = quill.root.innerHTML;
});


// ── Image Gallery — Drag & Drop Reorder ──────────────────────────────────
const existingGrid = document.getElementById('existingImages');
let dragging = null;

if (existingGrid) {
    existingGrid.addEventListener('dragstart', e => {
        dragging = e.target.closest('[data-id]');
        if (dragging) dragging.classList.add('opacity-40');
    });
    existingGrid.addEventListener('dragend', () => {
        if (dragging) dragging.classList.remove('opacity-40');
        dragging = null;
        saveOrder();
    });
    existingGrid.addEventListener('dragover', e => {
        e.preventDefault();
        const target = e.target.closest('[data-id]');
        if (target && target !== dragging) {
            const rect = target.getBoundingClientRect();
            existingGrid.insertBefore(dragging, e.clientX < rect.left + rect.width / 2 ? target : target.nextSibling);
        }
    });
}

function saveOrder() {
    const order = Array.from(existingGrid.querySelectorAll('[data-id]')).map(el => el.dataset.id);
    fetch(`/admin/vendors/products/{{ $product->id }}/images/reorder`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ order })
    });
}

function setPrimary(productId, imageId) {
    fetch(`/admin/vendors/products/${productId}/images/${imageId}/primary`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => location.reload());
}

function deleteImage(productId, imageId, btn) {
    if (!confirm('Delete this image?')) return;
    fetch(`/admin/vendors/products/${productId}/images/${imageId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => {
        if (r.ok) {
            document.getElementById(`img-${imageId}`)?.remove();
            updateExistingCount();
        }
    });
}


// ── Image Upload Limit ────────────────────────────────────────────────────
const MAX_IMAGES = 4;
let newFiles = [];

function existingCount() {
    return document.querySelectorAll('.existing-image-wrap').length;
}

function updateExistingCount() {
    const total = existingCount() + newFiles.length;
    document.getElementById('totalImageCount').textContent = `${total} / ${MAX_IMAGES}`;
    const dz = document.getElementById('dropZone');
    if (total >= MAX_IMAGES) {
        dz.classList.add('opacity-50', 'pointer-events-none');
    } else {
        dz.classList.remove('opacity-50', 'pointer-events-none');
    }
}

function addNewImages(files) {
    const warn      = document.getElementById('imageLimitWarn');
    warn.classList.add('hidden');

    const available = MAX_IMAGES - existingCount() - newFiles.length;
    const incoming  = Array.from(files);

    if (incoming.length > available) {
        warn.classList.remove('hidden');
    }

    incoming.slice(0, available).forEach(f => newFiles.push(f));
    rebuildNewInput();
    renderNewPreviews();
    updateExistingCount();
}

function rebuildNewInput() {
    const dt = new DataTransfer();
    newFiles.forEach(f => dt.items.add(f));
    document.getElementById('newImages').files = dt.files;
}

function renderNewPreviews() {
    const grid = document.getElementById('newPreviewGrid');
    grid.innerHTML = '';
    newFiles.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.className = 'relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100 shadow-sm';
            wrap.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105">
                <button type="button" onclick="removeNewImage(${i})"
                    class="absolute top-1.5 right-1.5 w-6 h-6 bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-[10px] shadow">
                    <i class="fas fa-times"></i>
                </button>
            `;
            grid.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}

function removeNewImage(index) {
    newFiles.splice(index, 1);
    rebuildNewInput();
    renderNewPreviews();
    document.getElementById('imageLimitWarn').classList.add('hidden');
    updateExistingCount();
}

function handleImageDrop(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('border-[#ff0808]', 'bg-red-50');
    addNewImages(event.dataTransfer.files);
}


// ── Video — Existing ─────────────────────────────────────────────────────
function removeExistingVideo() {
    document.getElementById('removeVideoFlag').value = '1';
    document.getElementById('existingVideoWrap').classList.add('opacity-40', 'pointer-events-none');
    document.getElementById('existingVideoRemovedNote').classList.remove('hidden');
}

function undoRemoveVideo() {
    document.getElementById('removeVideoFlag').value = '0';
    document.getElementById('existingVideoWrap').classList.remove('opacity-40', 'pointer-events-none');
    document.getElementById('existingVideoRemovedNote').classList.add('hidden');
}

// ── Video — New Upload ───────────────────────────────────────────────────
function previewNewVideo(input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 50 * 1024 * 1024) {
        alert('Video must be under 50 MB.');
        input.value = '';
        return;
    }

    const url = URL.createObjectURL(file);
    document.getElementById('newVideoEl').src = url;
    document.getElementById('newVideoName').textContent =
        `${file.name}  (${(file.size / 1024 / 1024).toFixed(1)} MB)`;
    document.getElementById('newVideoPreview').classList.remove('hidden');
    document.getElementById('videoDropZone').classList.add('hidden');
}

function removeNewVideo() {
    const input = document.getElementById('videoInput');
    const el    = document.getElementById('newVideoEl');
    URL.revokeObjectURL(el.src);
    el.src = '';
    input.value = '';
    document.getElementById('newVideoPreview').classList.add('hidden');
    document.getElementById('videoDropZone').classList.remove('hidden');
}

function handleVideoDrop(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('border-blue-400', 'bg-blue-50');
    const file = event.dataTransfer.files[0];
    if (!file) return;
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('videoInput').files = dt.files;
    previewNewVideo(document.getElementById('videoInput'));
}

// Init count on load
updateExistingCount();
</script>

<style>
#descriptionEditor .ql-container {
    min-height: 180px;
    font-size: 0.875rem;
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}
#descriptionEditor .ql-toolbar {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    border-color: #d1d5db;
}
#descriptionEditor .ql-container {
    border-color: #d1d5db;
}
</style>
@endsection
