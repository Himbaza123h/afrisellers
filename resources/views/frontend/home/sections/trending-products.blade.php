<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Trending Products</h2>
                <p class="text-gray-600">Hot deals and best sellers</p>
            </div>
            <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-2">
                View All
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-6">
            @for($i = 1; $i <= 6; $i++)
            <div class="bg-white rounded-lg border border-gray-200 hover:shadow-lg transition-all group cursor-pointer">
                <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400"
                         alt="Product"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">Hot</span>
                </div>
                <div class="p-3 md:p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 text-sm">Premium Headphones Wireless Bluetooth</h3>
                    <div class="text-blue-600 font-bold text-base md:text-lg mb-1">$99 - $149</div>
                    <div class="text-xs text-gray-500 mb-2">MOQ: 100 pcs</div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">🇹🇿 Tanzania</span>
                        <span class="text-green-600 font-semibold">✓ Verified</span>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>
