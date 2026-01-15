@extends('layouts.app')

@section('title', $businessProfile->business_name . ' - Products')

@section('content')
    <div class="py-12 min-h-screen bg-white">
        <div class="container px-4 mx-auto max-w-7xl">
            <!-- Breadcrumb and Business Info Row -->
            <div class="flex justify-between items-start mb-8">
                <!-- Breadcrumb -->
                <nav class="flex text-sm" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-4">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-[#ff0808] transition-colors font-medium">
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
                                <a href="{{ route('country.business-profiles', $businessProfile->country_id) }}" class="text-gray-600 hover:text-[#ff0808] transition-colors font-medium">
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

                <!-- Business Info -->
                <div class="text-right">
                    <div class="flex items-center justify-end gap-3 mb-2">
                        <h2 class="text-lg font-bold text-gray-900">{{ $businessProfile->business_name }}</h2>
                        <span class="bg-green-600 text-white text-xs font-semibold px-2.5 py-1 rounded flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified
                        </span>
                    </div>
                    <div class="flex items-center justify-end gap-1.5 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="font-medium">{{ $businessProfile->city }}, {{ $businessProfile->country->name }}</span>
                        @if($businessProfile->phone)
                        <div class="flex items-center justify-end gap-1.5 text-sm text-gray-600 mt-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="font-medium">{{ $businessProfile->phone_code }} {{ $businessProfile->phone }}</span>
                        </div>
                    @endif
                    </div>

                </div>
            </div>

            <!-- Main Content: Sidebar + Products -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="lg:w-72 flex-shrink-0">


                    <!-- Filter Panel -->
                    <div class="bg-white rounded-lg border-2 border-gray-200 overflow-hidden sticky mb-4">
                        <!-- Filter Header -->
                        <div class="bg-[#ff0808] to-[#dd0606] text-white p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-base flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                    Filters
                                </h3>
                                @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'min_moq', 'sort']))
                                    <a href="z{{ route('business-profile.products', $businessProfile->id) }}"
                                       class="text-xs bg-white/20 hover:bg-white/30 px-2 py-1 rounded transition-colors">
                                        Clear All
                                    </a>
                                @endif
                            </div>
                        </div>

                        <form method="GET" action="{{ route('business-profile.products', $businessProfile->id) }}" class="p-4 space-y-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                                <input type="text"
                                       id="search"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Product name..."
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                                <select id="category"
                                        name="category"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
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
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
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
                                        onclick="document.getElementById('advancedFilters').classList.toggle('hidden')"
                                        class="w-full flex items-center justify-between text-sm font-semibold text-gray-700 hover:text-[#ff0808] transition-colors">
                                    <span>Advanced Filters</span>
                                    <svg class="w-4 h-4 transform transition-transform" id="chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Advanced Filters (Hidden by Default) -->
                            <div id="advancedFilters" class="space-y-4 {{ request()->hasAny(['min_price', 'max_price', 'min_moq']) ? '' : 'hidden' }}">
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
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                                        <input type="number"
                                               name="max_price"
                                               value="{{ request('max_price') }}"
                                               placeholder="Max"
                                               min="0"
                                               step="0.01"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
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
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                                </div>
                            </div>

                            <!-- Apply Button -->
                            <button type="submit"
                                    class="w-full bg-[#ff0808] hover:bg-[#dd0606] text-white font-semibold py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
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
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                            {{ Str::limit(request('search'), 15) }}
                                            <a href="{{ route('business-profile.products', array_merge([$businessProfile->id], request()->except('search'))) }}" class="hover:text-blue-900">×</a>
                                        </span>
                                    @endif
                                    @if(request('category'))
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">
                                            {{ $categories->find(request('category'))->name ?? 'Category' }}
                                            <a href="{{ route('business-profile.products', array_merge([$businessProfile->id], request()->except('category'))) }}" class="hover:text-purple-900">×</a>
                                        </span>
                                    @endif
                                    @if(request('min_price') || request('max_price'))
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                            ${{ request('min_price', 0) }}-{{ request('max_price', '∞') }}
                                            <a href="{{ route('business-profile.products', array_merge([$businessProfile->id], request()->except('min_price', 'max_price'))) }}" class="hover:text-green-900">×</a>
                                        </span>
                                    @endif
                                    @if(request('min_moq'))
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded">
                                            MOQ: {{ request('min_moq') }}+
                                            <a href="{{ route('business-profile.products', array_merge([$businessProfile->id], request()->except('min_moq'))) }}" class="hover:text-orange-900">×</a>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                                        <!-- Stats Bar -->
                    <div class="grid grid-cols-2 gap-4 bg-blue-50 rounded-lg border-2 border-[#ff0808] p-6 top-4">
                        <div class="text-center">
                            <div class="text-lg font-bold text-[#ff0808] mb-1">
                                {{ $products->total() }}
                            </div>
                            <div class="text-sm text-gray-600">Total Products</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-purple-600 mb-1">
                                {{ $categories->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Categories</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-green-600 mb-1">
                                100%
                            </div>
                            <div class="text-sm text-gray-600">Verified</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-600 mb-1">
                                {{ $businessProfile->country->name }}
                            </div>
                            <div class="text-sm text-gray-600">Location</div>
                        </div>
                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="flex-1">
                    @if($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            @foreach($products as $product)
                                @php
                                    $featuredImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                    $firstPriceTier = $product->prices->first();
                                @endphp
                                <div class="group bg-white rounded-lg overflow-hidden transition-all duration-300 hover:shadow-lg border-2 border-transparent hover:border-[#ff0808]" data-product-id="{{ $product->id }}">
                                    <!-- Product Image -->
                                    <a href="{{ route('products.show', $product->slug) }}" class="block relative h-48 overflow-hidden">
                                        @if($featuredImage)
                                            <img src="{{ $featuredImage->image_url }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                 loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                                <span class="text-6xl">📦</span>
                                            </div>
                                        @endif

                                        <!-- Verified Badge -->
                                        @if($product->is_admin_verified)
                                            <span class="absolute top-3 right-3 bg-green-600 text-white text-xs font-semibold px-2.5 py-1 rounded flex items-center gap-1">
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
                                            <h3 class="text-base font-bold text-gray-900 mb-2 group-hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[3rem]">
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
                        <div class="bg-gray-50 rounded-lg p-20 text-center">
                            <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-200 rounded-full mb-6">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Products Found</h3>
                            <p class="text-gray-600 mb-8 max-w-md mx-auto text-lg">
                                {{ $businessProfile->business_name }} hasn't added any products matching your filters yet.
                            </p>
                            <a href="{{ route('business-profile.products', $businessProfile->id) }}"
                               class="inline-flex items-center gap-3 bg-[#ff0808] text-white px-8 py-3 rounded-lg font-semibold hover:bg-[#dd0606] transition-colors">
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
    // Toggle chevron rotation when advanced filters are toggled
    document.querySelector('button[onclick*="advancedFilters"]').addEventListener('click', function() {
        document.getElementById('chevron').classList.toggle('rotate-180');
    });

    // Product tracking functionality (Impressions only)
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== Tracking Script Loaded ===');

        const productCards = document.querySelectorAll('[data-product-id]');
        console.log('Found product cards:', productCards.length);

        const hoverTimers = new Map();

        // Intersection Observer for scroll-based impressions
        const observerOptions = {
            root: null,
            threshold: 0.5, // 50% of product must be visible
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const productId = entry.target.dataset.productId;
                console.log('Observer triggered for product:', productId, 'Intersecting:', entry.isIntersecting);

                if (entry.isIntersecting) {
                    // Start timer when product is visible
                    if (!hoverTimers.has(productId)) {
                        console.log('Starting 2s timer for product:', productId);
                        const timer = setTimeout(() => {
                            console.log('Timer completed, tracking impression for:', productId);
                            trackImpression(productId);
                        }, 2000);
                        hoverTimers.set(productId, timer);
                    }
                } else {
                    // Clear timer if product leaves viewport
                    if (hoverTimers.has(productId)) {
                        console.log('Product left viewport, clearing timer:', productId);
                        clearTimeout(hoverTimers.get(productId));
                        hoverTimers.delete(productId);
                    }
                }
            });
        }, observerOptions);

        // Observe all product cards
        productCards.forEach(card => {
            const productId = card.dataset.productId;
            console.log('Observing product card:', productId);
            observer.observe(card);

            // Hover-based impressions
            card.addEventListener('mouseenter', function() {
                const productId = this.dataset.productId;
                console.log('Mouse entered product:', productId);
                if (!hoverTimers.has(productId + '-hover')) {
                    console.log('Starting 2s hover timer for:', productId);
                    const timer = setTimeout(() => {
                        console.log('Hover timer completed, tracking impression:', productId);
                        trackImpression(productId);
                        hoverTimers.delete(productId + '-hover');
                    }, 2000);
                    hoverTimers.set(productId + '-hover', timer);
                }
            });

            card.addEventListener('mouseleave', function() {
                const productId = this.dataset.productId;
                console.log('Mouse left product:', productId);
                const timerKey = productId + '-hover';
                if (hoverTimers.has(timerKey)) {
                    console.log('Clearing hover timer:', productId);
                    clearTimeout(hoverTimers.get(timerKey));
                    hoverTimers.delete(timerKey);
                }
            });
        });

        // Track impression
        function trackImpression(productId) {
            console.log('=== Sending impression request for product:', productId, '===');

            fetch(`/products/${productId}/track-impression`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                console.log('Impression response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Impression tracked successfully:', data);
            })
            .catch(error => {
                console.error('Impression tracking error:', error);
            });
        }
    });
</script>
@endsection
