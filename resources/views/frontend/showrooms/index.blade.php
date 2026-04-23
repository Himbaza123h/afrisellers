{{-- resources/views/frontend/showrooms/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Showrooms - AfriSellers')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">

        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="mb-1 text-xl font-black text-gray-900 sm:text-2xl lg:text-lg sm:mb-2">
                        Showrooms & Display Centers
                    </h1>
                     <p class="text-xs text-gray-600 sm:text-sm">Browse permanent business showrooms across Africa</p>
                </div>
                <a href="{{ route('tradeshows.index') }}"
                   class="flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-store text-[#ff0808]"></i>
                    <span>View Tradeshows</span>
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

                    <form method="GET" action="{{ route('showrooms.index') }}" class="space-y-4">

                        <!-- Country -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Country</label>
                            <select name="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Countries</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">City</label>
                            <select name="city" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Cities</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Industry -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Industry</label>
                            <select name="industry" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Industries</option>
                                @foreach($industries as $industry)
                                    <option value="{{ $industry }}" {{ request('industry') == $industry ? 'selected' : '' }}>
                                        {{ $industry }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Business Type -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Business Type</label>
                            <select name="business_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Types</option>
                                <option value="Manufacturer" {{ request('business_type') == 'Manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                <option value="Dealer" {{ request('business_type') == 'Dealer' ? 'selected' : '' }}>Dealer</option>
                                <option value="Distributor" {{ request('business_type') == 'Distributor' ? 'selected' : '' }}>Distributor</option>
                            </select>
                        </div>

                        <!-- Verified Only -->
                        <div class="flex items-center">
                            <input type="checkbox" name="verified_only" id="verified_only" value="1"
                                   {{ request('verified_only') ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#ff0808] border-gray-300 rounded focus:ring-[#ff0808]">
                            <label for="verified_only" class="ml-2 text-xs text-gray-700">Verified showrooms only</label>
                        </div>

                        <!-- Authorized Dealer -->
                        <div class="flex items-center">
                            <input type="checkbox" name="authorized_dealer" id="authorized_dealer" value="1"
                                   {{ request('authorized_dealer') ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#ff0808] border-gray-300 rounded focus:ring-[#ff0808]">
                            <label for="authorized_dealer" class="ml-2 text-xs text-gray-700">Authorized dealers</label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                                Apply
                            </button>
                            <a href="{{ route('showrooms.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Showrooms Grid -->
            <div class="lg:col-span-3">

                <!-- Results Count -->
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs text-gray-500">
                        Showing {{ $showrooms->firstItem() ?? 0 }} - {{ $showrooms->lastItem() ?? 0 }} of {{ $showrooms->total() }} showrooms
                    </p>
                </div>

                @if($showrooms->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($showrooms as $showroom)
                            <a href="{{ route('showrooms.show', $showroom->slug) }}"
                               class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all overflow-hidden group">

                                <!-- Image -->
                                <div class="relative h-44 bg-gray-100">
                                    @if($showroom->primary_image)
                                        <img src="{{ $showroom->primary_image }}" alt="{{ $showroom->name }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="flex items-center justify-center h-full text-5xl">
                                            🏢
                                        </div>
                                    @endif

                                    @if($showroom->is_featured)
                                        <span class="absolute top-2 left-2 px-2 py-0.5 bg-amber-500 text-white text-xs font-semibold rounded shadow-sm">
                                            FEATURED
                                        </span>
                                    @endif

                                    @if($showroom->is_verified)
                                        <span class="absolute top-2 right-2 px-2 py-0.5 bg-green-500 text-white text-xs font-semibold rounded shadow-sm">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <h3 class="text-base font-semibold text-gray-900 mb-1 group-hover:text-[#ff0808] transition-colors line-clamp-1">
                                        {{ $showroom->name }}
                                    </h3>

                                    <p class="text-xs text-gray-600 mb-2">{{ $showroom->industry }}</p>

                                    <!-- Location -->
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fas fa-map-marker-alt text-[#ff0808] text-xs"></i>
                                        <span class="text-xs text-gray-700 font-medium">{{ $showroom->city }}, {{ $showroom->country->name }}</span>
                                    </div>

                                    <!-- Business Type -->
                                    <div class="flex items-center gap-2 mb-3">
                                        <i class="fas fa-briefcase text-[#ff0808] text-xs"></i>
                                        <span class="text-xs text-gray-600">{{ $showroom->business_type }}</span>
                                    </div>

                                    <!-- Rating -->
                                    @if($showroom->rating > 0)
                                        <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-100">
                                            <span class="flex items-center gap-1 text-xs text-amber-600">
                                                <i class="fas fa-star"></i>
                                                {{ number_format($showroom->rating, 1) }}
                                            </span>
                                            <span class="text-xs text-gray-600">
                                                ({{ $showroom->reviews_count }} reviews)
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Badges -->
                                    <div class="flex flex-wrap gap-1.5">
                                        @if($showroom->is_authorized_dealer)
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-medium rounded border border-blue-200">
                                                Authorized
                                            </span>
                                        @endif
                                        @if($showroom->has_parking)
                                            <span class="px-2 py-0.5 bg-gray-50 text-gray-700 text-xs rounded border border-gray-200">
                                                <i class="fas fa-parking"></i> Parking
                                            </span>
                                        @endif
                                        @if($showroom->wheelchair_accessible)
                                            <span class="px-2 py-0.5 bg-gray-50 text-gray-700 text-xs rounded border border-gray-200">
                                                <i class="fas fa-wheelchair"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $showrooms->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="text-5xl mb-3">🏢</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">No showrooms found</h3>
                        <p class="text-sm text-gray-500 mb-4">Try adjusting your filters to see more results</p>
                        <a href="{{ route('showrooms.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
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
