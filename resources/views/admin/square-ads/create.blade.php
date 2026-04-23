@extends('layouts.home')
@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.square-ads.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">New Square Ad</h1>
            <p class="text-xs text-gray-500 mt-0.5">Pick a file from the library and configure the ad</p>
        </div>
    </div>

    @if($errors->any())
        <div class="p-3 bg-red-50 rounded-lg border border-red-200">
            <p class="text-sm font-medium text-red-900 mb-1">Please fix the following errors:</p>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.square-ads.store') }}" method="POST"
          class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-6">
        @csrf

        {{-- Library Picker --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Ad Image / File <span class="text-red-500">*</span>
            </label>
            <input type="hidden" name="library_id" id="library_id" value="{{ old('library_id') }}" required>

            {{-- Search --}}
            <input type="text" id="librarySearch" placeholder="Search library..."
                   class="w-full mb-3 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">

            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2 max-h-72 overflow-y-auto border border-gray-200 rounded-lg p-2"
                 id="libraryGrid">
                @foreach($library as $item)
                    <div class="library-item cursor-pointer rounded-lg overflow-hidden border-2 border-transparent hover:border-[#ff0808] transition-all relative"
                         data-id="{{ $item->id }}"
                         data-name="{{ strtolower($item->name) }}"
                         onclick="selectMedia(this, {{ $item->id }})">
                        @if($item->type === 'video')
                            <div class="w-full h-16 bg-gray-800 flex items-center justify-center">
                                <i class="fas fa-play text-white text-lg"></i>
                            </div>
                        @else
                            <img src="{{ Storage::url($item->file_path) }}"
                                 class="w-full h-16 object-cover" loading="lazy">
                        @endif
                        <div class="absolute inset-0 hidden items-center justify-center bg-[#ff0808]/60 selected-overlay">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <p class="text-[9px] text-gray-500 truncate px-1 py-0.5 bg-white">{{ $item->name }}</p>
                    </div>
                @endforeach
            </div>

            @if($library->isEmpty())
                <p class="text-sm text-gray-400 mt-2">
                    No files in the library yet.
                    <a href="{{ route('admin.ad-library.index') }}" class="text-[#ff0808] underline">Upload files first</a>.
                </p>
            @endif

            {{-- Selected preview --}}
            <div id="selectedPreview" class="hidden mt-3 flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <img id="selectedImg" src="" class="w-12 h-12 object-cover rounded">
                <div>
                    <p class="text-xs font-semibold text-gray-800" id="selectedName"></p>
                    <button type="button" onclick="clearSelection()" class="text-[10px] text-red-500 hover:underline">Remove</button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Type</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
                    <option value="">-- Select Type --</option>
                    <option value="Weekly Special Offers" {{ old('type') == 'Weekly Special Offers' ? 'selected' : '' }}>Weekly Special Offers</option>
                    <option value="Hot Deals" {{ old('type') == 'Hot Deals' ? 'selected' : '' }}>Hot Deals</option>
                    <option value="The Popular Suppliers" {{ old('type') == 'The Popular Suppliers' ? 'selected' : '' }}>Most Recommended Sales</option>
                    {{-- <option value="The Popular Suppliers" {{ old('type') == 'The Popular Suppliers' ? 'selected' : '' }}>The Popular Suppliers</option> --}}
                    <option value="Trending Products" {{ old('type') == 'Trending Products' ? 'selected' : '' }}>Trending Products</option>
                </select>
            </div>

            {{-- Badge --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Badge Text</label>
                <input type="text" name="badge" value="{{ old('badge') }}" maxlength="50"
                       placeholder="SPONSORED"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
            </div>

        </div>

        {{-- Headline --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Headline <span class="text-red-500">*</span></label>
            <input type="text" name="headline" value="{{ old('headline') }}" required maxlength="255"
                   placeholder="Scale Your Business Across Africa"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
        </div>

        {{-- Sub Text --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Sub Text</label>
            <input type="text" name="sub_text" value="{{ old('sub_text') }}" maxlength="255"
                   placeholder="Reach verified buyers on Afrisellers"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            {{-- CTA URL --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">CTA URL</label>
                <input type="url" name="cta_url" value="{{ old('cta_url', '#') }}"
                       placeholder="https://... or #"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
            </div>

            {{-- Accent --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Accent Color</label>
                <div class="flex gap-2">
                    <input type="text" name="accent" id="accentText" value="{{ old('accent', '#ff0808') }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#ff0808]"
                           oninput="document.getElementById('accentPicker').value=this.value">
                    <input type="color" id="accentPicker" value="{{ old('accent', '#ff0808') }}"
                           class="w-10 h-10 rounded border border-gray-300 cursor-pointer p-0.5"
                           oninput="document.getElementById('accentText').value=this.value">
                </div>
            </div>

            {{-- Sort Order --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
                <p class="text-xs text-gray-400 mt-1">Lower = shows first</p>
            </div>

        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-6 py-2.5 bg-[#ff0808] text-white rounded-lg font-medium text-sm hover:bg-[#dd0606] transition-colors">
                <i class="fas fa-plus mr-2"></i>Create Square Ad
            </button>
            <a href="{{ route('admin.square-ads.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function selectMedia(el, id) {
    // Clear all
    document.querySelectorAll('.library-item').forEach(item => {
        item.classList.remove('border-[#ff0808]');
        item.querySelector('.selected-overlay').classList.remove('flex');
        item.querySelector('.selected-overlay').classList.add('hidden');
    });

    // Select this
    el.classList.add('border-[#ff0808]');
    el.querySelector('.selected-overlay').classList.add('flex');
    el.querySelector('.selected-overlay').classList.remove('hidden');

    document.getElementById('library_id').value = id;

    // Preview
    const img = el.querySelector('img');
    const preview = document.getElementById('selectedPreview');
    document.getElementById('selectedName').textContent = el.dataset.name;
    if (img) {
        document.getElementById('selectedImg').src = img.src;
        preview.classList.remove('hidden');
    }
}

function clearSelection() {
    document.getElementById('library_id').value = '';
    document.getElementById('selectedPreview').classList.add('hidden');
    document.querySelectorAll('.library-item').forEach(item => {
        item.classList.remove('border-[#ff0808]');
        item.querySelector('.selected-overlay').classList.remove('flex');
        item.querySelector('.selected-overlay').classList.add('hidden');
    });
}

// Search filter
document.getElementById('librarySearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.library-item').forEach(item => {
        item.style.display = item.dataset.name.includes(q) ? '' : 'none';
    });
});

// Restore selection on old()
const oldId = '{{ old('library_id') }}';
if (oldId) {
    const el = document.querySelector(`.library-item[data-id="${oldId}"]`);
    if (el) selectMedia(el, parseInt(oldId));
}
</script>
@endsection
