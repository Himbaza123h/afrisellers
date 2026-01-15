@extends('layouts.home')

@section('page-content')
<!-- Header Section -->
<div class="mb-4 sm:mb-6 lg:mb-8">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.product-category.index') }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-lg font-black text-gray-900 uppercase">Add New Product Category</h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Create a new product category entry</p>
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
    <form action="{{ route('admin.product-category.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Category Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                Category Name <span class="text-red-600">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                placeholder="e.g., Electronics"
            >
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Countries -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">
                Countries
            </label>
            <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto bg-white">
                @forelse($countries as $country)
                    <div class="flex items-center mb-3 last:mb-0">
                        <input
                            type="checkbox"
                            name="country_ids[]"
                            id="country_{{ $country->id }}"
                            value="{{ $country->id }}"
                            {{ in_array($country->id, old('country_ids', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-[#ff0808] border-gray-300 rounded focus:ring-[#ff0808] focus:ring-2"
                        >
                        <label for="country_{{ $country->id }}" class="ml-3 text-sm font-medium text-gray-900 cursor-pointer flex items-center gap-2">
                            @if($country->flag_url)
                                <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" class="w-5 h-4 object-cover rounded border border-gray-300">
                            @endif
                            <span>{{ $country->name }}</span>
                        </label>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No active countries available</p>
                @endforelse
            </div>
            @error('country_ids')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @error('country_ids.*')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-gray-500">Select one or more countries for this category. Leave unchecked for a global category.</p>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                Description
            </label>
            <textarea
                name="description"
                id="description"
                rows="4"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                placeholder="Enter category description..."
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
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
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button
                type="submit"
                class="px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md"
            >
                <i class="fas fa-save mr-2"></i>
                Create Category
            </button>
            <a href="{{ route('admin.product-category.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

