

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

        {{-- ── Section Header ─────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg md:text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Our Products
            </h2>
            <span class="text-xs text-gray-400">
                Showing {{ $latestVendorProducts->count() }} latest products
            </span>
        </div>

        {{-- ── Products Grid (5 cols × 2 rows) ────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4">
            @foreach($latestVendorProducts as $lpProduct)
                @php
                    $lpImage = $lpProduct->images->where('is_primary', true)->first()
                             ?? $lpProduct->images->first();
                    $lpPrice = $lpProduct->prices->first();
                    $lpSym   = $lpPrice ? ($lpCurrencySymbols[$lpPrice->currency] ?? $lpPrice->currency) : null;
                    $lpFinal = $lpPrice ? ($lpPrice->price - ($lpPrice->discount ?? 0)) : null;
                @endphp

                <a href="{{ route('products.show', $lpProduct->slug) }}"
                   class="group bg-white border border-gray-200 rounded overflow-hidden
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

                        {{-- Verified badge --}}
                        @if($lpProduct->is_admin_verified)
                            <span class="absolute top-2 right-2 bg-green-600 text-white text-[9px] font-semibold px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </span>
                        @endif

                        {{-- New badge --}}
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
                               data-price-currency="{{ $lpPrice->currency }}">
                                {{ $lpSym }}{{ number_format($lpFinal, 2) }}
                            </p>
                            @if(($lpPrice->discount ?? 0) > 0)
                                <p class="text-[10px] text-gray-400 line-through -mt-1">
                                    {{ $lpSym }}{{ number_format($lpPrice->price, 2) }}
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
</section>
@endif

@php endif; @endphp
