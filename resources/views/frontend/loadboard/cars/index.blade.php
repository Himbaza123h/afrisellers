@extends('layouts.app')

@section('title', 'Available Vehicles for Hire - LoadBoard')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">

        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="mb-1 text-xl font-black text-gray-900 sm:text-2xl lg:text-lg sm:mb-2">
                        Available Vehicles for Hire
                    </h1>
                    <p class="text-xs text-gray-600 sm:text-sm">Find reliable transport for your cargo across Africa</p>
                </div>
                <a href="{{ route('loadboard.loads.index') }}"
                   class="flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-box text-[#ff0808]"></i>
                    <span>View Loads</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 sticky top-4">
                    <div class="flex items-center gap-2 mb-5">
                        <i class="fas fa-sliders-h text-[#ff0808]"></i>
                        <h3 class="text-base font-semibold text-gray-900">Filters</h3>
                    </div>

                    <form method="GET" action="{{ route('loadboard.cars.index') }}" class="space-y-4">

                        <!-- From Location -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">From Location</label>
                            <select name="from_country" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Countries</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('from_country') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- To Location -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">To Location</label>
                            <select name="to_country" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Destinations</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('to_country') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Vehicle Type -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Vehicle Type</label>
                            <select name="vehicle_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Types</option>
                                @foreach($vehicleTypes as $type)
                                    <option value="{{ $type }}" {{ request('vehicle_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Minimum Capacity -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Min. Capacity (tons)</label>
                            <input type="number" name="min_capacity" step="0.1"
                                   value="{{ request('min_capacity') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm"
                                   placeholder="e.g., 2.5">
                        </div>

                        <!-- Pricing Type -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Pricing Type</label>
                            <select name="pricing_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Types</option>
                                <option value="per_trip" {{ request('pricing_type') == 'per_trip' ? 'selected' : '' }}>Per Trip</option>
                                <option value="per_day" {{ request('pricing_type') == 'per_day' ? 'selected' : '' }}>Per Day</option>
                                <option value="per_km" {{ request('pricing_type') == 'per_km' ? 'selected' : '' }}>Per KM</option>
                                <option value="negotiable" {{ request('pricing_type') == 'negotiable' ? 'selected' : '' }}>Negotiable</option>
                            </select>
                        </div>

                        <!-- Max Price -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Max Price (USD)</label>
                            <input type="number" name="max_price"
                                   value="{{ request('max_price') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm"
                                   placeholder="e.g., 5000">
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-[#ff0808] text-white rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                                Apply
                            </button>
                            <a href="{{ route('loadboard.cars.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cars Grid -->
            <div class="lg:col-span-3">

                <!-- Results Count & Sort -->
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs text-gray-500">
                        Showing {{ $cars->firstItem() ?? 0 }} - {{ $cars->lastItem() ?? 0 }} of {{ $cars->total() }} vehicles
                    </p>
                </div>

                @if($cars->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($cars as $car)
                            <a href="{{ route('loadboard.cars.show', $car->listing_number) }}"
                               class="block bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all duration-300 overflow-hidden group">

                                <!-- Image -->
                                <div class="relative h-44 bg-gray-100 overflow-hidden">
                                    @if($car->primary_image)
                                        <img src="{{ $car->primary_image }}"
                                             alt="{{ $car->full_name }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="flex items-center justify-center h-full text-5xl">
                                            🚚
                                        </div>
                                    @endif

                                    <!-- Badges -->
                                    <div class="absolute top-2 left-2 flex gap-1.5">
                                        @if($car->is_featured)
                                            <span class="px-2 py-0.5 bg-amber-500 text-white text-xs font-semibold rounded shadow-sm">
                                                FEATURED
                                            </span>
                                        @endif
                                    </div>

                                    @if($car->driver_included)
                                        <div class="absolute top-2 right-2">
                                            <span class="flex items-center gap-1 px-2 py-0.5 bg-blue-500 text-white text-xs font-semibold rounded shadow-sm">
                                                <i class="fas fa-user-check"></i>
                                                Driver
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <!-- Title -->
                                    <h3 class="text-base font-semibold text-gray-900 mb-1 group-hover:text-[#ff0808] transition-colors line-clamp-1">
                                        {{ $car->full_name }}
                                    </h3>

                                    <!-- Vehicle Type -->
                                    <p class="text-xs text-gray-500 mb-3">{{ $car->vehicle_type }}</p>

                                    <!-- Route -->
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fas fa-route text-[#ff0808] text-xs"></i>
                                        <span class="text-xs text-gray-700 font-medium">{{ $car->route }}</span>
                                    </div>

                                    <!-- Capacity -->
                                    <div class="flex items-center gap-2 mb-3">
                                        <i class="fas fa-weight-hanging text-[#ff0808] text-xs"></i>
                                        <span class="text-xs text-gray-600">{{ $car->formatted_capacity }} capacity</span>
                                    </div>

                                    <!-- Rating & Trips -->
                                    @if($car->rating > 0)
                                        <div class="flex items-center gap-3 mb-3 pb-3 border-b border-gray-100">
                                            <span class="flex items-center gap-1 text-xs text-amber-600">
                                                <i class="fas fa-star"></i>
                                                {{ number_format($car->rating, 1) }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ $car->completed_trips }} trips
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Price -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-baseline gap-1">
                                            @if($car->price)
                                                <span class="text-xl font-bold text-gray-900">
                                                    ${{ number_format($car->price, 0) }}
                                                </span>
                                                <span class="text-xs text-gray-500">/{{ $car->pricing_type }}</span>
                                            @else
                                                <span class="text-sm font-semibold text-gray-700">Negotiable</span>
                                            @endif
                                        </div>

                                        @if($car->price_negotiable)
                                            <span class="px-2 py-0.5 bg-green-50 text-green-700 text-xs font-medium rounded border border-green-200">
                                                Negotiable
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $cars->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                        <div class="text-5xl mb-3">🚚</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">No vehicles found</h3>
                        <p class="text-sm text-gray-500 mb-4">Try adjusting your filters to see more results</p>
                        <a href="{{ route('loadboard.cars.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                            <i class="fas fa-redo-alt"></i>
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
