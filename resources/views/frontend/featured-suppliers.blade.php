@extends('layouts.app')

@section('title', 'Featured Suppliers - Verified Business Profiles')

@section('content')
    <div class="py-4 sm:py-6 min-h-screen bg-gray-50">
        <div class="container px-3 sm:px-4 mx-auto max-w-7xl">
            <!-- Breadcrumb -->
            <nav class="flex mb-3 sm:mb-4 text-[10px] sm:text-xs" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600">Home</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="mx-1 w-2.5 h-2.5 sm:w-3 sm:h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
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
            <div class="mb-4 sm:mb-6">
                <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-1">Featured Suppliers</h1>
                <p class="text-xs sm:text-sm text-gray-600">Discover verified and trusted business profiles from across Africa</p>
            </div>

            <!-- Mobile Filter Toggle Button -->
            <div class="lg:hidden mb-4">
                <button id="mobileFilterToggle" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-colors flex items-center justify-center gap-2 text-sm font-semibold text-gray-700">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filters & Search</span>
                    @if(request()->hasAny(['search', 'country', 'sort']))
                        <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">Active</span>
                    @endif
                </button>
            </div>

            <!-- Main Layout: Sidebar + Content -->
            <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
                <!-- Left Sidebar - Filters (Desktop Always Visible, Mobile Modal) -->
