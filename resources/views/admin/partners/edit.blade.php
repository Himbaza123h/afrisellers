@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.partners.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-arrow-left text-gray-600 text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Partner</h1>
            <p class="text-xs text-gray-500">Update {{ $partner->name }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.partners.update', $partner) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-base font-bold text-gray-900 border-b pb-3">Partner Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $partner->name) }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Website URL</label>
                    <input type="url" name="website_url" value="{{ old('website_url', $partner->website_url) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]"
                           placeholder="https://example.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Industry</label>
                    <input type="text" name="industry" value="{{ old('industry', $partner->industry) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Partner Type</label>
                    <input type="text" name="partner_type" value="{{ old('partner_type', $partner->partner_type) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $partner->sort_order) }}" min="0"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                </div>
                <div class="flex items-center gap-3 pt-5">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $partner->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-[#ff0808] rounded">
                    <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Logo / GIF</label>
                @if($partner->logo_url)
                    <div class="mb-2 p-3 bg-gray-50 rounded-lg inline-block">
                        <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}"
                             class="h-12 w-auto object-contain">
                        <p class="text-xs text-gray-400 mt-1">Current logo</p>
                    </div>
                @endif
                <input type="file" name="logo" accept="image/*,.gif"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2
                              file:mr-3 file:py-1 file:px-3 file:rounded file:border-0
                              file:text-xs file:font-semibold file:bg-red-50 file:text-[#ff0808]">
                <p class="text-xs text-gray-400 mt-1">Leave empty to keep existing logo.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
                <div id="descEditor" style="min-height:150px; border:1px solid #d1d5db; border-radius:8px; overflow:hidden;"></div>
                <input type="hidden" name="description" id="descInput">
            </div>
        </div>

        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('admin.partners.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-[#ff0808] text-white text-sm font-semibold rounded-lg hover:bg-red-700 shadow-sm">
                <i class="fas fa-save mr-2"></i> Update Partner
            </button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
const quill = new Quill('#descEditor', {
    theme: 'snow',
    placeholder: 'Write a description...',
    modules: { toolbar: [['bold','italic','underline'],['link'],['clean']] }
});
quill.root.innerHTML = `{!! addslashes($partner->description ?? '') !!}`;
document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('descInput').value = quill.root.innerHTML;
});
</script>
@endsection
