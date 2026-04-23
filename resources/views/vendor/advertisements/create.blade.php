@extends('layouts.home')
@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('vendor.advertisements.index') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Create Advertisement</h1>
            <p class="mt-1 text-xs text-gray-500">Submit a new ad for review</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <form action="{{ route('vendor.advertisements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-sm @error('title') border-red-500 @enderror"
                       placeholder="e.g., Summer Sale Banner">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Position --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Position <span class="text-red-500">*</span></label>
                <select name="position" id="position" onchange="updateSizeHint()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-sm @error('position') border-red-500 @enderror">
                    <option value="">Select position</option>
                    @foreach($positions as $key => $pos)
                        <option value="{{ $key }}" {{ old('position')===$key?'selected':'' }}>
                            {{ $pos['label'] }} — {{ $pos['size'] }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500" id="sizeHint">Select a position to see the recommended size.</p>
                @error('position')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Type <span class="text-red-500">*</span></label>
                <select name="type" id="adType" onchange="updateTypeFields()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-sm">
                    <option value="">Select type</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ old('type')===$key?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Media Upload --}}
            <div id="mediaField">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Media <span class="text-red-500">*</span></label>
                <input type="file" name="media" accept="image/*,video/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('media') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">JPG, PNG, WebP, GIF, MP4 — max 20MB</p>
                @error('media')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Text Ad fields --}}
            <div id="textFields" style="display:none;" class="space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Text Ad Settings</p>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Background Gradient</label>
                    <input type="text" name="bg_gradient" value="{{ old('bg_gradient','linear-gradient(135deg,#ff0808 0%,#c80000 100%)') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                           placeholder="linear-gradient(135deg,#ff0808 0%,#c80000 100%)">
                </div>
            </div>

            {{-- Headline / Sub / Badge --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Headline</label>
                    <input type="text" name="headline" value="{{ old('headline') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                           placeholder="e.g., 70% OFF Today">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sub Text</label>
                    <input type="text" name="sub_text" value="{{ old('sub_text') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                           placeholder="e.g., Limited time only">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Badge</label>
                    <input type="text" name="badge_text" value="{{ old('badge_text') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                           placeholder="e.g., SALE">
                </div>
            </div>

            {{-- Destination URL --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Destination URL</label>
                <input type="url" name="destination_url" value="{{ old('destination_url') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-sm"
                       placeholder="https://...">
            </div>

            {{-- Accent Color --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Accent Color</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="accent_color" value="{{ old('accent_color','#ff0808') }}"
                           class="w-10 h-10 border border-gray-300 rounded cursor-pointer">
                    <span class="text-xs text-gray-500">Used for badge background and left border accent</span>
                </div>
            </div>

            {{-- Duration & Start Date --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (days) <span class="text-red-500">*</span></label>
                    <select name="duration_days" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-sm">
                        <option value="7"   {{ old('duration_days')==7?'selected':'' }}>7 days</option>
                        <option value="14"  {{ old('duration_days')==14?'selected':'' }}>14 days</option>
                        <option value="30"  {{ old('duration_days',30)==30?'selected':'' }}>30 days</option>
                        <option value="60"  {{ old('duration_days')==60?'selected':'' }}>60 days</option>
                        <option value="90"  {{ old('duration_days')==90?'selected':'' }}>90 days</option>
                        <option value="180" {{ old('duration_days')==180?'selected':'' }}>180 days</option>
                        <option value="365" {{ old('duration_days')==365?'selected':'' }}>365 days</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-sm">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to start immediately after approval</p>
                </div>
            </div>

            {{-- Info box --}}
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-xs font-semibold text-blue-800 mb-1"><i class="fas fa-info-circle mr-1"></i>Review Process</p>
                <p class="text-xs text-blue-700">Your ad will be reviewed by our team within 24 hours. Once approved, it will go live on your selected start date.</p>
            </div>

            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#dd0606] font-medium text-sm">
                    <i class="fas fa-paper-plane mr-1"></i> Submit for Review
                </button>
                <a href="{{ route('vendor.advertisements.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const sizeMap = {!! json_encode(collect(\App\Models\Advertisement::positions())->map(fn($p) => $p['size'])) !!};
// fallback — inline the data
const sizes = @json(collect($positions)->map(fn($p) => $p['size']));

function updateSizeHint(){
    const pos = document.getElementById('position').value;
    const hint = document.getElementById('sizeHint');
    hint.textContent = sizes[pos] ? 'Recommended size: ' + sizes[pos] : 'Select a position to see the recommended size.';
}

function updateTypeFields(){
    const type = document.getElementById('adType').value;
    document.getElementById('mediaField').style.display  = type === 'text' ? 'none' : 'block';
    document.getElementById('textFields').style.display  = type === 'text' ? 'block' : 'none';
}
</script>
@endsection
