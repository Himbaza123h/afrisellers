<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900">Show by region</h2>
            <a href="#" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                View more
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-6">
            @php
            $countries = [
                ['name' => 'Tanzania', 'flag' => '🇹🇿'],
                ['name' => 'Uganda', 'flag' => '🇺🇬'],
                ['name' => 'Rwanda', 'flag' => '🇷🇼'],
                ['name' => 'Senegal', 'flag' => '🇸🇳'],
                ['name' => 'Ivory Coast', 'flag' => '🇨🇮'],
                ['name' => 'Cameroon', 'flag' => '🇨🇲'],
                ['name' => 'Tunisia', 'flag' => '🇹🇳']
            ];
            @endphp

            @foreach($countries as $country)
            <a href="#" class="flex flex-col items-center group">
                <div class="w-20 h-20 bg-white rounded-full shadow-sm hover:shadow-md transition-shadow flex items-center justify-center mb-3 group-hover:scale-105 transform duration-200">
                    <span class="text-4xl">{{ $country['flag'] }}</span>
                </div>
                <span class="text-sm font-medium text-gray-900 text-center">{{ $country['name'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
