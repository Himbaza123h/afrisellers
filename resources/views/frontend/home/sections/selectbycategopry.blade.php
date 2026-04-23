{{--
|──────────────────────────────────────────────────────────────────────────────
|  Homepage Section: Select by Category  (category-products-section.blade.php)
|  Include in your home view with:  @include('frontend.sections.category-products-section')
|──────────────────────────────────────────────────────────────────────────────
--}}

@php
    // ── UISection guard ───────────────────────────────────────────────────────
    $catProdSection       = \App\Models\UISection::where('section_key', 'category_products')->first();
    $catProdSectionActive = $catProdSection?->is_active ?? true;
    $catProdItemsPerPage  = $catProdSection?->number_items ?? 6; // 2 cols × 3 rows = 6 per page

    if ($catProdSectionActive):

    // ── Load active top-level categories ordered by product count DESC ────────
    $homepageCategories = \App\Models\ProductCategory::where('status', 'active')
        ->withCount(['products' => function ($q) {
            $q->where('status', 'active')->where('is_admin_verified', true);
        }])
        ->having('products_count', '>', 0)
        ->orderByDesc('products_count') // most products first → left to right
        ->get();

    // ── Pre-load products for every category (2 cols × 3 rows = 6 per page) ──
    $catProductsMap = [];
    foreach ($homepageCategories as $cat) {
        $catProductsMap[$cat->id] = \App\Models\Product::where('product_category_id', $cat->id)
            ->where('status', 'active')
            ->where('is_admin_verified', true)
            ->with([
                'images'      => fn($q) => $q->orderBy('is_primary','desc')->orderBy('sort_order','asc')->limit(1),
                'country',
                'user.vendor.businessProfile',
                'prices'      => fn($q) => $q->orderBy('min_qty','asc'),
            ])
            ->latest()
            ->limit(max($catProdItemsPerPage, 18)) // grab up to 18 so carousel has 3 pages
            ->get();
    }

    $catCurrencySymbols = [
        'USD' => '$', 'EUR' => '€', 'GBP' => '£',
        'RWF' => 'RF', 'KES' => 'KSh', 'UGX' => 'USh', 'TZS' => 'TSh',
    ];

@endphp

