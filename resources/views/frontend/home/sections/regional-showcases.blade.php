<section class="py-6 md:py-8 bg-white">
    <div class="container px-4 mx-auto">
        <div class="flex justify-between items-center mb-3 md:mb-4">
            <h2 class="text-base md:text-lg lg:text-xl font-bold text-gray-900">{{ __('messages.show_by_region') }}</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 md:gap-3">
            @php
                // Region-specific icons and colors based on actual database regions
                $regionData = [
                    'East Africa' => ['icon' => '🌍', 'color' => 'from-blue-50 to-blue-100'],
                    'West Africa' => ['icon' => '🌎', 'color' => 'from-green-50 to-green-100'],
                    'Southern Africa' => ['icon' => '🗺️', 'color' => 'from-purple-50 to-purple-100'],
                    'North Africa' => ['icon' => '🌏', 'color' => 'from-orange-50 to-orange-100'],
                    'Central Africa' => ['icon' => '🧭', 'color' => 'from-teal-50 to-teal-100'],
                    'Region Diaspora' => ['icon' => '✈️', 'color' => 'from-pink-50 to-pink-100'],
                ];

                // Default fallback
                $defaultData = ['icon' => '🌐', 'color' => 'from-gray-50 to-gray-100'];

                // Use passed regions or get from database
                $activeRegions = $regions ?? \App\Models\Region::active()
                    ->withCount('countries')
                    ->orderBy('name', 'asc')
                    ->get();
            @endphp

            @forelse($activeRegions as $region)
                @php
                    $data = $regionData[$region->name] ?? $defaultData;
                    $icon = $data['icon'];
                    $colorClass = $data['color'];

                    // Get region statistics - only countries count
                    $countriesCount = $region->countries()->where('status', 'active')->count();
                @endphp
                <a href="{{ route('regions.countries', $region->id) }}"
                   class="bg- {{ $colorClass }} hover:shadow-lg rounded-lg md:rounded-xl p-2 md:p-3 text-center transition-all group border border-transparent hover:border-blue-400">
                    <div class="mb-1.5 md:mb-2 text-xl md:text-2xl transition-transform duration-300 group-hover:scale-110">{{ $icon }}</div>
                    <div class="mb-0.5 md:mb-1 text-[9px] md:text-[10px] font-bold text-gray-900 transition-colors group-hover:text-blue-600 leading-tight">{{ $region->name }}</div>
                    <div class="text-[8px] md:text-[9px] font-semibold text-gray-600">{{ $countriesCount }} {{ Str::plural('Country', $countriesCount) }}</div>
                </a>
            @empty
                <div class="col-span-full py-12 text-center">
                    <p class="text-gray-500 text-sm">{{ __('messages.no_regions_available') ?? 'No regions available yet.' }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
