@extends('layouts.app')

@section('title', 'Available Loads - LoadBoard')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">

        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                   <h1 class="mb-1 text-xl font-black text-gray-900 sm:text-2xl lg:text-lg sm:mb-2">
                        Available Loads
                    </h1>
                    <p class="text-xs text-gray-600 sm:text-sm">Find cargo that needs transport and place your bid</p>
                </div>
                <a href="{{ route('loadboard.cars.index') }}"
                   class="flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-truck text-[#ff0808]"></i>
                    <span>View Vehicles</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-4">
                    <div class="flex items-center gap-2 mb-5">
                        <i class="fas fa-sliders-h text-[#ff0808]"></i>
                        <h3 class="text-base font-semibold text-gray-900">Filters</h3>
                    </div>

                    <form method="GET" action="{{ route('loadboard.loads.index') }}" class="space-y-4">

                        <!-- Origin Country -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Origin</label>
                            <select name="origin_country" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Countries</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('origin_country') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Destination Country -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Destination</label>
                            <select name="destination_country" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Countries</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('destination_country') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cargo Type -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Cargo Type</label>
                            <select name="cargo_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Types</option>
                                @foreach($cargoTypes as $type)
                                    <option value="{{ $type }}" {{ request('cargo_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Minimum Weight -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Min. Weight (kg)</label>
                            <input type="number" name="min_weight"
                                   value="{{ request('min_weight') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm"
                                   placeholder="e.g., 1000">
                        </div>

                        <!-- Pickup Date -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Pickup Date From</label>
                            <input type="date" name="pickup_date"
                                   value="{{ request('pickup_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                                Apply
                            </button>
                            <a href="{{ route('loadboard.loads.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Loads List -->
            <div class="lg:col-span-3">

                <!-- Results Count -->
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs text-gray-500">
                        Showing {{ $loads->firstItem() ?? 0 }} - {{ $loads->lastItem() ?? 0 }} of {{ $loads->total() }} loads
                    </p>
                </div>

                @if($loads->count() > 0)
                    <div class="space-y-3">
                        @foreach($loads as $load)
                            <a href="{{ route('loadboard.loads.show', $load->load_number) }}"
                               class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all p-5 group">

                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                                    <!-- Main Info -->
                                    <div class="flex-1">
                                        <div class="flex items-start gap-3 mb-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center text-xl flex-shrink-0">
                                                📦
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-base font-semibold text-gray-900 group-hover:text-[#ff0808] transition-colors mb-0.5">
                                                    {{ $load->cargo_type }}
                                                </h3>
                                                <p class="text-xs text-gray-600 line-clamp-2">
                                                    {{ $load->cargo_description }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Route -->
                                        <div class="flex items-center gap-2 mb-3 text-xs flex-wrap">
                                            <div class="flex items-center gap-1.5 text-gray-700">
                                                <i class="fas fa-map-marker-alt text-green-600"></i>
                                                <span class="font-medium">{{ $load->origin_city }}, {{ $load->originCountry->name }}</span>
                                            </div>
                                            <i class="fas fa-arrow-right text-[#ff0808] text-xs"></i>
                                            <div class="flex items-center gap-1.5 text-gray-700">
                                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                                <span class="font-medium">{{ $load->destination_city }}, {{ $load->destinationCountry->name }}</span>
                                            </div>
                                        </div>

                                        <!-- Details Grid -->
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <i class="fas fa-weight-hanging text-[#ff0808]"></i>
                                                <span>{{ number_format($load->weight) }} {{ $load->weight_unit }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <i class="fas fa-calendar text-[#ff0808]"></i>
                                                <span>{{ $load->pickup_date->format('M d, Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <i class="fas fa-box text-[#ff0808]"></i>
                                                <span>{{ $load->packaging_type ?? 'Standard' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <i class="fas fa-comments text-[#ff0808]"></i>
                                                <span>{{ $load->bids_count }} {{ $load->bids_count === 1 ? 'bid' : 'bids' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price & Status -->
                                    <div class="flex md:flex-col items-center md:items-end gap-3 md:gap-2">
                                        <div class="text-center md:text-right">
                                            @if($load->budget)
                                                <div class="text-xl font-bold text-gray-900">
                                                    ${{ number_format($load->budget, 0) }}
                                                </div>
                                                <div class="text-xs text-gray-500">Budget</div>
                                            @else
                                                <div class="text-sm font-semibold text-gray-700">Open to Bids</div>
                                            @endif
                                        </div>

                                        <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full border border-green-200">
                                            {{ ucfirst($load->status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                                    <span>Posted {{ $load->created_at->diffForHumans() }}</span>
                                    <span class="font-medium text-[#ff0808] group-hover:underline flex items-center gap-1">
                                        View Details & Place Bid
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $loads->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="text-5xl mb-3">📦</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">No loads found</h3>
                        <p class="text-sm text-gray-500 mb-4">Try adjusting your filters to see more results</p>
                        <a href="{{ route('loadboard.loads.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
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
