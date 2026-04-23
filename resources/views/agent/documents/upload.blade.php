@extends('layouts.home')

@push('styles')
<style>
    #drop-zone.drag-over {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
    #drop-zone.drag-over i { color: #3b82f6; }
    #file-preview { display: none; }
    #file-preview.visible { display: flex; }
</style>
@endpush

@section('page-content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.documents.index') }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Upload Document</h1>
            <p class="text-xs text-gray-500 mt-0.5">Add a new document to your library</p>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('agent.documents.store') }}" method="POST"
          enctype="multipart/form-data" class="space-y-5" id="uploadForm">
        @csrf

        {{-- Drop Zone --}}
        <div class="bg-white rounded-xl border-2 border-dashed border-gray-300 shadow-sm p-8 text-center
                    cursor-pointer hover:border-blue-400 transition-colors"
             id="drop-zone"
             onclick="document.getElementById('fileInput').click()">
            <input type="file" name="file" id="fileInput" class="hidden" required
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.png,.jpg,.jpeg,.gif,.webp,.zip,.rar,.ppt,.pptx">

            {{-- Default state --}}
            <div id="drop-placeholder">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cloud-upload-alt text-3xl text-blue-400"></i>
                </div>
                <p class="text-sm font-semibold text-gray-700 mb-1">
                    Drop your file here or <span class="text-blue-600 underline">browse</span>
                </p>
                <p class="text-xs text-gray-400">
                    PDF, DOC, XLS, PNG, ZIP and more &middot; Max 20 MB
                </p>
            </div>

            {{-- Selected file preview --}}
            <div id="file-preview"
                 class="items-center gap-4 justify-center">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file text-blue-600 text-xl" id="preview-icon"></i>
                </div>
                <div class="text-left">
                    <p class="text-sm font-bold text-gray-800" id="preview-name"></p>
                    <p class="text-xs text-gray-500" id="preview-size"></p>
                </div>
                <button type="button" onclick="clearFile(event)"
                    class="ml-4 text-red-400 hover:text-red-600 transition-colors">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
        </div>

        {{-- Document Details --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Document Details
            </h2>

            {{-- Title --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    placeholder="e.g. Vendor Agreement Q1 2025"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Category + Expiry --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a category</option>
                        @foreach([
                            'contract'  => 'Contract',
                            'invoice'   => 'Invoice',
                            'identity'  => 'Identity Document',
                            'agreement' => 'Agreement',
                            'report'    => 'Report',
                            'license'   => 'License / Certificate',
                            'other'     => 'Other',
                        ] as $val => $label)
                            <option value="{{ $val }}" {{ old('category') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Expiry Date
                        <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input type="date" name="expires_at"
                        value="{{ old('expires_at') }}"
                        min="{{ now()->addDay()->toDateString() }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Leave blank if it doesn't expire.</p>
                    @error('expires_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Description
                    <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <textarea name="description" rows="3"
                    placeholder="Brief notes about this document…"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 resize-none leading-relaxed">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Tags --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Tags
                    <span class="text-gray-400 font-normal">(optional, comma-separated)</span>
                </label>
                <input type="text" name="tags" value="{{ old('tags') }}"
                    placeholder="e.g. vendor, 2025, renewal"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-400">Separate tags with commas to make filtering easier.</p>
                @error('tags')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Share toggle --}}
            <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-lg border border-purple-100">
                <input type="checkbox" name="is_shared" id="isShared" value="1"
                    {{ old('is_shared') ? 'checked' : '' }}
                    class="mt-0.5 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                <div>
                    <label for="isShared" class="text-sm font-semibold text-gray-800 cursor-pointer">
                        Share with my vendors
                    </label>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Vendors you manage will be able to view and download this document.
                    </p>
                </div>
            </div>
        </div>

                    {{-- Requires Admin Attention --}}
            <div class="flex items-start gap-3 p-4 bg-amber-50 rounded-xl border border-amber-200">
                <input type="checkbox" name="requires_attention" id="requiresAttention" value="1"
                    {{ old('requires_attention') ? 'checked' : '' }}
                    class="mt-0.5 rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                <div>
                    <label for="requiresAttention" class="text-sm font-semibold text-gray-800 cursor-pointer flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-amber-500"></i>
                        Requires Admin Attention
                    </label>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Notify the admin team to review this document — it will appear highlighted in their queue.
                    </p>
                </div>
            </div>
        </div>{{-- close Document Details card --}}

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agent.documents.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" id="submitBtn"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                <i class="fas fa-upload"></i> Upload Document
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const dropZone    = document.getElementById('drop-zone');
const fileInput   = document.getElementById('fileInput');
const placeholder = document.getElementById('drop-placeholder');
const preview     = document.getElementById('file-preview');
const previewName = document.getElementById('preview-name');
const previewSize = document.getElementById('preview-size');
const previewIcon = document.getElementById('preview-icon');

const iconMap = {
    pdf:  'fa-file-pdf text-red-500',
    doc:  'fa-file-word text-blue-600', docx: 'fa-file-word text-blue-600',
    xls:  'fa-file-excel text-green-600', xlsx: 'fa-file-excel text-green-600',
    csv:  'fa-file-excel text-green-600',
    png:  'fa-file-image text-purple-500', jpg: 'fa-file-image text-purple-500',
    jpeg: 'fa-file-image text-purple-500', gif: 'fa-file-image text-purple-500',
    zip:  'fa-file-archive text-amber-500', rar: 'fa-file-archive text-amber-500',
    txt:  'fa-file-alt text-gray-500',
    ppt:  'fa-file-powerpoint text-orange-500', pptx: 'fa-file-powerpoint text-orange-500',
};

function formatBytes(bytes) {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
    if (bytes >= 1024)    return (bytes / 1024).toFixed(2) + ' KB';
    return bytes + ' B';
}

function showPreview(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    previewIcon.className = 'fas ' + (iconMap[ext] ?? 'fa-file text-gray-400') + ' text-xl';
    previewName.textContent = file.name;
    previewSize.textContent = formatBytes(file.size);
    placeholder.classList.add('hidden');
    preview.classList.add('visible');

    // Auto-fill title if empty
    const titleInput = document.querySelector('input[name="title"]');
    if (!titleInput.value) {
        titleInput.value = file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' ');
    }
}

function clearFile(e) {
    e.stopPropagation();
    fileInput.value = '';
    placeholder.classList.remove('hidden');
    preview.classList.remove('visible');
}

fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) showPreview(fileInput.files[0]);
});

// Drag and drop
dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.classList.add('drag-over');
});
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        showPreview(file);
    }
});

// Upload progress feedback
document.getElementById('uploadForm').addEventListener('submit', () => {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading…';
});
</script>
@endpush
