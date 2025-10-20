<section class="py-16 bg-gradient-to-br from-blue-50 to-purple-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Shop by Region</h2>
            <p class="text-gray-600">Discover suppliers across Africa</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['region' => 'East Africa', 'countries' => 'Kenya, Tanzania, Uganda, Rwanda', 'suppliers' => '3,456', 'bg' => 'from-green-400 to-green-600'],
                ['region' => 'West Africa', 'countries' => 'Nigeria, Ghana, Senegal, Ivory Coast', 'suppliers' => '4,567', 'bg' => 'from-yellow-400 to-orange-600'],
                ['region' => 'Southern Africa', 'countries' => 'South Africa, Zimbabwe, Botswana', 'suppliers' => '2,345', 'bg' => 'from-purple-400 to-purple-600'],
            ] as $region)
            <a href="#" class="relative bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all group">
                <div class="h-48 bg-gradient-to-br {{ $region['bg'] }} flex items-center justify-center text-7xl group-hover:scale-110 transition-transform">
                    🌍
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $region['region'] }}</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ $region['countries'] }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 font-semibold">{{ $region['suppliers'] }} suppliers</span>
                        <span class="text-blue-600 font-semibold group-hover:translate-x-2 transition-transform inline-flex items-center gap-1">
                            Explore
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
