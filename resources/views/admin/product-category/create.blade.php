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
    <div class="mb-4 p-3 bg-red-50 border border-red-300 rounded-lg">
        <p class="text-sm font-medium text-red-900 mb-1">Please fix the following errors:</p>
        <ul class="text-xs text-red-700 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Form -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
    <form action="{{ route('admin.product-category.store') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Category Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-gray-900 mb-1">
                Category Name <span class="text-red-600">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
                required
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                placeholder="e.g., Electronics"
            >
            @error('name')
                <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Parent Category -->
        <div>
            <label for="parent_id" class="block text-xs font-semibold text-gray-900 mb-1">
                Parent Category
            </label>
            <select
                name="parent_id"
                id="parent_id"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
            >
                <option value="">Select Parent Category (Optional)</option>
                @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Leave empty to create a main category</p>
            @error('parent_id')
                <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Countries -->
        <div>
            <label class="block text-xs font-semibold text-gray-900 mb-1">
                Countries
            </label>
            <div class="border border-gray-300 rounded-lg p-3 max-h-48 overflow-y-auto bg-white">
                @forelse($countries as $country)
                    <div class="flex items-center mb-2 last:mb-0">
                        <input
                            type="checkbox"
                            name="country_ids[]"
                            id="country_{{ $country->id }}"
                            value="{{ $country->id }}"
                            {{ in_array($country->id, old('country_ids', [])) ? 'checked' : '' }}
                            class="w-3.5 h-3.5 text-[#ff0808] border-gray-300 rounded focus:ring-[#ff0808] focus:ring-2"
                        >
                        <label for="country_{{ $country->id }}" class="ml-2 text-xs font-medium text-gray-900 cursor-pointer flex items-center gap-1">
                            @if($country->flag_url)
                                <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" class="w-4 h-3 object-cover rounded border border-gray-300">
                            @endif
                            <span>{{ $country->name }}</span>
                        </label>
                    </div>
                @empty
                    <p class="text-xs text-gray-500">No active countries available</p>
                @endforelse
            </div>
            @error('country_ids')
                <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @error('country_ids.*')
                <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Select one or more countries for this category. Leave unchecked for a global category.</p>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-xs font-semibold text-gray-900 mb-1">
                Description
            </label>
            <textarea
                name="description"
                id="description"
                rows="3"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                placeholder="Enter category description..."
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div>
            <label for="status" class="block text-xs font-semibold text-gray-900 mb-1">
                Status <span class="text-red-600">*</span>
            </label>
            <select
                name="status"
                id="status"
                required
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
            >
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 pt-3 border-t border-gray-200">
            <button
                type="submit"
                class="px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-colors font-semibold text-sm shadow-sm"
            >
                <i class="fas fa-save mr-1 text-xs"></i>
                Create Category
            </button>
            <a href="{{ route('admin.product-category.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
