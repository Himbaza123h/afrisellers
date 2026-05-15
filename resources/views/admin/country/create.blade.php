@extends('layouts.home')

@section('page-content')
<div class="mb-4 sm:mb-6 lg:mb-8">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.country.index') }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-lg font-black text-gray-900 uppercase">Add New Country</h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Create a new country entry</p>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-300 rounded-lg">
        <p class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</p>
        <ul class="text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <form action="{{ route('admin.country.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Country Name --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                    Country Name <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                    placeholder="e.g., Rwanda">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Country Code --}}
            <div>
                <label for="code" class="block text-sm font-semibold text-gray-900 mb-2">
                    Country Code <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" maxlength="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900 uppercase"
                    placeholder="e.g., RW">
                @error('code')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">2–3 letter ISO code</p>
            </div>

            {{-- Region --}}
            <div>
                <label for="region_id" class="block text-sm font-semibold text-gray-900 mb-2">
                    Region <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <select name="region_id" id="region_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900">
                    <option value="">— No Region —</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                            @if($region->code) ({{ $region->code }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('region_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-900 mb-2">
                    Status <span class="text-red-600">*</span>
                </label>
                <select name="status" id="status" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900">
                    <option value="active"   {{ old('status', 'active') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Flag URL --}}
        <div>
            <label for="flag_url" class="block text-sm font-semibold text-gray-900 mb-2">
                Flag URL <span class="text-gray-400 font-normal">(optional)</span>
            </label>
            <div class="flex gap-3 items-start">
                <div class="flex-1">
                    <input type="url" name="flag_url" id="flag_url" value="{{ old('flag_url') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                        placeholder="https://example.com/flags/rwanda.png"
                        oninput="previewFlag(this.value)">
                    @error('flag_url')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Enter the URL to the country's flag image</p>
                </div>
                {{-- Live preview --}}
                <div id="flagPreviewWrap" class="{{ old('flag_url') ? '' : 'hidden' }} border border-gray-200 rounded-lg p-2 bg-gray-50 flex-shrink-0">
                    <img id="flagPreview"
                         src="{{ old('flag_url', '') }}"
                         alt="Flag preview"
                         class="w-16 h-11 object-cover rounded border border-gray-200"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'44\'%3E%3Crect fill=\'%23e5e7eb\' width=\'64\' height=\'44\'/%3E%3Ctext x=\'50%25\' y=\'55%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%239ca3af\' font-size=\'10\'%3ENo flag%3C/text%3E%3C/svg%3E'">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button type="submit"
                class="px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md">
                <i class="fas fa-save mr-2"></i>Create Country
            </button>
            <a href="{{ route('admin.country.index') }}"
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function previewFlag(url) {
    const wrap = document.getElementById('flagPreviewWrap');
    const img  = document.getElementById('flagPreview');
    if (url) { img.src = url; wrap.classList.remove('hidden'); }
    else     { wrap.classList.add('hidden'); }
}
</script>
@endsection