<!-- Left Sidebar - Filters (Desktop Always Visible, Mobile Modal) -->
<aside id="filterSidebar" class="fixed inset-0 z-50 lg:relative lg:z-auto lg:w-64 lg:flex-shrink-0 hidden lg:block">
    <!-- Mobile Overlay -->
    <div class="lg:hidden fixed inset-0 bg-black/50" id="filterOverlay"></div>

    <!-- Sidebar Content -->
    <div class="lg:relative fixed inset-y-0 left-0 w-full max-w-xs sm:max-w-sm lg:max-w-none bg-white overflow-y-auto lg:overflow-visible lg:z-auto">
        <div class="bg-white rounded-lg border border-gray-200">
            <!-- Filter Header -->
            <div class="p-3 sm:p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs sm:text-sm font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filters
                    </h2>
                    <div class="flex items-center gap-2">
                        @if(request()->hasAny(['search', 'country', 'sort']))
                        <a href="{{ route('featured-suppliers') }}"
                           class="text-[10px] sm:text-xs text-blue-600 hover:text-blue-700 font-medium">
                            Clear All
                        </a>
                        @endif
                        <!-- Mobile Close Button -->
                        <button id="closeFilterSidebar" class="lg:hidden text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('featured-suppliers') }}" class="p-3 sm:p-4 space-y-4 sm:space-y-5">
                <!-- Search -->
                <div>
                    <label for="search" class="block mb-1.5 sm:mb-2 text-[10px] sm:text-xs font-semibold text-gray-700">Search Suppliers</label>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Business name..."
                           class="px-2.5 sm:px-3 py-1.5 sm:py-2 w-full text-xs sm:text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Country Filter -->
                <div>
                    <label for="country" class="block mb-1.5 sm:mb-2 text-[10px] sm:text-xs font-semibold text-gray-700">Country</label>
                    <select id="country"
                            name="country"
                            class="px-2.5 sm:px-3 py-1.5 sm:py-2 w-full text-xs sm:text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                    <label for="sort" class="block mb-1.5 sm:mb-2 text-[10px] sm:text-xs font-semibold text-gray-700">Sort By</label>
                    <select id="sort"
                            name="sort"
                            class="px-2.5 sm:px-3 py-1.5 sm:py-2 w-full text-xs sm:text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                    </select>
                </div>

                <!-- Apply Button -->
                <button type="submit"
                        class="w-full px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-semibold text-white bg-blue-600 rounded-lg transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="mr-1 fas fa-filter"></i>Apply Filters
                </button>

                <!-- Active Filters Display -->
                @if(request()->hasAny(['search', 'country', 'sort']))
                    <div class="pt-3 sm:pt-4 border-t border-gray-200">
                        <span class="block mb-2 text-[10px] sm:text-xs font-semibold text-gray-700">Active Filters:</span>
                        <div class="flex flex-col gap-1.5 sm:gap-2">
                            @if(request('search'))
                                <span class="inline-flex items-center justify-between px-2 py-1 text-[10px] sm:text-xs text-blue-800 bg-blue-50 rounded">
                                    <span class="truncate">{{ request('search') }}</span>
                                    <a href="{{ route('featured-suppliers', request()->except('search')) }}"
                                       class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times text-[9px] sm:text-xs"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('country'))
                                <span class="inline-flex items-center justify-between px-2 py-1 text-[10px] sm:text-xs text-blue-800 bg-blue-50 rounded">
                                    <span class="truncate">{{ $countries->find(request('country'))->name ?? 'N/A' }}</span>
                                    <a href="{{ route('featured-suppliers', request()->except('country')) }}"
                                       class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times text-[9px] sm:text-xs"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('sort') && request('sort') != 'latest')
                                <span class="inline-flex items-center justify-between px-2 py-1 text-[10px] sm:text-xs text-blue-800 bg-blue-50 rounded">
                                    <span class="truncate">{{ ucwords(str_replace('_', ' ', request('sort'))) }}</span>
                                    <a href="{{ route('featured-suppliers', request()->except('sort')) }}"
                                       class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times text-[9px] sm:text-xs"></i>
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</aside>

                <!-- Right Content Area -->
                <div class="flex-1 min-w-0">
                    <!-- Tab Navigation -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-4 sm:mb-6">
                        <div class="flex border-b border-gray-200 overflow-x-auto">
                            <button onclick="switchTab('all')"
                                    id="tab-all"
                                    class="tab-button flex-1 min-w-[120px] sm:min-w-0 px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs font-semibold text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1 sm:gap-2">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="hidden sm:inline">All Suppliers</span>
                                    <span class="sm:hidden">All</span>
                                    <span class="ml-0.5 sm:ml-1 text-[9px] sm:text-xs bg-gray-200 text-gray-700 px-1.5 sm:px-2 py-0.5 rounded-full">{{ $businessProfiles->total() }}</span>
                                </div>
                            </button>
                            <button onclick="switchTab('verified')"
                                    id="tab-verified"
                                    class="tab-button flex-1 min-w-[120px] sm:min-w-0 px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs font-semibold text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1 sm:gap-2">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="hidden sm:inline">Verified Only</span>
                                    <span class="sm:hidden">Verified</span>
                                </div>
                            </button>
                            <button onclick="switchTab('featured')"
                                    id="tab-featured"
                                    class="tab-button flex-1 min-w-[120px] sm:min-w-0 px-2 sm:px-4 py-2 sm:py-3 text-[10px] sm:text-xs font-semibold text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-all duration-200 border-b-2 border-transparent whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1 sm:gap-2">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                    <span class="hidden sm:inline">Top Rated</span>
                                    <span class="sm:hidden">Top</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="relative">
                        <!-- All Suppliers Tab -->
                        <div id="content-all" class="tab-content">
                            @if($businessProfiles->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
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
                                            <div class="relative h-32 sm:h-36 md:h-40 bg-gray-100">
                                                @if($productImage)
                                                    <img src="{{ $productImage->image_url }}"
                                                         alt="{{ $businessProfile->business_name }}"
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                @else
                                                    <div class="w-full h-full bg-gray-100 to-gray-200 flex items-center justify-center">
                                                        <span class="text-3xl sm:text-4xl">🏢</span>
                                                    </div>
                                                @endif
                                                <span class="absolute top-1.5 sm:top-2 right-1.5 sm:right-2 bg-blue-600 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-[9px] sm:text-xs font-medium">
                                                    Verified
                                                </span>
                                            </div>
                                            <div class="p-3 sm:p-4">
                                                <h3 class="font-semibold text-sm sm:text-base text-gray-900 mb-1 group-hover:text-blue-600 transition-colors line-clamp-1">
                                                    {{ $businessProfile->business_name }}
                                                </h3>
                                                <p class="text-[10px] sm:text-xs text-gray-600 mb-1.5 sm:mb-2 truncate">
                                                    {{ $categoryName }}
                                                </p>
                                                @if($avgRating > 0)
                                                    <div class="flex items-center gap-1 mb-1.5 sm:mb-2">
                                                        <span class="text-yellow-500 text-xs sm:text-sm">★</span>
                                                        <span class="text-[10px] sm:text-xs font-medium text-gray-900">{{ number_format($avgRating, 1) }}</span>
                                                        <span class="text-[10px] sm:text-xs text-gray-500">({{ $reviewsCount }})</span>
                                                    </div>
                                                @endif
                                                <div class="flex items-center justify-between pt-1.5 sm:pt-2 border-t border-gray-100">
                                                    <span class="text-[10px] sm:text-xs text-gray-600 flex items-center truncate">
                                                        @if($businessProfile->country && $businessProfile->country->flag_url)
                                                            <img src="{{ $businessProfile->country->flag_url }}"
                                                                 alt="{{ $businessProfile->country->name }}"
                                                                 class="inline-block mr-1 w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0">
                                                        @endif
                                                        <span class="truncate">{{ $businessProfile->city }}</span>
                                                    </span>
                                                    <span class="text-[10px] sm:text-xs text-blue-600 font-medium group-hover:text-blue-700 whitespace-nowrap ml-2">
                                                        View →
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                @if($businessProfiles->hasPages())
                                    <div class="mt-4 sm:mt-6">
                                        {{ $businessProfiles->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="bg-white rounded-lg border border-gray-200 p-8 sm:p-12 text-center">
                                    <i class="text-4xl sm:text-5xl text-gray-300 mb-3 sm:mb-4 fas fa-building"></i>
                                    <p class="text-sm sm:text-base font-medium text-gray-600 mb-1 sm:mb-2">No suppliers found</p>
                                    <p class="text-xs sm:text-sm text-gray-500">
                                        @if(request()->hasAny(['search', 'country']))
                                            No suppliers match your search criteria. Try adjusting your filters.
                                        @else
                                            There are no verified business profiles available yet.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['search', 'country']))
                                        <a href="{{ route('featured-suppliers') }}" class="inline-block mt-3 sm:mt-4 px-4 sm:px-5 py-1.5 sm:py-2 text-xs sm:text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                            Clear Filters
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Verified Only Tab -->
                        <div id="content-verified" class="tab-content hidden">
                            <div class="bg-white rounded-lg border border-gray-200 p-8 sm:p-12 text-center">
                                <i class="text-4xl sm:text-5xl text-green-300 mb-3 sm:mb-4 fas fa-shield-check"></i>
                                <p class="text-sm sm:text-base font-medium text-gray-600 mb-1 sm:mb-2">Verified Suppliers</p>
                                <p class="text-xs sm:text-sm text-gray-500">All displayed suppliers are verified</p>
                            </div>
                        </div>

                        <!-- Top Rated Tab -->
                        <div id="content-featured" class="tab-content hidden">
                            <div class="bg-white rounded-lg border border-gray-200 p-8 sm:p-12 text-center">
                                <i class="text-4xl sm:text-5xl text-yellow-300 mb-3 sm:mb-4 fas fa-star"></i>
                                <p class="text-sm sm:text-base font-medium text-gray-600 mb-1 sm:mb-2">Top Rated Suppliers</p>
                                <p class="text-xs sm:text-sm text-gray-500">Showing highest rated suppliers</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
    /* Tab Styling */
    .tab-button {
        position: relative;
    }

    .tab-button.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
        background-color: #eff6ff;
    }

    /* Tab Content Animations - Bottom to Top */
    .tab-content {
        animation: slideUpFadeIn 0.4s ease-out;
    }

    @keyframes slideUpFadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-content.hidden {
        display: none;
    }

    /* Smooth transitions */
    * {
        transition-property: color, background-color, border-color, transform, box-shadow;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    /* Mobile Filter Sidebar - Only fixed on mobile */
    @media (max-width: 1023px) {
        #filterSidebar.show {
            display: block;
            position: fixed;
            z-index: 9999;
        }

        #filterSidebar {
            z-index: -1;
        }
    }

    /* Desktop - Normal flow, no z-index issues */
    @media (min-width: 1024px) {
        #filterSidebar {
            position: relative;
            z-index: auto !important;
            display: block !important;
        }
    }

    /* Prevent body scroll when mobile filter is open */
    @media (max-width: 1023px) {
        body.filter-open {
            overflow: hidden;
        }
    }
</style>

    <script>
        // Mobile Filter Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileFilterToggle = document.getElementById('mobileFilterToggle');
            const filterSidebar = document.getElementById('filterSidebar');
            const closeFilterSidebar = document.getElementById('closeFilterSidebar');
            const filterOverlay = document.getElementById('filterOverlay');

            if (mobileFilterToggle) {
                mobileFilterToggle.addEventListener('click', function() {
                    filterSidebar.classList.add('show');
                    document.body.classList.add('filter-open');
                });
            }

            if (closeFilterSidebar) {
                closeFilterSidebar.addEventListener('click', function() {
                    filterSidebar.classList.remove('show');
                    document.body.classList.remove('filter-open');
                });
            }

            if (filterOverlay) {
                filterOverlay.addEventListener('click', function() {
                    filterSidebar.classList.remove('show');
                    document.body.classList.remove('filter-open');
                });
            }
        });

        // Tab Switching Function with Bottom-to-Top Animation
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab content
            const selectedContent = document.getElementById('content-' + tabName);
            selectedContent.classList.remove('hidden');

            // Add active class to selected tab
            document.getElementById('tab-' + tabName).classList.add('active');

            // Save to localStorage
            localStorage.setItem('activeFeaturedTab', tabName);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Restore last active tab or default to 'all'
            const savedTab = localStorage.getItem('activeFeaturedTab') || 'all';
            switchTab(savedTab);
        });
    </script>
@endsection
