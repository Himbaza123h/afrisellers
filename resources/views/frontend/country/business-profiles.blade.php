@extends('layouts.app')

@section('title', 'Business Profiles in ' . $country->name)

@section('content')
    <div class="py-8 min-h-screen bg-gray-50">
        <div class="container px-4 mx-auto max-w-7xl">
            <!-- Breadcrumb -->
            <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-[#ff0808] transition-colors font-medium">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            Home
                        </a>
                    </li>
                    @if($country->region)
                    <li>
                        <div class="flex items-center">
                            <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('regions.countries', $country->region_id) }}" class="text-gray-600 hover:text-[#ff0808] transition-colors font-medium">
                                {{ $country->region->name }}
                            </a>
                        </div>
                    </li>
                    @endif
                    <li>
                        <div class="flex items-center">
                            <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-900 font-semibold">{{ $country->name }} Suppliers</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Verified Suppliers in {{ $country->name }}</h2>

                <!-- Stats Bar -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div class="text-center">
                        <div class="text-xl font-bold text-blue-600 mb-1">
                            {{ $businessProfiles->total() }}
                        </div>
                        <div class="text-xs text-gray-600">Total Suppliers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-purple-600 mb-1">
                            {{ \App\Models\Product::whereHas('user.businessProfile', function($q) use ($country) {
                                $q->where('country_id', $country->id)->where('is_admin_verified', true);
                            })->where('status', 'active')->count() }}
                        </div>
                        <div class="text-xs text-gray-600">Products</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-green-600 mb-1">
                            100%
                        </div>
                        <div class="text-xs text-gray-600">Verified</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-orange-600 mb-1">
                            {{ $country->region ? $country->region->name : 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-600">Region</div>
                    </div>
                </div>
            </div>

            <!-- Main Content with Sidebar -->
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Sidebar Filter -->
                <aside class="w-full lg:w-64 flex-shrink-0">
                    <div class="bg-white rounded-lg border-2 border-gray-200 overflow-hidden sticky top-4">
                        <div class="bg-[#ff0808] text-white p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-base flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                    Filters
                                </h3>
                                @if(request()->hasAny(['search', 'city', 'category', 'rating', 'sort']))
                                <a href="{{ route('country.business-profiles', $country) }}" class="text-xs text-white hover:underline font-medium">
                                    Clear All
                                </a>
                                @endif
                            </div>
                        </div>

                        <form method="GET" action="{{ route('country.business-profiles', $country) }}" class="p-4 space-y-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Business name..."
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                            </div>

                            <!-- City Filter -->
                            @if($cities->count() > 0)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                                <select name="city" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="">All Cities</option>
                                    @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Category Filter -->
                            @if($categories->count() > 0)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                                <select name="category" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Rating Filter -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Rating</label>
                                <select name="rating" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="">Any Rating</option>
                                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2+ Stars</option>
                                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1+ Stars</option>
                                </select>
                            </div>

                            <!-- Sort By -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                                <select name="sort" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                </select>
                            </div>

                            <!-- Apply Button -->
                            <button type="submit" class="w-full bg-[#ff0808] text-white py-2 px-4 rounded-lg font-semibold hover:bg-[#dd0606] transition-colors text-sm">
                                Apply Filters
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Main Content Area -->
                <div class="flex-1 min-w-0">
                    <!-- Active Filters Display -->
                    @if(request()->hasAny(['search', 'city', 'category', 'rating']))
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="text-sm font-medium text-gray-700">Active filters:</span>

                        @if(request('search'))
                        <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1 rounded">
                            Search: "{{ request('search') }}"
                            <a href="{{ route('country.business-profiles', array_merge(['country' => $country->id], request()->except('search'))) }}" class="hover:text-blue-900">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </span>
                        @endif

                        @if(request('city'))
                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded">
                            City: {{ request('city') }}
                            <a href="{{ route('country.business-profiles', array_merge(['country' => $country->id], request()->except('city'))) }}" class="hover:text-green-900">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </span>
                        @endif

                        @if(request('category'))
                        <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-1 rounded">
                            Category: {{ $categories->find(request('category'))->name ?? 'Unknown' }}
                            <a href="{{ route('country.business-profiles', array_merge(['country' => $country->id], request()->except('category'))) }}" class="hover:text-purple-900">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </span>
                        @endif

                        @if(request('rating'))
                        <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-1 rounded">
                            Rating: {{ request('rating') }}+ Stars
                            <a href="{{ route('country.business-profiles', array_merge(['country' => $country->id], request()->except('rating'))) }}" class="hover:text-yellow-900">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </span>
                        @endif
                    </div>
                    @endif

                    <!-- Results Count -->
                    <div class="mb-4 text-sm text-gray-600">
                        Showing <span class="font-semibold">{{ $businessProfiles->firstItem() ?? 0 }}</span> to
                        <span class="font-semibold">{{ $businessProfiles->lastItem() ?? 0 }}</span> of
                        <span class="font-semibold">{{ $businessProfiles->total() }}</span> suppliers
                    </div>

                    <!-- Business Profiles Grid -->
                    @if($businessProfiles->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
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

                                    $allReviews = App\Models\ProductUserReview::whereHas('product', function($query) use ($businessProfile) {
                                        $query->where('user_id', $businessProfile->user_id)
                                            ->where('status', 'active')
                                            ->where('is_admin_verified', true);
                                    })
                                    ->where('status', true)
                                    ->get();

                                    $avgRating = $allReviews->count() > 0 ? $allReviews->avg('mark') : 0;
                                    $reviewsCount = $allReviews->count();

                                    $productsCount = \App\Models\Product::where('user_id', $businessProfile->user_id)
                                        ->where('status', 'active')
                                        ->where('is_admin_verified', true)
                                        ->count();
                                @endphp
                                <div class="group bg-white rounded-lg overflow-hidden transition-all duration-300 hover:shadow-lg border-2 border-transparent hover:border-[#ff0808]">
                                    <!-- Image Section -->
                                    <a href="{{ route('business-profile.products', $businessProfile->id) }}" class="block relative h-40 overflow-hidden">
                                        @if($productImage)
                                            <img src="{{ $productImage->image_url }}"
                                                 alt="{{ $businessProfile->business_name }}"
                                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                 loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                                <span class="text-5xl">🏢</span>
                                            </div>
                                        @endif

                                        <!-- Verified Badge -->
                                        <span class="absolute top-2 right-2 bg-green-600 text-white text-xs font-semibold px-2 py-0.5 rounded flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Verified
                                        </span>
                                    </a>

                                    <!-- Content -->
                                    <div class="p-3 bg-white">
                                        <!-- Business Name -->
                                        <a href="{{ route('business-profile.products', $businessProfile->id) }}">
                                            <h3 class="text-sm font-bold text-gray-900 mb-2 group-hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[2.5rem]">
                                                {{ $businessProfile->business_name }}
                                            </h3>
                                        </a>

                                        <!-- Category -->
                                        <div class="mb-2">
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                </svg>
                                                {{ $categoryName }}
                                            </span>
                                        </div>

                                        <!-- Stats Row -->
                                        <div class="flex items-center gap-2 text-xs text-gray-600 mb-2">
                                            <a href="{{ route('business-profile.products', $businessProfile->id) }}" class="flex items-center gap-1 hover:opacity-80 transition-opacity">
                                                <div class="bg-gray-800 text-white text-xs font-bold px-1.5 py-0.5 rounded">
                                                    {{ number_format($productsCount) }}
                                                </div>
                                                <span class="font-medium">{{ Str::plural('Product', $productsCount) }}</span>
                                            </a>

                                            @if($avgRating > 0)
                                            <a href="{{ route('business-profile.products', $businessProfile->id) }}" class="flex items-center gap-1 hover:opacity-80 transition-opacity">
                                                <div class="bg-yellow-400 text-gray-900 text-xs font-bold px-1.5 py-0.5 rounded flex items-center gap-0.5">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                    {{ number_format($avgRating, 1) }}
                                                </div>
                                                <span class="font-medium">({{ $reviewsCount }})</span>
                                            </a>
                                            @endif
                                        </div>

                                        <!-- Location -->
                                        <div class="flex items-center gap-1 text-xs text-gray-600">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span class="font-medium">{{ $businessProfile->city }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($businessProfiles->hasPages())
                            <div class="mt-6">
                                {{ $businessProfiles->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="bg-white rounded-lg p-12 text-center border border-gray-200">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">No Suppliers Found</h3>
                            <p class="text-gray-600 mb-6">
                                Try adjusting your filters or search criteria.
                            </p>
                            <a href="{{ route('country.business-profiles', $country) }}"
                               class="inline-flex items-center gap-2 bg-[#ff0808] text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#dd0606] transition-colors text-sm">
                                Clear All Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
