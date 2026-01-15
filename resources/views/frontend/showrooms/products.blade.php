@extends('layouts.app')

@section('title', $showroom->name . ' - Products')

@section('content')
<div class="py-8 min-h-screen bg-gray-50">
    <div class="container px-4 mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-[#ff0808]">Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('showrooms.index') }}" class="text-gray-700 hover:text-[#ff0808]">Showrooms</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('showrooms.show', $showroom->slug) }}" class="text-gray-700 hover:text-[#ff0808]">
                            {{ $showroom->name }}
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500">Products</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Showroom Header -->
        <div class="flex flex-col py-6 mb-8 bg-white rounded-xl shadow-sm border border-gray-200 px-6">
            <div class="flex flex-col gap-6 items-start md:flex-row md:items-center">
                <!-- Logo -->
                @if($showroom->logo_image)
                    <div class="w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-200 flex-shrink-0">
                        <img src="{{ Storage::url($showroom->logo_image) }}"
                             alt="{{ $showroom->name }}"
                             class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="flex-1">
                    <div class="flex gap-3 items-center mb-2">
                        <h1 class="text-lg font-bold text-gray-900">{{ $showroom->name }}</h1>
                        @if($showroom->is_verified)
                            <span class="px-3 py-1 text-xs font-medium text-white bg-green-600 rounded-full">
                                <i class="fas fa-check-circle mr-1"></i>Verified
                            </span>
                        @endif
                        @if($showroom->is_featured)
                            <span class="px-3 py-1 text-xs font-medium text-white bg-amber-500 rounded-full">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                        @endif
                    </div>
                    <p class="mb-2 text-gray-600">
                        <i class="mr-2 fas fa-map-marker-alt text-[#ff0808]"></i>
                        {{ $showroom->city }}, {{ $showroom->country->name }}
                    </p>
                    <div class="flex items-center gap-4">
                        @if($showroom->phone)
                            <p class="text-gray-600 text-sm">
                                <i class="mr-2 fas fa-phone text-[#ff0808]"></i>
                                {{ $showroom->phone }}
                            </p>
                        @endif
                        @if($showroom->rating > 0)
                            <div class="flex items-center gap-1.5 text-amber-500 text-sm">
                                <i class="fas fa-star"></i>
                                <span class="font-semibold text-gray-900">{{ number_format($showroom->rating, 1) }}</span>
                                <span class="text-gray-600">({{ $showroom->reviews_count }} reviews)</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('showrooms.show', $showroom->slug) }}"
                       class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                        <i class="fas fa-building mr-2"></i>Showroom Details
                    </a>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <form method="GET" action="{{ route('showrooms.products', $showroom->slug) }}" id="filterForm" class="space-y-4">
                    <!-- Search and Quick Filters Row -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block mb-1 text-sm font-medium text-gray-700">Search Products</label>
                            <input type="text"
                                   id="search"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search by name..."
                                   class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category" class="block mb-1 text-sm font-medium text-gray-700">Category</label>
                            <select id="category"
                                    name="category"
                                    class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Min Price -->
                        <div>
                            <label for="min_price" class="block mb-1 text-sm font-medium text-gray-700">Min Price</label>
                            <input type="number"
                                   id="min_price"
                                   name="min_price"
                                   value="{{ request('min_price') }}"
                                   placeholder="0"
                                   min="0"
                                   step="0.01"
                                   class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                        </div>

                        <!-- Max Price -->
                        <div>
                            <label for="max_price" class="block mb-1 text-sm font-medium text-gray-700">Max Price</label>
                            <input type="number"
                                   id="max_price"
                                   name="max_price"
                                   value="{{ request('max_price') }}"
                                   placeholder="No limit"
                                   min="0"
                                   step="0.01"
                                   class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                        </div>
                    </div>

                    <!-- Second Row: MOQ and Sort -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <!-- Min MOQ -->
                        <div>
                            <label for="min_moq" class="block mb-1 text-sm font-medium text-gray-700">Min Order Quantity</label>
                            <input type="number"
                                   id="min_moq"
                                   name="min_moq"
                                   value="{{ request('min_moq') }}"
                                   placeholder="Any"
                                   min="1"
                                   class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                        </div>

                        <!-- Sort By -->
                        <div>
                            <label for="sort" class="block mb-1 text-sm font-medium text-gray-700">Sort By</label>
                            <select id="sort"
                                    name="sort"
                                    class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 items-end md:col-span-2">
                            <button type="submit"
                                    class="flex-1 px-6 py-2 font-medium text-white bg-[#ff0808] rounded-lg transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:ring-offset-2">
                                <i class="mr-2 fas fa-filter"></i>Apply Filters
                            </button>
                            <a href="{{ route('showrooms.products', $showroom->slug) }}"
                               class="px-6 py-2 font-medium text-gray-700 bg-white rounded-lg border border-gray-300 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                <i class="mr-2 fas fa-times"></i>Clear
                            </a>
                        </div>
                    </div>

                    <!-- Active Filters Display -->
                    @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'min_moq', 'sort']))
                        <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
                            <span class="text-sm font-medium text-gray-700">Active Filters:</span>
                            @if(request('search'))
                                <span class="inline-flex items-center px-3 py-1 text-sm text-[#ff0808] bg-red-50 rounded-full">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ route('showrooms.products', array_merge([$showroom->slug], request()->except('search'))) }}"
                                       class="ml-2 text-[#ff0808] hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('category'))
                                <span class="inline-flex items-center px-3 py-1 text-sm text-[#ff0808] bg-red-50 rounded-full">
                                    Category: {{ $categories->find(request('category'))->name ?? 'N/A' }}
                                    <a href="{{ route('showrooms.products', array_merge([$showroom->slug], request()->except('category'))) }}"
                                       class="ml-2 text-[#ff0808] hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('min_price') || request('max_price'))
                                <span class="inline-flex items-center px-3 py-1 text-sm text-[#ff0808] bg-red-50 rounded-full">
                                    Price: {{ request('min_price') ? number_format(request('min_price'), 0) : '0' }} - {{ request('max_price') ? number_format(request('max_price'), 0) : '∞' }}
                                    <a href="{{ route('showrooms.products', array_merge([$showroom->slug], request()->except('min_price', 'max_price'))) }}"
                                       class="ml-2 text-[#ff0808] hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('min_moq'))
                                <span class="inline-flex items-center px-3 py-1 text-sm text-[#ff0808] bg-red-50 rounded-full">
                                    Min MOQ: {{ request('min_moq') }}
                                    <a href="{{ route('showrooms.products', array_merge([$showroom->slug], request()->except('min_moq'))) }}"
                                       class="ml-2 text-[#ff0808] hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('sort') && request('sort') != 'latest')
                                <span class="inline-flex items-center px-3 py-1 text-sm text-[#ff0808] bg-red-50 rounded-full">
                                    Sort: {{ ucwords(str_replace('_', ' ', request('sort'))) }}
                                    <a href="{{ route('showrooms.products', array_merge([$showroom->slug], request()->except('sort'))) }}"
                                       class="ml-2 text-[#ff0808] hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-box text-[#ff0808] mr-2"></i>
                    Products ({{ $products->total() }})
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $product)
                    @php
                        $featuredImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        $firstPriceTier = $product->prices->first();
                    @endphp
                    <a href="{{ route('products.show', $product->slug) }}"
                       class="block overflow-hidden bg-white rounded-lg border border-gray-200 transition-all duration-300 hover:shadow-lg group">
                        <div class="relative h-48 bg-gray-100">
                            @if($featuredImage)
                                <img src="{{ $featuredImage->image_url }}"
                                     alt="{{ $product->name }}"
                                     class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105">
                            @else
                                <div class="flex justify-center items-center w-full h-full bg-gradient-to-br from-gray-100 to-gray-200">
                                    <span class="text-5xl">📦</span>
                                </div>
                            @endif
                            @if($product->is_admin_verified)
                                <span class="absolute top-3 right-3 px-2 py-1 text-xs font-medium text-white bg-green-600 rounded">
                                    Verified
                                </span>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="mb-2 text-lg font-semibold text-gray-900 transition-colors line-clamp-2 group-hover:text-[#ff0808]">
                                {{ $product->name }}
                            </h3>
                            @if($product->short_description)
                                <p class="mb-3 text-sm text-gray-600 line-clamp-2">
                                    {{ $product->short_description }}
                                </p>
                            @endif
                            @if($firstPriceTier)
                                <div class="mb-2 text-xl font-bold text-[#ff0808]">
                                    {{ number_format($firstPriceTier->price, 0) }} {{ $firstPriceTier->currency }}
                                    @if($product->prices->count() > 1)
                                        <span class="text-sm font-normal text-gray-500">({{ $product->prices->count() }} tiers)</span>
                                    @endif
                                </div>
                            @endif
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <span class="text-xs text-gray-500">MOQ: {{ $product->min_order_quantity }} pcs</span>
                                @if($product->country)
                                    <span class="text-xs text-gray-500">📍 {{ $product->country->name }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center bg-white rounded-lg border border-gray-200">
                <i class="mb-4 text-6xl text-gray-300 fas fa-box"></i>
                <p class="mb-2 text-lg font-medium text-gray-600">No products found</p>
                <p class="text-sm text-gray-500">{{ $showroom->name }} hasn't added any products yet or no products match your filters.</p>
                <a href="{{ route('showrooms.show', $showroom->slug) }}"
                   class="inline-block px-6 py-3 mt-6 font-semibold text-white bg-[#ff0808] rounded-lg transition-colors hover:bg-red-700">
                    Back to Showroom
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
