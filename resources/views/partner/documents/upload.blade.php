@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.documents.index') }}" class="hover:text-gray-600">Documents</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Upload</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Upload Document</h1>
    </div>
    <a href="{{ route('partner.documents.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('partner.documents.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider pb-2 border-b border-gray-100">Document Details</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                       placeholder="e.g. Business Registration Certificate">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                <select name="type" required
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                    <option value="">Select type...</option>
                    @foreach(\App\Models\PartnerDocument::$types as $val => $label)
                        <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">File <span class="text-red-500">*</span></label>
            <div id="drop-zone"
                 class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-[#ff0808] hover:bg-red-50 transition-all"
                 onclick="document.getElementById('file-input').click()">
                <i class="fas fa-cloud-upload-alt text-gray-300 text-3xl mb-3"></i>
                <p class="text-sm font-semibold text-gray-600" id="drop-label">Click or drag a file here</p>
                <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG, DOC, DOCX — max 20 MB</p>
                <input type="file" id="file-input" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden"
                       onchange="updateLabel(this)">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Notes <span class="text-gray-400 font-normal">(optional)</span></label>
            <textarea name="notes" rows="3"
                      class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent resize-none"
                      placeholder="Any additional context about this document...">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-end gap-3">
        <a href="{{ route('partner.documents.index') }}"
           class="px-5 py-2.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</a>
        <button type="submit"
                class="px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-upload"></i> Upload Document
        </button>
    </div>
</form>

<script>
function updateLabel(input) {
    const label = document.getElementById('drop-label');
    if (input.files.length) {
        label.textContent = input.files[0].name;
        label.classList.add('text-[#ff0808]');
    }
}

const zone = document.getElementById('drop-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-[#ff0808]', 'bg-red-50'); });
zone.addEventListener('dragleave', () => zone.classList.remove('border-[#ff0808]', 'bg-red-50'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('border-[#ff0808]', 'bg-red-50');
    const fi = document.getElementById('file-input');
    fi.files = e.dataTransfer.files;
    updateLabel(fi);
});
</script>
@endsection
