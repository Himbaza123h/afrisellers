@php
    // ── UISection settings ────────────────────────────────────────
    $weeklySection     = \App\Models\UISection::where('section_key', 'weekly_special_offers')->first();
    $hotDealsSection   = \App\Models\UISection::where('section_key', 'hot_deals')->first();
    $suppliersSection  = \App\Models\UISection::where('section_key', 'most_recommended_suppliers')->first();

    $weeklyAnim    = $weeklySection?->getAnimationMode()   ?? 'none';
    $hotAnim       = $hotDealsSection?->getAnimationMode() ?? 'none';
    $suppliersAnim = $suppliersSection?->getAnimationMode() ?? 'none';

    $weeklyItems    = $weeklySection?->number_items    ?? 6;
    $hotItems       = $hotDealsSection?->number_items  ?? 6;
    $suppliersItems = $suppliersSection?->number_items ?? 5;

    $weeklyActive    = $weeklySection?->is_active    ?? true;
    $hotActive       = $hotDealsSection?->is_active  ?? true;
    $suppliersActive = $suppliersSection?->is_active ?? true;

    // ── Weekly Special Offers — load all, group by country ────────
    $weeklyOffers = collect([]);
    if ($weeklyActive) {
        $weeklyOffers = App\Models\Product::where('status', 'active')
            ->where('is_admin_verified', true)
            ->whereHas('addonUsers', function($query) {
                $query->whereNotNull('paid_at')
                    ->where(function($q) {
                        $q->whereNull('ended_at')->orWhere('ended_at', '>', now());
                    })
                    ->whereHas('addon', function($addonQuery) {
                        $addonQuery->where('locationX', 'Homepage')
                                   ->where('locationY', 'weeklyoffers');
                    });
            })
            ->with(['images', 'country', 'user.businessProfile', 'productCategory', 'prices'])
            ->limit(40)
            ->get();
    }

    // ── Hot Deals — load all, group by country ────────────────────
    $hotDeals = collect([]);
    if ($hotActive) {
        $hotDeals = App\Models\Product::where('status', 'active')
            ->where('is_admin_verified', true)
            ->whereHas('addonUsers', function($query) {
                $query->whereNotNull('paid_at')
                    ->where(function($q) {
                        $q->whereNull('ended_at')->orWhere('ended_at', '>', now());
                    })
                    ->whereHas('addon', function($addonQuery) {
                        $addonQuery->where('locationX', 'Homepage')
                                   ->where('locationY', 'hotdeals');
                    });
            })
            ->with(['images', 'productCategory', 'country', 'user.businessProfile', 'prices'])
            ->latest()
            ->limit(40)
            ->get();
    }

    // ── Most Recommended Suppliers — load all ─────────────────────
    $topSuppliers = collect([]);
    $supplierProducts = [];
    if ($suppliersActive) {
        $topSuppliers = App\Models\BusinessProfile::where('verification_status', 'verified')
            ->where('is_admin_verified', true)
            ->whereHas('addonUsers', function($query) {
                $query->whereNotNull('paid_at')
                    ->where(function($q) {
                        $q->whereNull('ended_at')->orWhere('ended_at', '>', now());
                    })
                    ->whereHas('addon', function($addonQuery) {
                        $addonQuery->where('locationX', 'Homepage')
                                   ->where('locationY', 'recommendedsuppliers');
                    });
            })
            ->with(['user', 'country'])
            ->withCount(['user as products_count' => function($query) {
                $query->select(\DB::raw('count(distinct products.id)'))
                    ->join('products', 'products.user_id', '=', 'users.id')
                    ->where('products.status', 'active')
                    ->where('products.is_admin_verified', true);
            }])
            ->orderBy('products_count', 'desc')
            ->limit(40)
            ->get();

        foreach($topSuppliers as $supplier) {
            $product = App\Models\Product::where('user_id', $supplier->user_id)
                ->where('status', 'active')
                ->where('is_admin_verified', true)
                ->with(['images' => function($query) {
                    $query->orderBy('is_primary', 'desc')->limit(1);
                }])
                ->first();
            $supplierProducts[$supplier->id] = $product;
        }
    }
