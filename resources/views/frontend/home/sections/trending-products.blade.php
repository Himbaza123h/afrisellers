@php
    // ── UISection settings ────────────────────────────────────────
    $trendingSection = \App\Models\UISection::where('section_key', 'trending_products')->first();
    $trendingAnim    = $trendingSection?->getAnimationMode() ?? 'none';
    $trendingItems   = $trendingSection?->number_items ?? 10;
    $trendingActive  = $trendingSection?->is_active ?? true;

            $allSquareAds = \App\Models\SquareAd::with('media')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('type');

        $trendingProductsAds       = $allSquareAds->get('Trending Products',           collect());

    if ($trendingActive):

    // ── Trending products query ───────────────────────────────────
    $trendingProducts = App\Models\Product::where('status', 'active')
        ->where('is_admin_verified', true)
        ->whereHas('addonUsers', function($query) {
            $query->whereNotNull('paid_at')
                ->where(function($q) {
                    $q->whereNull('ended_at')->orWhere('ended_at', '>', now());
                })
                ->whereHas('addon', function($addonQuery) {
                    $addonQuery->where('locationX', 'Homepage')
                               ->where('locationY', 'trendingproducts');
                });
        })
        ->with(['images', 'country.region', 'user', 'productCategory', 'prices'])
        ->get();

    // ── Group by country ─────────────────────────────────────────
    $productsByCountry = $trendingProducts->groupBy('country_id')->map(function($products) {
        return $products->map(function($product) {
            $featuredImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
            $isHot         = $product->created_at->diffInDays(now()) <= 7;
            $currency      = 'RWF';
            $priceRange    = '0.00';
            if ($product->prices && $product->prices->count() > 0) {
                $minPrice  = $product->prices->min('price') ?? 0;
                $maxPrice  = $product->prices->max('price') ?? 0;
                $currency  = $product->prices->first()->currency ?? 'RWF';
                $priceRange = $minPrice != $maxPrice
                    ? number_format((float)$minPrice, 2).' - '.number_format((float)$maxPrice, 2)
                    : number_format((float)$minPrice, 2);
            }
            return compact('product', 'featuredImage', 'isHot', 'priceRange', 'currency');
        });
    });

    $countriesWithProducts = App\Models\Country::whereIn('id', $productsByCountry->keys())
        ->with('region')
        ->get()
        ->keyBy('id');

    // ── Regions for filter dropdown ───────────────────────────────
    $trendingRegions = App\Models\Region::where('status', 'active')
        ->orderByRaw("CASE name
            WHEN 'All Regions'      THEN 1
            WHEN 'East Africa'      THEN 2
            WHEN 'West Africa'      THEN 3
            WHEN 'Southern Africa'  THEN 4
            WHEN 'North Africa'     THEN 5
            WHEN 'Central Africa'   THEN 6
            WHEN 'Region Diaspora'  THEN 7
            ELSE 8 END")
        ->get();
@endphp

<section class="py-6 md:py-8 bg-white" id="trending-section">
    <div class="container px-4 mx-auto">

        @php $hasTrendingAd = $trendingProductsAds->isNotEmpty(); @endphp
        <div class="flex gap-4 md:gap-6 items-stretch">

            {{-- Main content: 70% or full --}}
            <div class="{{ $hasTrendingAd ? 'w-[70%]' : 'w-full' }} min-w-0">

                {{-- ── Header ─────────────────────────────────────── --}}
                <div class="flex flex-col gap-2 mb-3 md:mb-4 md:flex-row md:justify-between md:items-center">
                    <div class="flex items-center gap-2 flex-1">
                        <h2 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 whitespace-nowrap">
                            {{ __('messages.trending_products') }}
                        </h2>
                        <div class="flex-1 h-px bg-gray-300"></div>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        {{-- Region Dropdown --}}
                        <div class="relative">
                            <select id="trending-region-filter"
                                    class="px-2 py-1 md:px-2.5 md:py-1.5 text-[10px] md:text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors appearance-none pr-6 md:pr-7 cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#ff0808] focus:border-transparent"
                                    onchange="filterTrendingByRegion(this.value)">
                                <option value="all">{{ __('messages.all_regions') }}</option>
                                @foreach($trendingRegions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-1.5 md:pr-2 pointer-events-none">
                                <svg class="w-2.5 h-2.5 md:w-3 md:h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Refresh --}}
                        <button onclick="refreshTrending()" class="p-1 md:p-1.5 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- ── Country cards grid ──────────────────────────── --}}
                <div id="trending-grid"
                     class="grid grid-cols-2 gap-2 md:gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">

                    @forelse($countriesWithProducts as $country)
                        @php
                            $products  = $productsByCountry->get($country->id);
                            $regionId  = $country->region_id ?? 'none';
                        @endphp

                        <div class="trending-country-card bg-white rounded-lg border border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all"
                             data-region="{{ $regionId }}"
                             data-country-id="{{ $country->id }}">

                            {{-- Country Header --}}
                            <div class="p-1.5 md:p-2 border-b border-gray-200">
                                <div class="flex items-center gap-1 md:gap-1.5">
                                    @if($country->flag_url)
                                        <img src="{{ $country->flag_url }}" alt="{{ $country->name }}"
                                             class="w-4 h-3 md:w-5 md:h-3 rounded shadow-sm object-cover flex-shrink-0">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-[9px] md:text-[10px] font-bold text-gray-900 truncate">{{ $country->name }}</h3>
                                        <p class="text-[8px] md:text-[9px] text-gray-600">
                                            {{ $products->count() }}
                                            {{ $products->count() > 1 ? __('messages.products') : __('messages.product') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Product carousel inside each country card --}}
                            <div class="relative">

                                <div class="trending-carousel overflow-hidden"
                                     data-country="{{ $country->id }}"
                                     data-anim="{{ $trendingAnim }}">

                                    <div class="carousel-track flex
                                                @if($trendingAnim === 'fade') relative @endif
                                                transition-transform duration-500 ease-in-out"
                                         id="track-{{ $country->id }}">

                                        @foreach($products as $index => $productData)
                                            <div class="carousel-slide w-full flex-shrink-0
                                                        @if($trendingAnim === 'fade') absolute inset-0 transition-opacity duration-500 @endif"
                                                 style="@if($trendingAnim === 'fade') opacity:{{ $index === 0 ? '1' : '0' }}; z-index:{{ $index === 0 ? '1' : '0' }}; @endif">
                                                <div class="group">
                                                    <a href="{{ route('products.show', $productData['product']->slug) }}"
                                                       class="block relative h-24 md:h-32 overflow-hidden">
                                                        @if($productData['featuredImage'])
                                                            <img src="{{ $productData['featuredImage']->image_url }}"
                                                                 alt="{{ $productData['product']->name }}"
                                                                 class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110"
                                                                 loading="lazy">
                                                        @else
                                                            <div class="flex justify-center items-center w-full h-full bg-gray-100">
                                                                <span class="text-xl md:text-2xl">📦</span>
                                                            </div>
                                                        @endif
                                                        @if($productData['isHot'])
                                                        <span class="absolute top-1 right-1 md:top-2 md:right-2 bg-red-500 text-white text-[8px] md:text-[10px] font-bold px-1 md:px-1.5 py-0.5 rounded-full">
                                                            🔥 {{ __('messages.hot') }}
                                                        </span>
                                                        @endif
                                                    </a>
                                                    <div class="p-1.5 md:p-2">
                                                        <a href="{{ route('products.show', $productData['product']->slug) }}">
                                                            <h4 class="text-[9px] md:text-[10px] font-bold text-gray-900 mb-1 group-hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[1.5rem]">
                                                                {{ $productData['product']->name }}
                                                            </h4>
                                                        </a>
                                                        @if($productData['product']->productCategory)
                                                        <div class="mb-1">
                                                            <span class="inline-flex items-center gap-0.5 text-[7px] md:text-[8px] font-medium text-purple-600 bg-purple-50 px-1 py-0.5 rounded">
                                                                <span class="truncate">{{ $productData['product']->productCategory->name }}</span>
                                                            </span>
                                                        </div>
                                                        @endif
                                                        <div class="text-blue-600 font-bold text-[9px] md:text-[10px] mb-1">
                                                            {{ $productData['priceRange'] }} {{ $productData['currency'] }}
                                                        </div>
                                                        <div class="text-[7px] md:text-[8px] text-gray-500 mb-1">
                                                            {{ __('messages.moq') }}: {{ $productData['product']->min_order_quantity }} pcs
                                                        </div>
                                                        @if($productData['product']->is_admin_verified)
                                                        <div class="flex items-center gap-0.5 text-green-600">
                                                            <svg class="w-1.5 h-1.5 md:w-2 md:h-2" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span class="font-medium text-[7px] md:text-[8px]">{{ __('messages.verified') }}</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Arrows + dots (only if >1 product) --}}
                                @if($products->count() > 1)
                                    <button onclick="stopTrendingAutoSlide('{{ $country->id }}'); navigateTrendingCarousel('{{ $country->id }}', -1);"
                                        class="absolute left-0.5 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 rounded-full p-0.5 md:p-1 shadow-md transition-all z-10">
                                        <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>
                                    <button onclick="stopTrendingAutoSlide('{{ $country->id }}'); navigateTrendingCarousel('{{ $country->id }}', 1);"
                                        class="absolute right-0.5 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 rounded-full p-0.5 md:p-1 shadow-md transition-all z-10">
                                        <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                    <div class="flex justify-center gap-0.5 md:gap-1 py-1 md:py-1.5">
                                        @foreach($products as $index => $prod)
                                            <button onclick="stopTrendingAutoSlide('{{ $country->id }}'); goToTrendingSlide('{{ $country->id }}', {{ $index }});"
                                                class="trending-dot w-0.5 h-0.5 md:w-1 md:h-1 rounded-full bg-gray-300 transition-all {{ $index === 0 ? 'bg-blue-600 !w-2 md:!w-3' : '' }}"
                                                data-country="{{ $country->id }}"
                                                data-index="{{ $index }}">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                            </div>{{-- /relative --}}
                        </div>

                    @empty
                        <div class="col-span-full py-12 md:py-16 text-center">
                            <div class="flex flex-col items-center gap-3 md:gap-4">
                                <svg class="w-16 h-16 md:w-20 md:h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <div>
                                    <p class="text-base md:text-lg font-semibold text-gray-700 mb-1">{{ __('messages.no_trending_products') }}</p>
                                    <p class="text-xs md:text-sm text-gray-500">{{ __('messages.no_trending_products_description') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Country-filter empty notice --}}
                <div id="trending-country-empty" class="hidden py-10 text-center text-xs text-gray-400">
                    No trending products available for the selected country.
                </div>

            </div>

            {{-- Square Ad: 30% --}}
            @if($hasTrendingAd)
            <div class="w-[30%] flex-shrink-0">
                @include('frontend.home.sections._square-ad', ['ads' => $trendingProductsAds, 'instanceId' => 'trending'])
            </div>
            @endif

        </div>

    </div>
