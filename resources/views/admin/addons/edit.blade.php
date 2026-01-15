@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.addons.index') }}" class="p-2 text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Addon</h1>
            <p class="mt-1 text-sm text-gray-500">Update addon details</p>
        </div>
    </div>

    <!-- Messages -->
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</p>
                    <ul class="space-y-1 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.addons.update', $addon) }}" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Country Selection -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                Country
                <span class="text-gray-500 font-normal ml-1">(Leave empty for global)</span>
            </label>
            <select name="country_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                <option value="">Global (All Countries)</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ (old('country_id', $addon->country_id) == $country->id) ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
            @error('country_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Location X -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                Location Section
                <span class="text-red-500">*</span>
            </label>
            <select name="locationX" id="locationX" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" required onchange="updateLocationY()">
                <option value="">Select section...</option>
                @foreach($locations as $section => $positions)
                    <option value="{{ $section }}" {{ (old('locationX', $addon->locationX) == $section) ? 'selected' : '' }}>
                        {{ $section }}
                    </option>
                @endforeach
            </select>
            @error('locationX')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Location Y -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                Location Position
                <span class="text-red-500">*</span>
            </label>
            <select name="locationY" id="locationY" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" required>
                <option value="">Select position...</option>
            </select>
            @error('locationY')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Price -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                Price (per 30 days)
                <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 mt-3">$</span>
                <input type="number" name="price" value="{{ old('price', $addon->price) }}" step="0.01" min="0" max="999999.99" placeholder="0.00" class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" required>
            </div>
            @error('price')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-3 pt-4 border-t">
            <a href="{{ route('admin.addons.index') }}" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-center">
                Cancel
            </a>
            <button type="submit" class="flex-1 px-6 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700 font-medium">
                <i class="fas fa-save mr-2"></i>
                Update Addon
            </button>
        </div>
    </form>
</div>

<script>
const locations = @json($locations);

function updateLocationY() {
    const locationX = document.getElementById('locationX').value;
    const locationYSelect = document.getElementById('locationY');

    // Clear current options
    locationYSelect.innerHTML = '<option value="">Select position...</option>';

    // Add new options based on selected section
    if (locationX && locations[locationX]) {
        locations[locationX].forEach(position => {
            const option = document.createElement('option');
            option.value = position;
            option.textContent = position.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            locationYSelect.appendChild(option);
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateLocationY();
    const oldLocationY = "{{ old('locationY', $addon->locationY) }}";
    if (oldLocationY) {
        document.getElementById('locationY').value = oldLocationY;
    }
});
</script>
@endsection
