{{-- Browse by Region Section --}}
<section class="py-8 md:py-12 bg-gray-50">
    <div class="container px-4 mx-auto">
        <!-- Section Title -->
        <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-4 md:mb-6 lg:mb-8">Browse by Region</h2>

        <!-- Main Grid: 50% Left | 50% Right -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
            <!-- Left Side: Categories & Map (50%) -->
            <div class="relative order-2 lg:order-1">
                <!-- Categories List -->
                <div class="space-y-1">
                    @php
                        $categoryIcons = [
                            'Agriculture & Food' => '📦',
                            'Machinery & Equipment' => '▶️',
                            'Mining & Minerals' => '🌿',
                            'Construction & Building' => '🔶',
                            'Energy & Utilities' => '⚪',
                            'Textile, Apparel & Leather' => '🍃',
                            'Pharma & Chemicals' => '🔴',
                            'Logistics & Services' => '🚚',
                        ];
                        $categoryColors = [
                            'Agriculture & Food' => 'bg-red-100',
                            'Machinery & Equipment' => 'bg-orange-100',
                            'Mining & Minerals' => 'bg-green-100',
                            'Construction & Building' => 'bg-yellow-100',
                            'Energy & Utilities' => 'bg-gray-100',
                            'Textile, Apparel & Leather' => 'bg-emerald-100',
                            'Pharma & Chemicals' => 'bg-red-100',
                            'Logistics & Services' => 'bg-blue-100',
                        ];
                    @endphp
                    @foreach($categories->take(8) as $category)
                        <a href=""
                           class="flex items-center gap-2 md:gap-3 px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm text-gray-700 hover:bg-white rounded-lg transition-colors">
                            <span class="w-5 h-5 md:w-6 md:h-6 {{ $categoryColors[$category->name] ?? 'bg-gray-100' }} rounded flex items-center justify-center text-xs md:text-sm flex-shrink-0">
                                {{ $categoryIcons[$category->name] ?? '📦' }}
                            </span>
                            <span class="font-medium truncate">{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>

                <!-- Map Image Overlapping - Now visible on all screens -->
                <div class="absolute top-0 left-1/2 md:left-48 lg:left-48 bottom-0 w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg opacity-20 md:opacity-30 lg:opacity-100 pointer-events-none">
                    <img src="{{ asset('africaimage.png') }}"
                         alt="Africa Map"
                         class="w-full h-full object-contain">
                </div>
            </div>

            <!-- Right Side: Country Cards (50%) -->
            <div class="relative z-10 order-1 lg:order-2">
                <div class="mb-4 md:mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <h3 class="text-lg md:text-xl font-bold text-gray-900">
                        <span id="selected-region-name">Featured Countries</span>
                    </h3>
                    <a href="{{ route('featured-suppliers') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-xs md:text-sm flex items-center gap-2">
                        View All
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Country Cards Grid - Responsive: 2 cols mobile, 2 cols tablet, 3 cols desktop -->
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                    @php
                        // Get all countries from first region or all regions combined
                        $displayCountries = !empty($countriesByRegion) ? reset($countriesByRegion) : collect();
                    @endphp
                    @foreach($displayCountries->take(6) as $country)
                        <a href="{{ route('featured-suppliers', ['country' => $country->id]) }}"
                            class="block bg-gradient-to-br from-gray-50 to-gray-100 hover:shadow-xl rounded-xl transition-all group border-2 border-transparent hover:border-blue-400">
                            <!-- Country Image -->
                            <div class="relative h-20 sm:h-24 md:h-28 overflow-hidden rounded-t-xl">
                                @if($country->image)
                                    <img src="{{  $country->image }}"
                                         alt="{{ $country->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy">
                                @else
                                    <div class="flex justify-center items-center w-full h-full bg-gradient-to-br from-blue-100 to-blue-200">
                                        <span class="text-4xl md:text-5xl lg:text-6xl">🌍</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            </div>

                            <!-- Country Info -->
                            <div class="p-2 sm:p-3 md:p-4">
                                <div class="flex items-center gap-1 sm:gap-2 mb-1 sm:mb-2">
                                    @if($country->flag_url)
                                        <img src="{{ $country->flag_url }}"
                                             alt="{{ $country->name }}"
                                             class="w-5 h-4 sm:w-6 sm:h-5 md:w-8 md:h-6 object-cover rounded shadow-sm flex-shrink-0">
                                    @endif
                                    <h4 class="text-xs sm:text-sm md:text-base font-bold text-gray-900 transition-colors group-hover:text-blue-600 truncate">
                                        {{ $country->name }}
                                    </h4>
                                </div>

                                <div class="mb-1 sm:mb-2">
                                    <span class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900">{{ $country->suppliers_count }}+</span>
                                </div>

                                <p class="text-[10px] sm:text-xs text-gray-600">
                                    Visit {{ $country->name }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Add more countries if needed -->
                @if($displayCountries->count() > 6)
                    <div class="mt-3 md:mt-4 grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                        @foreach($displayCountries->slice(6)->take(6) as $country)
                            <a href="{{ route('featured-suppliers', ['country' => $country->id]) }}"
                            class="block bg-gradient-to-br from-gray-50 to-gray-100 hover:shadow-xl rounded-xl transition-all group border-2 border-transparent hover:border-blue-400">
                                <!-- Country Image -->
                                <div class="relative h-20 sm:h-24 md:h-28 overflow-hidden rounded-t-xl">
                                    @if($country->image)
                                        <img src="{{  $country->image }}"
                                             alt="{{ $country->name }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy">
                                    @else
                                        <div class="flex justify-center items-center w-full h-full bg-gradient-to-br from-blue-100 to-blue-200">
                                            <span class="text-4xl md:text-5xl lg:text-6xl">🌍</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                </div>

                                <!-- Country Info -->
                                <div class="p-2 sm:p-3 md:p-4">
                                    <div class="flex items-center gap-1 sm:gap-2 mb-1 sm:mb-2">
                                        @if($country->flag_url)
                                            <img src="{{ $country->flag_url }}"
                                                 alt="{{ $country->name }}"
                                                 class="w-5 h-4 sm:w-6 sm:h-5 md:w-8 md:h-6 object-cover rounded shadow-sm flex-shrink-0">
                                        @endif
                                        <h4 class="text-xs sm:text-sm md:text-base font-bold text-gray-900 transition-colors group-hover:text-blue-600 truncate">
                                            {{ $country->name }}
                                        </h4>
                                    </div>

                                    <div class="mb-1 sm:mb-2">
                                        <span class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900">{{ $country->suppliers_count }}+</span>
                                    </div>

                                    <p class="text-[10px] sm:text-xs text-gray-600">
                                        Visit {{ $country->name }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
{{-- End of Browse by Region Section --}}
