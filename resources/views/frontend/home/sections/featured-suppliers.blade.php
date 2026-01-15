<section class="py-16 bg-gray-50">
    <div class="container px-4 mx-auto">

        <!-- Recommended Suppliers Section -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-[#ff0808]"></div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.recommended_suppliers') }}</h2>
                </div>
                <div class="flex gap-2">
                    <!-- Region Dropdown -->
                    <div class="relative">
                        <select id="supplier-region-filter"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors appearance-none pr-10 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                onchange="filterSuppliersByRegion(this.value)">
                            <option value="all">{{ __('messages.all_regions') }}</option>
                            @php
                                $supplierRegions = App\Models\Region::where('status', 'active')
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
                            @foreach($supplierRegions as $region)
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
                    <button onclick="refreshSuppliers()" class="p-2 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>

            @php
        // Get verified business profiles with active featured supplier addons
        $verifiedBusinessProfiles = App\Models\BusinessProfile::where('verification_status', 'verified')
            ->where('is_admin_verified', true)
            ->whereHas('addonUsers', function($query) {
                $query->whereNotNull('paid_at')
                    ->where(function($q) {
                        $q->whereNull('ended_at')
                        ->orWhere('ended_at', '>', now());
                    })
                    ->whereHas('addon', function($addonQuery) {
                        $addonQuery->where('locationX', 'Homepage')
                                ->where('locationY', 'featuredsuppliers');
                    });
            })
            ->with(['user', 'country.region', 'vendor'])
            ->get();

                // Get user IDs
                $userIds = $verifiedBusinessProfiles->pluck('user_id');

                // Get first product with image for each user
                $userProducts = App\Models\Product::whereIn('user_id', $userIds)
                    ->where('status', 'active')
                    ->where('is_admin_verified', true)
                    ->with([
                        'images' => function ($imgQuery) {
                            $imgQuery->orderBy('is_primary', 'desc')->orderBy('sort_order', 'asc')->limit(1);
                        },
                        'productCategory',
                    ])
                    ->select('user_id', 'id', 'product_category_id')
                    ->get()
                    ->groupBy('user_id')
                    ->map(function ($products) {
                        return $products->first();
                    });

                // Get all reviews for rating calculations
                $allReviews = App\Models\ProductUserReview::whereHas('product', function ($query) use ($userIds) {
                    $query->whereIn('user_id', $userIds)
                        ->where('status', 'active')
                        ->where('is_admin_verified', true);
                })
                    ->where('status', true)
                    ->get()
                    ->groupBy(function($review) {
                        return $review->product->user_id;
                    });

                // Count products per user
                $productsCount = App\Models\Product::whereIn('user_id', $userIds)
                    ->where('status', 'active')
                    ->where('is_admin_verified', true)
                    ->select('user_id', \DB::raw('count(*) as count'))
                    ->groupBy('user_id')
                    ->pluck('count', 'user_id');

                // Group suppliers by country
                $suppliersByCountry = $verifiedBusinessProfiles->groupBy('country_id')->map(function($profiles, $countryId) use ($userProducts, $allReviews, $productsCount) {
                    return $profiles->map(function($profile) use ($userProducts, $allReviews, $productsCount) {
                        $firstProduct = $userProducts->get($profile->user_id);
                        $productImage = null;
                        if ($firstProduct && $firstProduct->images->count() > 0) {
                            $productImage = $firstProduct->images->first();
                        }

                        $categoryName = $firstProduct && $firstProduct->productCategory
                            ? $firstProduct->productCategory->name
                            : __('messages.supplier');

                        $userReviews = $allReviews->get($profile->user_id, collect());
                        $avgRating = $userReviews->count() > 0 ? $userReviews->avg('mark') : 0;
                        $reviewsCount = $userReviews->count();
                        $productCount = $productsCount->get($profile->user_id, 0);

                        return [
                            'profile' => $profile,
                            'image' => $productImage,
                            'category' => $categoryName,
                            'avgRating' => $avgRating,
                            'reviewsCount' => $reviewsCount,
                            'productsCount' => $productCount,
                        ];
                    });
                });

                // Get countries with suppliers
                $countriesWithSuppliers = App\Models\Country::whereIn('id', $suppliersByCountry->keys())
                    ->with('region')
                    ->get()
                    ->keyBy('id');
            @endphp

            <!-- Countries Grid - 6 per row -->
            <div id="suppliers-grid" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                @forelse($countriesWithSuppliers as $country)
                    @php
                        $suppliers = $suppliersByCountry->get($country->id);
                        $regionId = $country->region_id ?? 'none';
                    @endphp
                    <div class="country-card bg-white rounded-md border-2 border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all"
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
                                    <p class="text-xs text-gray-600">{{ $suppliers->count() }}
                                         @if($suppliers->count() > 1)
                                         {{ __('messages.suppliers') }}
                                         @else
                                         {{ __('messages.supplier') }}
                                         @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Suppliers Carousel -->
                        <div class="relative">
                            <div class="supplier-carousel overflow-hidden" data-country="{{ $country->id }}">
                                <div class="carousel-track flex transition-transform duration-500 ease-in-out">
                                    @foreach($suppliers as $index => $supplierData)
                                        <div class="carousel-slide w-full flex-shrink-0">
                                            <div class="group">
                                                <!-- Supplier Image - Clickable -->
                                                <a href="{{ route('country.business-profiles', $country->id) }}"
                                                   class="block relative h-32 overflow-hidden">
                                                    @if($supplierData['image'])
                                                        <img src="{{ $supplierData['image']->image_url }}"
                                                             alt="{{ $supplierData['profile']->business_name }}"
                                                             class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110"
                                                             loading="lazy">
                                                    @else
                                                        <div class="flex justify-center items-center w-full h-full bg-blue-50">
                                                            <span class="text-2xl">🏢</span>
                                                        </div>
                                                    @endif

                                                    <!-- Verified Badge -->
                                                    <span class="absolute top-2 right-2 bg-green-600 text-white text-xs font-semibold px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="text-[10px]">{{ __('messages.verified') }}</span>
                                                    </span>
                                                </a>

                                                <!-- Supplier Details -->
                                                <div class="p-3">
                                                    <!-- Business Name -->
                                                    <a href="{{ route('country.business-profiles', $country->id) }}">
                                                        <h4 class="text-xs font-bold text-gray-900 mb-2 group-hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[2rem]">
                                                            {{ $supplierData['profile']->business_name }}
                                                        </h4>
                                                    </a>

                                                    <!-- Category -->
                                                    <div class="mb-2">
                                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                            </svg>
                                                            <span class="truncate">{{ $supplierData['category'] }}</span>
                                                        </span>
                                                    </div>

                                                    <!-- Stats Row -->
                                                    <div class="flex items-center gap-2 text-xs text-gray-600 mb-2">
                                                        <!-- Products Badge -->
                                                        <a href="{{ route('country.business-profiles', $country->id) }}"
                                                           class="flex items-center gap-1 hover:opacity-80 transition-opacity">
                                                            <div class="bg-gray-800 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">
                                                                {{ number_format($supplierData['productsCount']) }}
                                                            </div>
                                                            <span class="font-medium text-[10px]">{{ Str::plural('Product', $supplierData['productsCount']) }}</span>
                                                        </a>

                                                        @if($supplierData['avgRating'] > 0)
                                                        <!-- Rating Badge -->
                                                        <a href="{{ route('country.business-profiles', $country->id) }}"
                                                           class="flex items-center gap-1 hover:opacity-80 transition-opacity">
                                                            <div class="bg-yellow-400 text-gray-900 text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                                {{ number_format($supplierData['avgRating'], 1) }}
                                                            </div>
                                                            <span class="font-medium text-[10px]">({{ $supplierData['reviewsCount'] }})</span>
                                                        </a>
                                                        @endif
                                                    </div>

                                                    <!-- Location -->
                                                    <div class="flex items-center gap-1 text-xs text-gray-600">
                                                        <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        </svg>
                                                        <span class="font-medium text-[10px] truncate">{{ $supplierData['profile']->city }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Navigation Arrows (only show if more than 1 supplier) -->
                            @if($suppliers->count() > 1)
                                <button onclick="stopAutoSlide('{{ $country->id }}'); navigateCarousel('{{ $country->id }}', -1);"
                                    class="carousel-prev absolute left-1 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 rounded-full p-1.5 shadow-lg transition-all hover:shadow-xl z-10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button onclick="stopAutoSlide('{{ $country->id }}'); navigateCarousel('{{ $country->id }}', 1);"
                                    class="carousel-next absolute right-1 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 rounded-full p-1.5 shadow-lg transition-all hover:shadow-xl z-10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                <!-- Dots Indicator -->
                                <div class="flex justify-center gap-1.5 py-2">
                                    @foreach($suppliers as $index => $supplier)
                                        <button onclick="stopAutoSlide('{{ $country->id }}'); goToSlide('{{ $country->id }}', {{ $index }});"
                                            class="carousel-dot w-1.5 h-1.5 rounded-full bg-gray-300 transition-all {{ $index === 0 ? 'bg-blue-600 w-4' : '' }}"
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <div>
                                <p class="text-lg font-semibold text-gray-700 mb-1">{{ __('messages.no_suppliers_available') }}</p>
                                <p class="text-sm text-gray-500">{{ __('messages.no_suppliers_description') }}</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Show All Link with Dropdown -->
            <div class="mt-6 flex justify-end">
                <div class="relative">
                    <select id="supplier-show-all-filter"
                            class="px-4 py-2 text-sm font-medium text-[#ff0808] bg-white border border-[#ff0808] rounded hover:bg-[#fff5f5] transition-colors appearance-none pr-10 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                            onchange="if(this.value) window.location.href=this.value">
                        <option value="">{{ __('messages.show_all') }}</option>
                        <option value="{{ route('featured-suppliers') }}">{{ __('messages.show_all_in_all_regions') }}</option>
                        @foreach($supplierRegions as $region)
                            <option value="{{ route('featured-suppliers', ['region' => $region->id]) }}">
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
        // Store current slide index and intervals for each carousel
        const carouselStates = {};
        const carouselIntervals = {};

        // Initialize carousel states and auto-slide
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.supplier-carousel').forEach(carousel => {
                const countryId = carousel.dataset.country;
                const slides = carousel.querySelectorAll('.carousel-slide');

                carouselStates[countryId] = 0;

                // Start auto-slide if more than 1 slide
                if (slides.length > 1) {
                    startAutoSlide(countryId);
                }
            });
        });

        // Start auto-slide for a carousel
        function startAutoSlide(countryId) {
            // Clear existing interval if any
            if (carouselIntervals[countryId]) {
                clearInterval(carouselIntervals[countryId]);
            }

            // Start new interval (slide every 4 seconds)
            carouselIntervals[countryId] = setInterval(() => {
                navigateCarousel(countryId, 1);
            }, 4000);
        }

        // Stop auto-slide for a carousel
        function stopAutoSlide(countryId) {
            if (carouselIntervals[countryId]) {
                clearInterval(carouselIntervals[countryId]);
                carouselIntervals[countryId] = null;
            }
        }

        // Navigate carousel
        function navigateCarousel(countryId, direction) {
            const carousel = document.querySelector(`[data-country="${countryId}"]`);
            const track = carousel.querySelector('.carousel-track');
            const slides = track.querySelectorAll('.carousel-slide');
            const totalSlides = slides.length;

            carouselStates[countryId] = (carouselStates[countryId] + direction + totalSlides) % totalSlides;

            updateCarousel(countryId);
        }

        // Go to specific slide
        function goToSlide(countryId, index) {
            carouselStates[countryId] = index;
            updateCarousel(countryId);
        }

        // Update carousel position and dots
        function updateCarousel(countryId) {
            const carousel = document.querySelector(`[data-country="${countryId}"]`);
            const track = carousel.querySelector('.carousel-track');
            const currentIndex = carouselStates[countryId];

            track.style.transform = `translateX(-${currentIndex * 100}%)`;

            // Update dots
            const dots = document.querySelectorAll(`.carousel-dot[data-country="${countryId}"]`);
            dots.forEach((dot, index) => {
                if (index === currentIndex) {
                    dot.classList.add('bg-blue-600', 'w-4');
                    dot.classList.remove('bg-gray-300', 'w-1.5');
                } else {
                    dot.classList.remove('bg-blue-600', 'w-4');
                    dot.classList.add('bg-gray-300', 'w-1.5');
                }
            });
        }

        // Filter suppliers by region
        function filterSuppliersByRegion(regionId) {
            const cards = document.querySelectorAll('.country-card');
            cards.forEach(card => {
                if (regionId === 'all' || card.dataset.region === regionId) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Refresh suppliers
        function refreshSuppliers() {
            location.reload();
        }

        // Pause auto-slide on hover
        document.querySelectorAll('.country-card').forEach(card => {
            const carousel = card.querySelector('.supplier-carousel');
            if (carousel) {
                const countryId = carousel.dataset.country;

                card.addEventListener('mouseenter', () => {
                    stopAutoSlide(countryId);
                });

                card.addEventListener('mouseleave', () => {
                    const slides = carousel.querySelectorAll('.carousel-slide');
                    if (slides.length > 1) {
                        startAutoSlide(countryId);
                    }
                });
            }
        });
    </script>
</section>

