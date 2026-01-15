@extends('layouts.home')

@section('page-content')
<!-- Header Section -->
<div class="mb-4 sm:mb-6 lg:mb-8">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.country.index') }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-lg font-black text-gray-900 uppercase">Edit Country</h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Update country information</p>
        </div>
    </div>
</div>

<!-- Error Messages -->
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

<!-- Form -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <form action="{{ route('admin.country.update', $country) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Country Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                Country Name <span class="text-red-600">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $country->name) }}"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                placeholder="e.g., Rwanda"
            >
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Flag URL -->
        <div>
            <label for="flag_url" class="block text-sm font-semibold text-gray-900 mb-2">
                Flag URL
            </label>
            <input
                type="url"
                name="flag_url"
                id="flag_url"
                value="{{ old('flag_url', $country->flag_url) }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                placeholder="https://example.com/flags/rwanda.png"
            >
            @error('flag_url')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Enter the URL to the country's flag image</p>
        </div>

        <!-- Status -->
        <div>
            <label for="status" class="block text-sm font-semibold text-gray-900 mb-2">
                Status <span class="text-red-600">*</span>
            </label>
            <select
                name="status"
                id="status"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
            >
                <option value="active" {{ old('status', $country->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $country->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Preview Flag -->
        @if($country->flag_url || old('flag_url'))
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Flag Preview</label>
                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 inline-block">
                    <img src="{{ old('flag_url', $country->flag_url) }}" alt="Flag preview" class="w-16 h-12 object-cover rounded border border-gray-300" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'48\'%3E%3Crect fill=\'%23e5e7eb\' width=\'64\' height=\'48\'/%3E%3C/svg%3E'">
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button
                type="submit"
                class="px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md"
            >
                <i class="fas fa-save mr-2"></i>
                Update Country
            </button>
            <a href="{{ route('admin.country.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

