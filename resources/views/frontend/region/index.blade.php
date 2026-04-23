@extends('layouts.app')

@section('title', 'Explore Suppliers by Region')

@section('content')
    <div class="py-12 min-h-screen bg-white">
        <div class="container px-4 mx-auto max-w-7xl">
            <!-- Breadcrumb Navigation -->
            <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-4">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-[#ff0808] transition-colors font-medium">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-900 font-semibold">Regions</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="mb-12">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-lg font-bold text-gray-900">Explore Suppliers by Region</h2>
                </div>
            </div>

            <!-- Regions Grid -->
            @if($regions->count() > 0)
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
                    @endphp

                    @foreach($regions as $region)
                        @php
                            $data = $regionData[$region->name] ?? $defaultData;
                            $icon = $data['icon'];
                            $colorClass = $data['color'];

                            // Get region statistics
                            $countriesCount = $region->countries()->active()->count();

                            $totalSuppliers = \App\Models\BusinessProfile::whereHas('country', function($q) use ($region) {
                                $q->where('region_id', $region->id)->where('status', 'active');
                            })->where('is_admin_verified', true)->count();

                            $totalProducts = \App\Models\Product::whereHas('country', function($q) use ($region) {
                                $q->where('region_id', $region->id)->where('status', 'active');
                            })->where('status', 'active')->where('is_admin_verified', true)->count();
                        @endphp

                        <!-- Wrapper div for positioning -->
                        <div class="relative group">
                            <a href="{{ route('regions.countries', $region->id) }}"
                               class="block bg-gradient-to-br {{ $colorClass }} hover:shadow-xl rounded-xl p-6 text-center transition-all duration-300 border-2 border-transparent group-hover:border-[#ff0808]">

                                <!-- Region Icon -->
                                <div class="mb-4 text-5xl transition-transform duration-300 group-hover:scale-125">
                                    {{ $icon }}
                                </div>

                                <!-- Region Name -->
                                <div class="mb-2 text-base font-bold text-gray-900 transition-colors group-hover:text-[#ff0808]">
                                    {{ $region->name }}
                                </div>

                                <!-- Country Count (visible by default) -->
                                <div class="text-sm font-semibold text-gray-600">
                                    {{ $countriesCount }} {{ Str::plural('Country', $countriesCount) }}
                                </div>
                            </a>

                            <!-- Hover Card - Now with clickable links -->
                            <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-56 bg-white rounded-lg shadow-2xl border-2 border-[#ff0808] p-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:-translate-y-2 z-50 pointer-events-none">
                                <!-- Arrow pointing down -->
                                <div class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-[#ff0808]"></div>
                                <div class="absolute left-1/2 -translate-x-1/2 top-full mt-[-2px] w-0 h-0 border-l-[7px] border-r-[7px] border-t-[7px] border-l-transparent border-r-transparent border-t-white"></div>

                                <!-- Countries Count -->
                                <div class="mb-3 pb-3 border-b border-gray-200">
                                    <a href="{{ route('regions.countries', $region->id) }}"
                                       class="flex items-center justify-between gap-2 hover:bg-gray-50 p-2 -m-2 rounded transition-colors pointer-events-auto">
                                        <span class="text-sm font-medium text-gray-600">Countries</span>
                                        <div class="bg-gray-800 text-white text-xs font-bold px-2 py-1 rounded">
                                            {{ number_format($countriesCount) }}
                                        </div>
                                    </a>
                                </div>

                                <!-- Suppliers Count -->
                                <div class="mb-3 pb-3 border-b border-gray-200">
                                    <a href=""
                                       class="flex items-center justify-between gap-2 hover:bg-gray-50 p-2 -m-2 rounded transition-colors pointer-events-auto">
                                        <span class="text-sm font-medium text-gray-600">Suppliers</span>
                                        <div class="bg-gray-800 text-white text-xs font-bold px-2 py-1 rounded">
                                            {{ number_format($totalSuppliers) }}
                                        </div>
                                    </a>
                                </div>

                                <!-- Products Count -->
                                <div>
                                    <a href=""
                                       class="flex items-center justify-between gap-2 hover:bg-gray-50 p-2 -m-2 rounded transition-colors pointer-events-auto">
                                        <span class="text-sm font-medium text-gray-600">Products</span>
                                        <div class="bg-gray-800 text-white text-xs font-bold px-2 py-1 rounded">
                                            {{ number_format($totalProducts) }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($regions->hasPages())
                    <div class="mt-8">
                        {{ $regions->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="bg-gray-50 rounded-lg p-20 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-200 rounded-full mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No Regions Available</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto text-lg">
                        There are currently no regions to display.
                    </p>
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-3 bg-[#ff0808] text-white px-8 py-3 rounded-lg font-semibold hover:bg-[#dd0606] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Home
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
