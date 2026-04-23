@extends('layouts.home')
@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.fallback-ads.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Fallback Ad</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ $fallbackAd->headline }}</p>
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

    <form action="{{ route('admin.fallback-ads.update', $fallbackAd) }}" method="POST"
          class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Position --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Position <span class="text-red-500">*</span></label>
                <select name="position" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
                    @foreach($positions as $key => $label)
                        <option value="{{ $key }}" {{ old('position', $fallbackAd->position)===$key?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ad Type <span class="text-red-500">*</span></label>
                <select name="type" required id="adType"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]"
                        onchange="toggleFields(this.value)">
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $fallbackAd->type)===$key?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Media URL --}}
        <div id="mediaField" class="{{ old('type', $fallbackAd->type) === 'text' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Media URL</label>
            <input type="url" name="media" value="{{ old('media', $fallbackAd->media) }}" placeholder="https://..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
        </div>

        {{-- BG Gradient --}}
        <div id="bgField" class="{{ old('type', $fallbackAd->type) !== 'text' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Background Gradient</label>
            <input type="text" name="bg" value="{{ old('bg', $fallbackAd->bg) }}"
                   placeholder="linear-gradient(135deg,#ff0808 0%,#c80000 100%)"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#ff0808]">
        </div>

        {{-- Headline --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Headline <span class="text-red-500">*</span></label>
            <input type="text" name="headline" value="{{ old('headline', $fallbackAd->headline) }}" required maxlength="255"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
        </div>

        {{-- Sub Text --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Sub Text</label>
            <input type="text" name="sub_text" value="{{ old('sub_text', $fallbackAd->sub_text) }}" maxlength="255"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">CTA URL</label>
                <input type="url" name="cta_url" value="{{ old('cta_url', $fallbackAd->cta_url) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Badge Text</label>
                <input type="text" name="badge" value="{{ old('badge', $fallbackAd->badge) }}" maxlength="50"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Overlay Color</label>
                <input type="text" name="overlay" value="{{ old('overlay', $fallbackAd->overlay) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#ff0808]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Accent Color</label>
                <div class="flex gap-2">
                    <input type="text" name="accent" id="accentText"
                           value="{{ old('accent', $fallbackAd->accent) }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#ff0808]"
                           oninput="document.getElementById('accentPicker').value=this.value">
                    <input type="color" id="accentPicker"
                           value="{{ old('accent', $fallbackAd->accent) }}"
                           class="w-10 h-10 rounded border border-gray-300 cursor-pointer p-0.5"
                           oninput="document.getElementById('accentText').value=this.value">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $fallbackAd->sort_order) }}" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808]">
            </div>

            <div class="flex items-center gap-3 pt-6">
                <input type="hidden" name="pattern" value="0">
                <input type="checkbox" name="pattern" id="pattern" value="1"
                       {{ old('pattern', $fallbackAd->pattern) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808]">
                <label for="pattern" class="text-sm font-medium text-gray-700">Show dot pattern overlay</label>
            </div>

        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-6 py-2.5 bg-[#ff0808] text-white rounded-lg font-medium text-sm hover:bg-[#dd0606]">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
            <a href="{{ route('admin.fallback-ads.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function toggleFields(type) {
    const mediaField = document.getElementById('mediaField');
    const bgField    = document.getElementById('bgField');
    if (type === 'text') {
        mediaField.classList.add('hidden');
        bgField.classList.remove('hidden');
    } else {
        mediaField.classList.remove('hidden');
        bgField.classList.add('hidden');
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const t = document.getElementById('adType').value;
    if (t) toggleFields(t);
});
</script>
@endsection
