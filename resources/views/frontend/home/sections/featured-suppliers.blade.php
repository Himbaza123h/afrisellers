<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Featured Suppliers</h2>
                <p class="text-gray-600">Trusted partners across Africa</p>
            </div>
            <a href="#" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                View All
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $suppliers = [
                [
                    'name' => 'TechHub Electronics',
                    'image' => 'https://images.pexels.com/photos/3184419/pexels-photo-3184419.jpeg?auto=compress&cs=tinysrgb&w=400',
                    'category' => 'Electronics & Technology',
                    'country' => 'Kenya',
                    'rating' => '4.9',
                    'reviews' => '2.4k',
                    'verified' => true
                ],
                [
                    'name' => 'AfriTextile Industries',
                    'image' => 'https://images.pexels.com/photos/6567607/pexels-photo-6567607.jpeg?auto=compress&cs=tinysrgb&w=400',
                    'category' => 'Textiles & Fashion',
                    'country' => 'Nigeria',
                    'rating' => '4.8',
                    'reviews' => '1.8k',
                    'verified' => true
                ],
                [
                    'name' => 'AgriPro Suppliers',
                    'image' => 'https://images.pexels.com/photos/2132180/pexels-photo-2132180.jpeg?auto=compress&cs=tinysrgb&w=400',
                    'category' => 'Agriculture & Food',
                    'country' => 'South Africa',
                    'rating' => '4.7',
                    'reviews' => '3.1k',
                    'verified' => true
                ],
                [
                    'name' => 'BuildMart Solutions',
                    'image' => 'https://images.pexels.com/photos/1216589/pexels-photo-1216589.jpeg?auto=compress&cs=tinysrgb&w=400',
                    'category' => 'Construction Materials',
                    'country' => 'Ghana',
                    'rating' => '4.6',
                    'reviews' => '1.5k',
                    'verified' => false
                ]
            ];
            @endphp

            @foreach($suppliers as $supplier)
            <div class="bg-white border border-gray-200 rounded-lg hover:shadow-lg transition-shadow duration-300">
                <div class="relative h-44">
                    <img src="{{ $supplier['image'] }}"
                         alt="{{ $supplier['name'] }}"
                         class="w-full h-full object-cover rounded-t-lg">
                    @if($supplier['verified'])
                    <span class="absolute top-3 right-3 bg-blue-600 text-white px-2 py-1 rounded text-xs font-medium">
                        Verified
                    </span>
                    @endif
                </div>

                <div class="p-5">
                    <h3 class="font-semibold text-lg text-gray-900 mb-1">
                        {{ $supplier['name'] }}
                    </h3>

                    <p class="text-sm text-gray-600 mb-3">
                        {{ $supplier['category'] }}
                    </p>

                    <div class="flex items-center gap-1 mb-4">
                        <span class="text-yellow-500 text-sm">★</span>
                        <span class="text-sm font-medium text-gray-900">{{ $supplier['rating'] }}</span>
                        <span class="text-sm text-gray-500">({{ $supplier['reviews'] }})</span>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <span class="text-sm text-gray-600">📍 {{ $supplier['country'] }}</span>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            View Profile →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
