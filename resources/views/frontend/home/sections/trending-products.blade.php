<section class="py-16 bg-white">
    <div class="container px-4 mx-auto">

        <!-- Trending Products Section -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-[#ff0808]"></div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.trending_products') }}</h2>
                </div>
                <div class="flex gap-2">
                    <!-- Region Dropdown -->
                    <div class="relative">
                        <select id="trending-region-filter"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors appearance-none pr-10 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                onchange="filterTrendingByRegion(this.value)">
                            <option value="all">{{ __('messages.all_regions') }}</option>
                            @php
                                $trendingRegions = App\Models\Region::where('status', 'active')
                                    ->orderByRaw("
                                        CASE name
                                            WHEN 'All Regions' THEN 1
                                            WHEN 'East Africa' THEN 2
                                            WHEN 'West Africa' THEN 3
                                            WHEN 'Southern Africa' THEN 4
                                            WHEN 'North Africa' THEN 5
                                            WHEN 'Central Africa' THEN 6
                                            WHEN 'Region Diaspora' THEN 7
                                            ELSE 8
                                        END
                                    ")
                                    ->get();
                            @endphp
                            @foreach($trendingRegions as $region)
                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Refresh Button -->
                    <button onclick="refreshTrending()" class="p-2 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>

            @php
                // Get products with active trending addons
                $trendingProducts = App\Models\Product::where('status', 'active')
                    ->where('is_admin_verified', true)
                    ->whereHas('addonUsers', function($query) {
                        $query->whereNotNull('paid_at')
                            ->where(function($q) {
                                $q->whereNull('ended_at')
                                  ->orWhere('ended_at', '>', now());
                            })
                            ->whereHas('addon', function($addonQuery) {
                                $addonQuery->where('locationX', 'Homepage')
                                           ->where('locationY', 'trendingproducts');
                            });
                    })
                    ->with(['images', 'country.region', 'user', 'productCategory', 'prices'])
                    ->get();

                // Group products by country
                $productsByCountry = $trendingProducts->groupBy('country_id')->map(function($products, $countryId) {
                    return $products->map(function($product) {
                        $featuredImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        $isHot = $product->created_at->diffInDays(now()) <= 7;

                        // Calculate price range
                        $currency = 'RWF';
                        if ($product->prices && $product->prices->count() > 0) {
                            $minPrice = $product->prices->min('price') ?? 0;
                            $maxPrice = $product->prices->max('price') ?? 0;
                            $currency = $product->prices->first()->currency ?? 'RWF';
                            $priceRange = $minPrice != $maxPrice
                                ? number_format((float)$minPrice, 2) . ' - ' . number_format((float)$maxPrice, 2)
                                : number_format((float)$minPrice, 2);
                        } else {
                            $priceRange = '0.00';
                        }

                        return [
                            'product' => $product,
                            'image' => $featuredImage,
                            'isHot' => $isHot,
                            'priceRange' => $priceRange,
                            'currency' => $currency,
                        ];
                    });
                });

                // Get countries with products
                $countriesWithProducts = App\Models\Country::whereIn('id', $productsByCountry->keys())
                    ->with('region')
                    ->get()
                    ->keyBy('id');
            @endphp

            <!-- Countries Grid - 6 per row -->
            <div id="trending-grid" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                @forelse($countriesWithProducts as $country)
                    @php
                        $products = $productsByCountry->get($country->id);
                        $regionId = $country->region_id ?? 'none';
                    @endphp
                    <div class="trending-country-card bg-white rounded-md border-2 border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all"
                         data-region="{{ $regionId }}">
                        <!-- Country Header -->
                        <div class="p-3 bg-blue-50 border-b border-gray-200">
                            <div class="flex items-center gap-2">
                                @if($country->flag_url)
                                    <img src="{{ $country->flag_url }}"
                                         alt="{{ $country->name }}"
                                         class="w-6 h-4 rounded shadow-sm object-cover">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-bold text-gray-900 truncate">{{ $country->name }}</h3>
                                    <p class="text-xs text-gray-600">{{ $products->count() }}
                                         @if($products->count() > 1)
                                         {{ __('messages.products') }}
                                         @else
                                         {{ __('messages.product') }}
                                         @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Products Carousel -->
                        <div class="relative">
                            <div class="trending-carousel overflow-hidden" data-country="{{ $country->id }}">
                                <div class="carousel-track flex transition-transform duration-500 ease-in-out">
                                    @foreach($products as $index => $productData)
                                        <div class="carousel-slide w-full flex-shrink-0">
                                            <div class="group">
                                                <!-- Product Image -->
                                                <a href="{{ route('products.show', $productData['product']->slug) }}"
                                                   class="block relative h-32 overflow-hidden">
                                                    @if($productData['image'])
                                                        <img src="{{ $productData['image']->image_url }}"
                                                             alt="{{ $productData['product']->name }}"
                                                             class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110"
                                                             loading="lazy">
                                                    @else
                                                        <div class="flex justify-center items-center w-full h-full bg-gray-100">
                                                            <span class="text-2xl">📦</span>
                                                        </div>
                                                    @endif

                                                    <!-- Hot Badge -->
                                                    @if($productData['isHot'])
                                                    <span class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                                        🔥 {{ __('messages.hot') }}
                                                    </span>
                                                    @endif
                                                </a>

                                                <!-- Product Details -->
                                                <div class="p-3">
                                                    <!-- Product Name -->
                                                    <a href="{{ route('products.show', $productData['product']->slug) }}">
                                                        <h4 class="text-xs font-bold text-gray-900 mb-2 group-hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[2rem]">
                                                            {{ $productData['product']->name }}
                                                        </h4>
                                                    </a>

                                                    <!-- Category -->
                                                    @if($productData['product']->productCategory)
                                                    <div class="mb-2">
                                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                            </svg>
                                                            <span class="truncate">{{ $productData['product']->productCategory->name }}</span>
                                                        </span>
                                                    </div>
                                                    @endif

                                                    <!-- Price -->
                                                    <div class="text-blue-600 font-bold text-xs mb-2">
                                                        {{ $productData['priceRange'] }} {{ $productData['currency'] }}
                                                    </div>

                                                    <!-- MOQ -->
                                                    <div class="text-[10px] text-gray-500 mb-2">
                                                        {{ __('messages.moq') }}: {{ $productData['product']->min_order_quantity }} pcs
                                                    </div>

                                                    <!-- Verified Badge -->
                                                    @if($productData['product']->is_admin_verified)
                                                    <div class="flex items-center gap-1 text-green-600">
                                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="font-medium text-[10px]">{{ __('messages.verified') }}</span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Navigation Arrows -->
                            @if($products->count() > 1)
                                <button onclick="stopTrendingAutoSlide('{{ $country->id }}'); navigateTrendingCarousel('{{ $country->id }}', -1);"
                                    class="absolute left-1 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 rounded-full p-1.5 shadow-lg transition-all z-10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button onclick="stopTrendingAutoSlide('{{ $country->id }}'); navigateTrendingCarousel('{{ $country->id }}', 1);"
                                    class="absolute right-1 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 rounded-full p-1.5 shadow-lg transition-all z-10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                <!-- Dots -->
                                <div class="flex justify-center gap-1.5 py-2">
                                    @foreach($products as $index => $prod)
                                        <button onclick="stopTrendingAutoSlide('{{ $country->id }}'); goToTrendingSlide('{{ $country->id }}', {{ $index }});"
                                            class="trending-dot w-1.5 h-1.5 rounded-full bg-gray-300 transition-all {{ $index === 0 ? 'bg-blue-600 w-4' : '' }}"
                                            data-country="{{ $country->id }}"
                                            data-index="{{ $index }}">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <div>
                                <p class="text-lg font-semibold text-gray-700 mb-1">{{ __('messages.no_trending_products') }}</p>
                                <p class="text-sm text-gray-500">{{ __('messages.no_trending_products_description') }}</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Show All Dropdown -->
            <div class="mt-6 flex justify-end">
                <div class="relative">
                    <select id="trending-show-all-filter"
                            class="px-4 py-2 text-sm font-medium text-[#ff0808] bg-white border border-[#ff0808] rounded hover:bg-[#fff5f5] transition-colors appearance-none pr-10 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#ff0808]"
                            onchange="if(this.value) window.location.href=this.value">
                        <option value="">{{ __('messages.show_all') }}</option>
                        <option value="">{{ __('messages.show_all_in_all_regions') }}</option>
                        @foreach($trendingRegions as $region)
                            <option value="">
                                {{ __('messages.show_all_in') }} {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        const trendingCarouselStates = {};
        const trendingCarouselIntervals = {};

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.trending-carousel').forEach(carousel => {
                const countryId = carousel.dataset.country;
                const slides = carousel.querySelectorAll('.carousel-slide');
                trendingCarouselStates[countryId] = 0;
                if (slides.length > 1) startTrendingAutoSlide(countryId);
            });
        });

        function startTrendingAutoSlide(countryId) {
            if (trendingCarouselIntervals[countryId]) clearInterval(trendingCarouselIntervals[countryId]);
            trendingCarouselIntervals[countryId] = setInterval(() => navigateTrendingCarousel(countryId, 1), 4000);
        }

        function stopTrendingAutoSlide(countryId) {
            if (trendingCarouselIntervals[countryId]) {
                clearInterval(trendingCarouselIntervals[countryId]);
                trendingCarouselIntervals[countryId] = null;
            }
        }

        function navigateTrendingCarousel(countryId, direction) {
            const carousel = document.querySelector(`.trending-carousel[data-country="${countryId}"]`);
            const track = carousel.querySelector('.carousel-track');
            const slides = track.querySelectorAll('.carousel-slide');
            trendingCarouselStates[countryId] = (trendingCarouselStates[countryId] + direction + slides.length) % slides.length;
            updateTrendingCarousel(countryId);
        }

        function goToTrendingSlide(countryId, index) {
            trendingCarouselStates[countryId] = index;
            updateTrendingCarousel(countryId);
        }

        function updateTrendingCarousel(countryId) {
            const carousel = document.querySelector(`.trending-carousel[data-country="${countryId}"]`);
            const track = carousel.querySelector('.carousel-track');
            track.style.transform = `translateX(-${trendingCarouselStates[countryId] * 100}%)`;

            document.querySelectorAll(`.trending-dot[data-country="${countryId}"]`).forEach((dot, index) => {
                if (index === trendingCarouselStates[countryId]) {
                    dot.classList.add('bg-blue-600', 'w-4');
                    dot.classList.remove('bg-gray-300', 'w-1.5');
                } else {
                    dot.classList.remove('bg-blue-600', 'w-4');
                    dot.classList.add('bg-gray-300', 'w-1.5');
                }
            });
        }

        function filterTrendingByRegion(regionId) {
            document.querySelectorAll('.trending-country-card').forEach(card => {
                card.style.display = (regionId === 'all' || card.dataset.region === regionId) ? 'block' : 'none';
            });
        }

        function refreshTrending() {
            location.reload();
        }

        document.querySelectorAll('.trending-country-card').forEach(card => {
            const carousel = card.querySelector('.trending-carousel');
            if (carousel) {
                const countryId = carousel.dataset.country;
                card.addEventListener('mouseenter', () => stopTrendingAutoSlide(countryId));
                card.addEventListener('mouseleave', () => {
                    const slides = carousel.querySelectorAll('.carousel-slide');
                    if (slides.length > 1) startTrendingAutoSlide(countryId);
                });
            }
        });
    </script>
</section>
