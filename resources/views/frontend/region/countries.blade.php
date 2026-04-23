@extends('layouts.app')

@section('title', 'Countries in ' . $region->name)

@section('content')
    <div class="py-12 min-h-screen bg-white">
        <div class="container px-4 mx-auto max-w-7xl">
            <!-- Breadcrumb -->
            <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-4">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors font-medium">
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
                            <a href="{{ route('regions.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors font-medium">Regions</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-900 font-semibold">{{ $region->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="mb-12">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-lg font-bold text-gray-900">Countries in {{ $region->name }}</h2>
                </div>

                @if($region->description)
                <div class="bg-blue-50 border-l-4 border-[#ff0808] p-5 mb-8">
                    <p class="text-gray-700 leading-relaxed">{{ $region->description }}</p>
                </div>
                @endif
            </div>

            <!-- Countries Grid -->
            @if($countries->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($countries as $country)
                        @php
                            $countryColors = [
                                'from-blue-50 to-blue-100',
                                'from-green-50 to-green-100',
                                'from-purple-50 to-purple-100',
                                'from-orange-50 to-orange-100',
                                'from-teal-50 to-teal-100',
                                'from-pink-50 to-pink-100',
                                'from-indigo-50 to-indigo-100',
                                'from-red-50 to-red-100',
                            ];

                            $colorClass = $countryColors[$country->id % count($countryColors)];

                            $suppliersCount = \App\Models\BusinessProfile::where('country_id', $country->id)
                                ->where('is_admin_verified', true)
                                ->count();

                            $productsCount = \App\Models\Product::where('country_id', $country->id)
                                ->where('status', 'active')
                                ->where('is_admin_verified', true)
                                ->count();
                        @endphp

                        <!-- Wrapper div for positioning -->
                        <div class="relative group">
                            <a href="{{ route('country.business-profiles', $country->id) }}"
                               class="block bg-gradient-to-br {{ $colorClass }} hover:shadow-xl rounded-xl p-6 text-center transition-all duration-300 border-2 border-transparent group-hover:border-[#ff0808]">

                                <!-- Country Flag/Icon -->
                                <div class="mb-4 transition-transform duration-300 group-hover:scale-125">
                                    @if($country->flag_url)
                                        <img src="{{ $country->flag_url }}"
                                             alt="{{ $country->name }} flag"
                                             class="w-16 h-16 mx-auto rounded-full object-cover border-2 border-white shadow-md">
                                    @else
                                        <div class="text-5xl">🌍</div>
                                    @endif
                                </div>

                                <!-- Country Name -->
                                <div class="mb-2 text-base font-bold text-gray-900 transition-colors group-hover:text-[#ff0808]">
                                    {{ $country->name }}
                                </div>
                            </a>

                            <!-- Hover Card - Now with pointer-events-auto on links -->
                            <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-48 bg-white rounded-lg shadow-2xl border-2 border-[#ff0808] p-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:-translate-y-2 z-50 pointer-events-none">
                                <!-- Arrow pointing down -->
                                <div class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-[#ff0808]"></div>
                                <div class="absolute left-1/2 -translate-x-1/2 top-full mt-[-2px] w-0 h-0 border-l-[7px] border-r-[7px] border-t-[7px] border-l-transparent border-r-transparent border-t-white"></div>

                                <!-- Suppliers Count -->
                                <div class="mb-3 pb-3 border-b border-gray-200">
                                    <a href="{{ route('country.business-profiles', $country->id) }}"
                                       class="flex items-center justify-between gap-2 hover:bg-gray-50 p-2 -m-2 rounded transition-colors pointer-events-auto">
                                        <span class="text-sm font-medium text-gray-600">Suppliers</span>
                                        <div class="bg-gray-800 text-white text-xs font-bold px-2 py-1 rounded">
                                            {{ number_format($suppliersCount) }}
                                        </div>
                                    </a>
                                </div>

                                <!-- Products Count -->
                                <div>
                                    <a href=""
                                       class="flex items-center justify-between gap-2 hover:bg-gray-50 p-2 -m-2 rounded transition-colors pointer-events-auto">
                                        <span class="text-sm font-medium text-gray-600">Products</span>
                                        <div class="bg-gray-800 text-white text-xs font-bold px-2 py-1 rounded">
                                            {{ number_format($productsCount) }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($countries->hasPages())
                    <div class="mt-8">
                        {{ $countries->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="bg-gray-50 rounded-lg p-20 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-200 rounded-full mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No Countries Found</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto text-lg">
                        There are currently no active countries in the {{ $region->name }} region.
                    </p>
                    <a href="{{ route('regions.index') }}"
                       class="inline-flex items-center gap-3 bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Regions
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
