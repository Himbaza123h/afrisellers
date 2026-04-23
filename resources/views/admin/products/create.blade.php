@extends('layouts.home')

@section('page-content')
{{-- Quill CSS --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="mb-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-black text-gray-900 uppercase sm:text-2xl lg:text-lg">Add New Product</h1>
            <p class="mt-1 text-xs text-gray-600 sm:text-sm">Create a product and assign it to a vendor</p>
        </div>
        <a href="{{ route('admin.vendor.product.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

@if($errors->any())
    <div class="p-4 mb-6 bg-red-50 rounded-lg border border-red-300">
        <p class="mb-2 text-sm font-semibold text-red-900">Please fix the following errors:</p>
        <ul class="space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.vendor.product.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Left: Main Fields ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Basic Info --}}
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
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]"
                               placeholder="Enter product name">
                    </div>

                    {{-- Short Description --}}
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Short Description</label>
                        <input type="text" name="short_description" value="{{ old('short_description') }}" maxlength="500"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]"
                               placeholder="Brief product summary (max 500 chars)">
                    </div>

                    {{-- Description (Rich Text) --}}
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">
                            Description <span class="text-red-500">*</span>
                        </label>
                        {{-- Hidden textarea that gets submitted --}}
                        <textarea name="description" id="descriptionInput" class="hidden">{{ old('description') }}</textarea>
                        {{-- Quill editor container --}}
                        <div id="descriptionEditor"
                             class="border border-gray-300 rounded-lg overflow-hidden"
                             style="min-height: 200px;">
                            {!! old('description') !!}
                        </div>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Min Qty / Negotiable --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700">Min Order Quantity</label>
                            <input type="number" name="min_order_quantity" value="{{ old('min_order_quantity', 1) }}" min="1"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="inline-flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="is_negotiable" value="0">
                                <input type="checkbox" name="is_negotiable" value="1" {{ old('is_negotiable') ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808]">
                                <span class="text-sm font-medium text-gray-700">Price is Negotiable</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Images ── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-900">Product Images</h2>
                    <span id="imageCount" class="text-xs font-semibold text-gray-400">0 / 4</span>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Drop Zone --}}
                    <div id="dropZone"
                         class="flex flex-col items-center justify-center w-full min-h-[140px] border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#ff0808] hover:bg-red-50 transition-colors"
                         onclick="document.getElementById('imageInput').click()"
                         ondragover="event.preventDefault(); this.classList.add('border-[#ff0808]','bg-red-50')"
                         ondragleave="this.classList.remove('border-[#ff0808]','bg-red-50')"
                         ondrop="handleDrop(event)">
                        <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">Drag & drop or click to browse</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — max 5 MB each — up to <strong>4 images</strong></p>
                        <input type="file" id="imageInput" name="images[]" multiple accept="image/*" class="hidden"
                               onchange="addImages(this.files)">
                    </div>

                    {{-- Preview Grid --}}
                    <div id="previewGrid" class="grid grid-cols-4 gap-3">
                        {{-- filled dynamically --}}
                    </div>

                    {{-- Limit warning --}}
                    <p id="imageLimitWarn" class="hidden text-xs text-red-500 font-medium">
                        <i class="fas fa-exclamation-circle mr-1"></i>Maximum 4 images allowed. Extra files were ignored.
                    </p>
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
                    {{-- Drop Zone --}}
                    <div id="videoDropZone"
                         class="flex flex-col items-center justify-center w-full min-h-[120px] border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors"
                         onclick="document.getElementById('videoInput').click()"
                         ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50')"
                         ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
                         ondrop="handleVideoDrop(event)">
                        <i class="fas fa-film text-gray-400 text-3xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">Drag & drop or click to upload video</p>
                        <p class="text-xs text-gray-400 mt-1">MP4, WebM, MOV — max 50 MB</p>
                        <input type="file" id="videoInput" name="video" accept="video/mp4,video/webm,video/quicktime" class="hidden"
                               onchange="previewVideo(this)">
                    </div>

                    {{-- Video preview --}}
                    <div id="videoPreview" class="hidden rounded-xl overflow-hidden border border-gray-200 bg-black relative">
                        <video id="videoEl" controls class="w-full max-h-64 object-contain bg-black"></video>
                        <button type="button" onclick="removeVideo()"
                                class="absolute top-2 right-2 w-7 h-7 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700 transition-colors shadow">
                            <i class="fas fa-times"></i>
                        </button>
                        <p id="videoName" class="px-3 py-2 text-xs text-gray-400 bg-gray-50 border-t border-gray-200"></p>
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
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">
                            Vendor <span class="text-red-500">*</span>
                        </label>
                        @php $lockedUserId = request('user_id') ?? old('user_id'); @endphp
                        @if($lockedUserId)
                            @php $lockedVendor = $vendors->firstWhere('id', $lockedUserId); @endphp
                            <input type="hidden" name="user_id" value="{{ $lockedUserId }}">
                            <div class="flex items-center gap-3 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="w-7 h-7 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($lockedVendor->name ?? 'V', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ $lockedVendor->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $lockedVendor->email ?? '' }}</p>
                                </div>
                                <a href="{{ url()->current() }}" class="text-xs text-gray-400 hover:text-gray-600 flex-shrink-0" title="Change vendor">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                            <p class="mt-1 text-xs text-gray-400"><i class="fas fa-lock mr-1"></i>Vendor pre-selected — click × to change</p>
                        @else
                            <select name="user_id" required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                                <option value="">Select vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('user_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }} ({{ $vendor->email }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                        <select name="product_category_id" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                            <option value="">Select category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('product_category_id') == $cat->id ? 'selected' : '' }}>
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
                            <option value="">Select country</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ old('country_id') == $c->id ? 'selected' : '' }}>
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
                            <option value="draft"    {{ old('status', 'draft') === 'draft'    ? 'selected' : '' }}>Draft</option>
                            <option value="active"   {{ old('status', 'draft') === 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', 'draft') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Admin Verified --}}
                    <div>
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_admin_verified" value="0">
                            <input type="checkbox" name="is_admin_verified" value="1" {{ old('is_admin_verified') ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808]">
                            <span class="text-sm font-medium text-gray-700">Mark as Admin Verified</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('admin.vendor.product.index') }}"
                   class="flex-1 inline-flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                    <i class="fas fa-save"></i> Create Product
                </button>
            </div>
        </div>{{-- end sidebar --}}

    </div>
</form>

{{-- Quill JS --}}
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
// ── Quill Rich Text Editor ────────────────────────────────────────────────
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

// Sync Quill → hidden textarea on submit
document.getElementById('productForm').addEventListener('submit', function () {
    document.getElementById('descriptionInput').value = quill.root.innerHTML;
});

// Pre-fill from old() value if present
const oldDesc = document.getElementById('descriptionInput').value.trim();
if (oldDesc) quill.root.innerHTML = oldDesc;


// ── Images (max 4) ────────────────────────────────────────────────────────
const MAX_IMAGES = 4;
let allFiles = [];

function updateImageCount() {
    document.getElementById('imageCount').textContent = `${allFiles.length} / ${MAX_IMAGES}`;
    // Grey out drop zone when full
    const dz = document.getElementById('dropZone');
    if (allFiles.length >= MAX_IMAGES) {
        dz.classList.add('opacity-50', 'pointer-events-none');
    } else {
        dz.classList.remove('opacity-50', 'pointer-events-none');
    }
}

function addImages(files) {
    const warn = document.getElementById('imageLimitWarn');
    warn.classList.add('hidden');

    const available = MAX_IMAGES - allFiles.length;
    const incoming  = Array.from(files);

    if (incoming.length > available) {
        warn.classList.remove('hidden');
    }

    incoming.slice(0, available).forEach(f => allFiles.push(f));
    rebuildInput();
    renderPreviews();
}

function rebuildInput() {
    const dt = new DataTransfer();
    allFiles.forEach(f => dt.items.add(f));
    document.getElementById('imageInput').files = dt.files;
    updateImageCount();
}

function renderPreviews() {
    const grid = document.getElementById('previewGrid');
    grid.innerHTML = '';
    allFiles.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.className = 'relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-100 shadow-sm';
            wrap.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105">
                ${i === 0
                    ? `<div class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-[#ff0808] text-white text-[9px] font-bold rounded shadow">Primary</div>`
                    : ''}
                <button type="button" onclick="removeImage(${i})"
                    class="absolute top-1.5 right-1.5 w-6 h-6 bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-[10px] shadow">
                    <i class="fas fa-times"></i>
                </button>
            `;
            grid.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}

function removeImage(index) {
    allFiles.splice(index, 1);
    rebuildInput();
    renderPreviews();
    document.getElementById('imageLimitWarn').classList.add('hidden');
}

function handleDrop(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('border-[#ff0808]', 'bg-red-50');
    addImages(event.dataTransfer.files);
}


// ── Video (single) ────────────────────────────────────────────────────────
function previewVideo(input) {
    const file = input.files[0];
    if (!file) return;

    const maxMB = 50;
    if (file.size > maxMB * 1024 * 1024) {
        alert(`Video must be under ${maxMB} MB.`);
        input.value = '';
        return;
    }

    const url = URL.createObjectURL(file);
    document.getElementById('videoEl').src = url;
    document.getElementById('videoName').textContent = `${file.name}  (${(file.size / 1024 / 1024).toFixed(1)} MB)`;
    document.getElementById('videoPreview').classList.remove('hidden');
    document.getElementById('videoDropZone').classList.add('hidden');
}

function removeVideo() {
    const input = document.getElementById('videoInput');
    input.value = '';
    const el = document.getElementById('videoEl');
    URL.revokeObjectURL(el.src);
    el.src = '';
    document.getElementById('videoPreview').classList.add('hidden');
    document.getElementById('videoDropZone').classList.remove('hidden');
}

function handleVideoDrop(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('border-blue-400', 'bg-blue-50');
    const file = event.dataTransfer.files[0];
    if (!file) return;
    const dt = new DataTransfer();
    dt.items.add(file);
    const input = document.getElementById('videoInput');
    input.files = dt.files;
    previewVideo(input);
}
</script>

{{-- Fix Quill editor height / border to match design --}}
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
