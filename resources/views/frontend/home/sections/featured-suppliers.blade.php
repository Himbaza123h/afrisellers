@php
    // ── UISection settings ────────────────────────────────────────
    $recSuppliersSection = \App\Models\UISection::where('section_key', 'recommended_suppliers')->first();
    $recSuppliersAnim    = $recSuppliersSection?->getAnimationMode() ?? 'none';
    $recSuppliersItems   = $recSuppliersSection?->number_items ?? 10;
    $recSuppliersActive  = $recSuppliersSection?->is_active ?? true;

    if ($recSuppliersActive):

    // ── Regions ───────────────────────────────────────────────────
    $supplierRegions = App\Models\Region::where('status', 'active')
        ->orderByRaw("CASE name
            WHEN 'All Regions'     THEN 1
            WHEN 'East Africa'     THEN 2
            WHEN 'West Africa'     THEN 3
            WHEN 'Southern Africa' THEN 4
            WHEN 'North Africa'    THEN 5
            WHEN 'Central Africa'  THEN 6
            WHEN 'Region Diaspora' THEN 7
            ELSE 8 END")
        ->get();

    // ── Verified business profiles with addon ─────────────────────
    $verifiedBusinessProfiles = App\Models\BusinessProfile::where('verification_status', 'verified')
        ->where('is_admin_verified', true)
        ->whereHas('addonUsers', function($query) {
            $query->whereNotNull('paid_at')
                ->where(function($q) {
                    $q->whereNull('ended_at')->orWhere('ended_at', '>', now());
                })
                ->whereHas('addon', function($addonQuery) {
                    $addonQuery->where('locationX', 'Homepage')
                               ->where('locationY', 'featuredsuppliers');
                });
        })
        ->with(['user', 'country.region', 'vendor'])
        ->get();

    $userIds = $verifiedBusinessProfiles->pluck('user_id');

    $userProducts = App\Models\Product::whereIn('user_id', $userIds)
        ->where('status', 'active')
        ->where('is_admin_verified', true)
        ->with([
            'images' => function($q) { $q->orderBy('is_primary','desc')->orderBy('sort_order','asc')->limit(1); },
            'productCategory',
        ])
        ->select('user_id','id','product_category_id')
        ->get()
        ->groupBy('user_id')
        ->map(fn($p) => $p->first());

    $allReviews = App\Models\ProductUserReview::whereHas('product', function($q) use ($userIds) {
            $q->whereIn('user_id', $userIds)->where('status','active')->where('is_admin_verified', true);
        })
        ->where('status', true)
        ->get()
        ->groupBy(fn($r) => $r->product->user_id);

    $productsCount = App\Models\Product::whereIn('user_id', $userIds)
        ->where('status','active')->where('is_admin_verified', true)
        ->select('user_id', \DB::raw('count(*) as count'))
        ->groupBy('user_id')
        ->pluck('count','user_id');

    $suppliersByCountry = $verifiedBusinessProfiles->groupBy('country_id')->map(
        function($profiles) use ($userProducts, $allReviews, $productsCount) {
            return $profiles->map(function($profile) use ($userProducts, $allReviews, $productsCount) {
                $firstProduct = $userProducts->get($profile->user_id);
                $productImage = $firstProduct?->images->count() > 0 ? $firstProduct->images->first() : null;
                $categoryName = $firstProduct?->productCategory?->name ?? __('messages.supplier');
                $userReviews  = $allReviews->get($profile->user_id, collect());
                return [
                    'profile'       => $profile,
                    'image'         => $productImage,
                    'category'      => $categoryName,
                    'avgRating'     => $userReviews->count() > 0 ? $userReviews->avg('mark') : 0,
                    'reviewsCount'  => $userReviews->count(),
                    'productsCount' => $productsCount->get($profile->user_id, 0),
                ];
            });
        }
    );

    $countriesWithSuppliers = App\Models\Country::whereIn('id', $suppliersByCountry->keys())
        ->with('region')
        ->get()
        ->keyBy('id');
@endphp

<section class="py-6 md:py-8 bg-gray-50" id="rec-suppliers-section">
    <div class="container px-4 mx-auto">

        {{-- ── Header ───────────────────────────────────────────── --}}
        <div class="flex flex-col gap-2 mb-3 md:mb-4 md:flex-row md:justify-between md:items-center">
            <div class="flex items-center gap-2 flex-1">
                <h2 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 whitespace-nowrap">
                    {{ __('messages.recommended_suppliers') }}
                </h2>
                <div class="flex-1 h-px bg-gray-300"></div>
            </div>

            <div class="flex flex-wrap gap-1.5">
                {{-- Show All --}}
                <div class="relative">
                    <select class="px-2 py-1 md:px-2.5 md:py-1.5 text-[10px] md:text-xs font-medium text-[#ff0808] bg-white border border-[#ff0808] rounded hover:bg-[#fff5f5] transition-colors appearance-none pr-6 md:pr-7 cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#ff0808] focus:border-transparent"
                            onchange="if(this.value) window.location.href=this.value">
                        <option value="">{{ __('messages.show_all') }}</option>
                        <option value="{{ route('featured-suppliers') }}">{{ __('messages.show_all_in_all_regions') }}</option>
                        @foreach($supplierRegions as $region)
                            <option value="{{ route('featured-suppliers', ['region' => $region->id]) }}">
                                {{ __('messages.show_all_in') }} {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-1.5 md:pr-2 pointer-events-none">
                        <svg class="w-2.5 h-2.5 md:w-3 md:h-3 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Region filter --}}
                <div class="relative">
                    <select id="supplier-region-filter"
                            class="px-2 py-1 md:px-2.5 md:py-1.5 text-[10px] md:text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors appearance-none pr-6 md:pr-7 cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#ff0808] focus:border-transparent"
                            onchange="filterSuppliersByRegion(this.value)">
                        <option value="all">{{ __('messages.all_regions') }}</option>
                        @foreach($supplierRegions as $region)
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
                <button onclick="refreshSuppliers()" class="p-1 md:p-1.5 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Country cards grid ────────────────────────────────── --}}
        <div id="rec-suppliers-grid"
             class="grid grid-cols-2 gap-2 md:gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">

            @forelse($countriesWithSuppliers as $country)
                @php
                    $suppliers = $suppliersByCountry->get($country->id);
                    $regionId  = $country->region_id ?? 'none';
                @endphp

                <div class="country-card bg-white rounded-lg border border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all"
                     data-region="{{ $regionId }}"
                     data-country-id="{{ $country->id }}">

                    {{-- Country header --}}
                    <div class="p-1.5 md:p-2 border-b border-gray-200">
                        <div class="flex items-center gap-1 md:gap-1.5">
                            @if($country->flag_url)
                                <img src="{{ $country->flag_url }}" alt="{{ $country->name }}"
                                     class="w-4 h-3 md:w-5 md:h-3 rounded shadow-sm object-cover flex-shrink-0">
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="text-[9px] md:text-[10px] font-bold text-gray-900 truncate">{{ $country->name }}</h3>
                                <p class="text-[8px] md:text-[9px] text-gray-600">
                                    {{ $suppliers->count() }}
                                    {{ $suppliers->count() > 1 ? __('messages.suppliers') : __('messages.supplier') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Supplier carousel --}}
                    <div class="relative">

                        <div class="supplier-carousel overflow-hidden"
                             data-country="{{ $country->id }}"
                             data-anim="{{ $recSuppliersAnim }}">

                            <div class="carousel-track flex transition-transform duration-500 ease-in-out"
                                 id="sup-track-{{ $country->id }}">

                                @foreach($suppliers as $index => $supplierData)
                                    <div class="carousel-slide w-full flex-shrink-0"
                                         style="@if(in_array($recSuppliersAnim,['fade','flip'])) position:absolute;top:0;left:0;opacity:{{ $index===0?'1':'0' }};z-index:{{ $index===0?'1':'0' }};transition:opacity 0.5s ease,transform 0.6s cubic-bezier(0.77,0,0.175,1); @if($recSuppliersAnim==='flip') transform:{{ $index===0?'rotateY(0deg)':'rotateY(90deg)' }}; @endif @endif">
                                        <div class="group">
                                            <a href="{{ route('country.business-profiles', $country->id) }}"
                                               class="block relative h-24 md:h-32 overflow-hidden">
                                                @if($supplierData['image'])
                                                    <img src="{{ $supplierData['image']->image_url }}"
                                                         alt="{{ $supplierData['profile']->business_name }}"
                                                         class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110"
                                                         loading="lazy">
                                                @else
                                                    <div class="flex justify-center items-center w-full h-full bg-gray-100">
                                                        <span class="text-xl md:text-2xl">🏢</span>
                                                    </div>
                                                @endif
                                                <span class="absolute top-1 right-1 md:top-2 md:right-2 bg-green-600 text-white text-[8px] md:text-[10px] font-semibold px-1 md:px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                                    <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span>{{ __('messages.verified') }}</span>
                                                </span>
                                            </a>

                                            <div class="p-1.5 md:p-2">
                                                <a href="{{ route('country.business-profiles', $country->id) }}">
                                                    <h4 class="text-[9px] md:text-[10px] font-bold text-gray-900 mb-1 group-hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[1.5rem]">
                                                        {{ $supplierData['profile']->business_name }}
                                                    </h4>
                                                </a>

                                                <div class="mb-1">
                                                    <span class="inline-flex items-center gap-0.5 text-[7px] md:text-[8px] font-medium text-purple-600 bg-purple-50 px-1 py-0.5 rounded">
                                                        <svg class="w-1.5 h-1.5 md:w-2 md:h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                        </svg>
                                                        <span class="truncate">{{ $supplierData['category'] }}</span>
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1 text-[7px] md:text-[8px] text-gray-600 mb-1">
                                                    <a href="{{ route('country.business-profiles', $country->id) }}"
                                                       class="flex items-center gap-0.5 hover:opacity-80 transition-opacity">
                                                        <div class="bg-gray-800 text-white text-[7px] md:text-[8px] font-bold px-1 py-0.5 rounded">
                                                            {{ number_format($supplierData['productsCount']) }}
                                                        </div>
                                                        <span class="font-medium">{{ Str::plural('Product', $supplierData['productsCount']) }}</span>
                                                    </a>
                                                    @if($supplierData['avgRating'] > 0)
                                                    <a href="{{ route('country.business-profiles', $country->id) }}"
                                                       class="flex items-center gap-0.5 hover:opacity-80 transition-opacity">
                                                        <div class="bg-yellow-400 text-gray-900 text-[7px] md:text-[8px] font-bold px-1 py-0.5 rounded flex items-center gap-0.5">
                                                            <svg class="w-1.5 h-1.5 md:w-2 md:h-2" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                            {{ number_format($supplierData['avgRating'], 1) }}
                                                        </div>
                                                        <span class="font-medium">({{ $supplierData['reviewsCount'] }})</span>
                                                    </a>
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-0.5 text-[7px] md:text-[8px] text-gray-600">
                                                    <svg class="w-2 h-2 md:w-2.5 md:h-2.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="font-medium truncate">{{ $supplierData['profile']->city }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>{{-- /carousel-track --}}
                        </div>{{-- /supplier-carousel --}}

                        {{-- Arrows + dots --}}
                        @if($suppliers->count() > 1)
                            <button onclick="stopSupAutoSlide('{{ $country->id }}'); navSupCarousel('{{ $country->id }}', -1);"
                                class="absolute left-0.5 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 rounded-full p-0.5 md:p-1 shadow-md transition-all hover:shadow-xl z-10">
                                <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button onclick="stopSupAutoSlide('{{ $country->id }}'); navSupCarousel('{{ $country->id }}', 1);"
                                class="absolute right-0.5 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 rounded-full p-0.5 md:p-1 shadow-md transition-all hover:shadow-xl z-10">
                                <svg class="w-2 h-2 md:w-2.5 md:h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <div class="flex justify-center gap-0.5 md:gap-1 py-1 md:py-1.5">
                                @foreach($suppliers as $index => $supplier)
                                    <button onclick="stopSupAutoSlide('{{ $country->id }}'); goToSupSlide('{{ $country->id }}', {{ $index }});"
                                        class="sup-dot w-0.5 h-0.5 md:w-1 md:h-1 rounded-full bg-gray-300 transition-all {{ $index === 0 ? 'bg-blue-600 !w-2 md:!w-3' : '' }}"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <div>
                            <p class="text-base md:text-lg font-semibold text-gray-700 mb-1">{{ __('messages.no_suppliers_available') }}</p>
                            <p class="text-xs md:text-sm text-gray-500">{{ __('messages.no_suppliers_description') }}</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Country-filter empty notice --}}
        <div id="rec-suppliers-country-empty" class="hidden py-10 text-center text-xs text-gray-400">
            No suppliers available for the selected country.
        </div>

    </div>