</section>

@push('scripts')
<script>
(function () {

    const ANIM = '{{ $trendingAnim }}';

    const states    = {};
    const intervals = {};

    function getSlides(countryId) {
        const carousel = document.querySelector(`.trending-carousel[data-country="${countryId}"]`);
        return carousel ? Array.from(carousel.querySelectorAll('.carousel-slide')) : [];
    }

    function getTrack(countryId) {
        return document.getElementById(`track-${countryId}`);
    }

    function updateTrendingCarousel(countryId) {
        const slides = getSlides(countryId);
        const idx    = states[countryId] ?? 0;
        const track  = getTrack(countryId);
        if (!track || !slides.length) return;

        if (ANIM === 'fade') {
            slides.forEach((s, i) => {
                s.style.transition = 'opacity 0.5s ease';
                s.style.opacity    = i === idx ? '1' : '0';
                s.style.zIndex     = i === idx ? '1' : '0';
            });
        } else if (ANIM === 'flip') {
            slides.forEach((s, i) => {
                s.style.transition = 'transform 0.6s cubic-bezier(0.77,0,0.175,1), opacity 0.6s';
                if (i === idx) {
                    s.style.transform = 'rotateY(0deg)';
                    s.style.opacity   = '1';
                    s.style.zIndex    = '1';
                } else {
                    s.style.transform = 'rotateY(90deg)';
                    s.style.opacity   = '0';
                    s.style.zIndex    = '0';
                }
            });
        } else {
            track.style.transition = ANIM === 'none' ? 'none' : 'transform 0.5s ease-in-out';
            track.style.transform  = `translateX(-${idx * 100}%)`;
        }

        document.querySelectorAll(`.trending-dot[data-country="${countryId}"]`).forEach((dot, i) => {
            if (i === idx) {
                dot.classList.add('bg-blue-600');
                dot.classList.remove('bg-gray-300');
                dot.style.minWidth = '12px';
                dot.style.width    = '12px';
            } else {
                dot.classList.remove('bg-blue-600');
                dot.classList.add('bg-gray-300');
                dot.style.minWidth = '';
                dot.style.width    = '';
            }
        });
    }

    window.navigateTrendingCarousel = function(countryId, direction) {
        const slides = getSlides(countryId);
        if (!slides.length) return;
        states[countryId] = ((states[countryId] ?? 0) + direction + slides.length) % slides.length;
        updateTrendingCarousel(countryId);
    };

    window.goToTrendingSlide = function(countryId, index) {
        states[countryId] = index;
        updateTrendingCarousel(countryId);
    };

    window.startTrendingAutoSlide = function(countryId) {
        stopTrendingAutoSlide(countryId);
        intervals[countryId] = setInterval(() => navigateTrendingCarousel(countryId, 1), 4000);
    };

    window.stopTrendingAutoSlide = function(countryId) {
        if (intervals[countryId]) {
            clearInterval(intervals[countryId]);
            intervals[countryId] = null;
        }
    };

    window.filterTrendingByRegion = function(regionId) {
        document.querySelectorAll('.trending-country-card').forEach(card => {
            card.style.display = (regionId === 'all' || card.dataset.region === regionId) ? '' : 'none';
        });
    };

    window.refreshTrending = function() { location.reload(); };

    function applyTrendingCountry(countryId) {
        const id      = parseInt(countryId) || 0;
        const cards   = document.querySelectorAll('.trending-country-card');
        const emptyEl = document.getElementById('trending-country-empty');
        let shown     = 0;

        cards.forEach(card => {
            const cardCountry = parseInt(card.getAttribute('data-country-id') || 0);
            const match = id === 0 || cardCountry === id;
            card.style.display = match ? '' : 'none';
            if (match) shown++;
        });

        if (emptyEl) emptyEl.classList.toggle('hidden', shown > 0);

        const regionSel = document.getElementById('trending-region-filter');
        if (regionSel) regionSel.value = 'all';
    }

    const _origSave = window.saveSelectedCountry;
    window.saveSelectedCountry = function(id) {
        if (_origSave) _origSave(id);
        applyTrendingCountry(id);
    };

    document.addEventListener('DOMContentLoaded', function () {

        if (window.HeroSlideshow) {
            const _orig = window.HeroSlideshow.applyCountry;
            window.HeroSlideshow.applyCountry = function(id) {
                _orig(id);
                applyTrendingCountry(id);
            };
        }

        document.querySelectorAll('.trending-carousel').forEach(carousel => {
            const cid    = carousel.dataset.country;
            const slides = carousel.querySelectorAll('.carousel-slide');
            states[cid]  = 0;

            if (ANIM === 'fade' || ANIM === 'flip') {
                const track = getTrack(cid);
                if (track) {
                    track.style.position = 'relative';
                    const firstSlide     = track.querySelector('.carousel-slide');
                    if (firstSlide) track.style.minHeight = firstSlide.offsetHeight + 'px';
                }
                slides.forEach((s, i) => {
                    s.style.position = 'absolute';
                    s.style.top      = '0';
                    s.style.left     = '0';
                    s.style.width    = '100%';
                    s.style.opacity  = i === 0 ? '1' : '0';
                    s.style.zIndex   = i === 0 ? '1' : '0';
                });
            }

            if (slides.length > 1) startTrendingAutoSlide(cid);

            const card = carousel.closest('.trending-country-card');
            if (card) {
                card.addEventListener('mouseenter', () => stopTrendingAutoSlide(cid));
                card.addEventListener('mouseleave', () => {
                    if (slides.length > 1) startTrendingAutoSlide(cid);
                });
            }
        });

        const saved = localStorage.getItem('uiselected_country');
        if (saved) applyTrendingCountry(saved);
    });

})();
</script>
@endpush

<style>
.trending-carousel .carousel-track { min-height: 1px; }
[data-anim="fade"]  .carousel-track,
[data-anim="flip"]  .carousel-track { display: block; }
[data-anim="slide"] .carousel-track,
[data-anim="none"]  .carousel-track { display: flex; }
</style>

@php endif; /* trendingActive */ @endphp
