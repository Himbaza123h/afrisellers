@php
    $latestProdSection       = \App\Models\UISection::where('section_key', 'bp_latest_products')->first();
    $latestProdSectionActive = $latestProdSection?->is_active ?? true;
    $latestProdItemsPerPage  = $latestProdSection?->number_items ?? 10;

    if ($latestProdSectionActive):

    $latestVendorProducts = \App\Models\Product::where('status', 'active')
        ->where('is_admin_verified', true)
        ->with([
            'images'  => fn($q) => $q->orderBy('is_primary', 'desc')->orderBy('sort_order', 'asc')->limit(1),
            'country',
            'user.vendor.businessProfile',
            'prices'  => fn($q) => $q->orderBy('min_qty', 'asc'),
        ])
        ->latest()
        ->limit($latestProdItemsPerPage)
        ->get();

    $lpCurrencySymbols = [
        'USD' => '$',  'EUR' => '€',  'GBP' => '£',
        'RWF' => 'RF', 'KES' => 'KSh','UGX' => 'USh','TZS' => 'TSh',
        'ETB' => 'Br', 'NGN' => '₦',  'GHS' => 'GH₵','ZAR' => 'R',
        'EGP' => 'E£', 'CNY' => '¥',  'INR' => '₹',
    ];
@endphp

