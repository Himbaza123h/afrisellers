@extends('layouts.app')

@section('title', 'Featured Suppliers - Verified Business Profiles')

@section('content')
    <div class="py-8 min-h-screen bg-gray-50">
        <div class="container px-4 mx-auto">
            <!-- Breadcrumb -->
            <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600">Home</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-500">Featured Suppliers</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-lg font-bold text-gray-900 mb-2">Featured Suppliers</h1>
                <p class="text-gray-600">Discover verified and trusted business profiles from across Africa</p>
            </div>

            <!-- Filters Section -->
            <div class="p-4 mb-8 bg-white rounded-lg border border-gray-200">
                <form method="GET" action="{{ route('featured-suppliers') }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block mb-1 text-sm font-medium text-gray-700">Search Suppliers</label>
                            <input type="text"
                                   id="search"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search by business name..."
                                   class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Country Filter -->
                        <div>
                            <label for="country" class="block mb-1 text-sm font-medium text-gray-700">Country</label>
                            <select id="country"
                                    name="country"
                                    class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Countries</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort By -->
                        <div>
                            <label for="sort" class="block mb-1 text-sm font-medium text-gray-700">Sort By</label>
                            <select id="sort"
                                    name="sort"
                                    class="px-3 py-2 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-6 py-2 font-medium text-white bg-blue-600 rounded-lg transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="mr-2 fas fa-filter"></i>Apply Filters
                        </button>
                        <a href="{{ route('featured-suppliers') }}"
                           class="px-6 py-2 font-medium text-gray-700 bg-white rounded-lg border border-gray-300 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <i class="mr-2 fas fa-times"></i>Clear
                        </a>
                    </div>

                    <!-- Active Filters Display -->
                    @if(request()->hasAny(['search', 'country', 'sort']))
                        <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
                            <span class="text-sm font-medium text-gray-700">Active Filters:</span>
                            @if(request('search'))
                                <span class="inline-flex items-center px-3 py-1 text-sm text-blue-800 bg-blue-100 rounded-full">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ route('featured-suppliers', request()->except('search')) }}"
                                       class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('country'))
                                <span class="inline-flex items-center px-3 py-1 text-sm text-blue-800 bg-blue-100 rounded-full">
                                    Country: {{ $countries->find(request('country'))->name ?? 'N/A' }}
                                    <a href="{{ route('featured-suppliers', request()->except('country')) }}"
                                       class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('sort') && request('sort') != 'latest')
                                <span class="inline-flex items-center px-3 py-1 text-sm text-blue-800 bg-blue-100 rounded-full">
                                    Sort: {{ ucwords(str_replace('_', ' ', request('sort'))) }}
                                    <a href="{{ route('featured-suppliers', request()->except('sort')) }}"
                                       class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                        </div>
                    @endif
                </form>
            </div>

            <!-- Business Profiles Grid -->
            @if($businessProfiles->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($businessProfiles as $businessProfile)
                        @php
                            $firstProduct = $userProducts->get($businessProfile->user_id);
                            $productImage = null;
                            if ($firstProduct && $firstProduct->images->count() > 0) {
                                $productImage = $firstProduct->images->first();
                            }

                            $categoryName = $firstProduct && $firstProduct->productCategory
                                ? $firstProduct->productCategory->name
                                : 'Supplier';

                            // Calculate average rating
                            $allReviews = App\Models\ProductUserReview::whereHas('product', function($query) use ($businessProfile) {
                                $query->where('user_id', $businessProfile->user_id)
                                    ->where('status', 'active')
                                    ->where('is_admin_verified', true);
                            })
                            ->where('status', true)
                            ->get();

                            $avgRating = $allReviews->count() > 0 ? $allReviews->avg('mark') : 0;
                            $reviewsCount = $allReviews->count();
                        @endphp
                        <a href="{{ route('business-profile.products', $businessProfile->id) }}"
                           class="block bg-white border border-gray-200 rounded-lg hover:shadow-lg transition-all duration-300 overflow-hidden group">
                            <div class="relative h-48 bg-gray-100">
                                @if($productImage)
                                    <img src="{{ $productImage->image_url }}"
                                         alt="{{ $businessProfile->business_name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                        <span class="text-5xl">🏢</span>
                                    </div>
                                @endif
                                <span class="absolute top-3 right-3 bg-blue-600 text-white px-2 py-1 rounded text-xs font-medium">
                                    Verified
                                </span>
                            </div>
                            <div class="p-5">
                                <h3 class="font-semibold text-lg text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                    {{ $businessProfile->business_name }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-3">
                                    {{ $categoryName }}
                                </p>
                                @if($avgRating > 0)
                                    <div class="flex items-center gap-1 mb-3">
                                        <span class="text-yellow-500 text-sm">★</span>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($avgRating, 1) }}</span>
                                        <span class="text-sm text-gray-500">({{ $reviewsCount }})</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-sm text-gray-600">
                                        @if($businessProfile->country && $businessProfile->country->flag_url)
                                            <img src="{{ $businessProfile->country->flag_url }}"
                                                 alt="{{ $businessProfile->country->name }}"
                                                 class="inline-block mr-1 w-4 h-4">
                                        @endif
                                        {{ $businessProfile->city }}, {{ $businessProfile->country->name ?? 'N/A' }}
                                    </span>
                                    <span class="text-sm text-blue-600 font-medium group-hover:text-blue-700">
                                        View Products →
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($businessProfiles->hasPages())
                    <div class="mt-8">
                        {{ $businessProfiles->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                    <i class="text-6xl text-gray-300 mb-4 fas fa-building"></i>
                    <p class="text-lg font-medium text-gray-600 mb-2">No featured suppliers found</p>
                    <p class="text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'country']))
                            No suppliers match your search criteria. Try adjusting your filters.
                        @else
                            There are no verified business profiles available yet.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'country']))
                        <a href="{{ route('featured-suppliers') }}" class="inline-block mt-6 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            Clear Filters
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="inline-block mt-6 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            Back to Home
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection

