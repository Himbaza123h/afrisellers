{{-- Browse by Region Section - Compact Size with Interactive Map --}}
<section class="py-6 md:py-8 bg-gray-50">
    <div class="container px-4 mx-auto">
        <!-- Section Title with Left-Aligned Decorative Underline -->
        <div class="flex items-center mb-3 md:mb-4 lg:mb-6 gap-3">
            <h2 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-900 whitespace-nowrap">
                {{ __('messages.browse_by_region') ?? 'Browse by Region' }}
            </h2>
            <div class="flex-1 h-px bg-gray-300 to-transparent"></div>
        </div>

        <!-- Main Grid: 40% Left | 60% Right -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
            <!-- Left Side: Categories & Map (40%) -->
            <div class="relative order-2 lg:order-1 lg:col-span-5">
                <!-- Categories List - Compact Size -->
                <div class="space-y-0.5">
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
                           class="flex items-center gap-2 px-2.5 md:px-3 py-1.5 md:py-2 text-[10px] md:text-xs text-gray-700 hover:bg-white rounded transition-colors">
                            <span class="w-4 h-4 md:w-5 md:h-5 {{ $categoryColors[$category->name] ?? 'bg-gray-100' }} rounded flex items-center justify-center text-[10px] md:text-xs flex-shrink-0">
                                {{ $categoryIcons[$category->name] ?? '📦' }}
                            </span>
                            <span class="font-medium truncate">{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>

                <!-- Map Image Overlapping - Interactive Regions -->
                <div class="absolute top-0 left-1/2 md:left-36 lg:left-40 bottom-0 w-full max-w-xs md:max-w-sm lg:max-w-md opacity-20 md:opacity-30 lg:opacity-90 pointer-events-none lg:pointer-events-auto">
                    <!-- Clickable Map Center for Reset -->
                    <div id="mapCenter" class="absolute inset-0 cursor-pointer" title="Click to show all countries">
                        <img src="{{ asset('africaimage.png') }}"
                             alt="Africa Map"
                             class="w-full h-full object-contain">
                    </div>

                    <!-- Interactive Region Labels (Hidden on mobile, visible on desktop) -->
                    <div class="hidden lg:block">
                        @php
                            // Get active regions from database with same conditions as the other section
                            $activeRegions = $regions ?? \App\Models\Region::active()
                                ->withCount('countries')
                                ->orderBy('name', 'asc')
                                ->get();

                            // Region positioning and colors
                            $regionPositions = [
                                'West Africa' => ['top' => '25%', 'left' => '15%', 'color' => 'teal'],
                                'East Africa' => ['top' => '30%', 'right' => '25%', 'color' => 'orange'],
                                'Central Africa' => ['top' => '45%', 'left' => '45%', 'color' => 'blue'],
                                'Southern Africa' => ['bottom' => '15%', 'left' => '35%', 'color' => 'green'],
                                'North Africa' => ['top' => '10%', 'left' => '30%', 'color' => 'yellow'],
                                'Region Diaspora' => ['bottom' => '5%', 'right' => '10%', 'color' => 'purple'],
                            ];
                        @endphp

                        @foreach($activeRegions as $region)
                            @php
                                $position = $regionPositions[$region->name] ?? ['top' => '50%', 'left' => '50%', 'color' => 'gray'];
                                $color = $position['color'];

                                // Build position styles
                                $positionStyle = '';
                                if (isset($position['top'])) $positionStyle .= "top: {$position['top']}; ";
                                if (isset($position['bottom'])) $positionStyle .= "bottom: {$position['bottom']}; ";
                                if (isset($position['left'])) $positionStyle .= "left: {$position['left']}; ";
                                if (isset($position['right'])) $positionStyle .= "right: {$position['right']}; ";

                                // Create slug for data attribute
                                $regionSlug = Str::slug($region->name);

                                // Get countries count
                                $countriesCount = $region->countries()->where('status', 'active')->count();
                            @endphp

                            @if($countriesCount > 0)
                                <div class="absolute cursor-pointer region-label z-10"
                                     style="{{ $positionStyle }}"
                                     data-region-id="{{ $region->id }}"
                                     data-region-slug="{{ $regionSlug }}"
                                     data-region-name="{{ $region->name }}">
                                    <div class="bg-{{ $color }}-500 text-white text-[9px] px-2 py-0.5 rounded-full font-bold shadow-lg hover:bg-{{ $color }}-600 transition-colors whitespace-nowrap">
                                        {{ $region->name }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Side: Country Cards (60%) -->
            <div class="relative z-10 order-1 lg:order-2 lg:col-span-7">
                <div class="mb-3 md:mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <h3 class="text-sm md:text-base lg:text-lg font-bold text-gray-900">
                        <span id="selected-region-name">{{ __('messages.all_countries') ?? 'All Countries' }}</span>
                    </h3>
                    <a href="{{ route('featured-suppliers') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-[10px] md:text-xs flex items-center gap-1">
                        {{ __('messages.view_all_regions') ?? 'View All Regions' }}
                        <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Country Cards Grid - 2 cols mobile, 4 cols desktop -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3" id="countriesGrid">
                    @php
                        // Prepare all countries data for JavaScript
                        $allCountriesData = [];
                        $regionCountriesData = (object)[];

                        // Get all active regions with their active countries
                        $regionsWithCountries = \App\Models\Region::active()
                            ->with(['countries' => function($query) {
                                $query->where('status', 'active')
                                    ->orderBy('name', 'asc');
                            }])
                            ->orderBy('name', 'asc')
                            ->get();

                        foreach ($regionsWithCountries as $region) {
                            $regionSlug = Str::slug($region->name);
                            $regionCountries = [];

                            foreach ($region->countries as $country) {
                                $countryData = [
                                    'id' => $country->id,
                                    'name' => $country->name,
                                    'image' => $country->image,
                                    'flag_url' => $country->flag_url,
                                    'suppliers_count' => $country->suppliers_count ?? $country->getVendorsCount(),
                                    'url' => route('featured-suppliers', ['country' => $country->id]),
                                    'region_id' => $region->id,
                                    'region_name' => $region->name,
                                ];

                                $regionCountries[] = $countryData;
                                $allCountriesData[] = $countryData;
                            }

                            $regionCountriesData->{$regionSlug} = $regionCountries;
                            $regionCountriesData->{$region->id} = $regionCountries;
                        }

                        // Get initial display countries (all countries)
                        $displayCountries = collect($allCountriesData)->take(8);
                    @endphp

                    @forelse($displayCountries as $country)
                        <a href="{{ $country['url'] }}"
                            class="country-card block bg-white to-gray-50 hover:shadow-lg rounded-lg transition-all group border border-gray-200 hover:border-blue-400"
                            data-country-id="{{ $country['id'] }}"
                            data-country-name="{{ $country['name'] }}"
                            data-region-id="{{ $country['region_id'] ?? '' }}">
                            <!-- Country Image - Compact -->
                            <div class="relative h-16 md:h-20 overflow-hidden rounded-t-lg">
                                @if($country['image'])
                                    <img src="{{ $country['image'] }}"
                                         alt="{{ $country['name'] }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy">
                                @else
                                    <div class="flex justify-center items-center w-full h-full bg-blue-100 to-blue-200">
                                        <span class="text-2xl md:text-3xl">🌍</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/30 to-transparent"></div>
                            </div>

                            <!-- Country Info - Compact -->
                            <div class="p-2 md:p-2.5">
                                <div class="flex items-center gap-1 mb-1">
                                    @if($country['flag_url'])
                                        <img src="{{ $country['flag_url'] }}"
                                             alt="{{ $country['name'] }}"
                                             class="w-4 h-3 md:w-5 md:h-4 object-cover rounded shadow-sm flex-shrink-0">
                                    @endif
                                    <h4 class="text-[10px] md:text-xs font-bold text-gray-900 transition-colors group-hover:text-blue-600 truncate">
                                        {{ $country['name'] }}
                                    </h4>
                                </div>

                                <div class="mb-0.5">
                                    <span class="text-base md:text-lg font-bold text-gray-900">{{ $country['suppliers_count'] }}+</span>
                                </div>

                                <p class="text-[9px] md:text-[10px] text-gray-600">
                                    {{ __('messages.products_sell_requests') ?? 'Products / Sell Requests' }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-8 text-center">
                            <div class="text-gray-400 text-4xl mb-2">🌍</div>
                            <p class="text-gray-600 text-sm">{{ __('messages.no_countries_available') ?? 'No countries available' }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="hidden text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
                    <p class="mt-2 text-gray-600 text-sm">{{ __('messages.loading_countries') ?? 'Loading countries...' }}</p>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="hidden text-center py-8">
                    <div class="text-gray-400 text-4xl mb-2">🌍</div>
                    <p class="text-gray-600 text-sm">{{ __('messages.no_countries_in_region') ?? 'No countries available for this region' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Region labels interactive functionality
    const regionLabels = document.querySelectorAll('.region-label');
    const regionNameDisplay = document.getElementById('selected-region-name');
    const countriesGrid = document.getElementById('countriesGrid');
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    const mapCenter = document.getElementById('mapCenter');

    // Get countries data from backend
    const allCountries = {!! json_encode($allCountriesData ?? []) !!};
    const regionCountries = {!! json_encode($regionCountriesData ?? new stdClass()) !!};

    let currentRegion = null; // Track current selected region

    // Function to create country card HTML
    function createCountryCard(country) {
        const imageHtml = country.image
            ? `<img src="${country.image}" alt="${country.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">`
            : `<div class="flex justify-center items-center w-full h-full bg-blue-100 to-blue-200"><span class="text-2xl md:text-3xl">🌍</span></div>`;

        const flagHtml = country.flag_url
            ? `<img src="${country.flag_url}" alt="${country.name}" class="w-4 h-3 md:w-5 md:h-4 object-cover rounded shadow-sm flex-shrink-0">`
            : '';

        return `
            <a href="${country.url}"
                class="country-card block bg-white to-gray-50 hover:shadow-lg rounded-lg transition-all group border border-gray-200 hover:border-blue-400"
                data-country-id="${country.id}"
                data-country-name="${country.name}"
                data-region-id="${country.region_id || ''}">
                <div class="relative h-16 md:h-20 overflow-hidden rounded-t-lg">
                    ${imageHtml}
                    <div class="absolute inset-0 bg-black/30 to-transparent"></div>
                </div>
                <div class="p-2 md:p-2.5">
                    <div class="flex items-center gap-1 mb-1">
                        ${flagHtml}
                        <h4 class="text-[10px] md:text-xs font-bold text-gray-900 transition-colors group-hover:text-blue-600 truncate">
                            ${country.name}
                        </h4>
                    </div>
                    <div class="mb-0.5">
                        <span class="text-base md:text-lg font-bold text-gray-900">${country.suppliers_count || 0}+</span>
                    </div>
                    <p class="text-[9px] md:text-[10px] text-gray-600">
                        {{ __('messages.products_sell_requests') ?? 'Products / Sell Requests' }}
                    </p>
                </div>
            </a>
        `;
    }

    // Function to update countries grid
    function updateCountriesGrid(countries, regionName = null) {
        // Show loading state
        countriesGrid.classList.add('hidden');
        loadingState.classList.remove('hidden');
        emptyState.classList.add('hidden');

        // Simulate loading delay for better UX
        setTimeout(() => {
            loadingState.classList.add('hidden');

            if (countries && countries.length > 0) {
                // Update grid with countries (take first 8)
                const displayCountries = countries.slice(0, 8);
                countriesGrid.innerHTML = displayCountries.map(country => createCountryCard(country)).join('');
                countriesGrid.classList.remove('hidden');
                emptyState.classList.add('hidden');

                // Update region name
                if (regionNameDisplay) {
                    regionNameDisplay.textContent = regionName || '{{ __("messages.all_countries") ?? "All Countries" }}';
                }
            } else {
                // Show empty state
                countriesGrid.classList.add('hidden');
                emptyState.classList.remove('hidden');
            }
        }, 300);
    }

    // Function to reset all region highlights
    function resetRegionHighlights() {
        regionLabels.forEach(l => {
            const labelDiv = l.querySelector('div');
            labelDiv.classList.remove('ring-2', 'ring-white', 'scale-110');
        });
    }

    // Map center click handler - Reset to show all countries
    if (mapCenter) {
        mapCenter.addEventListener('click', function(e) {
            // Only trigger if clicking directly on map, not on region labels
            if (e.target === this || e.target.tagName === 'IMG') {
                console.log('Map center clicked - showing all countries');

                currentRegion = null;
                resetRegionHighlights();
                updateCountriesGrid(allCountries, '{{ __("messages.all_countries") ?? "All Countries" }}');
            }
        });
    }

    // Region label click handlers
    regionLabels.forEach(label => {
        label.addEventListener('click', function(e) {
            e.stopPropagation();

            const regionId = this.getAttribute('data-region-id');
            const regionSlug = this.getAttribute('data-region-slug');
            const regionName = this.getAttribute('data-region-name');

            console.log('Selected region:', regionName, 'ID:', regionId);

            currentRegion = regionId;

            // Reset all highlights
            resetRegionHighlights();

            // Highlight selected region
            const thisLabelDiv = this.querySelector('div');
            thisLabelDiv.classList.add('ring-2', 'ring-white', 'scale-110');

            // Get countries for this region
            let countries = regionCountries[regionId] || regionCountries[regionSlug] || [];

            console.log('Countries found:', countries.length);

            // Update countries grid
            updateCountriesGrid(countries, regionName);
        });

        // Hover effect
        label.addEventListener('mouseenter', function() {
            const labelDiv = this.querySelector('div');
            labelDiv.classList.add('scale-110');
        });

        label.addEventListener('mouseleave', function() {
            const labelDiv = this.querySelector('div');
            if (!labelDiv.classList.contains('ring-2')) {
                labelDiv.classList.remove('scale-110');
            }
        });
    });

    console.log('Browse by Region initialized');
    console.log('Total countries:', allCountries.length);
    console.log('Regions with countries:', Object.keys(regionCountries).length);
});
</script>
@endpush

<style>
/* Smooth transitions for region labels */
.region-label div {
    transition: all 0.3s ease;
}

.region-label:hover div {
    transform: scale(1.1);
}

/* Map center hover effect */
#mapCenter {
    transition: opacity 0.3s ease;
}

#mapCenter:hover {
    opacity: 0.95;
}

/* Responsive grid adjustments */
@media (max-width: 768px) {
    #countriesGrid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    #countriesGrid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 1025px) {
    #countriesGrid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Fade in animation for country cards */
.country-card {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
{{-- End of Browse by Region Section --}}
