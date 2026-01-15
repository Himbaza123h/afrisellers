<section class="py-12 bg-white">
    <div class="container px-4 mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-lg font-bold text-gray-900">{{ __('messages.show_by_region') }}</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
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
                   class="bg-gradient-to-br {{ $colorClass }} hover:shadow-xl rounded-xl p-6 text-center transition-all group border-2 border-transparent hover:border-blue-400">
                    <div class="mb-4 text-5xl transition-transform duration-300 group-hover:scale-125">{{ $icon }}</div>
                    <div class="mb-2 text-base font-bold text-gray-900 transition-colors group-hover:text-blue-600">{{ $region->name }}</div>
                    <div class="text-sm font-semibold text-gray-600">{{ $countriesCount }} {{ Str::plural('Country', $countriesCount) }}</div>
                </a>
            @empty
                <div class="col-span-full py-12 text-center">
                    <p class="text-gray-500">{{ __('messages.no_regions_available') ?? 'No regions available yet.' }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