@endphp

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- SECTIONS 1 & 2: Weekly Special Offers & Hot Deals             --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
@if($weeklyActive || $hotActive)
<div class="py-6 bg-blue-50 md:py-8">
    <div class="container px-4 mx-auto">
        <div class="flex flex-col gap-4 lg:flex-row md:gap-6">

            {{-- ── Section 1: Weekly Special Offers ─────────────── --}}
            @if($weeklyActive)
            <div class="flex-1">
                <div class="flex gap-2 items-center mb-3 md:mb-4">
                    <h2 class="text-base font-bold text-gray-900 whitespace-nowrap md:text-lg lg:text-xl">
                        {{ __('messages.weekly_special_offers') }}
                    </h2>
                    <div class="flex-1 h-px bg-gray-300"></div>
                    <a href="#" class="flex items-center gap-0.5 md:gap-1 text-[10px] md:text-xs font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors whitespace-nowrap">
                        <span>{{ __('messages.view_all') }}</span>
                        <svg class="w-2.5 h-2.5 md:w-3 md:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                {{-- Slide wrapper or grid --}}
                @if(in_array($weeklyAnim, ['slide','fade','flip']))
                {{-- ── CAROUSEL MODE ─────────────────────────────── --}}
                <div class="relative section-carousel" id="weekly-carousel"
                     data-anim="{{ $weeklyAnim }}"
                     data-items="{{ $weeklyItems }}"
                     data-section="weekly">
                    <div class="overflow-hidden section-carousel-track">
                        <div class="flex transition-transform duration-500 ease-in-out section-carousel-slides" id="weekly-slides">
                            @forelse($weeklyOffers as $product)
                                @php
                                    $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                    $businessProfile = $product->user->businessProfile ?? null;
                                    $price = $product->prices->first();
                                    $mainPrice = $price ? $price->min_price : 0;
                                    $maxPrice  = $price ? $price->max_price : 0;
                                    $currency  = $price ? $price->currency : 'RWF';
                                @endphp
                                <div class="flex-shrink-0 px-1 section-slide"
                                     data-country-id="{{ $product->country_id ?? 0 }}"
                                     style="width: calc(100% / {{ min(3, $weeklyItems) }})">
                                    @include('frontend.home.sections._product-card', [
                                        'product'         => $product,
                                        'image'           => $image,
                                        'businessProfile' => $businessProfile,
                                        'mainPrice'       => $mainPrice,
                                        'maxPrice'        => $maxPrice,
                                        'currency'        => $currency,
                                        'badge'           => '🔥 '.__('messages.tofl_et'),
                                        'showExporter'    => true,
                                    ])
                                </div>
                            @empty
                                <div class="py-8 w-full text-center text-gray-400">{{ __('messages.no_weekly_offers') }}</div>
                            @endforelse
                        </div>
                    </div>
                    @if($weeklyOffers->count() > $weeklyItems)
                    <button class="carousel-prev absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 z-10 w-7 h-7 bg-white shadow-md rounded-full flex items-center justify-center hover:bg-[#ff0808] hover:text-white transition-all text-gray-600 text-xs" data-target="weekly-carousel">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-next absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 z-10 w-7 h-7 bg-white shadow-md rounded-full flex items-center justify-center hover:bg-[#ff0808] hover:text-white transition-all text-gray-600 text-xs" data-target="weekly-carousel">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    @endif
                    {{-- Country empty notice --}}
                    <div class="hidden col-span-full py-8 text-xs text-center text-gray-400 section-empty-notice" id="weekly-empty">
                        No weekly offers available for the selected country.
                    </div>
                </div>

                @else
                {{-- ── GRID MODE ─────────────────────────────────── --}}
                <div class="relative">
                    <div class="grid grid-cols-2 gap-2 md:gap-3 lg:grid-cols-2 xl:grid-cols-3" id="weekly-grid">
                        @forelse($weeklyOffers as $product)
                            @php
                                $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                $businessProfile = $product->user->businessProfile ?? null;
                                $price = $product->prices->first();
                                $mainPrice = $price ? $price->min_price : 0;
                                $maxPrice  = $price ? $price->max_price : 0;
                                $currency  = $price ? $price->currency : 'RWF';
                            @endphp
                            <div class="section-card"
                                 data-country-id="{{ $product->country_id ?? 0 }}"
                                 data-section="weekly">
                                @include('frontend.home.sections._product-card', [
                                    'product'         => $product,
                                    'image'           => $image,
                                    'businessProfile' => $businessProfile,
                                    'mainPrice'       => $mainPrice,
                                    'maxPrice'        => $maxPrice,
                                    'currency'        => $currency,
                                    'badge'           => '🔥 '.__('messages.tofl_et'),
                                    'showExporter'    => true,
                                ])
                            </div>
                        @empty
                            <div class="col-span-full py-8 text-center" id="weekly-empty-static">
                                <svg class="mx-auto mb-2 w-10 h-10 text-gray-300 md:w-12 md:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                                <p class="text-xs font-semibold text-gray-700 md:text-sm">{{ __('messages.no_weekly_offers') }}</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="hidden col-span-full py-6 text-xs text-center text-gray-400" id="weekly-empty">
                        No weekly offers available for the selected country.
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- ── Section 2: Hot Deals ─────────────────────────── --}}
            @if($hotActive)
            <div class="flex-1">
                <div class="flex gap-2 items-center mb-3 md:mb-4">
                    <h2 class="text-base font-bold text-gray-900 whitespace-nowrap md:text-lg lg:text-xl">
                        {{ __('messages.hot_deals') }}
                    </h2>
                    <div class="flex-1 h-px bg-gray-300"></div>
                    <a href="#" class="flex items-center gap-0.5 md:gap-1 text-[10px] md:text-xs font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors whitespace-nowrap">
                        <span>{{ __('messages.view_all') }}</span>
                        <svg class="w-2.5 h-2.5 md:w-3 md:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                @if(in_array($hotAnim, ['slide','fade','flip']))
                {{-- ── CAROUSEL MODE ─────────────────────────────── --}}
                <div class="relative section-carousel" id="hot-carousel"
                     data-anim="{{ $hotAnim }}"
                     data-items="{{ $hotItems }}"
                     data-section="hot">
                    <div class="overflow-hidden section-carousel-track">
                        <div class="flex transition-transform duration-500 ease-in-out section-carousel-slides" id="hot-slides">
                            @forelse($hotDeals as $product)
                                @php
                                    $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                    $businessProfile = $product->user->businessProfile ?? null;
                                    $price = $product->prices->first();
                                    $mainPrice = $price ? $price->min_price : 0;
                                    $maxPrice  = $price ? $price->max_price : 0;
                                    $currency  = $price ? $price->currency : 'RWF';
                                @endphp
                                <div class="flex-shrink-0 px-1 section-slide"
                                     data-country-id="{{ $product->country_id ?? 0 }}"
                                     style="width: calc(100% / {{ min(3, $hotItems) }})">
                                    @include('frontend.home.sections._product-card', [
                                        'product'         => $product,
                                        'image'           => $image,
                                        'businessProfile' => $businessProfile,
                                        'mainPrice'       => $mainPrice,
                                        'maxPrice'        => $maxPrice,
                                        'currency'        => $currency,
                                        'badge'           => '🔥 '.__('messages.hot'),
                                        'showExporter'    => false,
                                    ])
                                </div>
                            @empty
                                <div class="py-8 w-full text-center text-gray-400">{{ __('messages.no_hot_deals_available') }}</div>
                            @endforelse
                        </div>
                    </div>
                    @if($hotDeals->count() > $hotItems)
                    <button class="carousel-prev absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 z-10 w-7 h-7 bg-white shadow-md rounded-full flex items-center justify-center hover:bg-[#ff0808] hover:text-white transition-all text-gray-600 text-xs" data-target="hot-carousel">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-next absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 z-10 w-7 h-7 bg-white shadow-md rounded-full flex items-center justify-center hover:bg-[#ff0808] hover:text-white transition-all text-gray-600 text-xs" data-target="hot-carousel">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    @endif
                    <div class="hidden py-8 text-xs text-center text-gray-400 section-empty-notice" id="hot-empty">
                        No hot deals available for the selected country.
                    </div>
                </div>

                @else
                {{-- ── GRID MODE ─────────────────────────────────── --}}
                <div class="relative">
                    <div class="grid grid-cols-2 gap-2 md:gap-3 lg:grid-cols-2 xl:grid-cols-3" id="hot-grid">
                        @forelse($hotDeals as $product)
                            @php
                                $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                $businessProfile = $product->user->businessProfile ?? null;
                                $price = $product->prices->first();
                                $mainPrice = $price ? $price->min_price : 0;
                                $maxPrice  = $price ? $price->max_price : 0;
                                $currency  = $price ? $price->currency : 'RWF';
                            @endphp
                            <div class="section-card"
                                 data-country-id="{{ $product->country_id ?? 0 }}"
                                 data-section="hot">
                                @include('frontend.home.sections._product-card', [
                                    'product'         => $product,
                                    'image'           => $image,
                                    'businessProfile' => $businessProfile,
                                    'mainPrice'       => $mainPrice,
                                    'maxPrice'        => $maxPrice,
                                    'currency'        => $currency,
                                    'badge'           => '🔥 '.__('messages.hot'),
                                    'showExporter'    => false,
                                ])
                            </div>
                        @empty
                            <div class="col-span-full py-8 text-center" id="hot-empty-static">
                                <svg class="mx-auto mb-2 w-10 h-10 text-gray-300 md:w-12 md:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                                <p class="text-xs font-semibold text-gray-700 md:text-sm">{{ __('messages.no_hot_deals_available') }}</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="hidden py-6 text-xs text-center text-gray-400" id="hot-empty">
                        No hot deals available for the selected country.
                    </div>
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- SECTION 3: Most Recommended Suppliers                         --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
@if($suppliersActive)
<section class="py-6 bg-white md:py-8">
    <div class="container px-4 mx-auto">
        <div class="flex gap-2 items-center mb-3 md:mb-4">
            <h2 class="text-base font-bold text-gray-900 whitespace-nowrap md:text-lg lg:text-xl">
                {{ __('messages.most_recommended_suppliers') }}
            </h2>
            <div class="flex-1 h-px bg-gray-300"></div>
            <a href="#" class="flex items-center gap-0.5 md:gap-1 text-[10px] md:text-xs font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors whitespace-nowrap">
                <span>{{ __('messages.view_all') }}</span>
                <svg class="w-2.5 h-2.5 md:w-3 md:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if(in_array($suppliersAnim, ['slide','fade','flip']))
        {{-- ── CAROUSEL MODE ─────────────────────────────────────── --}}
        <div class="relative section-carousel" id="suppliers-carousel"
             data-anim="{{ $suppliersAnim }}"
             data-items="{{ $suppliersItems }}"
             data-section="suppliers">
            <div class="overflow-hidden section-carousel-track">
                <div class="flex transition-transform duration-500 ease-in-out section-carousel-slides" id="suppliers-slides">
                    @forelse($topSuppliers as $supplier)
                        @php
                            $product = $supplierProducts[$supplier->id] ?? null;
                            $image   = $product && $product->images->count() > 0 ? $product->images->first() : null;
                        @endphp
                        <div class="flex-shrink-0 px-1 section-slide"
                             data-country-id="{{ $supplier->country_id ?? 0 }}"
                             style="width: calc(100% / {{ min(5, $suppliersItems) }})">
                            @include('frontend.home.sections._supplier-card', [
                                'supplier' => $supplier,
                                'image'    => $image,
                            ])
                        </div>
                    @empty
                        <div class="py-8 w-full text-center text-gray-400">{{ __('messages.no_suppliers_available') }}</div>
                    @endforelse
                </div>
            </div>
            @if($topSuppliers->count() > $suppliersItems)
            <button class="carousel-prev absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 z-10 w-7 h-7 bg-white shadow-md rounded-full flex items-center justify-center hover:bg-[#ff0808] hover:text-white transition-all text-gray-600 text-xs" data-target="suppliers-carousel">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-next absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 z-10 w-7 h-7 bg-white shadow-md rounded-full flex items-center justify-center hover:bg-[#ff0808] hover:text-white transition-all text-gray-600 text-xs" data-target="suppliers-carousel">
                <i class="fas fa-chevron-right"></i>
            </button>
            @endif
            <div class="hidden py-8 text-xs text-center text-gray-400 section-empty-notice" id="suppliers-empty">
                No suppliers available for the selected country.
            </div>
        </div>

        @else
        {{-- ── GRID MODE ─────────────────────────────────────────── --}}
        <div class="relative">
            <div class="grid grid-cols-2 gap-2 md:gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5" id="suppliers-grid">
                @forelse($topSuppliers as $supplier)
                    @php
                        $product = $supplierProducts[$supplier->id] ?? null;
                        $image   = $product && $product->images->count() > 0 ? $product->images->first() : null;
                    @endphp
                    <div class="section-card"
                         data-country-id="{{ $supplier->country_id ?? 0 }}"
                         data-section="suppliers">
                        @include('frontend.home.sections._supplier-card', [
                            'supplier' => $supplier,
                            'image'    => $image,
                        ])
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center" id="suppliers-empty-static">
                        <svg class="mx-auto mb-2 w-10 h-10 text-gray-300 md:w-12 md:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-xs font-semibold text-gray-700 md:text-sm">{{ __('messages.no_suppliers_available') }}</p>
                    </div>
                @endforelse
            </div>
            <div class="hidden py-6 text-xs text-center text-gray-400" id="suppliers-empty">
                No suppliers available for the selected country.
            </div>
        </div>
        @endif
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- REUSABLE PRODUCT CARD PARTIAL (inline — no separate file)     --}}
{{-- If you use @include above, create these as actual partials.   --}}
{{-- ══════════════════════════════════════════════════════════════ --}}

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════════
// SECTION COUNTRY FILTER
// Listens for country selection (same localStorage key as hero)
// and shows/hides .section-card items and carousel slides
// ══════════════════════════════════════════════════════════════
(function() {

    // How many items each section shows at once (from PHP)
    const sectionLimits = {
        weekly:    {{ $weeklyItems }},
        hot:       {{ $hotItems }},
        suppliers: {{ $suppliersItems }},
    };

    // ── Filter grid-mode cards ───────────────────────────────────
    function filterGridSection(sectionKey, countryId) {
        const cards   = document.querySelectorAll(`.section-card[data-section="${sectionKey}"]`);
        const emptyEl = document.getElementById(`${sectionKey}-empty`);
        const limit   = sectionLimits[sectionKey] ?? 6;

        let shown = 0;
        cards.forEach(card => {
            const cardCountry = parseInt(card.getAttribute('data-country-id') || 0);
            const match = !countryId || cardCountry === countryId;
            if (match && shown < limit) {
                card.style.display = '';
                shown++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyEl) emptyEl.classList.toggle('hidden', shown > 0);
    }

    // ── Filter carousel-mode slides ──────────────────────────────
    function filterCarouselSection(carouselId, countryId) {
        const carousel  = document.getElementById(carouselId);
        if (!carousel) return;

        const slides    = Array.from(carousel.querySelectorAll('.section-slide'));
        const emptyEl   = carousel.querySelector('.section-empty-notice');
        const sectionKey = carousel.getAttribute('data-section');
        const limit     = sectionLimits[sectionKey] ?? 6;

        // Filter matching slides
        const matching = slides.filter(s => {
            const c = parseInt(s.getAttribute('data-country-id') || 0);
            return !countryId || c === countryId;
        });

        // Hide non-matching, show matching up to limit
        slides.forEach(s => s.classList.add('carousel-hidden'));
        matching.slice(0, limit).forEach(s => s.classList.remove('carousel-hidden'));

        if (emptyEl) emptyEl.classList.toggle('hidden', matching.length > 0);

        // Reset position
        const track = carousel.querySelector('.section-carousel-slides');
        if (track) {
            track.style.transform = 'translateX(0)';
            carouselStates[carouselId] = { current: 0 };
        }
    }

    // ── Apply country to all sections ───────────────────────────
    function applySectionCountry(countryId) {
        const id = parseInt(countryId) || 0;
        const cid = id === 0 ? null : id;

        // Grid sections
        filterGridSection('weekly',    cid);
        filterGridSection('hot',       cid);
        filterGridSection('suppliers', cid);

        // Carousel sections
        filterCarouselSection('weekly-carousel',    cid);
        filterCarouselSection('hot-carousel',       cid);
        filterCarouselSection('suppliers-carousel', cid);
    }

    // ── Carousel logic ───────────────────────────────────────────
    const carouselStates = {};

    function getVisibleSlides(carouselId) {
        const carousel = document.getElementById(carouselId);
        if (!carousel) return [];
        return Array.from(carousel.querySelectorAll('.section-slide:not(.carousel-hidden)'));
    }

    function moveCarousel(carouselId, direction) {
        const carousel  = document.getElementById(carouselId);
        if (!carousel) return;

        const sectionKey  = carousel.getAttribute('data-section');
        const visibleCount = sectionLimits[sectionKey] ?? 3;
        const slides      = getVisibleSlides(carouselId);
        const track       = carousel.querySelector('.section-carousel-slides');
        const anim        = carousel.getAttribute('data-anim');

        if (!slides.length || !track) return;

        if (!carouselStates[carouselId]) carouselStates[carouselId] = { current: 0 };
        const state = carouselStates[carouselId];

        const maxStep = Math.max(0, slides.length - visibleCount);
        state.current = Math.max(0, Math.min(state.current + direction, maxStep));

        const slideWidth = slides[0]?.offsetWidth ?? 0;
        const offset     = -(state.current * slideWidth);

        if (anim === 'fade') {
            // Fade: show only the current page of slides
            slides.forEach((s, i) => {
                const inView = i >= state.current && i < state.current + visibleCount;
                s.style.transition = 'opacity 0.5s ease';
                s.style.opacity    = inView ? '1' : '0';
                s.style.pointerEvents = inView ? '' : 'none';
            });
        } else if (anim === 'flip') {
            track.style.transition = 'transform 0.6s cubic-bezier(0.77,0,0.175,1)';
            track.style.transform  = `translateX(${offset}px)`;
        } else {
            // slide (default)
            track.style.transition = 'transform 0.5s ease-in-out';
            track.style.transform  = `translateX(${offset}px)`;
        }
    }

    // Wire carousel arrow buttons
    document.querySelectorAll('.carousel-prev, .carousel-next').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const dir      = this.classList.contains('carousel-prev') ? -1 : 1;
            moveCarousel(targetId, dir);
        });
    });

    // Auto-slide for carousels with animation enabled
    @if(in_array($weeklyAnim, ['slide','fade','flip']))
    setInterval(() => moveCarousel('weekly-carousel', 1), 5000);
    @endif
    @if(in_array($hotAnim, ['slide','fade','flip']))
    setInterval(() => moveCarousel('hot-carousel', 1), 5500);
    @endif
    @if(in_array($suppliersAnim, ['slide','fade','flip']))
    setInterval(() => moveCarousel('suppliers-carousel', 1), 6000);
    @endif

    // ── Hook into global country selector ───────────────────────
    // Patch saveSelectedCountry to also update sections
    const _origSave = window.saveSelectedCountry;
    window.saveSelectedCountry = function(id) {
        if (_origSave) _origSave(id);
        applySectionCountry(id);
    };

    // Also patch HeroSlideshow.applyCountry if available
    document.addEventListener('DOMContentLoaded', function() {
        if (window.HeroSlideshow) {
            const _origApply = window.HeroSlideshow.applyCountry;
            window.HeroSlideshow.applyCountry = function(id) {
                _origApply(id);
                applySectionCountry(id);
            };
        }

        // Apply saved country on load
        const saved = localStorage.getItem('uiselected_country');
        if (saved) applySectionCountry(saved);
    });

})();
</script>
@endpush

<style>
/* Carousel hidden state */
.section-slide.carousel-hidden {
    visibility: hidden;
    pointer-events: none;
    position: absolute;
}
/* Carousel track: relative container so absolute-hidden slides don't shift layout */
.section-carousel-slides {
    position: relative;
}
/* Fade carousel: all slides stacked */
[data-anim="fade"] .section-carousel-slides {
    display: grid;
}
[data-anim="fade"] .section-slide {
    grid-area: 1 / 1;
    opacity: 0;
    transition: opacity 0.5s ease;
}
[data-anim="fade"] .section-slide:not(.carousel-hidden) {
    opacity: 1;
    position: relative;
}
</style>
