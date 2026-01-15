<section class="py-8 md:py-16 bg-gray-50">
    <div class="container px-4 mx-auto">
        <div class="grid grid-cols-1 gap-6 md:gap-8 lg:grid-cols-2">

            <!-- Recommended Suppliers Section (Left) -->
            <div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 md:mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-6 md:h-8 bg-[#ff0808]"></div>
                        <h2 class="text-lg md:text-2xl font-bold text-gray-900">{{ __('messages.most_recommended_suppliers') }}</h2>
                    </div>
                </div>

                @php
                    // Get top verified suppliers with active addon
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
                        ->limit(3)
                        ->get();

                    // Get product images for each supplier
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

                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                    @forelse($topSuppliers as $supplier)
                        @php
                            $product = $supplierProducts[$supplier->id] ?? null;
                            $image = $product && $product->images->count() > 0 ? $product->images->first() : null;
                        @endphp
                        <a href="{{ route('country.business-profiles', $supplier->country_id) }}" class="bg-white rounded-md border-2 border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all block">
                            <!-- Supplier Image -->
                            <div class="relative h-24 sm:h-28 md:h-32 overflow-hidden group">
                                @if($image)
                                    <img src="{{ $image->image_url }}"
                                         alt="{{ $supplier->business_name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                         loading="lazy">
                                @else
                                    <div class="flex justify-center items-center w-full h-full bg-gray-100">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Top Exporter Badge -->
                                <span class="absolute top-1 right-1 sm:top-2 sm:right-2 bg-[#ff0808] text-white text-[8px] sm:text-[10px] font-bold px-1 sm:px-1.5 py-0.5 rounded-full">
                                    ⭐ {{ __('messages.top_exporter') }}
                                </span>

                                <!-- Verified Badge -->
                                <span class="absolute top-1 left-1 sm:top-2 sm:left-2 bg-green-600 text-white text-[8px] sm:text-[10px] font-bold px-1 sm:px-1.5 py-0.5 rounded">
                                    ✓ {{ __('messages.verified') }}
                                </span>
                            </div>

                            <!-- Supplier Details -->
                            <div class="p-2 sm:p-3">
                                <!-- Supplier Name -->
                                <h4 class="text-[10px] sm:text-xs font-bold text-gray-900 mb-1 sm:mb-2 hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[1.5rem] sm:min-h-[2rem]">
                                    {{ $supplier->business_name }}
                                </h4>

                                <!-- Location -->
                                <div class="flex items-center gap-0.5 sm:gap-1 text-[8px] sm:text-[10px] text-gray-500 mb-1 sm:mb-2">
                                    <svg class="w-2 h-2 sm:w-2.5 sm:h-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <span class="truncate">{{ $supplier->city }}, {{ $supplier->country->name ?? '' }}</span>
                                </div>

                                <!-- Products Count -->
                                <div class="mb-1 sm:mb-2">
                                    <span class="inline-flex items-center gap-0.5 text-[8px] sm:text-[10px] font-medium text-purple-600 bg-purple-50 px-1 sm:px-1.5 py-0.5 rounded">
                                        <svg class="w-2 h-2 sm:w-2.5 sm:h-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span class="truncate">{{ number_format($supplier->products_count) }} {{ __('messages.products') }}</span>
                                    </span>
                                </div>

                                <!-- Business Type -->
                                @if($supplier->business_type)
                                <div>
                                    <span class="inline-flex items-center gap-0.5 text-[8px] sm:text-[10px] font-medium text-blue-600 bg-blue-50 px-1 sm:px-1.5 py-0.5 rounded">
                                        <svg class="w-2 h-2 sm:w-2.5 sm:h-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span class="truncate">{{ $supplier->business_type }}</span>
                                    </span>
                                </div>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="col-span-2 md:col-span-3 py-12 md:py-16 text-center">
                            <div class="flex flex-col items-center gap-3 md:gap-4">
                                <svg class="w-16 h-16 md:w-20 md:h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div>
                                    <p class="text-base md:text-lg font-semibold text-gray-700 mb-1">{{ __('messages.no_suppliers_available') }}</p>
                                    <p class="text-xs md:text-sm text-gray-500">{{ __('messages.check_back_later') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Hot Deals Section (Right) -->
            <div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 md:mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-6 md:h-8 bg-[#ff0808]"></div>
                        <h2 class="text-lg md:text-2xl font-bold text-gray-900">{{ __('messages.hot_deals') }}</h2>
                    </div>
                </div>

                @php
                    // Get hot deal products
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
                        ->limit(3)
                        ->get();
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                    @forelse($hotDeals as $product)
                        @php
                            $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                            $businessProfile = $product->user->businessProfile ?? null;
                            $price = $product->prices->first();
                            $mainPrice = $price ? $price->min_price : 0;
                            $maxPrice = $price ? $price->max_price : 0;
                            $currency = $price ? $price->currency : 'RWF';
                        @endphp

                        <a href="{{ route('products.show', $product->slug) }}" class="bg-white rounded-md border-2 border-transparent shadow-sm overflow-hidden hover:shadow-lg hover:border-[#faafaf] transition-all block">
                            <!-- Product Image -->
                            <div class="relative h-24 sm:h-28 md:h-32 overflow-hidden group">
                                @if($image)
                                    <img src="{{ $image->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                         loading="lazy">
                                @else
                                    <div class="flex justify-center items-center w-full h-full bg-gray-100">
                                        <span class="text-xl sm:text-2xl">📦</span>
                                    </div>
                                @endif

                                <!-- Hot Deal Badge -->
                                <span class="absolute top-1 right-1 sm:top-2 sm:right-2 bg-[#ff0808] text-white text-[8px] sm:text-[10px] font-bold px-1 sm:px-1.5 py-0.5 rounded-full animate-pulse">
                                    🔥 {{ __('messages.hot') }}
                                </span>

                                <!-- Verified Badge -->
                                @if($product->is_admin_verified)
                                <span class="absolute top-1 left-1 sm:top-2 sm:left-2 bg-green-600 text-white text-[8px] sm:text-[10px] font-bold px-1 sm:px-1.5 py-0.5 rounded">
                                    ✓ {{ __('messages.verified') }}
                                </span>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="p-2 sm:p-3">
                                <!-- Product Name -->
                                <h4 class="text-[10px] sm:text-xs font-bold text-gray-900 mb-1 sm:mb-2 hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[1.5rem] sm:min-h-[2rem]">
                                    {{ $product->name }}
                                </h4>

                                <!-- Category -->
                                @if($product->productCategory)
                                <div class="mb-1 sm:mb-2">
                                    <span class="inline-flex items-center gap-0.5 text-[8px] sm:text-[10px] font-medium text-purple-600 bg-purple-50 px-1 sm:px-1.5 py-0.5 rounded">
                                        <svg class="w-2 h-2 sm:w-2.5 sm:h-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <span class="truncate">{{ $product->productCategory->name }}</span>
                                    </span>
                                </div>
                                @endif

                                <!-- Price -->
                                <div class="text-[#ff0808] font-bold text-[10px] sm:text-xs mb-1 sm:mb-2">
                                    {{ $currency }} {{ number_format($mainPrice, 2) }}
                                    @if($maxPrice && $maxPrice != $mainPrice)
                                        <span class="text-[8px] sm:text-[10px]">- {{ number_format($maxPrice, 2) }}</span>
                                    @endif
                                </div>

                                <!-- MOQ -->
                                <div class="text-[8px] sm:text-[10px] text-gray-500 mb-1 sm:mb-2">
                                    {{ __('messages.moq') }}: {{ number_format($product->min_order_quantity) }} pcs
                                </div>

                                <!-- Location -->
                                <div class="flex items-center gap-0.5 sm:gap-1 text-[8px] sm:text-[10px] text-gray-500 mb-1 sm:mb-2">
                                    <svg class="w-2 h-2 sm:w-2.5 sm:h-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <span class="truncate">{{ $product->country->name ?? '' }}</span>
                                </div>

                                <!-- Supplier Badge -->
                                @if($businessProfile)
                                <div>
                                    <span class="inline-flex items-center gap-0.5 text-[8px] sm:text-[10px] font-medium text-blue-600 bg-blue-50 px-1 sm:px-1.5 py-0.5 rounded">
                                        <svg class="w-2 h-2 sm:w-2.5 sm:h-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span class="truncate">{{ $businessProfile->business_name }}</span>
                                    </span>
                                </div>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="col-span-2 md:col-span-3 py-12 md:py-16 text-center">
                            <div class="flex flex-col items-center gap-3 md:gap-4">
                                <svg class="w-16 h-16 md:w-20 md:h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                                <div>
                                    <p class="text-base md:text-lg font-semibold text-gray-700 mb-1">{{ __('messages.no_hot_deals_available') }}</p>
                                    <p class="text-xs md:text-sm text-gray-500">{{ __('messages.check_back_later') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</section>
