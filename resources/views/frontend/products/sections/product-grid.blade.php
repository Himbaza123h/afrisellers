<main class="flex-1">
    <!-- Header with Results Count and Controls -->
    <div class="bg-white rounded-lg border p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-gray-900">
                @if($tab === 'suppliers')
                    @if(isset($category))
                        Showing {{ $totalSuppliers }} {{ Str::plural('supplier', $totalSuppliers) }} in "{{ $category->name }}"
                    @else
                        Showing {{ $totalSuppliers }} {{ Str::plural('supplier', $totalSuppliers) }} for "{{ $searchQuery }}"
                    @endif
                @else
                    @if(isset($category))
                        Showing {{ $totalProducts }} {{ Str::plural('product', $totalProducts) }} in "{{ $category->name }}"
                    @else
                        Showing {{ $totalProducts }} {{ Str::plural('product', $totalProducts) }} for "{{ $searchQuery }}"
                    @endif
                @endif
            </h1>

            @if($tab !== 'suppliers')
            <div class="flex items-center gap-4">
                <!-- Sort Dropdown -->
                <div class="relative">
                    <button class="sort-dropdown-btn flex items-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="text-gray-700 font-medium">Sort by {{ request('sort', 'relevance') }}</span>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>
                    <div class="sort-dropdown absolute right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg hidden z-50">
                        @if(isset($category))
                            <a href="{{ route('products.search', ['type' => $type, 'slug' => $slug, 'tab' => $tab, 'sort' => 'latest']) }}" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Newest First</a>
                            <a href="{{ route('products.search', ['type' => $type, 'slug' => $slug, 'tab' => $tab, 'sort' => 'oldest']) }}" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Oldest First</a>
                            <a href="{{ route('products.search', ['type' => $type, 'slug' => $slug, 'tab' => $tab, 'sort' => 'price_low']) }}" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Price: Low to High</a>
                            <a href="{{ route('products.search', ['type' => $type, 'slug' => $slug, 'tab' => $tab, 'sort' => 'price_high']) }}" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Price: High to Low</a>
                            <a href="{{ route('products.search', ['type' => $type, 'slug' => $slug, 'tab' => $tab, 'sort' => 'name_asc']) }}" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Name: A to Z</a>
                            <a href="{{ route('products.search', ['type' => $type, 'slug' => $slug, 'tab' => $tab, 'sort' => 'name_desc']) }}" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Name: Z to A</a>
                        @endif
                    </div>
                </div>

                <!-- View Toggle -->
                <div class="flex items-center gap-2 border rounded-lg p-1">
                    <button class="view-toggle active p-2 rounded hover:bg-gray-100 transition-colors" data-view="grid">
                        <i class="fas fa-th text-gray-700"></i>
                    </button>
                    <button class="view-toggle p-2 rounded hover:bg-gray-100 transition-colors" data-view="list">
                        <i class="fas fa-bars text-gray-700"></i>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Content Based on Tab -->
    @if($tab === 'suppliers')
        <!-- Suppliers Grid -->
        @if($suppliers->count() > 0)
            <div id="suppliers-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($suppliers as $supplier)
                    <a href="{{ route('business-profile.products', $supplier->id) }}"
                       class="block bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-all">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                <span class="text-2xl font-bold text-blue-600">
                                    {{ strtoupper(substr($supplier->business_name, 0, 2)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 text-lg mb-1 truncate">{{ $supplier->business_name }}</h3>
                                @if($supplier->country)
                                    <p class="text-sm text-gray-600">📍 {{ $supplier->country->name }}</p>
                                @endif
                            </div>
                        </div>

                        @if($supplier->business_description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $supplier->business_description }}</p>
                        @endif

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                <span><i class="fas fa-box text-blue-600 mr-1"></i> {{ $supplier->products_count ?? 0 }} products</span>
                                @if($supplier->verification_status === 'verified')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i>Verified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($suppliers->hasPages())
                <div class="mt-8">
                    {{ $suppliers->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center bg-white rounded-lg border border-gray-200">
                <i class="mb-4 text-6xl text-gray-300 fas fa-store"></i>
                <p class="mb-2 text-lg font-medium text-gray-600">No suppliers found</p>
                <p class="text-sm text-gray-500">Try adjusting your search criteria</p>
            </div>
        @endif
    @else
        <!-- Products Grid -->
        @if($products->count() > 0)
            <div id="products-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
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
                    <div class="p-12 text-center bg-white rounded-lg border border-gray-200">
                        <i class="mb-4 text-6xl text-gray-300 fas fa-box"></i>
                        <p class="mb-2 text-lg font-medium text-gray-600">No products found</p>
                        <p class="text-sm text-gray-500">
                            @if(isset($category))
                                No products available in "{{ $category->name }}" category yet.
                            @else
                                No products found for "{{ $searchQuery }}".
                            @endif
                        </p>
                    </div>
                @endif
            @endif

    <!-- Scroll to Top Button -->
    <button id="scroll-top" class="fixed bottom-8 right-8 w-12 h-12 bg-orange-500 text-white rounded-full shadow-lg hover:bg-orange-600 transition-all opacity-0 pointer-events-none z-50">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
// Toggle chevron rotation when advanced filters are toggled (if applicable)
const advancedFiltersBtn = document.querySelector('button[onclick*="advancedFilters"]');
if (advancedFiltersBtn) {
    advancedFiltersBtn.addEventListener('click', function() {
        document.getElementById('chevron').classList.toggle('rotate-180');
    });
}

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
</main>