@if($latestVendorProducts->isNotEmpty())
<section class="py-6 md:py-10 bg-gray-50" id="latest-products-section">
    <div class="container px-4 mx-auto">

        {{-- Section Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg md:text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Our Products
            </h2>
            {{-- Prev / Next arrows --}}
            <div class="flex items-center gap-2">
                <button id="lpPrev"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 shadow-sm text-gray-600 hover:text-[#ff0808] hover:border-[#ff0808] transition-colors">
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                <button id="lpNext"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 shadow-sm text-gray-600 hover:text-[#ff0808] hover:border-[#ff0808] transition-colors">
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>

        {{-- Slider Wrapper --}}
        <div class="relative overflow-hidden" id="lpSliderOuter">
            <div class="flex gap-3 md:gap-4 transition-transform duration-500 ease-in-out" id="lpSliderTrack">

                @foreach($latestVendorProducts as $lpProduct)
                @php
                    $lpImage       = $lpProduct->images->where('is_primary', true)->first()
                                  ?? $lpProduct->images->first();
                    $lpPrice       = $lpProduct->prices->first();
                    $lpCurrency    = $lpPrice->currency ?? 'USD';
                    $lpSym         = $lpCurrencySymbols[$lpCurrency] ?? $lpCurrency;
                    $lpFinal       = $lpPrice ? ($lpPrice->price - ($lpPrice->discount ?? 0)) : null;
                    $lpHasDiscount = $lpPrice && ($lpPrice->discount ?? 0) > 0;
                @endphp

                {{-- Each card: fixed width, flex-shrink-0 --}}
                <a href="{{ route('products.show', $lpProduct->slug) }}"
                   class="lp-slide group bg-white border border-gray-200 rounded overflow-hidden flex-shrink-0
                          hover:shadow-lg hover:border-[#ff0808] transition-all duration-300 hover:-translate-y-1">

                    {{-- Image --}}
                    <div class="relative h-36 overflow-hidden bg-gray-50">
                        @if($lpImage)
                            <img src="{{ $lpImage->image_url }}"
                                 alt="{{ $lpProduct->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                 loading="lazy">
                        @else
                            <div class="flex items-center justify-center w-full h-full bg-gray-100">
                                <span class="text-4xl">📦</span>
                            </div>
                        @endif

                        @if($lpProduct->is_admin_verified)
                            <span class="absolute top-2 right-2 bg-green-600 text-white text-[9px] font-semibold px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </span>
                        @endif

                        @if($lpProduct->created_at->diffInDays() <= 14)
                            <span class="absolute top-2 left-2 bg-[#ff0808] text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                                NEW
                            </span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-3">
                        <h3 class="text-xs font-semibold text-gray-900 mb-2 line-clamp-2
                                   group-hover:text-[#ff0808] transition-colors min-h-[2.5rem]">
                            {{ $lpProduct->name }}
                        </h3>

                        @if($lpFinal !== null)
                            <p class="text-sm font-bold text-[#ff0808] mb-1 lp-price-display"
                               data-price-native="{{ $lpFinal }}"
                               data-price-currency="{{ $lpCurrency }}"
                               data-price-original="{{ $lpPrice->price }}"
                               data-has-discount="{{ $lpHasDiscount ? '1' : '0' }}"
                               data-symbol-native="{{ $lpSym }}">
                                <span class="lp-price-main">{{ $lpSym }}{{ number_format($lpFinal, 2) }}</span>
                            </p>
                            @if($lpHasDiscount)
                                <p class="text-[10px] text-gray-400 line-through -mt-1 lp-price-original-wrap"
                                   data-price-native="{{ $lpPrice->price }}"
                                   data-price-currency="{{ $lpCurrency }}"
                                   data-symbol-native="{{ $lpSym }}">
                                    <span class="lp-price-original">{{ $lpSym }}{{ number_format($lpPrice->price, 2) }}</span>
                                </p>
                            @endif
                        @else
                            <p class="text-xs text-gray-400 italic mb-1">Price on request</p>
                        @endif

                        <p class="text-[10px] text-gray-500 mt-1">
                            MOQ: {{ number_format($lpProduct->min_order_quantity ?? 1) }} pcs
                        </p>
                    </div>
                </a>

                @endforeach
            </div>
        </div>

        {{-- Dots --}}
        {{-- <div class="flex justify-center gap-1.5 mt-4" id="lpDots"></div> --}}

    </div>
</section>
@endif

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Slider ───────────────────────────────────────────────────────────
    const track     = document.getElementById('lpSliderTrack');
    const outer     = document.getElementById('lpSliderOuter');
    const prevBtn   = document.getElementById('lpPrev');
    const nextBtn   = document.getElementById('lpNext');
    const dotsWrap  = document.getElementById('lpDots');
    const slides    = Array.from(document.querySelectorAll('.lp-slide'));
    const AUTO_MS   = 3000;

    let current   = 0;
    let autoTimer = null;

    function getVisible() {
        const w = outer.offsetWidth;
        if (w >= 1024) return 5;
        if (w >= 768)  return 4;
        if (w >= 640)  return 3;
        return 2;
    }

    function getCardWidth() {
        if (!slides.length) return 0;
        const gap = window.innerWidth >= 768 ? 16 : 12;
        return (outer.offsetWidth - gap * (getVisible() - 1)) / getVisible();
    }

    function setCardWidths() {
        const w   = getCardWidth();
        const gap = window.innerWidth >= 768 ? 16 : 12;
        slides.forEach(s => {
            s.style.width    = w + 'px';
            s.style.minWidth = w + 'px';
        });
        track.style.gap = gap + 'px';
    }

    function maxIndex() {
        return Math.max(0, slides.length - getVisible());
    }

function buildDots() {
        dotsWrap.innerHTML = '';
        for (let i = 0; i < 3; i++) {
            const d = document.createElement('button');
            d.className = 'w-2 h-2 rounded-full transition-all duration-300 ' +
                          (i === 0 ? 'bg-[#ff0808] w-5' : 'bg-gray-300');
            dotsWrap.appendChild(d);
        }
    }

    function updateDots() {
        const max      = maxIndex();
        const segment  = max > 0 ? Math.round((current / max) * 2) : 0; // 0, 1, or 2
        Array.from(dotsWrap.children).forEach((d, i) => {
            d.className = 'w-2 h-2 rounded-full transition-all duration-300 ' +
                          (i === segment ? 'bg-[#ff0808] w-5' : 'bg-gray-300');
        });
    }

    function goTo(idx) {
        current = Math.max(0, Math.min(idx, maxIndex()));
        const gap  = window.innerWidth >= 768 ? 16 : 12;
        const move = current * (getCardWidth() + gap);
        track.style.transform = 'translateX(-' + move + 'px)';
        updateDots();
    }

    function next() { goTo(current >= maxIndex() ? 0 : current + 1); }
    function prev() { goTo(current <= 0 ? maxIndex() : current - 1); }

    function startTimer() {
        clearInterval(autoTimer);
        autoTimer = setInterval(next, AUTO_MS);
    }

    function resetTimer() { startTimer(); }

    // Touch swipe
    let touchX = 0;
    outer.addEventListener('touchstart', e => { touchX = e.changedTouches[0].screenX; }, { passive: true });
    outer.addEventListener('touchend',   e => {
        const diff = touchX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 40) { diff > 0 ? next() : prev(); resetTimer(); }
    }, { passive: true });

    prevBtn.addEventListener('click', () => { prev(); resetTimer(); });
    nextBtn.addEventListener('click', () => { next(); resetTimer(); });

    // Pause on hover
    outer.addEventListener('mouseenter', () => clearInterval(autoTimer));
    outer.addEventListener('mouseleave', startTimer);

    function init() {
        setCardWidths();
        buildDots();
        goTo(0);
        startTimer();
    }

    window.addEventListener('resize', () => {
        setCardWidths();
        buildDots();
        goTo(Math.min(current, maxIndex()));
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ── Currency conversion ───────────────────────────────────────────────
    function getStoredRate()   { return parseFloat(localStorage.getItem('ui_currency_usd_rate') || '1'); }
    function getStoredSymbol() { return localStorage.getItem('ui_currency_symbol') || '$'; }

    function getLiveRates() {
        try { return JSON.parse(localStorage.getItem('ui_currency_rates_cache') || '{}'); }
        catch (e) { return {}; }
    }

    function convertPrice(amount, currency, rate) {
        const rates = getLiveRates();
        return (amount / (rates[currency] || 1)) * rate;
    }

    function numberFormat(n) {
        return n >= 1000 ? Math.round(n).toLocaleString() : n.toFixed(2);
    }

    function updateAllPrices() {
        const rate   = getStoredRate();
        const symbol = getStoredSymbol();

        document.querySelectorAll('.lp-price-display').forEach(el => {
            const a = parseFloat(el.dataset.priceNative);
            const c = el.dataset.priceCurrency;
            if (isNaN(a)) return;
            const m = el.querySelector('.lp-price-main');
            if (m) m.textContent = symbol + numberFormat(convertPrice(a, c, rate));
        });

        document.querySelectorAll('.lp-price-original-wrap').forEach(el => {
            const a = parseFloat(el.dataset.priceNative);
            const c = el.dataset.priceCurrency;
            if (isNaN(a)) return;
            const o = el.querySelector('.lp-price-original');
            if (o) o.textContent = symbol + numberFormat(convertPrice(a, c, rate));
        });
    }

    window.addEventListener('currencyChanged', updateAllPrices);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(updateAllPrices, 300));
    } else {
        setTimeout(updateAllPrices, 300);
    }

})();
</script>
@endpush

@php endif; @endphp