</section>

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════════
// RECOMMENDED SUPPLIERS — carousel + country/region filter
// ══════════════════════════════════════════════════════════════
(function () {

    const ANIM = '{{ $recSuppliersAnim }}'; // slide | fade | flip | none

    const states    = {};  // countryId → current index
    const intervals = {};  // countryId → interval id

    function getSlides(cid) {
        const c = document.querySelector(`.supplier-carousel[data-country="${cid}"]`);
        return c ? Array.from(c.querySelectorAll('.carousel-slide')) : [];
    }
    function getTrack(cid) { return document.getElementById(`sup-track-${cid}`); }

    // ── Update display ───────────────────────────────────────────
    function updateSupCarousel(cid) {
        const slides = getSlides(cid);
        const idx    = states[cid] ?? 0;
        const track  = getTrack(cid);
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
                s.style.opacity    = i === idx ? '1' : '0';
                s.style.zIndex     = i === idx ? '1' : '0';
                s.style.transform  = i === idx ? 'rotateY(0deg)' : 'rotateY(90deg)';
            });

        } else {
            track.style.transition = ANIM === 'none' ? 'none' : 'transform 0.5s ease-in-out';
            track.style.transform  = `translateX(-${idx * 100}%)`;
        }

        // Dots
        document.querySelectorAll(`.sup-dot[data-country="${cid}"]`).forEach((dot, i) => {
            if (i === idx) {
                dot.classList.add('bg-blue-600');
                dot.classList.remove('bg-gray-300');
                dot.style.width = '12px';
            } else {
                dot.classList.remove('bg-blue-600');
                dot.classList.add('bg-gray-300');
                dot.style.width = '';
            }
        });
    }

    // ── Navigation ───────────────────────────────────────────────
    window.navSupCarousel = function(cid, dir) {
        const slides = getSlides(cid);
        if (!slides.length) return;
        states[cid] = ((states[cid] ?? 0) + dir + slides.length) % slides.length;
        updateSupCarousel(cid);
    };

    window.goToSupSlide = function(cid, index) {
        states[cid] = index;
        updateSupCarousel(cid);
    };

    // ── Auto-slide ───────────────────────────────────────────────
    window.startSupAutoSlide = function(cid) {
        stopSupAutoSlide(cid);
        intervals[cid] = setInterval(() => navSupCarousel(cid, 1), 4000);
    };

    window.stopSupAutoSlide = function(cid) {
        if (intervals[cid]) { clearInterval(intervals[cid]); intervals[cid] = null; }
    };

    // ── Region filter ────────────────────────────────────────────
    window.filterSuppliersByRegion = function(regionId) {
        document.querySelectorAll('.country-card').forEach(card => {
            card.style.display = (regionId === 'all' || card.dataset.region === regionId) ? '' : 'none';
        });
    };

    window.refreshSuppliers = function() { location.reload(); };

    // ── Country filter (top-bar selector) ───────────────────────
    function applyRecSuppliersCountry(countryId) {
        const id      = parseInt(countryId) || 0;
        const cards   = document.querySelectorAll('#rec-suppliers-section .country-card');
        const emptyEl = document.getElementById('rec-suppliers-country-empty');
        let shown     = 0;

        cards.forEach(card => {
            const match = id === 0 || parseInt(card.getAttribute('data-country-id') || 0) === id;
            card.style.display = match ? '' : 'none';
            if (match) shown++;
        });

        if (emptyEl) emptyEl.classList.toggle('hidden', shown > 0);

        // Reset region dropdown
        const regionSel = document.getElementById('supplier-region-filter');
        if (regionSel) regionSel.value = 'all';
    }

    // ── Hook into global country selector ────────────────────────
    const _origSave = window.saveSelectedCountry;
    window.saveSelectedCountry = function(id) {
        if (_origSave) _origSave(id);
        applyRecSuppliersCountry(id);
    };

    // ── Boot ─────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {

        if (window.HeroSlideshow) {
            const _orig = window.HeroSlideshow.applyCountry;
            window.HeroSlideshow.applyCountry = function(id) {
                _orig(id);
                applyRecSuppliersCountry(id);
            };
        }

        document.querySelectorAll('.supplier-carousel').forEach(carousel => {
            const cid    = carousel.dataset.country;
            const slides = carousel.querySelectorAll('.carousel-slide');
            states[cid]  = 0;

            // Fade / flip: stack slides
            if (ANIM === 'fade' || ANIM === 'flip') {
                const track = getTrack(cid);
                if (track) {
                    track.style.position   = 'relative';
                    track.style.display    = 'block';
                    const first = track.querySelector('.carousel-slide');
                    if (first) track.style.minHeight = first.offsetHeight + 'px';
                }
                slides.forEach((s, i) => {
                    s.style.position = 'absolute';
                    s.style.top      = '0';
                    s.style.left     = '0';
                    s.style.width    = '100%';
                    s.style.opacity  = i === 0 ? '1' : '0';
                    s.style.zIndex   = i === 0 ? '1' : '0';
                    if (ANIM === 'flip') s.style.transform = i === 0 ? 'rotateY(0deg)' : 'rotateY(90deg)';
                });
            }

            if (slides.length > 1) startSupAutoSlide(cid);

            const card = carousel.closest('.country-card');
            if (card) {
                card.addEventListener('mouseenter', () => stopSupAutoSlide(cid));
                card.addEventListener('mouseleave', () => {
                    if (slides.length > 1) startSupAutoSlide(cid);
                });
            }
        });

        // Apply saved country on load
        const saved = localStorage.getItem('uiselected_country');
        if (saved) applyRecSuppliersCountry(saved);
    });

})();
</script>
@endpush

<style>
[data-anim="fade"]  #rec-suppliers-section .carousel-track,
[data-anim="flip"]  #rec-suppliers-section .carousel-track { display: block; }
[data-anim="slide"] #rec-suppliers-section .carousel-track,
[data-anim="none"]  #rec-suppliers-section .carousel-track { display: flex; }
</style>

@php endif; /* recSuppliersActive */ @endphp
