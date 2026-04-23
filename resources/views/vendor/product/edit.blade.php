@extends('layouts.home')

@section('page-content')
{{-- Quill CSS --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('vendor.product.index') }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
                <p class="mt-1 text-sm text-gray-500">Update product information</p>
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
    <form action="{{ route('vendor.product.update', $product) }}" method="POST" class="space-y-6" id="productForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" style="min-height: 900px !important; height: auto !important;">
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
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                        placeholder="e.g., Premium Wireless Headphones">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Slug (Auto-generated)
                    </label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $product->slug) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-mono text-sm"
                        placeholder="product-slug" readonly>
                    @error('slug')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Short Description -->
                <div>
                    <label for="short_description" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Short Description
                    </label>
                    <input type="text" name="short_description" id="short_description"
                        value="{{ old('short_description', $product->short_description) }}" maxlength="500"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                        placeholder="Brief description (max 500 characters)">
                    @error('short_description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>



                <!-- Category -->
                <div>
                    <label for="product_category_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Category
                    </label>
                    <select name="product_category_id" id="product_category_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
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
                    <label for="country_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Country <span class="text-red-600">*</span>
                    </label>
                    @if($vendorCountryId)
                        <select id="country_id_display" disabled
                            class="px-4 py-3 w-full text-gray-600 bg-gray-100 rounded-lg border border-gray-300 cursor-not-allowed">
                            @foreach ($countries as $country)
                                @if((old('country_id', $product->country_id ?? $vendorCountryId) == $country->id))
                                    <option value="{{ $country->id }}" selected>{{ $country->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <input type="hidden" name="country_id" id="country_id" value="{{ old('country_id', $product->country_id ?? $vendorCountryId) }}" required>
                        <p class="mt-1 text-xs text-gray-500">Country is automatically set based on your business profile.</p>
                    @else
                        <select name="country_id" id="country_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                            <option value="">Select a country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ old('country_id', $product->country_id) == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('country_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                                <!-- Overview — plain textarea, placed right after Short Description -->
                <div class="md:col-span-2">
                    <label for="overview" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Overview
                    </label>
                    <textarea name="overview" id="overview" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                        placeholder="Enter product overview...">{{ old('overview', $product->overview) }}</textarea>
                    @error('overview')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ═══════════════════════════════════════════════════
                     DESCRIPTION — Quill rich text (instance #1)
                ════════════════════════════════════════════════════ -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Description
                    </label>
                    {{-- Hidden textarea that holds the HTML value submitted with the form --}}
                    <textarea name="description" id="descriptionInput" class="hidden">{{ old('description', $product->description) }}</textarea>
                    {{-- Quill mount point --}}
                    <div id="descriptionEditor" class="quill-editor-wrap border border-gray-300 rounded-lg overflow-hidden"></div>
                    @error('description')
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
                @if($product->variations && $product->variations->count() > 0)
                    @foreach($product->variations as $index => $variation)
                        <div class="p-5 bg-gray-50 rounded-xl border border-gray-200 existing-variation">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                                <div>
                                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type *</label>
                                    <input type="text" name="variations[{{ $index }}][variation_type]" required
                                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500"
                                        value="{{ $variation->variation_type }}">
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name *</label>
                                    <input type="text" name="variations[{{ $index }}][variation_name]" required
                                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500"
                                        value="{{ $variation->variation_name }}">
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Value</label>
                                    <input type="text" name="variations[{{ $index }}][variation_value]"
                                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500"
                                        value="{{ $variation->variation_value }}">
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sort Order</label>
                                    <input type="number" name="variations[{{ $index }}][sort_order]" value="{{ $variation->sort_order }}"
                                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500" min="0">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" onclick="this.closest('.existing-variation').remove()"
                                        class="px-3 py-2 w-full text-sm font-medium text-red-600 bg-white rounded-lg border border-red-300 transition-colors hover:bg-red-50">
                                        <i class="mr-1 fas fa-trash"></i>Remove
                                    </button>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="flex gap-2 items-center text-sm">
                                    <input type="checkbox" name="variations[{{ $index }}][is_active]" value="1" {{ $variation->is_active ? 'checked' : '' }} class="rounded">
                                    <span class="text-xs text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                @endif
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
                @php
                    $specs = is_array($product->specifications) ? $product->specifications : (is_string($product->specifications) ? json_decode($product->specifications, true) : []);
                    $specIndex = 0;
                @endphp
                @if($specs && count($specs) > 0)
                    @foreach($specs as $key => $value)
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 existing-spec">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Key *</label>
                                    <input type="text" name="spec_key[{{ $specIndex }}]" required
                                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500" value="{{ $key }}">
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Value *</label>
                                    <input type="text" name="spec_value[{{ $specIndex }}]" required
                                        class="px-3 py-2 w-full text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500" value="{{ $value }}">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" onclick="this.closest('.existing-spec').remove()"
                                        class="px-3 py-2 w-full text-sm font-medium text-red-600 bg-white rounded-lg border border-red-300 transition-colors hover:bg-red-50">
                                        <i class="mr-1 fas fa-trash"></i>Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                        @php $specIndex++; @endphp
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Product Images -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                <i class="fas fa-images text-pink-600"></i>
                Product Images
                <span id="imgCounter" class="ml-auto text-sm font-normal text-gray-500">
                    {{ $product->images ? $product->images->count() : 0 }} / 4
                </span>
            </h2>
            <p class="text-xs text-gray-500 mb-5">Up to 4 images total (existing + new). JPG, PNG, WebP — max 5 MB each.</p>

            <!-- Existing Images -->
            @if($product->images && $product->images->count() > 0)
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Existing Images</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="existingImagesContainer">
                        @foreach($product->images->sortBy('sort_order') as $index => $image)
                            <div class="relative rounded-xl overflow-hidden border border-gray-200 group existing-image-item" data-image-id="{{ $image->id }}">
                                <img src="{{ $image->image_url }}" alt="{{ $image->alt_text ?? $product->name }}" class="w-full h-48 object-cover">
                                @if($image->is_primary)
                                    <span class="absolute top-2 left-2 px-2.5 py-1 bg-blue-600 text-white text-xs font-bold rounded-lg shadow-lg">
                                        <i class="fas fa-star mr-1"></i> Primary
                                    </span>
                                @endif
                                <button type="button" onclick="removeExistingImage({{ $image->id }})"
                                    class="absolute top-2 right-2 bg-red-600 text-white w-7 h-7 rounded-lg hover:bg-red-700 shadow-lg flex items-center justify-center">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                                    <label class="flex items-center gap-2 text-sm mb-2">
                                        <input type="checkbox" name="existing_image_data[{{ $image->id }}][is_primary]" value="1"
                                            {{ $image->is_primary ? 'checked' : '' }}
                                            class="existing-primary-checkbox rounded"
                                            onchange="updatePrimaryStatus({{ $image->id }})">
                                        <span class="text-xs text-white font-medium">Primary</span>
                                    </label>
                                    <input type="text" name="existing_image_data[{{ $image->id }}][alt_text]"
                                        value="{{ $image->alt_text ?? $product->name }}"
                                        placeholder="Alt text"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded mb-1">
                                    <input type="number" name="existing_image_data[{{ $image->id }}][sort_order]"
                                        value="{{ $image->sort_order }}"
                                        placeholder="Order"
                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded">
                                    <input type="hidden" name="existing_images[]" value="{{ $image->id }}" class="existing-image-id">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- New Image Upload -->
            <div id="imageUploadContainer" class="space-y-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                    Upload New Images
                </label>

                <div id="imgDropZone"
                     class="flex flex-col items-center justify-center w-full min-h-[110px] border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#ff0808] hover:bg-red-50 transition-colors"
                     onclick="document.getElementById('images').click()"
                     ondragover="event.preventDefault(); this.classList.add('border-[#ff0808]','bg-red-50')"
                     ondragleave="this.classList.remove('border-[#ff0808]','bg-red-50')"
                     ondrop="handleImgDrop(event)">
                    <i class="fas fa-plus-circle text-gray-400 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-500 font-medium">Click or drag new images here</p>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                           onchange="addImages(this.files)">
                </div>

                <p id="imgLimitWarn" class="hidden mt-2 text-xs text-red-500 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i> Maximum 4 images total. Extra files were ignored.
                </p>

                @error('images.*')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div id="imagePreviewContainer" class="grid grid-cols-2 md:grid-cols-4 gap-4 hidden"></div>
            </div>
        </div>

        <!-- Product Video -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                <i class="fas fa-video text-blue-600"></i>
                Product Video
            </h2>
            <p class="text-xs text-gray-500 mb-4">Optional — MP4, WebM, MOV — max 50 MB</p>

            @if($product->video_url)
                <div id="currentVideoSection" class="mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Current Video</p>
                    <div class="rounded-xl overflow-hidden border border-gray-200 bg-black">
                        <video controls class="w-full max-h-56 object-contain bg-black" preload="metadata">
                            <source src="{{ Storage::url($product->video_url) }}" type="video/mp4">
                            <source src="{{ Storage::url($product->video_url) }}" type="video/webm">
                        </video>
                    </div>
                    <div class="mt-3 flex items-center gap-3">
                        <button type="button" id="removeVideoBtn" onclick="softRemoveVideo()"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                            <i class="fas fa-trash"></i> Remove video
                        </button>
                        <span id="removeVideoUndo" class="hidden text-xs text-gray-500">
                            Video will be removed on save.
                            <button type="button" onclick="undoRemoveVideo()" class="text-blue-600 underline ml-1">Undo</button>
                        </span>
                    </div>
                    <input type="hidden" name="remove_video" id="removeVideoFlag" value="0">
                </div>
            @endif

            <div id="videoDropZone"
                 class="{{ $product->video_url ? 'mt-2' : '' }} flex flex-col items-center justify-center w-full min-h-[100px] border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors"
                 onclick="document.getElementById('videoInput').click()"
                 ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50')"
                 ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
                 ondrop="handleVideoDrop(event)">
                <i class="fas fa-film text-gray-400 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-600">
                    {{ $product->video_url ? 'Upload replacement video' : 'Drag & drop or click to upload video' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">MP4, WebM, MOV — max 50 MB</p>
                <input type="file" id="videoInput" name="video" accept="video/mp4,video/webm,video/quicktime" class="hidden"
                       onchange="previewVideo(this)">
            </div>

            <div id="videoPreview" class="hidden mt-4 rounded-xl overflow-hidden border border-gray-200 bg-black relative">
                <video id="videoEl" controls class="w-full max-h-56 object-contain bg-black"></video>
                <button type="button" onclick="removeNewVideo()"
                    class="absolute top-2 right-2 w-7 h-7 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700 shadow">
                    <i class="fas fa-times"></i>
                </button>
                <p id="videoName" class="px-3 py-2 text-xs text-gray-400 bg-gray-50 border-t border-gray-200"></p>
            </div>

            @error('video')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status & Pricing Options -->
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
                    <select name="status" id="status" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                        <option value="draft"    {{ old('status', $product->status) == 'draft'    ? 'selected' : '' }}>Draft</option>
                        <option value="active"   {{ old('status', $product->status) == 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                    <input type="number" name="min_order_quantity" id="min_order_quantity"
                        value="{{ old('min_order_quantity', $product->min_order_quantity ?? 1) }}" min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                        placeholder="1">
                    @error('min_order_quantity')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Negotiable Price -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg border-2 border-yellow-200 cursor-pointer hover:border-yellow-300 transition-colors">
                        <input type="checkbox" name="is_negotiable" value="1"
                            {{ old('is_negotiable', $product->is_negotiable) ? 'checked' : '' }}
                            class="w-5 h-5 text-yellow-600 rounded focus:ring-2 focus:ring-yellow-500">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-handshake text-yellow-600"></i>
                                <span class="text-sm font-bold text-gray-900">Price is Negotiable</span>
                            </div>
                            <p class="text-xs text-gray-600">Check this if buyers can negotiate the price with you.</p>
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
            <a href="{{ route('vendor.product.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-colors font-semibold shadow-md">
                <i class="fas fa-save"></i> Update Product
            </button>
        </div>
    </form>
</div>

{{-- Load Quill BEFORE the inline scripts below --}}
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════════════════
//  TWO SEPARATE QUILL INSTANCES — no shared state, no conflicts
// ═══════════════════════════════════════════════════════════════════════

// Shared toolbar config (same look, different instances)
const TOOLBAR_OPTIONS = [
    ['bold', 'italic', 'underline', 'strike'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    [{ header: [1, 2, 3, false] }],
    ['link', 'blockquote'],
    ['clean']
];

// ── Instance #1 : Description ──────────────────────────────────────────
const descriptionQuill = new Quill('#descriptionEditor', {
    theme: 'snow',
    placeholder: 'Enter product description…',
    modules: { toolbar: TOOLBAR_OPTIONS }
});

const existingDesc = document.getElementById('descriptionInput').value.trim();
if (existingDesc) descriptionQuill.root.innerHTML = existingDesc;


// ── Slug auto-generate ─────────────────────────────────────────────────
document.getElementById('name').addEventListener('input', function () {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value) {
        slugInput.value = this.value.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
});


// ── Images ─────────────────────────────────────────────────────────────
const MAX_IMAGES = 4;
let newImageFiles      = [];
let newImageCount      = 0;
let existingImageCount = {{ $product->images ? $product->images->count() : 0 }};

function addImages(files) {
    const warn      = document.getElementById('imgLimitWarn');
    warn.classList.add('hidden');
    const available = MAX_IMAGES - existingImageCount - newImageFiles.length;
    const incoming  = Array.from(files);
    if (incoming.length > available) warn.classList.remove('hidden');
    incoming.slice(0, Math.max(0, available)).forEach(f => newImageFiles.push(f));
    rebuildImageInput();
    renderNewPreviews();
    updateImgCounter();
    updateDropZone();
}

function rebuildImageInput() {
    const dt = new DataTransfer();
    newImageFiles.forEach(f => dt.items.add(f));
    document.getElementById('images').files = dt.files;
}

function renderNewPreviews() {
    const container = document.getElementById('imagePreviewContainer');
    container.innerHTML = '';
    container.classList.toggle('hidden', newImageFiles.length === 0);
    newImageCount = 0;

    newImageFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative border border-gray-300 rounded-xl overflow-hidden';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="w-full h-48 object-cover">
                <button type="button" onclick="removeNewImage(${index})"
                    class="absolute top-2 right-2 w-7 h-7 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700 shadow">
                    <i class="fas fa-times"></i>
                </button>
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                    <label class="flex items-center gap-2 text-sm mb-2">
                        <input type="checkbox" name="image_is_primary[${newImageCount}]" value="1"
                            class="new-primary-checkbox rounded" onchange="updatePrimaryStatusNew(this)">
                        <span class="text-xs text-white font-medium">Primary Image</span>
                    </label>
                    <input type="hidden" name="image_sort_order[${newImageCount}]" value="${existingImageCount + newImageCount}">
                    <input type="text" name="image_alt_text[${newImageCount}]" placeholder="Alt text (optional)"
                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded"
                        value="${file.name.replace(/\.[^/.]+$/, '')}">
                </div>
            `;
            container.appendChild(div);
            newImageCount++;
        };
        reader.readAsDataURL(file);
    });
}

function removeNewImage(index) {
    newImageFiles.splice(index, 1);
    document.getElementById('imgLimitWarn').classList.add('hidden');
    rebuildImageInput();
    renderNewPreviews();
    updateImgCounter();
    updateDropZone();
}

function removeExistingImage(imageId) {
    const item = document.querySelector(`[data-image-id="${imageId}"]`);
    if (item) {
        item.querySelector('.existing-image-id')?.remove();
        item.remove();
        existingImageCount--;
        updateImgCounter();
        updateDropZone();
    }
}

function updateImgCounter() {
    const el = document.getElementById('imgCounter');
    if (el) el.textContent = `${existingImageCount + newImageFiles.length} / 4`;
}

function updateDropZone() {
    const dz = document.getElementById('imgDropZone');
    if (existingImageCount + newImageFiles.length >= MAX_IMAGES) {
        dz.classList.add('opacity-50', 'pointer-events-none');
    } else {
        dz.classList.remove('opacity-50', 'pointer-events-none');
    }
}

function handleImgDrop(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('border-[#ff0808]', 'bg-red-50');
    addImages(event.dataTransfer.files);
}

function updatePrimaryStatus(imageId) {
    document.querySelectorAll('.existing-primary-checkbox').forEach(cb => {
        if (cb.closest('[data-image-id]').dataset.imageId != imageId) cb.checked = false;
    });
    document.querySelectorAll('.new-primary-checkbox').forEach(cb => cb.checked = false);
}

function updatePrimaryStatusNew(checkbox) {
    if (checkbox.checked) {
        document.querySelectorAll('.existing-primary-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.new-primary-checkbox').forEach(cb => { if (cb !== checkbox) cb.checked = false; });
    }
}


// ── Video — existing ───────────────────────────────────────────────────
function softRemoveVideo() {
    document.getElementById('removeVideoFlag').value = '1';
    document.getElementById('removeVideoBtn').classList.add('hidden');
    document.getElementById('removeVideoUndo').classList.remove('hidden');
}

function undoRemoveVideo() {
    document.getElementById('removeVideoFlag').value = '0';
    document.getElementById('removeVideoBtn').classList.remove('hidden');
    document.getElementById('removeVideoUndo').classList.add('hidden');
}


// ── Video — new upload ─────────────────────────────────────────────────
function previewVideo(input) {
    const file = input.files[0];
    if (!file) return;
    if (file.size > 50 * 1024 * 1024) {
        alert('Video must be under 50 MB.');
        input.value = '';
        return;
    }
    document.getElementById('videoEl').src = URL.createObjectURL(file);
    document.getElementById('videoName').textContent =
        `${file.name}  (${(file.size / 1024 / 1024).toFixed(1)} MB)`;
    document.getElementById('videoPreview').classList.remove('hidden');
    document.getElementById('videoDropZone').classList.add('hidden');
}

function removeNewVideo() {
    const el = document.getElementById('videoEl');
    URL.revokeObjectURL(el.src);
    el.src = '';
    document.getElementById('videoInput').value = '';
    document.getElementById('videoPreview').classList.add('hidden');
    document.getElementById('videoDropZone').classList.remove('hidden');
}

function handleVideoDrop(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('border-blue-400', 'bg-blue-50');
    const file = event.dataTransfer.files[0];
    if (!file) return;
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('videoInput').files = dt.files;
    previewVideo(document.getElementById('videoInput'));
}


// ── Form submit — flush both Quill editors into their hidden textareas ─
document.getElementById('productForm').addEventListener('submit', function () {
    // Flush Description
    document.getElementById('descriptionInput').value = descriptionQuill.root.innerHTML;

    // Build specifications JSON
    const specKeys = document.querySelectorAll('input[name^="spec_key"]');
    const specifications = {};
    specKeys.forEach((keyInput, index) => {
        const key = keyInput.value.trim();
        const valueInput = document.querySelector(`input[name="spec_value[${index}]"]`);
        const value = valueInput ? valueInput.value.trim() : '';
        if (key && value) specifications[key] = value;
    });
    const specInput   = document.createElement('input');
    specInput.type    = 'hidden';
    specInput.name    = 'specifications';
    specInput.value   = JSON.stringify(specifications);
    this.appendChild(specInput);
});


// ── Variations ─────────────────────────────────────────────────────────
let variationCount = {{ $product->variations ? $product->variations->count() : 0 }};

function addVariation() {
    const container = document.getElementById('variationsContainer');
    const div = document.createElement('div');
    div.className = 'p-5 bg-gray-50 rounded-xl border border-gray-200';
    div.innerHTML = `
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
    container.appendChild(div);
    variationCount++;
}


// ── Specifications ─────────────────────────────────────────────────────
@php
    $specs = is_array($product->specifications) ? $product->specifications : (is_string($product->specifications) ? json_decode($product->specifications, true) : []);
@endphp
let specCount = {{ $specs ? count($specs) : 0 }};

function addSpecification() {
    const container = document.getElementById('specificationsContainer');
    const div = document.createElement('div');
    div.className = 'p-4 bg-gray-50 rounded-xl border border-gray-200';
    div.innerHTML = `
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
    container.appendChild(div);
    specCount++;
}
</script>

<style>
/* ── Shared Quill editor styles ──────────────────────────────────────── */
.quill-editor-wrap .ql-toolbar {
    border-top-left-radius:    0.5rem;
    border-top-right-radius:   0.5rem;
    border-color:              #d1d5db;
    background:                #f9fafb;
}
.quill-editor-wrap .ql-container {
    min-height:                160px;
    font-size:                 0.875rem;
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius:0.5rem;
    border-color:              #d1d5db;
}
</style>
@endpush
@endsection
