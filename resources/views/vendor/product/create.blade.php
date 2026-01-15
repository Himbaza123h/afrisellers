@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('vendor.product.index') }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
                <p class="mt-1 text-sm text-gray-500">Create a new product entry</p>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</p>
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('vendor.product.store') }}" method="POST" class="space-y-6" id="productForm" enctype="multipart/form-data">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                Basic Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Product Name <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                        placeholder="e.g., Premium Wireless Headphones"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Slug (Auto-generated)
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        value="{{ old('slug') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-mono text-sm"
                        placeholder="product-slug"
                        readonly
                    >
                    @error('slug')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Short Description -->
                <div>
                    <label for="short_description" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Short Description
                    </label>
                    <input
                        type="text"
                        name="short_description"
                        id="short_description"
                        value="{{ old('short_description') }}"
                        maxlength="500"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                        placeholder="Brief description (max 500 characters)"
                    >
                    @error('short_description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="product_category_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Category
                    </label>
                    <select
                        name="product_category_id"
                        id="product_category_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                    >
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
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
                    <label for="country_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Country <span class="text-red-600">*</span>
                    </label>
                    @if($vendorCountryId)
                        <select id="country_id_display" disabled
                            class="px-4 py-3 w-full text-gray-600 bg-gray-100 rounded-lg border border-gray-300 cursor-not-allowed">
                            @foreach($countries as $country)
                                @if((old('country_id', $vendorCountryId) == $country->id))
                                    <option value="{{ $country->id }}" selected>{{ $country->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <input type="hidden" name="country_id" id="country_id" value="{{ old('country_id', $vendorCountryId) }}" required>
                        <p class="mt-1 text-xs text-gray-500">Country is automatically set based on your business profile.</p>
                    @else
                        <select name="country_id" id="country_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                            <option value="">Select a country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('country_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Description
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                        placeholder="Enter product description..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Overview -->
                <div class="md:col-span-2">
                    <label for="overview" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Overview
                    </label>
                    <textarea
                        name="overview"
                        id="overview"
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                        placeholder="Enter product overview..."
                    >{{ old('overview') }}</textarea>
                    @error('overview')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Product Variations -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-sitemap text-purple-600"></i>
                Product Variations
            </h2>
            <p class="mb-4 text-xs text-gray-500">Add variations like color, size, connectivity type, etc.</p>

            <div class="flex justify-end mb-4">
                <button type="button" onclick="addVariation()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#ff0808] text-white text-sm rounded font-medium shadow-sm hover:shadow-md hover:bg-[#e60707] transition-all duration-200 active:scale-95">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Add Variation</span>
                </button>
            </div>

            <div id="variationsContainer" class="space-y-4">
                <!-- Variations will be added here dynamically -->
            </div>
        </div>

        <!-- Specifications -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-list-ul text-indigo-600"></i>
                Specifications
            </h2>
            <p class="mb-4 text-xs text-gray-500">Add product specifications as key-value pairs (e.g., Weight: 500g, Dimensions: 10x5x2cm)</p>

            <div class="flex justify-end mb-4">
                <button type="button" onclick="addSpecification()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#ff0808] text-white text-sm rounded font-medium shadow-sm hover:shadow-md hover:bg-[#e60707] transition-all duration-200 active:scale-95">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Add Specification</span>
                </button>
            </div>

            <div id="specificationsContainer" class="space-y-4">
                <!-- Specifications will be added here dynamically -->
            </div>
        </div>

        <!-- Product Images -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-images text-pink-600"></i>
                Product Images
            </h2>

            <div id="imageUploadContainer" class="space-y-4">
                <div>
                    <label for="images" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Upload Images
                    </label>
                    <input
                        type="file"
                        name="images[]"
                        id="images"
                        multiple
                        accept="image/*"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                        onchange="previewImages(this)"
                    >
                    <p class="mt-1 text-xs text-gray-500">You can select multiple images. Supported formats: JPG, PNG, GIF</p>
                    @error('images.*')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Preview Container -->
                <div id="imagePreviewContainer" class="grid grid-cols-2 md:grid-cols-4 gap-4 hidden">
                    <!-- Preview items will be added here dynamically -->
                </div>
            </div>
        </div>

        <!-- Status -->
        <!-- Status -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-toggle-on text-gray-600"></i>
                Status & Pricing Options
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select
                        name="status"
                        id="status"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                    >
                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Min Order Quantity -->
                <div>
                    <label for="min_order_quantity" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Min Order Quantity
                    </label>
                    <input
                        type="number"
                        name="min_order_quantity"
                        id="min_order_quantity"
                        value="{{ old('min_order_quantity', 1) }}"
                        min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                        placeholder="1"
                    >
                    @error('min_order_quantity')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Negotiable Price Checkbox -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg border-2 border-yellow-200 cursor-pointer hover:border-yellow-300 transition-colors">
                        <input type="checkbox" name="is_negotiable" value="1"
                            {{ old('is_negotiable') ? 'checked' : '' }}
                            class="w-5 h-5 text-yellow-600 rounded focus:ring-2 focus:ring-yellow-500">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-handshake text-yellow-600"></i>
                                <span class="text-sm font-bold text-gray-900">Price is Negotiable</span>
                            </div>
                            <p class="text-xs text-gray-600">Check this if buyers can negotiate the price with you. You can still set base prices, but buyers will know they can discuss pricing.</p>
                        </div>
                    </label>
                    @error('is_negotiable')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 justify-end items-center pt-6">
            <a href="{{ route('vendor.product.index') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-colors font-semibold shadow-md">
                <i class="fas fa-save"></i>
                <span>Create Product</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const slugInput = document.getElementById('slug');
        if (!slugInput.value) {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        }
    });

    // Image preview functionality
    let imageCount = 0;

    function previewImages(input) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        container.classList.remove('hidden');

        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'relative border border-gray-300 rounded-xl overflow-hidden';
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Preview" class="w-full h-48 object-cover">
                            <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                ${index + 1}
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                                <label class="flex items-center gap-2 text-sm mb-2">
                                    <input type="checkbox" name="image_is_primary[${imageCount}]" value="1" ${index === 0 ? 'checked' : ''} class="rounded">
                                    <span class="text-xs text-white font-medium">Primary Image</span>
                                </label>
                                <input type="hidden" name="image_sort_order[${imageCount}]" value="${imageCount}">
                                <input type="text" name="image_alt_text[${imageCount}]" placeholder="Alt text (optional)"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded"
                                    value="${file.name.replace(/\.[^/.]+$/, "")}">
                            </div>
                        `;
                        container.appendChild(previewDiv);
                        imageCount++;
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            container.classList.add('hidden');
        }
    }

    // Variations Management
    let variationCount = 0;

    function addVariation() {
        const container = document.getElementById('variationsContainer');

        const variationDiv = document.createElement('div');
        variationDiv.className = 'p-5 bg-gray-50 rounded-xl border border-gray-200';
        variationDiv.innerHTML = `
            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type *</label>
                    <input type="text" name="variations[${variationCount}][variation_type]" required
                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500" placeholder="e.g., Color, Size">
                </div>
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name *</label>
                    <input type="text" name="variations[${variationCount}][variation_name]" required
                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500" placeholder="e.g., Red, Large">
                </div>
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Value</label>
                    <input type="text" name="variations[${variationCount}][variation_value]"
                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500" placeholder="Optional slug">
                </div>
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sort Order</label>
                    <input type="number" name="variations[${variationCount}][sort_order]" value="${variationCount}"
                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500" min="0">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.p-5').remove()"
                        class="px-3 py-2 w-full text-sm font-medium text-red-600 bg-white rounded-lg border border-red-300 transition-colors hover:bg-red-50">
                        <i class="mr-1 fas fa-trash"></i>Remove
                    </button>
                </div>
            </div>
            <div class="mt-3">
                <label class="flex gap-2 items-center text-sm">
                    <input type="checkbox" name="variations[${variationCount}][is_active]" value="1" checked class="rounded">
                    <span class="text-xs text-gray-700">Active</span>
                </label>
            </div>
        `;
        container.appendChild(variationDiv);
        variationCount++;
    }

    // Specifications Management
    let specCount = 0;

    function addSpecification() {
        const container = document.getElementById('specificationsContainer');

        const specDiv = document.createElement('div');
        specDiv.className = 'p-4 bg-gray-50 rounded-xl border border-gray-200';
        specDiv.innerHTML = `
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Key *</label>
                    <input type="text" name="spec_key[${specCount}]" required
                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500" placeholder="e.g., Weight">
                </div>
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Value *</label>
                    <input type="text" name="spec_value[${specCount}]" required
                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500" placeholder="e.g., 500g">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.p-4').remove()"
                        class="px-3 py-2 w-full text-sm font-medium text-red-600 bg-white rounded-lg border border-red-300 transition-colors hover:bg-red-50">
                        <i class="mr-1 fas fa-trash"></i>Remove
                    </button>
                </div>
            </div>
        `;
        container.appendChild(specDiv);
        specCount++;
    }

    // Convert specifications to JSON before form submission
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const specKeys = document.querySelectorAll('input[name^="spec_key"]');
        const specifications = {};

        specKeys.forEach((keyInput, index) => {
            const key = keyInput.value.trim();
            const valueInput = document.querySelector(`input[name="spec_value[${index}]"]`);
            const value = valueInput ? valueInput.value.trim() : '';

            if (key && value) {
                specifications[key] = value;
            }
        });

        const specInput = document.createElement('input');
        specInput.type = 'hidden';
        specInput.name = 'specifications';
        specInput.value = JSON.stringify(specifications);
        this.appendChild(specInput);
    });
</script>
@endpush
@endsection