@if($homepageCategories->isNotEmpty())
<section class="py-6 md:py-10 bg-gray-50" id="cat-products-section">
    <div class="container px-4 mx-auto">

        {{-- ── Section Header ────────────────────────────────────────────── --}}
        <div class="flex flex-col gap-2 mb-4 md:mb-5 md:flex-row md:justify-between md:items-center">
            <div class="flex items-center gap-3 flex-1">
                <div class="w-1 h-6 bg-[#ff0808] rounded-full flex-shrink-0"></div>
                <h2 class="text-base md:text-lg lg:text-xl font-black text-gray-900 whitespace-nowrap tracking-tight">
                    Select by Category
                </h2>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>
            {{-- <a href="{{ route('home') }}"
               class="self-start text-[11px] font-semibold text-[#ff0808] hover:underline flex items-center gap-1 flex-shrink-0">
                View All Categories
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a> --}}
        </div>

        {{-- ── Category Tab Bar ───────────────────────────────────────────── --}}
        <div class="relative mb-6">
            <div class="flex gap-1.5 overflow-x-auto scrollbar-hide" id="cat-tab-bar">
                @foreach($homepageCategories as $index => $cat)
                <button
                    class="cat-tab relative flex-shrink-0 px-3 py-1.5 md:px-4 md:py-2 rounded-md text-xs md:text-sm font-bold transition-all duration-200 whitespace-nowrap border
                           {{ $index === 0
                               ? 'bg-[#ff0808] text-white border-[#ff0808] shadow-md cat-tab--active'
                               : 'bg-white text-gray-700 border-gray-200 hover:border-[#ff0808]/40 hover:text-[#ff0808]' }}"
                    data-cat-id="{{ $cat->id }}"
                    onclick="switchCatTab({{ $cat->id }}, this)">
                    {{ $cat->name }}
                    <span class="ml-1 text-[9px] font-medium opacity-70">({{ number_format($cat->products_count) }})</span>
                </button>
                @endforeach
            </div>
            {{-- Fade hint on right --}}
            <div class="absolute right-0 top-0 bottom-2 w-8 bg-gradient-to-l from-gray-50 to-transparent pointer-events-none md:hidden"></div>
        </div>

        {{-- ── Per-category product carousels ────────────────────────────── --}}
        @foreach($homepageCategories as $index => $cat)
        @php $catProds = $catProductsMap[$cat->id] ?? collect(); @endphp

        <div class="cat-panel {{ $index === 0 ? '' : 'hidden' }}"
             data-cat-panel="{{ $cat->id }}">

            @if($catProds->isNotEmpty())

            {{-- carousel wrapper --}}
            <div class="relative cat-carousel-wrap" data-cat-carousel="{{ $cat->id }}">

                {{-- Left Arrow --}}
                <button onclick="navCatCarousel({{ $cat->id }}, -1)"
                    class="cat-prev-{{ $cat->id }} absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 md:-translate-x-4 z-20
                           w-7 h-7 md:w-8 md:h-8 bg-white border border-gray-200 hover:border-[#ff0808] hover:text-[#ff0808]
                           text-gray-600 rounded-sm shadow-md flex items-center justify-center transition-all duration-200 opacity-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Right Arrow --}}
                <button onclick="navCatCarousel({{ $cat->id }}, 1)"
                    class="cat-next-{{ $cat->id }} absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 md:translate-x-4 z-20
                           w-7 h-7 md:w-8 md:h-8 bg-white border border-gray-200 hover:border-[#ff0808] hover:text-[#ff0808]
                           text-gray-600 rounded-sm shadow-md flex items-center justify-center transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                {{-- Overflow viewport --}}
                <div class="overflow-hidden rounded-md">
                    <div class="cat-track flex transition-transform duration-500 ease-in-out"
                         id="cat-track-{{ $cat->id }}">

                        {{-- Chunk products into pages of 6 (2 cols × 3 rows) ──── --}}
                        @foreach($catProds->chunk(6) as $pageIndex => $pageProducts)
                        <div class="cat-page w-full flex-shrink-0">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                                @foreach($pageProducts as $product)
                                @php
                                    $img          = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                    $firstPrice   = $product->prices->sortBy('min_qty')->first();
                                    $currency     = $firstPrice->currency ?? 'USD';
                                    $symbol       = $catCurrencySymbols[$currency] ?? $currency;
                                    $finalPrice   = $firstPrice ? ($firstPrice->price - ($firstPrice->discount ?? 0)) : null;
                                    $hasDiscount  = $firstPrice && ($firstPrice->discount ?? 0) > 0;
                                    $bizProfile   = optional(optional(optional($product->user)->vendor)->businessProfile);
                                @endphp

                                {{-- Horizontal card: image left, content right --}}
                                <div class="bg-white border border-gray-200 overflow-hidden
                                            hover:shadow-md hover:border-[#ff0808]/30
                                            transition-all duration-300 group rounded-md flex flex-row">

                                    {{-- Product Image (fixed width on the left) --}}
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="relative flex-shrink-0 bg-gray-50 overflow-hidden"
                                       style="width: 110px; min-height: 110px;">
                                        @if($img)
                                            <img src="{{ $img->image_url }}"
                                                 alt="{{ $product->name }}"
                                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                 loading="lazy">
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center text-3xl bg-gray-100">📦</div>
                                        @endif

                                        {{-- Badges --}}
                                        <div class="absolute top-1 left-1 flex flex-col gap-0.5">
                                            @if($product->ready_to_ship)
                                                <span class="px-1 py-0.5 bg-green-500 text-white text-[7px] font-bold rounded-sm leading-none">Ready</span>
                                            @endif
                                            @if($hasDiscount)
                                                <span class="px-1 py-0.5 bg-[#ff0808] text-white text-[7px] font-bold rounded-sm leading-none">Sale</span>
                                            @endif
                                        </div>
                                    </a>

                                    {{-- Card Body --}}
                                    <div class="p-3 flex flex-col flex-1 min-w-0">

                                        {{-- Product name --}}
                                        <a href="{{ route('products.show', $product->slug) }}">
                                            <h4 class="text-xs md:text-sm font-bold text-gray-900 line-clamp-1
                                                        group-hover:text-[#ff0808] transition-colors mb-0.5 leading-tight">
                                                {{ $product->name }}
                                            </h4>
                                        </a>

                                        {{-- Short description if available --}}
                                        @if($product->short_description ?? $product->description ?? false)
                                        <p class="text-[10px] text-gray-500 line-clamp-1 mb-1.5">
                                            {{ Str::limit(strip_tags($product->short_description ?? $product->description ?? ''), 60) }}
                                        </p>
                                        @endif

                                        {{-- Supplier + flag --}}
                                        @if($bizProfile->business_name ?? false)
                                        <p class="text-[10px] text-gray-500 mb-2 flex items-center gap-1 truncate">
                                            @if(optional($product->country)->flag_url)
                                                <img src="{{ $product->country->flag_url }}"
                                                     alt="{{ $product->country->name }}"
                                                     class="w-4 h-3 object-cover rounded-sm flex-shrink-0">
                                            @endif
                                            <span class="truncate font-medium">{{ $bizProfile->business_name }}</span>
                                            @if($bizProfile->is_admin_verified ?? false)
                                                <i class="fas fa-check-circle text-green-500 text-[8px] flex-shrink-0"></i>
                                            @endif
                                        </p>
                                        @elseif($product->country)
                                        <p class="text-[10px] text-gray-500 mb-2 flex items-center gap-1">
                                            @if($product->country->flag_url)
                                                <img src="{{ $product->country->flag_url }}"
                                                     alt="{{ $product->country->name }}"
                                                     class="w-4 h-3 object-cover rounded-sm flex-shrink-0">
                                            @endif
                                            <span class="font-medium">{{ $product->country->name }}</span>
                                        </p>
                                        @endif

                                        {{-- Price --}}
                                        @if($finalPrice !== null)
                                        <p class="text-[11px] font-black text-gray-900 mb-2">
                                            {{ $symbol }}{{ number_format($finalPrice, 2) }}
                                            @if($hasDiscount)
                                                <span class="text-[9px] text-gray-400 line-through font-normal ml-1">
                                                    {{ $symbol }}{{ number_format($firstPrice->price, 2) }}
                                                </span>
                                            @endif
                                        </p>
                                        @endif

                                        {{-- CTA buttons ─ 3 buttons like the screenshot --}}
                                        <div class="mt-auto flex items-center gap-1.5 flex-wrap">
                                            <a href="{{ route('products.show', $product->slug) }}"
                                               class="px-2.5 py-1 bg-[#ff0808] hover:bg-red-700 text-white
                                                      text-[9px] font-bold rounded-sm transition-colors whitespace-nowrap">
                                                View Product
                                            </a>
                                            <a href="mailto:?subject=Inquiry: {{ urlencode($product->name) }}"
                                               class="px-2.5 py-1 border border-gray-300
                                                      hover:border-[#ff0808] hover:text-[#ff0808] text-gray-600
                                                      text-[9px] font-semibold rounded-sm transition-colors whitespace-nowrap flex items-center gap-1">
                                                <i class="fas fa-envelope text-[8px]"></i>
                                                Send Inquiry
                                            </a>
                                            @if($bizProfile->website ?? false)
                                            <a href="{{ $bizProfile->website }}" target="_blank" rel="noopener noreferrer"
                                               class="px-2.5 py-1 border border-gray-300
                                                      hover:border-[#ff0808] hover:text-[#ff0808] text-gray-600
                                                      text-[9px] font-semibold rounded-sm transition-colors whitespace-nowrap flex items-center gap-1">
                                                <i class="fas fa-globe text-[8px]"></i>
                                                Visit Website
                                            </a>
                                            @else
                                            <a href="{{ route('products.show', $product->slug) }}"
                                               class="px-2.5 py-1 border border-gray-300
                                                      hover:border-[#ff0808] hover:text-[#ff0808] text-gray-600
                                                      text-[9px] font-semibold rounded-sm transition-colors whitespace-nowrap flex items-center gap-1">
                                                <i class="fas fa-eye text-[8px]"></i>
                                                View Details
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @endforeach
                            </div>
                        </div>
                        @endforeach

                    </div>{{-- /cat-track --}}
                </div>{{-- /overflow-hidden --}}

                {{-- Dot indicators --}}
                @if($catProds->count() > 6)
                <div class="flex justify-center gap-1.5 mt-3" id="cat-dots-{{ $cat->id }}">
                    @foreach($catProds->chunk(6) as $pi => $pg)
                    <button onclick="goToCatPage({{ $cat->id }}, {{ $pi }})"
                            class="cat-dot-{{ $cat->id }} w-1.5 h-1.5 rounded-full transition-all duration-300
                                   {{ $pi === 0 ? 'bg-[#ff0808] w-4' : 'bg-gray-300' }}"
                            data-page="{{ $pi }}">
                    </button>
                    @endforeach
                </div>
                @endif

            </div>{{-- /relative carousel wrap --}}

            @else
            {{-- Empty state --}}
            <div class="py-12 text-center bg-white rounded-md border border-gray-200">
                <div class="text-4xl mb-3">📦</div>
                <p class="text-sm font-semibold text-gray-700">No products in {{ $cat->name }} yet</p>
                <p class="text-xs text-gray-400 mt-1">Check back soon for new listings.</p>
            </div>
            @endif

            {{-- View all link for this category --}}
            <div class="mt-3 text-right">
                <a href="{{ route('categories.products', ['slug' => $cat->slug ?? \Illuminate\Support\Str::slug($cat->name)]) }}"
                   class="inline-flex items-center gap-1 text-[11px] font-bold text-[#1a2942] hover:text-[#ff0808] transition-colors">
                    View all in {{ $cat->name }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

        </div>{{-- /cat-panel --}}
        @endforeach

    </div>
</section>
@endif

@push('scripts')
<script>
(function () {
    'use strict';

    // ── State ────────────────────────────────────────────────────────────────
    const catPages = {};   // catId → current page index
    const catTotals = {};  // catId → total pages

    // ── Init: count pages per category ──────────────────────────────────────
    document.querySelectorAll('.cat-carousel-wrap').forEach(wrap => {
        const cid    = wrap.dataset.catCarousel;
        const pages  = wrap.querySelectorAll('.cat-page');
        catPages[cid]  = 0;
        catTotals[cid] = pages.length;
        updateCatArrows(cid);
    });

    // ── Update track position ────────────────────────────────────────────────
    function updateCatTrack(cid) {
        const track = document.getElementById('cat-track-' + cid);
        if (!track) return;
        track.style.transform = 'translateX(-' + (catPages[cid] * 100) + '%)';
        updateCatDots(cid);
        updateCatArrows(cid);
    }

    // ── Navigate ─────────────────────────────────────────────────────────────
    window.navCatCarousel = function (cid, dir) {
        const total = catTotals[cid] || 1;
        catPages[cid] = Math.max(0, Math.min((catPages[cid] || 0) + dir, total - 1));
        updateCatTrack(cid);
    };

    window.goToCatPage = function (cid, page) {
        catPages[cid] = page;
        updateCatTrack(cid);
    };

    // ── Dots ─────────────────────────────────────────────────────────────────
    function updateCatDots(cid) {
        document.querySelectorAll('.cat-dot-' + cid).forEach(dot => {
            const active = parseInt(dot.dataset.page) === catPages[cid];
            dot.style.width           = active ? '16px' : '';
            dot.style.backgroundColor = active ? '#ff0808' : '';
            if (!active) dot.classList.add('bg-gray-300');
            else dot.classList.remove('bg-gray-300');
        });
    }

    // ── Arrow visibility ──────────────────────────────────────────────────────
    function updateCatArrows(cid) {
        const prevBtn = document.querySelector('.cat-prev-' + cid);
        const nextBtn = document.querySelector('.cat-next-' + cid);
        const page    = catPages[cid] || 0;
        const total   = catTotals[cid] || 1;

        if (prevBtn) prevBtn.style.opacity = page === 0 ? '0' : '1';
        if (nextBtn) nextBtn.style.opacity = page >= total - 1 ? '0' : '1';
    }

    // ── Category tab switching ────────────────────────────────────────────────
    window.switchCatTab = function (catId, clickedTab) {

        // Update tabs
        document.querySelectorAll('.cat-tab').forEach(tab => {
            tab.classList.remove('bg-[#ff0808]', 'text-white', 'border-[#ff0808]', 'shadow-md', 'cat-tab--active');
            tab.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
        });
        clickedTab.classList.add('bg-[#ff0808]', 'text-white', 'border-[#ff0808]', 'shadow-md', 'cat-tab--active');
        clickedTab.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');

        // Scroll tab into view on mobile
        clickedTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });

        // Show correct panel
        document.querySelectorAll('.cat-panel').forEach(panel => {
            panel.classList.add('hidden');
        });
        const target = document.querySelector('[data-cat-panel="' + catId + '"]');
        if (target) target.classList.remove('hidden');
    };

})();
</script>
@endpush

<style>
/* ── Hide scrollbar on tab bar (all browsers) ───────────────────────────── */
#cat-tab-bar::-webkit-scrollbar { display: none; }
#cat-tab-bar { -ms-overflow-style: none; scrollbar-width: none; }

/* ── Active tab: downward arrow / notch indicator ───────────────────────── */
.cat-tab {
    position: relative;
}

.cat-tab--active::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-top: 8px solid #ff0808;
    pointer-events: none;
    z-index: 10;
}

/* ── Smooth dot transitions ─────────────────────────────────────────────── */
[class*="cat-dot-"] {
    transition: width 0.3s ease, background-color 0.3s ease;
}

/* ── Arrow hover ring ───────────────────────────────────────────────────── */
[class*="cat-prev-"]:hover,
[class*="cat-next-"]:hover {
    box-shadow: 0 0 0 3px rgba(255, 8, 8, 0.12);
}

/* ── Card image area fixed height on mobile ─────────────────────────────── */
@media (max-width: 640px) {
    .cat-page .bg-white.flex-row > a:first-child {
        width: 90px;
        min-height: 90px;
    }
}
</style>

@php endif; /* catProdSectionActive */ @endphp
