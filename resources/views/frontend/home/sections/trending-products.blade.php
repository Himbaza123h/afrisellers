<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Trending Products</h2>
                <p class="text-gray-600">Discover today's most popular items</p>
            </div>
            <a href="{{ route('products.search', ['type' => 'trendings', 'slug' => 'products']) }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                View All
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">
            @php
            $products = [
                [
                    'name' => 'Wireless Bluetooth Headphones Premium',
                    'image' => 'https://images.pexels.com/photos/3825517/pexels-photo-3825517.jpeg?auto=compress&cs=tinysrgb&w=500',
                    'price' => '$45 - $89',
                    'moq' => '50 pcs',
                    'country' => 'Kenya',
                    'flag' => '🇰🇪',
                    'hot' => true
                ],
                [
                    'name' => 'Solar Power Bank 20000mAh Portable',
                    'image' => 'https://images.pexels.com/photos/4219861/pexels-photo-4219861.jpeg?auto=compress&cs=tinysrgb&w=500',
                    'price' => '$12 - $25',
                    'moq' => '100 pcs',
                    'country' => 'Nigeria',
                    'flag' => '🇳🇬',
                    'hot' => true
                ],
                [
                    'name' => 'LED Smart Bulbs RGB Color Changing',
                    'image' => 'https://images.pexels.com/photos/1112598/pexels-photo-1112598.jpeg?auto=compress&cs=tinysrgb&w=500',
                    'price' => '$3 - $8',
                    'moq' => '500 pcs',
                    'country' => 'Ghana',
                    'flag' => '🇬🇭',
                    'hot' => false
                ],
                [
                    'name' => 'Stainless Steel Insulated Water Bottles',
                    'image' => 'https://images.pexels.com/photos/4318831/pexels-photo-4318831.jpeg?auto=compress&cs=tinysrgb&w=500',
                    'price' => '$5 - $15',
                    'moq' => '200 pcs',
                    'country' => 'South Africa',
                    'flag' => '🇿🇦',
                    'hot' => false
                ],
                [
                    'name' => 'Laptop Backpack with USB Charging Port',
                    'image' => 'https://images.pexels.com/photos/2905238/pexels-photo-2905238.jpeg?auto=compress&cs=tinysrgb&w=500',
                    'price' => '$18 - $35',
                    'moq' => '100 pcs',
                    'country' => 'Tanzania',
                    'flag' => '🇹🇿',
                    'hot' => false
                ],
                [
                    'name' => 'Smartwatch Fitness Tracker Heart Rate',
                    'image' => 'https://images.pexels.com/photos/393047/pexels-photo-393047.jpeg?auto=compress&cs=tinysrgb&w=500',
                    'price' => '$25 - $55',
                    'moq' => '50 pcs',
                    'country' => 'Egypt',
                    'flag' => '🇪🇬',
                    'hot' => false
                ],
                [
                    'name' => 'Cotton T-Shirts Wholesale Bulk',
                    'image' => 'https://images.pexels.com/photos/8532616/pexels-photo-8532616.jpeg?auto=compress&cs=tinysrgb&w=500',
                    'price' => '$4 - $12',
                    'moq' => '300 pcs',
                    'country' => 'Morocco',
                    'flag' => '🇲🇦',
                    'hot' => false
                ],
                [
                    'name' => 'Portable Bluetooth Speaker Waterproof',
                    'image' => 'https://images.pexels.com/photos/1034653/pexels-photo-1034653.jpeg?auto=compress&cs=tinysrgb&w=500',
                    'price' => '$15 - $35',
                    'moq' => '100 pcs',
                    'country' => 'Rwanda',
                    'flag' => '🇷🇼',
                    'hot' => false
                ],
                [
                    'name' => 'Organic Coffee Beans Premium Quality',
                    'image' => 'https://images.pexels.com/photos/4109998/pexels-photo-4109998.jpeg?auto=compress&cs=tinysrgb&w=500',
                    'price' => '$8 - $20',
                    'moq' => '100 kg',
                    'country' => 'Ethiopia',
                    'flag' => '🇪🇹',
                    'hot' => false
                ],
                [
                    'name' => 'USB Type-C Fast Charging Cables',
                    'image' => 'https://images.pexels.com/photos/3921713/pexels-photo-3921713.jpeg?auto=compress&cs=tinysrgb&w=500',
                    'price' => '$2 - $6',
                    'moq' => '1000 pcs',
                    'country' => 'Uganda',
                    'flag' => '🇺🇬',
                    'hot' => false
                ]
            ];
            @endphp

            @foreach($products as $product)
            <a href="{{ route('products.show', $product['name'])}}" class="bg-white rounded-lg border border-gray-200 hover:border-blue-400 hover:shadow-md transition-all duration-200 group block">
                <div class="aspect-square bg-gray-50 rounded-t-lg overflow-hidden relative">
                    <img src="{{ $product['image'] }}"
                         alt="{{ $product['name'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @if($product['hot'])
                    <span class="absolute top-3 right-3 bg-red-500 text-white text-xs px-2.5 py-1 rounded-full font-medium">
                        Hot
                    </span>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-medium text-gray-900 mb-2.5 line-clamp-2 text-sm leading-relaxed h-10">
                        {{ $product['name'] }}
                    </h3>
                    <div class="text-blue-600 font-semibold text-lg mb-1">
                        {{ $product['price'] }}
                    </div>
                    <div class="text-xs text-gray-500 mb-3">
                        MOQ: {{ $product['moq'] }}
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <span class="text-xs text-gray-600 flex items-center gap-1">
                            <span>{{ $product['flag'] }}</span>
                            <span>{{ $product['country'] }}</span>
                        </span>
                        <span class="text-green-600 text-xs font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
