<section class="py-16 bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="container px-4 mx-auto">

        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1 h-8 bg-[#ff0808]"></div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.weekly_special_offers') }}</h2>
            </div>
            <a href=""
               class="px-4 py-2 text-sm font-semibold text-white bg-[#ff0808] rounded hover:bg-[#dd0606] transition-colors">
                {{ __('messages.see_more_products') }}
            </a>
        </div>

        @php
            // Get products with active weekly offers addons
            $weeklyOffers = App\Models\Product::where('status', 'active')
                ->where('is_admin_verified', true)
                ->whereHas('addonUsers', function($query) {
                    $query->whereNotNull('paid_at')
                        ->where(function($q) {
                            $q->whereNull('ended_at')
                              ->orWhere('ended_at', '>', now());
                        })
                        ->whereHas('addon', function($addonQuery) {
                            $addonQuery->where('locationX', 'Homepage')
                                       ->where('locationY', 'weeklyoffers');
                        });
                })
                ->with(['images', 'country', 'user.businessProfile', 'productCategory', 'prices'])
                ->limit(6)
                ->get();
        @endphp

        <!-- Products Grid - 6 per row -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
            @forelse($weeklyOffers as $product)
                @php
                    $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                    $businessProfile = $product->user->businessProfile ?? null;
                    $price = $product->prices->first();
                    $mainPrice = $price ? $price->min_price : 0;
                    $maxPrice = $price ? $price->max_price : 0;
                    $currency = $price ? $price->currency : 'RWF';
                @endphp

                <div class="bg-white rounded-md border-2 border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all">
                    <!-- Product Image -->
                    <a href="{{ route('products.show', $product->slug) }}" class="block relative h-32 overflow-hidden group">
                        @if($image)
                            <img src="{{ $image->image_url }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                 loading="lazy">
                        @else
                            <div class="flex justify-center items-center w-full h-full bg-gray-100">
                                <span class="text-2xl">📦</span>
                            </div>
                        @endif

                        <!-- TOFL ET Badge -->
                        <span class="absolute top-2 right-2 bg-[#ff0808] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                            🔥 {{ __('messages.tofl_et') }}
                        </span>

                        <!-- Exporters Badge -->
                        @if($businessProfile && $businessProfile->verification_status === 'verified')
                        <span class="absolute top-2 left-2 bg-green-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">
                            ✓ {{ __('messages.exporters') }}
                        </span>
                        @endif
                    </a>

                    <!-- Product Details -->
                    <div class="p-3">
                        <!-- Product Name -->
                        <a href="{{ route('products.show', $product->slug) }}">
                            <h4 class="text-xs font-bold text-gray-900 mb-2 hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[2rem]">
                                {{ $product->name }}
                            </h4>
                        </a>

                        <!-- Category -->
                        @if($product->productCategory)
                        <div class="mb-2">
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="truncate">{{ $product->productCategory->name }}</span>
                            </span>
                        </div>
                        @endif

                        <!-- Price -->
                        <div class="text-[#ff0808] font-bold text-xs mb-2">
                            {{ $currency }} {{ number_format($mainPrice, 2) }}
                            @if($maxPrice && $maxPrice != $mainPrice)
                                - {{ number_format($maxPrice, 2) }}
                            @endif
                        </div>

                        <!-- MOQ -->
                        <div class="text-[10px] text-gray-500 mb-2">
                            {{ __('messages.moq') }}: {{ number_format($product->min_order_quantity) }} pcs
                        </div>

                        <!-- Location -->
                        <div class="flex items-center gap-1 text-[10px] text-gray-500 mb-2">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <span>{{ $product->country->name ?? '' }}</span>
                        </div>

                        <!-- Supplier Badge -->
                        @if($businessProfile)
                        <div class="mb-2">
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="truncate">{{ $businessProfile->business_name }}</span>
                            </span>
                        </div>
                        @endif

                        <!-- Verified Badge -->
                        @if($product->is_admin_verified)
                        <div class="flex items-center gap-1 text-green-600 mb-2">
                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium text-[10px]">{{ __('messages.verified') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                        <div>
                            <p class="text-lg font-semibold text-gray-700 mb-1">{{ __('messages.no_weekly_offers') }}</p>
                            <p class="text-sm text-gray-500">{{ __('messages.no_weekly_offers_description') }}</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</section>
