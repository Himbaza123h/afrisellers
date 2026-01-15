<aside class="w-full lg:w-64 flex-shrink-0">
    <div class="bg-white rounded-lg border p-6 sticky top-24 max-h-[calc(100vh-7rem)] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Filters</h2>
            <span id="filter-count-badge" class="hidden px-2 py-1 bg-blue-600 text-white text-xs rounded-full font-semibold"></span>
        </div>

        <form id="filter-form" method="GET" action="{{ route('products.search', ['type' => $type, 'slug' => $slug]) }}">
            <!-- Preserve tab parameter -->
            <input type="hidden" name="tab" value="{{ $tab }}">

            <!-- Preserve sort parameter -->
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <!-- Trade Assurance -->
            <div class="mb-6 pb-6 border-b">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-5 h-5 bg-yellow-400 rounded flex items-center justify-center mt-0.5">
                        <i class="fas fa-shield-alt text-white text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="trade_assurance" value="1"
                                   {{ request('trade_assurance') ? 'checked' : '' }}
                                   class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="font-semibold text-gray-900">Trade Assurance</span>
                        </label>
                        <p class="text-sm text-gray-600 mt-1">Protects your orders</p>
                    </div>
                </div>
            </div>

            <!-- Supplier Features -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-certificate text-blue-600 mr-2"></i>
                    Supplier Features
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer group hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="verified_supplier" value="1"
                               {{ request('verified_supplier') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-blue-600 font-medium group-hover:underline">Verified Supplier</span>
                        <i class="fas fa-check-circle text-green-500 ml-auto text-sm"></i>
                    </label>
                    <label class="flex items-center cursor-pointer group hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="verified_pro" value="1"
                               {{ request('verified_pro') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-blue-600 font-medium group-hover:underline">
                            Verified <span class="bg-blue-600 text-white text-xs px-1 rounded">PRO</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Price Range -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                    Price Range
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="radio" name="price_range" value="0-100"
                               {{ request('price_range') == '0-100' ? 'checked' : '' }}
                               class="mr-2 filter-radio text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Under $100</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="radio" name="price_range" value="100-500"
                               {{ request('price_range') == '100-500' ? 'checked' : '' }}
                               class="mr-2 filter-radio text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">$100 - $500</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="radio" name="price_range" value="500-1000"
                               {{ request('price_range') == '500-1000' ? 'checked' : '' }}
                               class="mr-2 filter-radio text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">$500 - $1,000</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="radio" name="price_range" value="1000-5000"
                               {{ request('price_range') == '1000-5000' ? 'checked' : '' }}
                               class="mr-2 filter-radio text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">$1,000 - $5,000</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="radio" name="price_range" value="5000-plus"
                               {{ request('price_range') == '5000-plus' ? 'checked' : '' }}
                               class="mr-2 filter-radio text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Over $5,000</span>
                    </label>
                </div>
            </div>

            <!-- Minimum Order Quantity -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-boxes text-orange-600 mr-2"></i>
                    Min Order Quantity
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="moq_1_10" value="1"
                               {{ request('moq_1_10') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">1-10 pieces</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="moq_11_50" value="1"
                               {{ request('moq_11_50') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">11-50 pieces</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="moq_51_100" value="1"
                               {{ request('moq_51_100') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">51-100 pieces</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="moq_100_plus" value="1"
                               {{ request('moq_100_plus') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">100+ pieces</span>
                    </label>
                </div>
            </div>

            <!-- Store Reviews -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                    Store Reviews
                </h3>
                <p class="text-sm text-gray-600 mb-3">Based on 5-star rating</p>
                <div class="space-y-2">
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="radio" name="rating" value="4_up"
                               {{ request('rating') == '4_up' ? 'checked' : '' }}
                               class="mr-2 filter-radio text-blue-600 focus:ring-blue-500">
                        <div class="flex items-center">
                            <span class="text-gray-700 mr-2">4.0 & up</span>
                            <div class="flex text-yellow-400 text-xs">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="radio" name="rating" value="4.5_up"
                               {{ request('rating') == '4.5_up' ? 'checked' : '' }}
                               class="mr-2 filter-radio text-blue-600 focus:ring-blue-500">
                        <div class="flex items-center">
                            <span class="text-gray-700 mr-2">4.5 & up</span>
                            <div class="flex text-yellow-400 text-xs">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="radio" name="rating" value="5_up"
                               {{ request('rating') == '5_up' ? 'checked' : '' }}
                               class="mr-2 filter-radio text-blue-600 focus:ring-blue-500">
                        <div class="flex items-center">
                            <span class="text-gray-700 mr-2">5.0</span>
                            <div class="flex text-yellow-400 text-xs">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Shipping Options -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-shipping-fast text-blue-600 mr-2"></i>
                    Shipping Options
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="free_shipping" value="1"
                               {{ request('free_shipping') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Free Shipping</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="fast_dispatch" value="1"
                               {{ request('fast_dispatch') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Fast Dispatch (≤7 days)</span>
                    </label>
                </div>
            </div>

            <!-- Product Features -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tags text-purple-600 mr-2"></i>
                    Product Features
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="paid_samples" value="1"
                               {{ request('paid_samples') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Paid samples available</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="customizable" value="1"
                               {{ request('customizable') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Customizable</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="eco_friendly" value="1"
                               {{ request('eco_friendly') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Eco-friendly</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="ready_to_ship" value="1"
                               {{ request('ready_to_ship') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Ready to ship</span>
                    </label>
                </div>
            </div>

            <!-- Supplier Location -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                    Supplier Location
                </h3>
                <div class="space-y-3">
                    @php
                        $popularCountries = ['China', 'India', 'United States', 'Germany', 'Japan', 'South Korea', 'Vietnam', 'Thailand'];
                    @endphp
                    @foreach($popularCountries as $country)
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                            <input type="checkbox" name="countries[]" value="{{ strtolower(str_replace(' ', '_', $country)) }}"
                                   {{ in_array(strtolower(str_replace(' ', '_', $country)), request('countries', [])) ? 'checked' : '' }}
                                   class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">{{ $country }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Response Time -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-clock text-indigo-600 mr-2"></i>
                    Response Time
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="response_within_24h" value="1"
                               {{ request('response_within_24h') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Within 24 hours</span>
                    </label>
                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                        <input type="checkbox" name="response_within_1h" value="1"
                               {{ request('response_within_1h') ? 'checked' : '' }}
                               class="mr-2 filter-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Within 1 hour</span>
                    </label>
                </div>
            </div>

            <!-- Clear Filters Button -->
            <button type="button" class="clear-filters-btn w-full bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-900 font-semibold py-3 px-4 rounded-lg transition-all shadow-sm hover:shadow-md">
                <i class="fas fa-times-circle mr-2"></i>
                Clear all filters
            </button>
        </form>
    </div>
</aside>
