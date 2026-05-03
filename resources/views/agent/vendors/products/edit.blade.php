@extends('layouts.home')

@section('page-content')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.vendors.products.index', $vendor->id) }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Product</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ $product->name }}</p>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="p-4 mb-6 bg-red-50 rounded-lg border border-red-300">
        <p class="mb-2 text-sm font-semibold text-red-900">Please fix the following errors:</p>
        <ul class="space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ route('agent.vendors.products.updateProduct', [$vendor->id, $product->id]) }}"
      method="POST" enctype="multipart/form-data" id="productForm">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Left ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Basic Info --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-bold text-gray-900">Basic Information</h2>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Short Description</label>
                        <input type="text" name="short_description"
                               value="{{ old('short_description', $product->short_description) }}" maxlength="500"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="descriptionInput" class="hidden">{{ old('description', $product->description) }}</textarea>
                        <div id="descriptionEditor" class="border border-gray-300 rounded-lg overflow-hidden" style="min-height:200px">
                            {!! old('description', $product->description) !!}
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700">Min Order Quantity</label>
                            <input type="number" name="min_order_quantity"
                                   value="{{ old('min_order_quantity', $product->min_order_quantity ?? 1) }}" min="1"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                        </div>
                        <div class="flex items-end pb-2">
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

            {{-- Existing Images --}}
            @if($product->images->isNotEmpty())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-900">Current Images</h2>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-red-600 font-medium hover:text-red-800">
                        <input type="checkbox" name="delete_all_images" value="1" id="deleteAllCheck"
                               class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500"
                               onchange="toggleDeleteAll(this)">
                        Remove All
                    </label>
                </div>
                <div class="p-6">
                    <p class="text-xs text-gray-400 mb-3">Tick the box under an image to delete it on save. Or use "Remove All" above.</p>
                    <div id="existingImagesGrid" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                        @foreach($product->images->sortBy('sort_order') as $image)
                            @php
                                $rawUrl = $image->getRawOriginal('image_url');
                                $imgSrc = str_starts_with($rawUrl, 'http') ? $rawUrl : asset('storage/' . $rawUrl);
                            @endphp
                            <div class="relative group" id="img-wrap-{{ $image->id }}">
                                <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-100">
                                    <img src="{{ $imgSrc }}" alt="{{ $image->alt_text }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                </div>
                                @if($image->is_primary)
                                    <span class="absolute top-1 left-1 px-1.5 py-0.5 bg-green-600 text-white text-[9px] font-bold rounded shadow">Primary</span>
                                @endif
                                <div class="mt-1.5 flex items-center gap-1.5">
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}"
                                           id="del_{{ $image->id }}" class="del-checkbox w-3.5 h-3.5 text-red-500 border-gray-300 rounded"
                                           onchange="markForDelete(this, '{{ $image->id }}')">
                                    <label for="del_{{ $image->id }}" class="text-xs text-red-500 font-medium cursor-pointer">Delete</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- New Images --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Add New Images</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Leave empty to keep existing. Max 5 MB each, up to 4 total.</p>
                    </div>
                    <span id="imageCount" class="text-xs font-semibold text-gray-400">0 / 4</span>
                </div>
                <div class="p-6 space-y-4">
                    <div id="dropZone"
                         class="flex flex-col items-center justify-center w-full min-h-[120px] border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#ff0808] hover:bg-red-50 transition-colors"
                         onclick="document.getElementById('imageInput').click()"
                         ondragover="event.preventDefault(); this.classList.add('border-[#ff0808]','bg-red-50')"
                         ondragleave="this.classList.remove('border-[#ff0808]','bg-red-50')"
                         ondrop="handleImageDrop(event)">
                        <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">Drag & drop or click to browse</p>
                        <input type="file" id="imageInput" name="images[]" multiple accept="image/*" class="hidden"
                               onchange="addImages(this.files)">
                    </div>
                    <div id="previewGrid" class="grid grid-cols-4 gap-3"></div>
                    <div id="imageLimitWarn" class="hidden text-xs text-red-500 font-medium">
                        <i class="fas fa-exclamation-circle mr-1"></i>Maximum 4 new images.
                    </div>
                    <button type="button" id="clearAllBtn" onclick="clearAllImages()"
                            class="hidden text-xs text-red-500 hover:text-red-700 font-medium underline">
                        <i class="fas fa-trash mr-1"></i>Clear new uploads
                    </button>
                </div>
            </div>

            {{-- Video --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Product Video</h2>
                        <p class="text-xs text-gray-400 mt-0.5">MP4, WebM, MOV — max 50 MB</p>
                    </div>
                    <i class="fas fa-video text-gray-300 text-xl"></i>
                </div>
                <div class="p-6 space-y-4">

                    {{-- Existing video --}}
                    @if($product->video_url)
                        @php
                            $vRaw = $product->video_url;
                            $vSrc = str_starts_with($vRaw, 'http') ? $vRaw : asset('storage/' . $vRaw);
                        @endphp
                        <div id="existingVideoWrap" class="rounded-xl overflow-hidden border border-gray-200 bg-black relative">
                            <video src="{{ $vSrc }}" controls class="w-full max-h-64 object-contain bg-black"></video>
                            <div class="px-3 py-2 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                                <span class="text-xs text-gray-500 truncate">Current video</span>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="remove_video" value="1" id="removeVideoCheck"
                                           class="w-4 h-4 text-red-500 border-gray-300 rounded"
                                           onchange="document.getElementById('existingVideoWrap').classList.toggle('opacity-40', this.checked)">
                                    <span class="text-xs text-red-500 font-medium">Remove video</span>
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400">Upload a new video below to replace the current one.</p>
                    @endif

                    <div id="videoDropZone"
                         class="flex flex-col items-center justify-center w-full min-h-[100px] border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors"
                         onclick="document.getElementById('videoInput').click()"
                         ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50')"
                         ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
                         ondrop="handleVideoDrop(event)">
                        <i class="fas fa-film text-gray-400 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">{{ $product->video_url ? 'Upload replacement video' : 'Drag & drop or click to upload' }}</p>
                        <input type="file" id="videoInput" name="video" accept="video/mp4,video/webm,video/quicktime" class="hidden"
                               onchange="previewVideo(this)">
                    </div>
                    <div id="videoPreview" class="hidden rounded-xl overflow-hidden border border-gray-200 bg-black relative">
                        <video id="videoEl" controls class="w-full max-h-64 object-contain bg-black"></video>
                        <button type="button" onclick="removeVideo()"
                                class="absolute top-2 right-2 w-7 h-7 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700 shadow">
                            <i class="fas fa-times"></i>
                        </button>
                        <p id="videoName" class="px-3 py-2 text-xs text-gray-400 bg-gray-50 border-t border-gray-200 truncate"></p>
                    </div>

                </div>
            </div>

        </div>{{-- end left --}}

        {{-- ── Sidebar ── --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-bold text-gray-900">Settings</h2>
                </div>
                <div class="p-6 space-y-5">

                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Vendor</label>
                        <div class="flex items-center gap-3 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="w-7 h-7 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($vendor->businessProfile?->business_name ?? 'V', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $vendor->businessProfile?->business_name }}</p>
                                <p class="text-xs text-gray-400">{{ $vendor->user?->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Category</label>
                        <select name="product_category_id"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                            <option value="">— Select —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('product_category_id', $product->product_category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Country of Origin</label>
                        <select name="country_id"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                            <option value="">— Select —</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('country_id', $product->country_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                            @foreach(['draft','active','inactive'] as $s)
                                <option value="{{ $s }}" {{ old('status', $product->status) == $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Meta --}}
                    <div class="pt-2 border-t border-gray-100 space-y-1">
                        <p class="text-xs text-gray-400">Created: {{ $product->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-400">Updated: {{ $product->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('agent.vendors.products.index', $vendor->id) }}"
                   class="flex-1 inline-flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>

        {{-- Danger --}}
            <div class="bg-white rounded-xl border border-red-200 shadow-sm">
                <div class="px-6 py-4 border-b border-red-100">
                    <h2 class="text-sm font-bold text-red-700">Danger Zone</h2>
                </div>
                <div class="p-4">
                    <button type="submit" form="deleteForm"
                        class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 border border-red-300 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50">
                        <i class="fas fa-trash"></i> Delete Product
                    </button>
                </div>
            </div>

        </div>

    </div>
</form>
<form id="deleteForm"
      action="{{ route('agent.vendors.products.destroy', [$vendor->id, $product->id]) }}"
      method="POST"
      onsubmit="return confirm('Permanently delete this product and all its images?')">
    @csrf
    @method('DELETE')
</form>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
// ── Quill ──────────────────────────────────────────────────────────────────
const quill = new Quill('#descriptionEditor', {
    theme: 'snow',
    placeholder: 'Enter full product description…',
    modules: { toolbar: [['bold','italic','underline','strike'],[{'list':'ordered'},{'list':'bullet'}],[{'header':[1,2,3,false]}],['link','blockquote'],['clean']] }
});
document.getElementById('productForm').addEventListener('submit', function () {
    document.getElementById('descriptionInput').value = quill.root.innerHTML;
});
const oldDesc = document.getElementById('descriptionInput').value.trim();
if (oldDesc) quill.root.innerHTML = oldDesc;

// ── Existing image delete highlights ──────────────────────────────────────
function markForDelete(cb, id) {
    const wrap = document.getElementById(`img-wrap-${id}`);
    if (wrap) wrap.classList.toggle('opacity-40 ring-2 ring-red-400', cb.checked);
}

function toggleDeleteAll(cb) {
    document.querySelectorAll('.del-checkbox').forEach(c => {
        c.checked = cb.checked;
        const id = c.value;
        const wrap = document.getElementById(`img-wrap-${id}`);
        if (wrap) wrap.classList.toggle('opacity-40', cb.checked);
    });
}

// ── New images ──────────────────────────────────────────────────────────────
const MAX = 4;
let allFiles = [];

function updateCount() {
    document.getElementById('imageCount').textContent = `${allFiles.length} / ${MAX}`;
    document.getElementById('clearAllBtn').classList.toggle('hidden', allFiles.length === 0);
    const dz = document.getElementById('dropZone');
    dz.classList.toggle('opacity-50', allFiles.length >= MAX);
    dz.classList.toggle('pointer-events-none', allFiles.length >= MAX);
}

function addImages(files) {
    document.getElementById('imageLimitWarn').classList.add('hidden');
    const avail = MAX - allFiles.length;
    const inc   = Array.from(files);
    if (inc.length > avail) document.getElementById('imageLimitWarn').classList.remove('hidden');
    inc.slice(0, avail).forEach(f => allFiles.push(f));
    syncInput(); renderPreviews();
}

function syncInput() {
    const dt = new DataTransfer();
    allFiles.forEach(f => dt.items.add(f));
    document.getElementById('imageInput').files = dt.files;
    updateCount();
}

function renderPreviews() {
    const grid = document.getElementById('previewGrid');
    grid.innerHTML = '';
    allFiles.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100 shadow-sm';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                ${i === 0 ? `<div class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-[#ff0808] text-white text-[9px] font-bold rounded">New Primary</div>` : ''}
                <button type="button" onclick="removeImage(${i})"
                    class="absolute top-1.5 right-1.5 w-6 h-6 bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-[10px] shadow">
                    <i class="fas fa-times"></i>
                </button>
            `;
            grid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function removeImage(i) {
    allFiles.splice(i, 1);
    syncInput(); renderPreviews();
    document.getElementById('imageLimitWarn').classList.add('hidden');
}

function clearAllImages() {
    allFiles = [];
    syncInput(); renderPreviews();
}

function handleImageDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('border-[#ff0808]','bg-red-50');
    addImages(e.dataTransfer.files);
}

// ── Video ──────────────────────────────────────────────────────────────────
function previewVideo(input) {
    const file = input.files[0];
    if (!file) return;
    if (file.size > 50 * 1024 * 1024) { alert('Video must be under 50 MB.'); input.value = ''; return; }
    const url = URL.createObjectURL(file);
    document.getElementById('videoEl').src = url;
    document.getElementById('videoName').textContent = `${file.name} (${(file.size/1024/1024).toFixed(1)} MB)`;
    document.getElementById('videoPreview').classList.remove('hidden');
    document.getElementById('videoDropZone').classList.add('hidden');
}

function removeVideo() {
    const input = document.getElementById('videoInput');
    input.value = '';
    const el = document.getElementById('videoEl');
    URL.revokeObjectURL(el.src); el.src = '';
    document.getElementById('videoPreview').classList.add('hidden');
    document.getElementById('videoDropZone').classList.remove('hidden');
}

function handleVideoDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('border-blue-400','bg-blue-50');
    const dt = new DataTransfer();
    dt.items.add(e.dataTransfer.files[0]);
    const input = document.getElementById('videoInput');
    input.files = dt.files;
    previewVideo(input);
}
</script>

<style>
#descriptionEditor .ql-toolbar { border-top-left-radius:.5rem; border-top-right-radius:.5rem; border-color:#d1d5db; }
#descriptionEditor .ql-container { min-height:180px; font-size:.875rem; border-bottom-left-radius:.5rem; border-bottom-right-radius:.5rem; border-color:#d1d5db; }
</style>
@endsection
