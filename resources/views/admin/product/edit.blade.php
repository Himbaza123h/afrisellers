@extends('layouts.home')

@section('page-content')
<!-- Header Section -->
<div class="mb-4 sm:mb-6 lg:mb-8">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.product.index') }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-lg font-black text-gray-900 uppercase">Edit Product</h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Update product information</p>
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
    <form action="{{ route('admin.product.update', $product) }}" method="POST" class="space-y-6" id="productForm">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="border-b border-gray-200 pb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                        Product Name <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $product->name) }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                        placeholder="e.g., Premium Wireless Headphones"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-semibold text-gray-900 mb-2">
                        Slug
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        value="{{ old('slug', $product->slug) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                        placeholder="product-slug"
                    >
                    @error('slug')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Short Description -->
                <div>
                    <label for="short_description" class="block text-sm font-semibold text-gray-900 mb-2">
                        Short Description
                    </label>
                    <input
                        type="text"
                        name="short_description"
                        id="short_description"
                        value="{{ old('short_description', $product->short_description) }}"
                        maxlength="500"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                        placeholder="Brief description (max 500 characters)"
                    >
                    @error('short_description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vendor -->
                <div>
                    <label for="vendor_id" class="block text-sm font-semibold text-gray-900 mb-2">
                        Vendor <span class="text-red-600">*</span>
                    </label>
                    <select
                        name="vendor_id"
                        id="vendor_id"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                    >
                        <option value="">Select a vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor['id'] }}" {{ old('vendor_id', $product->vendor_id) == $vendor['id'] ? 'selected' : '' }}>
                                {{ $vendor['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="product_category_id" class="block text-sm font-semibold text-gray-900 mb-2">
                        Category
                    </label>
                    <select
                        name="product_category_id"
                        id="product_category_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                    >
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_category_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Country -->
                <div>
                    <label for="country_id" class="block text-sm font-semibold text-gray-900 mb-2">
                        Country
                    </label>
                    <select
                        name="country_id"
                        id="country_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                    >
                        <option value="">Select a country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', $product->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                    Description
                </label>
                <textarea
                    name="description"
                    id="description"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                    placeholder="Enter product description..."
                >{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Overview -->
            <div class="mt-6">
                <label for="overview" class="block text-sm font-semibold text-gray-900 mb-2">
                    Overview
                </label>
                <textarea
                    name="overview"
                    id="overview"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                    placeholder="Enter product overview..."
                >{{ old('overview', $product->overview) }}</textarea>
                @error('overview')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Pricing Information -->
        <div class="border-b border-gray-200 pb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Pricing Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Base Price -->
                <div>
                    <label for="base_price" class="block text-sm font-semibold text-gray-900 mb-2">
                        Base Price <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        name="base_price"
                        id="base_price"
                        value="{{ old('base_price', $product->base_price) }}"
                        required
                        min="0"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                        placeholder="0.00"
                    >
                    @error('base_price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Currency -->
                <div>
                    <label for="currency" class="block text-sm font-semibold text-gray-900 mb-2">
                        Currency <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        name="currency"
                        id="currency"
                        value="{{ old('currency', $product->currency) }}"
                        required
                        maxlength="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900 uppercase"
                        placeholder="RWF"
                    >
                    @error('currency')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Min Order Quantity -->
                <div>
                    <label for="min_order_quantity" class="block text-sm font-semibold text-gray-900 mb-2">
                        Min Order Quantity <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="number"
                        name="min_order_quantity"
                        id="min_order_quantity"
                        value="{{ old('min_order_quantity', $product->min_order_quantity) }}"
                        required
                        min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                        placeholder="1"
                    >
                    @error('min_order_quantity')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="border-b border-gray-200 pb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Additional Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Rating -->
                <div>
                    <label for="rating" class="block text-sm font-semibold text-gray-900 mb-2">
                        Rating (0-5)
                    </label>
                    <input
                        type="number"
                        step="0.1"
                        name="rating"
                        id="rating"
                        value="{{ old('rating', $product->rating) }}"
                        min="0"
                        max="5"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                        placeholder="0.0"
                    >
                    @error('rating')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reviews Count -->
                <div>
                    <label for="reviews_count" class="block text-sm font-semibold text-gray-900 mb-2">
                        Reviews Count
                    </label>
                    <input
                        type="number"
                        name="reviews_count"
                        id="reviews_count"
                        value="{{ old('reviews_count', $product->reviews_count) }}"
                        min="0"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                        placeholder="0"
                    >
                    @error('reviews_count')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sold Count -->
                <div>
                    <label for="sold_count" class="block text-sm font-semibold text-gray-900 mb-2">
                        Sold Count
                    </label>
                    <input
                        type="number"
                        name="sold_count"
                        id="sold_count"
                        value="{{ old('sold_count', $product->sold_count) }}"
                        min="0"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                        placeholder="0"
                    >
                    @error('sold_count')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hot Selling Rank -->
                <div>
                    <label for="hot_selling_rank" class="block text-sm font-semibold text-gray-900 mb-2">
                        Hot Selling Rank
                    </label>
                    <input
                        type="number"
                        name="hot_selling_rank"
                        id="hot_selling_rank"
                        value="{{ old('hot_selling_rank', $product->hot_selling_rank) }}"
                        min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                        placeholder="e.g., 2"
                    >
                    @error('hot_selling_rank')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Checkboxes -->
            <div class="mt-6 flex gap-6 items-center">
                <label class="flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_lower_priced"
                        value="1"
                        {{ old('is_lower_priced', $product->is_lower_priced) ? 'checked' : '' }}
                        class="w-4 h-4 text-[#ff0808] border-gray-300 rounded focus:ring-[#ff0808] focus:ring-2"
                    >
                    <span class="ml-2 text-sm font-medium text-gray-900">Lower Priced</span>
                </label>

                <label class="flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_admin_verified"
                        value="1"
                        {{ old('is_admin_verified', $product->is_admin_verified) ? 'checked' : '' }}
                        class="w-4 h-4 text-[#ff0808] border-gray-300 rounded focus:ring-[#ff0808] focus:ring-2"
                    >
                    <span class="ml-2 text-sm font-medium text-gray-900">Admin Verified</span>
                </label>
            </div>
        </div>

        <!-- Status -->
        <div class="border-b border-gray-200 pb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Status</h2>

            <div>
                <label for="status" class="block text-sm font-semibold text-gray-900 mb-2">
                    Status <span class="text-red-600">*</span>
                </label>
                <select
                    name="status"
                    id="status"
                    required
                    class="w-full md:w-64 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent text-gray-900"
                >
                    <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 justify-end items-center pt-6">
            <a href="{{ route('admin.product.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md">
                Update Product
            </button>
        </div>
    </form>
</div>
@endsection

