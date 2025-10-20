<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Featured Suppliers</h2>
                <p class="text-gray-600">Connect with verified suppliers</p>
            </div>
            <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-2">
                View All
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @for($i = 1; $i <= 4; $i++)
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all overflow-hidden group">
                <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-500"></div>
                <div class="p-6 -mt-12">
                    <div class="w-20 h-20 bg-white rounded-lg shadow-md mb-4 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                        🏢
                    </div>
                    <h3 class="font-bold text-lg mb-2 text-gray-900">Premium Supplier {{ $i }}</h3>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-yellow-400">★★★★★</span>
                        <span class="text-sm text-gray-600">(4.8)</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">Electronics, Mobile Accessories, Smart Devices & More</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">🇰🇪 Kenya</span>
                        <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-semibold text-xs">Gold Member</span>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>
