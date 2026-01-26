<!-- Section 1: Weekly Special Offers -->
<section class="py-8 bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="container px-4 mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1 h-8 bg-[#ff0808]"></div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.weekly_special_offers') }}</h2>
            </div>
            <a href="#" class="flex items-center gap-1 text-sm font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors">
                <span>{{ __('messages.view_all') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @php
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
                ->limit(5)
                ->get();
        @endphp

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
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

                        <span class="absolute top-2 right-2 bg-[#ff0808] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                            🔥 {{ __('messages.tofl_et') }}
                        </span>

                        @if($businessProfile && $businessProfile->verification_status === 'verified')
                        <span class="absolute top-2 left-2 bg-green-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">
                            ✓ {{ __('messages.exporters') }}
                        </span>
                        @endif
                    </a>

                    <div class="p-3">
                        <a href="{{ route('products.show', $product->slug) }}">
                            <h4 class="text-xs font-bold text-gray-900 mb-2 hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[2rem]">
                                {{ $product->name }}
                            </h4>
                        </a>

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

                        <div class="text-[#ff0808] font-bold text-xs mb-2">
                            {{ $currency }} {{ number_format($mainPrice, 2) }}
                            @if($maxPrice && $maxPrice != $mainPrice)
                                - {{ number_format($maxPrice, 2) }}
                            @endif
                        </div>

                        <div class="text-[10px] text-gray-500 mb-2">
                            {{ __('messages.moq') }}: {{ number_format($product->min_order_quantity) }} pcs
                        </div>

                        <div class="flex items-center gap-1 text-[10px] text-gray-500 mb-2">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <span>{{ $product->country->name ?? '' }}</span>
                        </div>

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
                <div class="col-span-full py-8 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                    <p class="text-sm font-semibold text-gray-700">{{ __('messages.no_weekly_offers') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Section 2: Most Recommended Suppliers -->
<section class="py-8 bg-white">
    <div class="container px-4 mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1 h-8 bg-[#ff0808]"></div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.most_recommended_suppliers') }}</h2>
            </div>
            <a href="#" class="flex items-center gap-1 text-sm font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors">
                <span>{{ __('messages.view_all') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @php
            $topSuppliers = App\Models\BusinessProfile::where('verification_status', 'verified')
                ->where('is_admin_verified', true)
                ->whereHas('addonUsers', function($query) {
                    $query->whereNotNull('paid_at')
                        ->where(function($q) {
                            $q->whereNull('ended_at')
                              ->orWhere('ended_at', '>', now());
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
                ->limit(5)
                ->get();

            $supplierProducts = [];
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
        @endphp

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @forelse($topSuppliers as $supplier)
                @php
                    $product = $supplierProducts[$supplier->id] ?? null;
                    $image = $product && $product->images->count() > 0 ? $product->images->first() : null;
                @endphp

                <div class="bg-white rounded-md border-2 border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all">
                    <div class="block relative h-32 overflow-hidden group">
                        @if($image)
                            <img src="{{ $image->image_url }}"
                                 alt="{{ $supplier->business_name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                 loading="lazy">
                        @else
                            <div class="flex justify-center items-center w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100">
                                <span class="text-4xl">🏢</span>
                            </div>
                        @endif

                        <span class="absolute top-2 right-2 bg-[#ff0808] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                            ⭐ {{ __('messages.top_exporter') }}
                        </span>

                        <span class="absolute top-2 left-2 bg-green-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">
                            ✓ {{ __('messages.verified') }}
                        </span>
                    </div>

                    <div class="p-3">
                        <h4 class="text-xs font-bold text-gray-900 mb-2 line-clamp-2 min-h-[2rem]">
                            {{ $supplier->business_name }}
                        </h4>

                        <div class="flex items-center gap-1 text-[10px] text-gray-500 mb-2">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <span>{{ $supplier->city }}, {{ $supplier->country->name ?? '' }}</span>
                        </div>

                        <div class="mb-2">
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span>{{ number_format($supplier->products_count) }} {{ __('messages.products') }}</span>
                            </span>
                        </div>

                        @if($supplier->business_type)
                        <div class="mb-2">
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="truncate">{{ $supplier->business_type }}</span>
                            </span>
                        </div>
                        @endif

                        <div class="flex items-center gap-1 text-[10px] font-medium text-[#ff0808]">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>{{ __('messages.view_profile') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-sm font-semibold text-gray-700">{{ __('messages.no_suppliers_available') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Section 3: Hot Deals -->
<section class="py-8 bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="container px-4 mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1 h-8 bg-[#ff0808]"></div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.hot_deals') }}</h2>
            </div>
            <a href="#" class="flex items-center gap-1 text-sm font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors">
                <span>{{ __('messages.view_all') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @php
            $hotDeals = App\Models\Product::where('status', 'active')
                ->where('is_admin_verified', true)
                ->whereHas('addonUsers', function($query) {
                    $query->whereNotNull('paid_at')
                        ->where(function($q) {
                            $q->whereNull('ended_at')
                              ->orWhere('ended_at', '>', now());
                        })
                        ->whereHas('addon', function($addonQuery) {
                            $addonQuery->where('locationX', 'Homepage')
                                       ->where('locationY', 'hotdeals');
                        });
                })
                ->with(['images', 'productCategory', 'country', 'user.businessProfile', 'prices'])
                ->latest()
                ->limit(5)
                ->get();
        @endphp

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @forelse($hotDeals as $product)
                @php
                    $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                    $businessProfile = $product->user->businessProfile ?? null;
                    $price = $product->prices->first();
                    $mainPrice = $price ? $price->min_price : 0;
                    $maxPrice = $price ? $price->max_price : 0;
                    $currency = $price ? $price->currency : 'RWF';
                @endphp

                <div class="bg-white rounded-md border-2 border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all">
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

                        <span class="absolute top-2 right-2 bg-[#ff0808] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                            🔥 {{ __('messages.hot') }}
                        </span>

                        @if($product->is_admin_verified)
                        <span class="absolute top-2 left-2 bg-green-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">
                            ✓ {{ __('messages.verified') }}
                        </span>
                        @endif
                    </a>

                    <div class="p-3">
                        <a href="{{ route('products.show', $product->slug) }}">
                            <h4 class="text-xs font-bold text-gray-900 mb-2 hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[2rem]">
                                {{ $product->name }}
                            </h4>
                        </a>

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

                        <div class="text-[#ff0808] font-bold text-xs mb-2">
                            {{ $currency }} {{ number_format($mainPrice, 2) }}
                            @if($maxPrice && $maxPrice != $mainPrice)
                                - {{ number_format($maxPrice, 2) }}
                            @endif
                        </div>

                        <div class="text-[10px] text-gray-500 mb-2">
                            {{ __('messages.moq') }}: {{ number_format($product->min_order_quantity) }} pcs
                        </div>

                        <div class="flex items-center gap-1 text-[10px] text-gray-500 mb-2">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <span>{{ $product->country->name ?? '' }}</span>
                        </div>

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

                        @if($businessProfile)
                        <div class="flex items-center gap-1 text-[10px] font-medium text-[#ff0808]">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>{{ __('messages.view_profile') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                    <p class="text-sm font-semibold text-gray-700">{{ __('messages.no_hot_deals_available') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
