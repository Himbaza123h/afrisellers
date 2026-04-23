@extends('layouts.app')

@section('title', $businessProfile->business_name . ' - Products')

@section('content')
    <div class="py-8 min-h-screen bg-gray-50 to-gray-100">
        <div class="container px-4 mx-auto max-w-7xl">
            <!-- Breadcrumb and Business Header -->
            <div class="mb-6">
                <!-- Breadcrumb -->
                <nav class="flex text-sm mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-[#ff0808] transition-colors duration-200 font-medium">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('country.business-profiles', $businessProfile->country_id) }}" class="text-gray-600 hover:text-[#ff0808] transition-colors duration-200 font-medium">
                                    {{ $businessProfile->country->name }}
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-900 font-semibold">{{ $businessProfile->business_name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Business Profile Card -->
                <div class="bg-white rounded border border-gray-200 shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h1 class="text-2xl font-bold text-gray-900">{{ $businessProfile->business_name }}</h1>
                                <span class="bg-green-600 text-white text-xs font-semibold px-2.5 py-1 rounded flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="font-medium">{{ $businessProfile->city }}, {{ $businessProfile->country->name }}</span>
                                </div>
                                @if($businessProfile->phone)
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span class="font-medium">{{ $businessProfile->phone_code }} {{ $businessProfile->phone }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                            <div class="text-center">
                                <div class="text-xl font-bold text-[#ff0808]">{{ $products->total() }}</div>
                                <div class="text-xs text-gray-500 mt-1">Products</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-purple-600">{{ $categories->count() }}</div>
                                <div class="text-xs text-gray-500 mt-1">Categories</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-green-600">100%</div>
                                <div class="text-xs text-gray-500 mt-1">Verified</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-blue-600 truncate">{{ Str::limit($businessProfile->country->name, 8) }}</div>
                                <div class="text-xs text-gray-500 mt-1">Location</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden">
                    <div class="flex border-b border-gray-200">
                        <button onclick="switchTab('products')"
                                id="tab-products"
                                class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#ff0808] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent relative">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span>Products</span>
                                <span class="ml-1 text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">{{ $products->total() }}</span>
                            </div>
                        </button>
                        <button onclick="switchTab('videos')"
                                id="tab-videos"
                                class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#ff0808] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Videos</span>
                            </div>
                        </button>
                        <button onclick="switchTab('articles')"
                                id="tab-articles"
                                class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-gray-600 hover:text-[#ff0808] hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                                <span>Articles</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="relative">
                <!-- Products Tab Content -->
                <div id="content-products" class="tab-content">
                    <div class="flex flex-col lg:flex-row gap-6">
                        <!-- Sidebar Filters -->
                        <aside class="lg:w-80 flex-shrink-0">
                            <!-- Filter Panel -->
                            <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden sticky top-4">
                                <!-- Filter Header -->
                                <div class="bg-gradient-to-r from-[#ff0808] to-[#dd0606] text-white p-4">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-bold text-base flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                            </svg>
                                            Filters
                                        </h3>
                                        @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'min_moq', 'sort']))
                                            <a href="{{ route('business-profile.products', $businessProfile->id) }}"
                                               class="text-xs bg-white/20 hover:bg-white/30 px-2.5 py-1 rounded transition-all duration-200">
                                                Clear All
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('business-profile.products', $businessProfile->id) }}" class="p-4 space-y-4">
                                    <!-- Search -->
                                    <div>
                                        <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">Search Products</label>
                                        <div class="relative">
                                            <input type="text"
                                                   id="search"
                                                   name="search"
                                                   value="{{ request('search') }}"
                                                   placeholder="Search by name..."
                                                   class="w-full pl-10 pr-3 py-2.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] transition-all duration-200">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                                        <select id="category"
                                                name="category"
                                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] transition-all duration-200">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Sort By -->
                                    <div>
                                        <label for="sort" class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                                        <select id="sort"
                                                name="sort"
                                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] transition-all duration-200">
                                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                                        </select>
                                    </div>

                                    <!-- Advanced Filters Toggle -->
                                    <div class="border-t border-gray-200 pt-4">
                                        <button type="button"
                                                onclick="toggleAdvancedFilters()"
                                                class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 hover:text-[#ff0808] transition-colors duration-200">
                                            <span>Advanced Filters</span>
                                            <svg class="w-4 h-4 transform transition-transform duration-200" id="chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Advanced Filters -->
                                    <div id="advancedFilters" class="space-y-4 overflow-hidden transition-all duration-300 {{ request()->hasAny(['min_price', 'max_price', 'min_moq']) ? 'max-h-96' : 'max-h-0' }}">
                                        <!-- Price Range -->
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Price Range</label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <input type="number"
                                                       name="min_price"
                                                       value="{{ request('min_price') }}"
                                                       placeholder="Min"
                                                       min="0"
                                                       step="0.01"
                                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] transition-all duration-200">
                                                <input type="number"
                                                       name="max_price"
                                                       value="{{ request('max_price') }}"
                                                       placeholder="Max"
                                                       min="0"
                                                       step="0.01"
                                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] transition-all duration-200">
                                            </div>
                                        </div>

                                        <!-- Min Order Quantity -->
                                        <div>
                                            <label for="min_moq" class="block text-sm font-semibold text-gray-700 mb-2">Min Order Qty</label>
                                            <input type="number"
                                                   id="min_moq"
                                                   name="min_moq"
                                                   value="{{ request('min_moq') }}"
                                                   placeholder="Any quantity"
                                                   min="1"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] transition-all duration-200">
                                        </div>
                                    </div>

                                    <!-- Apply Button -->
                                    <button type="submit"
                                            class="w-full bg-[#ff0808] hover:bg-[#dd0606] text-white font-semibold py-2.5 px-4 rounded transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        Apply Filters
                                    </button>
                                </form>

                                <!-- Active Filters -->
                                @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'min_moq']))
                                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                                        <p class="text-xs font-semibold text-gray-700 mb-2">Active Filters:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @if(request('search'))
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                                    {{ Str::limit(request('search'), 15) }}
                                                    <a href="{{ route('business-profile.products', array_merge([$businessProfile->id], request()->except('search'))) }}" class="hover:text-blue-900 font-bold">×</a>
                                                </span>
                                            @endif
                                            @if(request('category'))
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs bg-purple-100 text-purple-800 rounded">
                                                    {{ $categories->find(request('category'))->name ?? 'Category' }}
                                                    <a href="{{ route('business-profile.products', array_merge([$businessProfile->id], request()->except('category'))) }}" class="hover:text-purple-900 font-bold">×</a>
                                                </span>
                                            @endif
                                            @if(request('min_price') || request('max_price'))
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs bg-green-100 text-green-800 rounded">
                                                    ${{ request('min_price', 0) }}-{{ request('max_price', '∞') }}
                                                    <a href="{{ route('business-profile.products', array_merge([$businessProfile->id], request()->except('min_price', 'max_price'))) }}" class="hover:text-green-900 font-bold">×</a>
                                                </span>
                                            @endif
                                            @if(request('min_moq'))
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs bg-orange-100 text-orange-800 rounded">
                                                    MOQ: {{ request('min_moq') }}+
                                                    <a href="{{ route('business-profile.products', array_merge([$businessProfile->id], request()->except('min_moq'))) }}" class="hover:text-orange-900 font-bold">×</a>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </aside>

                        <!-- Products Grid -->
                        <div class="flex-1">
                            @if($products->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
                                    @foreach($products as $product)
                                        @php
                                            $featuredImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                            $firstPriceTier = $product->prices->first();
                                        @endphp
                                        <div class="group bg-white rounded border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:border-[#ff0808] hover:-translate-y-1" data-product-id="{{ $product->id }}">
                                            <!-- Product Image -->
                                            <a href="{{ route('products.show', $product->slug) }}" class="block relative h-48 overflow-hidden bg-gray-50">
                                                @if($featuredImage)
                                                    <img src="{{ $featuredImage->image_url }}"
                                                         alt="{{ $product->name }}"
                                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                                         loading="lazy">
                                                @else
                                                    <div class="w-full h-full bg-gray-100 to-gray-200 flex items-center justify-center">
                                                        <span class="text-6xl">📦</span>
                                                    </div>
                                                @endif

                                                <!-- Verified Badge -->
                                                @if($product->is_admin_verified)
                                                    <span class="absolute top-2 right-2 bg-green-600 text-white text-xs font-semibold px-2 py-1 rounded flex items-center gap-1 shadow-sm">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Verified
                                                    </span>
                                                @endif
                                            </a>

                                            <!-- Content -->
                                            <div class="p-4">
                                                <!-- Product Name -->
                                                <a href="{{ route('products.show', $product->slug) }}">
                                                    <h3 class="text-base font-bold text-gray-900 mb-2 group-hover:text-[#ff0808] transition-colors duration-200 line-clamp-2 min-h-[3rem]">
                                                        {{ $product->name }}
                                                    </h3>
                                                </a>

                                                <!-- Short Description -->
                                                @if($product->short_description)
                                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                                        {{ $product->short_description }}
                                                    </p>
                                                @endif

                                                <!-- Price -->
                                                @if($firstPriceTier)
                                                    <div class="mb-3">
                                                        <span class="text-xl font-bold text-[#ff0808]">
                                                            {{ number_format($firstPriceTier->price, 0) }} {{ $firstPriceTier->currency }}
                                                        </span>
                                                        @if($product->prices->count() > 1)
                                                            <span class="text-xs text-gray-500 ml-1">({{ $product->prices->count() }} tiers)</span>
                                                        @endif
                                                    </div>
                                                @endif

                                                <!-- Stats Row -->
                                                <div class="flex items-center justify-between text-sm text-gray-600 pt-3 border-t border-gray-100">
                                                    <span class="font-medium">MOQ: {{ $product->min_order_quantity }}</span>
                                                    @if($product->country)
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                            {{ $product->country->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                @if($products->hasPages())
                                    <div class="mt-8">
                                        {{ $products->links() }}
                                    </div>
                                @endif
                            @else
                                <!-- Empty State -->
                                <div class="bg-white rounded border border-gray-200 p-16 text-center">
                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No Products Found</h3>
                                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                                        {{ $businessProfile->business_name }} hasn't added any products matching your filters yet.
                                    </p>
                                    <a href="{{ route('business-profile.products', $businessProfile->id) }}"
                                       class="inline-flex items-center gap-2 bg-[#ff0808] text-white px-6 py-3 rounded font-semibold hover:bg-[#dd0606] transition-all duration-200 shadow-sm hover:shadow-md">
                                        Clear All Filters
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Videos Tab Content -->
                <div id="content-videos" class="tab-content hidden">
                    <div class="bg-white rounded border border-gray-200 p-16 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 to-pink-100 rounded-full mb-6">
                            <svg class="w-10 h-10 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Videos Coming Soon</h3>
                        <p class="text-gray-600 max-w-md mx-auto">
                            {{ $businessProfile->business_name }} will be adding product videos and company introductions soon.
                        </p>
                    </div>
                </div>

                <!-- Articles Tab Content -->
                <div id="content-articles" class="tab-content hidden">
                    <div class="bg-white rounded border border-gray-200 p-16 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 to-indigo-100 rounded-full mb-6">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Articles Coming Soon</h3>
                        <p class="text-gray-600 max-w-md mx-auto">
                            Stay tuned for industry insights, product guides, and company updates from {{ $businessProfile->business_name }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Tab Styling */
        .tab-button {
            position: relative;
        }

        .tab-button.active {
            color: #ff0808;
            border-bottom-color: #ff0808;
            background-color: #fff5f5;
        }

        /* Tab Content Animations */
        .tab-content {
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tab-content.hidden {
            display: none;
        }

        /* Smooth transitions */
        * {
            transition-property: color, background-color, border-color, transform, box-shadow;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    </style>

    <script>
        // Tab Switching
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active class to selected tab
            document.getElementById('tab-' + tabName).classList.add('active');

            // Save to localStorage
            localStorage.setItem('activeTab', tabName);
        }

        // Advanced Filters Toggle
        function toggleAdvancedFilters() {
            const filters = document.getElementById('advancedFilters');
            const chevron = document.getElementById('chevron');

            if (filters.classList.contains('max-h-0')) {
                filters.classList.remove('max-h-0');
                filters.classList.add('max-h-96');
                chevron.classList.add('rotate-180');
            } else {
                filters.classList.add('max-h-0');
                filters.classList.remove('max-h-96');
                chevron.classList.remove('rotate-180');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Restore last active tab or default to products
            const savedTab = localStorage.getItem('activeTab') || 'products';
            switchTab(savedTab);

            // Product tracking functionality
            const productCards = document.querySelectorAll('[data-product-id]');
            const hoverTimers = new Map();

            // Intersection Observer for scroll-based impressions
            const observerOptions = {
                root: null,
                threshold: 0.5,
                rootMargin: '0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const productId = entry.target.dataset.productId;

                    if (entry.isIntersecting) {
                        if (!hoverTimers.has(productId)) {
                            const timer = setTimeout(() => {
                                trackImpression(productId);
                            }, 2000);
                            hoverTimers.set(productId, timer);
                        }
                    } else {
                        if (hoverTimers.has(productId)) {
                            clearTimeout(hoverTimers.get(productId));
                            hoverTimers.delete(productId);
                        }
                    }
                });
            }, observerOptions);

            // Observe all product cards
            productCards.forEach(card => {
                observer.observe(card);

                // Hover-based impressions
                card.addEventListener('mouseenter', function() {
                    const productId = this.dataset.productId;
                    if (!hoverTimers.has(productId + '-hover')) {
                        const timer = setTimeout(() => {
                            trackImpression(productId);
                            hoverTimers.delete(productId + '-hover');
                        }, 2000);
                        hoverTimers.set(productId + '-hover', timer);
                    }
                });

                card.addEventListener('mouseleave', function() {
                    const productId = this.dataset.productId;
                    const timerKey = productId + '-hover';
                    if (hoverTimers.has(timerKey)) {
                        clearTimeout(hoverTimers.get(timerKey));
                        hoverTimers.delete(timerKey);
                    }
                });
            });

            // Track impression
            function trackImpression(productId) {
                fetch(`/products/${productId}/track-impression`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Impression tracked:', data);
                })
                .catch(error => {
                    console.error('Tracking error:', error);
                });
            }
        });
    </script>
@endsection
