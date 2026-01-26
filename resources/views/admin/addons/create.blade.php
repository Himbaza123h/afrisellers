@extends('layouts.home')

@section('page-content')
<div class="space-y-4 max-w-3xl">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.addons.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Create Addon</h1>
                <p class="mt-0.5 text-xs text-gray-500">Add a new promotional addon placement</p>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3 bg-red-50 rounded-lg border border-red-200">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-900 mb-1">Please fix the errors:</p>
                    <ul class="space-y-0.5 text-xs text-red-700">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.addons.store') }}" method="POST" class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 space-y-4">
        @csrf

        <!-- Country Selection -->
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Country (Leave empty for global)
            </label>
            <select name="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm">
                <option value="">Global (All Countries)</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Location X -->
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Location Section *
            </label>
            <select name="locationX" id="locationX" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm" required onchange="updateLocationY()">
                <option value="">Select section...</option>
                @foreach($locations as $section => $positions)
                    <option value="{{ $section }}" {{ old('locationX') == $section ? 'selected' : '' }}>
                        {{ $section }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Location Y -->
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Location Position *
            </label>
            <select name="locationY" id="locationY" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm" required>
                <option value="">Select position...</option>
            </select>
        </div>

        <!-- Price -->
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Price (per 30 days) *
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">$</span>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" max="999999.99" placeholder="0.00" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm" required>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-2 pt-4 border-t">
            <a href="{{ route('admin.addons.index') }}" class="flex-1 inline-flex items-center justify-center gap-1 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1 px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 text-sm font-medium shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Create Addon</span>
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

// Initialize on page load if locationX has a value
document.addEventListener('DOMContentLoaded', function() {
    const locationX = document.getElementById('locationX').value;
    if (locationX) {
        updateLocationY();
        const oldLocationY = "{{ old('locationY') }}";
        if (oldLocationY) {
            document.getElementById('locationY').value = oldLocationY;
        }
    }
});
</script>
@endsection
