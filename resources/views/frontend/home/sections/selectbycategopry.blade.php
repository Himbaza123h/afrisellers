@php
    $latestProdSection       = \App\Models\UISection::where('section_key', 'bp_latest_products')->first();
    $latestProdSectionActive = $latestProdSection?->is_active ?? true;
    $latestProdItemsPerPage  = $latestProdSection?->number_items ?? 30;

    if ($latestProdSectionActive):

    $lpCurrencySymbols = [
        'USD' => '$',  'EUR' => '€',  'GBP' => '£',
        'RWF' => 'RF', 'KES' => 'KSh','UGX' => 'USh','TZS' => 'TSh',
        'ETB' => 'Br', 'NGN' => '₦',  'GHS' => 'GH₵','ZAR' => 'R',
        'EGP' => 'E£', 'CNY' => '¥',  'INR' => '₹',
    ];

    $latestProductsByCategory = \App\Models\ProductCategory::where('status', 'active')
        ->whereHas('products', fn($q) =>
            $q->where('status', 'active')->where('is_admin_verified', true)
        )
        ->with([
            'products' => fn($q) => $q
                ->where('status', 'active')
                ->where('is_admin_verified', true)
                ->with([
                    'images'   => fn($q) => $q->orderBy('is_primary', 'desc')->orderBy('sort_order', 'asc')->limit(1),
                    'country',
                    'user.vendor.businessProfile',
                    'prices'   => fn($q) => $q->orderBy('min_qty', 'asc'),
                ])
                ->latest()
                ->limit(6),
        ])
        ->orderBy('name')
        ->limit(12)
        ->get();

@endphp

@if($latestProductsByCategory->isNotEmpty())
<section class="py-6 md:py-10 bg-white" id="latest-products-section">
    <div class="container px-4 mx-auto">

        {{-- Section Title --}}
        <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="w-1 h-5 bg-[#ff0808] rounded inline-block"></span>
            Select by Category
        </h2>

        {{-- Category Tabs --}}
        <div class="flex gap-2 overflow-x-auto pb-2 mb-5 scrollbar-hide" id="lpCategoryTabs">
            @foreach($latestProductsByCategory as $i => $lpCat)
            @if($lpCat->products->isNotEmpty())
            <button type="button"
                    onclick="lpSwitchCategory('cat-{{ $lpCat->id }}')"
                    data-tab="cat-{{ $lpCat->id }}"
                    class="lp-tab-btn flex-shrink-0 px-3 py-1.5 rounded text-xs font-semibold transition-colors whitespace-nowrap border
                           {{ $i === 0 ? 'bg-[#ff0808] text-white border-[#ff0808]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#ff0808] hover:text-[#ff0808]' }}">
                {{ $lpCat->name }}
                <span class="ml-1 text-[10px] opacity-75">({{ $lpCat->products->count() }})</span>
            </button>
            @endif
            @endforeach
        </div>

        {{-- Category Panels --}}
        @foreach($latestProductsByCategory as $i => $lpCategory)
        @if($lpCategory->products->isNotEmpty())
        <div id="cat-{{ $lpCategory->id }}"
             class="lp-category-panel {{ $i !== 0 ? 'hidden' : '' }}">

            {{-- 2-column product grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($lpCategory->products->take(6) as $lpProduct)
                @php
                    $lpImage       = $lpProduct->images->where('is_primary', true)->first()
                                  ?? $lpProduct->images->first();
                    $lpPrice       = $lpProduct->prices->first();
                    $lpCurrency    = $lpPrice->currency ?? 'USD';
                    $lpSym         = $lpCurrencySymbols[$lpCurrency] ?? $lpCurrency;
                    $lpFinal       = $lpPrice ? ($lpPrice->price - ($lpPrice->discount ?? 0)) : null;
                    $lpHasDiscount = $lpPrice && ($lpPrice->discount ?? 0) > 0;
                    $lpBusiness    = $lpProduct->user?->vendor?->businessProfile;
                    $lpCountry     = $lpProduct->country;
                @endphp

                <div class="flex gap-3 bg-white border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow">

                    {{-- Thumbnail --}}
                    <a href="{{ route('products.show', $lpProduct->slug) }}" class="relative w-28 h-28 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 border border-gray-100 block">
                        @if($lpImage)
                            <img src="{{ $lpImage->image_url }}"
                                 alt="{{ $lpProduct->name }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                 loading="lazy">
                        @else
                            <div class="flex items-center justify-center w-full h-full text-3xl">📦</div>
                        @endif
                        @if($lpHasDiscount)
                            <span class="absolute top-1 left-1 bg-[#ff0808] text-white text-[8px] font-bold px-1 py-0.5 rounded">Sale</span>
                        @endif
                    </a>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0 flex flex-col justify-between">
                        <div>
                            {{-- Product name --}}
                            <h3 class="text-sm font-bold text-gray-900 line-clamp-2 leading-tight mb-0.5">
                                {{ $lpProduct->name }}
                            </h3>

                            {{-- Short description --}}
                            @if($lpProduct->short_description)
                                <p class="text-[11px] text-gray-500 line-clamp-1 mb-1.5">
                                    {{ $lpProduct->short_description }}
                                </p>
                            @endif

                            {{-- Supplier + country --}}
                            <div class="flex items-center gap-1.5 mb-2">
                                @if($lpCountry?->flag_emoji)
                                    <span class="text-sm">{{ $lpCountry->flag_emoji }}</span>
                                @endif
                                <span class="text-[10px] font-semibold text-gray-600 truncate">
                                    {{ $lpBusiness?->business_name ?? $lpProduct->user?->name ?? 'Seller' }}
                                </span>
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full flex-shrink-0"></span>
                            </div>

                            {{-- Price --}}
                            <div class="flex items-baseline gap-2 mb-2">
                                @if($lpFinal !== null)
                                    <span class="text-sm font-bold text-gray-900 lp-price-display"
                                          data-price-native="{{ $lpFinal }}"
                                          data-price-currency="{{ $lpCurrency }}"
                                          data-price-original="{{ $lpPrice->price }}"
                                          data-has-discount="{{ $lpHasDiscount ? '1' : '0' }}"
                                          data-symbol-native="{{ $lpSym }}">
                                        <span class="lp-price-main">{{ $lpSym }}{{ number_format($lpFinal, 2) }}</span>
                                    </span>
                                    @if($lpHasDiscount)
                                        <span class="text-xs text-gray-400 line-through lp-price-original-wrap"
                                              data-price-native="{{ $lpPrice->price }}"
                                              data-price-currency="{{ $lpCurrency }}"
                                              data-symbol-native="{{ $lpSym }}">
                                            <span class="lp-price-original">{{ $lpSym }}{{ number_format($lpPrice->price, 2) }}</span>
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400 italic">Price on request</span>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <a href="{{ route('products.show', $lpProduct->slug) }}"
                               class="px-2.5 py-1 bg-[#ff0808] text-white text-[10px] font-bold rounded hover:bg-red-700 transition-colors whitespace-nowrap">
                                View Product
                            </a>
                            <a href="{{ route('rfqs.create', ['product_id' => $lpProduct->id]) }}"
                               class="px-2.5 py-1 bg-white border border-gray-300 text-gray-700 text-[10px] font-semibold rounded hover:border-[#ff0808] hover:text-[#ff0808] transition-colors whitespace-nowrap">
                                Send Inquiry
                            </a>
                            @if($lpBusiness?->website)
                                <a href="{{ $lpBusiness->website }}" target="_blank"
                                   class="px-2.5 py-1 bg-white border border-gray-300 text-gray-700 text-[10px] font-semibold rounded hover:border-[#ff0808] hover:text-[#ff0808] transition-colors whitespace-nowrap">
                                    Visit Website
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
                @endforeach
            </div>

            {{-- View all link --}}
            <div class="flex justify-end mt-3">
                <span class="text-xs text-gray-500 font-semibold">
                    View {{ $lpCategory->name }} <i class="fas fa-chevron-right text-[9px]"></i>
                </span>
            </div>

        </div>
        @endif
        @endforeach

    </div>
</section>
@endif

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Category tab switcher ────────────────────────────────────
    window.lpSwitchCategory = function(panelId) {
        document.querySelectorAll('.lp-category-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.lp-tab-btn').forEach(btn => {
            const active = btn.dataset.tab === panelId;
            btn.classList.toggle('bg-[#ff0808]',   active);
            btn.classList.toggle('text-white',      active);
            btn.classList.toggle('border-[#ff0808]',active);
            btn.classList.toggle('bg-white',       !active);
            btn.classList.toggle('text-gray-700',  !active);
            btn.classList.toggle('border-gray-300',!active);
        });
        const panel = document.getElementById(panelId);
        if (panel) panel.classList.remove('hidden');
    };

    // ── Currency conversion ───────────────────────────────────────
    function getStoredRate()   { return parseFloat(localStorage.getItem('ui_currency_usd_rate') || '1'); }
    function getStoredSymbol() { return localStorage.getItem('ui_currency_symbol') || '$'; }

    function getLiveRates() {
        try { return JSON.parse(localStorage.getItem('ui_currency_rates_cache') || '{}'); }
        catch (e) { return {}; }
    }

    function convertPrice(nativeAmount, nativeCurrency, targetRate) {
        const rates      = getLiveRates();
        const nativeRate = rates[nativeCurrency] || 1;
        return (nativeAmount / nativeRate) * targetRate;
    }

    function numberFormat(n) {
        return n >= 1000 ? Math.round(n).toLocaleString() : n.toFixed(2);
    }

    function updateAllPrices() {
        const targetRate   = getStoredRate();
        const targetSymbol = getStoredSymbol();

        document.querySelectorAll('.lp-price-display').forEach(function (el) {
            const amount   = parseFloat(el.dataset.priceNative);
            const currency = el.dataset.priceCurrency;
            if (isNaN(amount)) return;
            const mainEl = el.querySelector('.lp-price-main');
            if (mainEl) mainEl.textContent = targetSymbol + numberFormat(convertPrice(amount, currency, targetRate));
        });

        document.querySelectorAll('.lp-price-original-wrap').forEach(function (el) {
            const amount   = parseFloat(el.dataset.priceNative);
            const currency = el.dataset.priceCurrency;
            if (isNaN(amount)) return;
            const origEl = el.querySelector('.lp-price-original');
            if (origEl) origEl.textContent = targetSymbol + numberFormat(convertPrice(amount, currency, targetRate));
        });
    }

    window.addEventListener('currencyChanged', updateAllPrices);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { setTimeout(updateAllPrices, 300); });
    } else {
        setTimeout(updateAllPrices, 300);
    }
})();
</script>
@endpush

@php endif; @endphp
