@extends('layouts.app')

@section('title', 'Products from ' . $country->name)

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
                            <span class="text-gray-900 font-semibold">{{ $country->name }} Products</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-4">
                    @if($country->flag_url)
                        <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" class="w-12 h-9 rounded shadow-sm object-cover">
                    @endif
                    <h2 class="text-2xl font-bold text-gray-900">Products from {{ $country->name }}</h2>
                </div>

                <!-- Stats Bar -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div class="text-center">
                        <div class="text-xl font-bold text-blue-600 mb-1">
                            {{ $products->total() }}
                        </div>
                        <div class="text-xs text-gray-600">Total Products</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-purple-600 mb-1">
                            {{ $suppliers->count() }}
                        </div>
                        <div class="text-xs text-gray-600">Suppliers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-green-600 mb-1">
                            {{ $categories->count() }}
                        </div>
                        <div class="text-xs text-gray-600">Categories</div>
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
                                @if(request()->hasAny(['search', 'category', 'supplier', 'sort']))
                                <a href="{{ route('country.products', $country) }}" class="text-xs text-white hover:underline font-medium">
                                    Clear All
                                </a>
                                @endif
                            </div>
                        </div>

                        <form method="GET" action="{{ route('country.products', $country) }}" class="p-4 space-y-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Product name..."
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                            </div>

                            <!-- Supplier Filter -->
                            @if($suppliers->count() > 0)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Supplier</label>
                                <select name="supplier" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="">All Suppliers</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->user_id }}" {{ request('supplier') == $supplier->user_id ? 'selected' : '' }}>
                                        {{ $supplier->business_name }}
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

                            <!-- Sort By -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                                <select name="sort" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
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
                    @if(request()->hasAny(['search', 'category', 'supplier']))
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="text-sm font-medium text-gray-700">Active filters:</span>

                        @if(request('search'))
                        <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1 rounded">
                            Search: "{{ request('search') }}"
                            <a href="{{ route('country.products', array_merge(['country' => $country->id], request()->except('search'))) }}" class="hover:text-blue-900">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </span>
                        @endif

                        @if(request('supplier'))
                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded">
                            Supplier: {{ $suppliers->where('user_id', request('supplier'))->first()->business_name ?? 'Unknown' }}
                            <a href="{{ route('country.products', array_merge(['country' => $country->id], request()->except('supplier'))) }}" class="hover:text-green-900">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </span>
                        @endif

                        @if(request('category'))
                        <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-1 rounded">
                            Category: {{ $categories->find(request('category'))->name ?? 'Unknown' }}
                            <a href="{{ route('country.products', array_merge(['country' => $country->id], request()->except('category'))) }}" class="hover:text-purple-900">
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
                        Showing <span class="font-semibold">{{ $products->firstItem() ?? 0 }}</span> to
                        <span class="font-semibold">{{ $products->lastItem() ?? 0 }}</span> of
                        <span class="font-semibold">{{ $products->total() }}</span> products
                    </div>

                    <!-- Products Grouped by Supplier -->
                    @if($products->count() > 0)
                        @foreach($productsBySupplier as $userId => $supplierProducts)
                            @php
                                $supplier = $supplierProducts->first()->user->businessProfile;
                            @endphp

                            <!-- Supplier Header -->
                            <div class="mb-6">
                                <div class="bg-white rounded-lg border-2 border-gray-200 p-4 mb-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <span class="text-2xl">🏢</span>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">{{ $supplier->business_name }}</h3>
                                                <p class="text-sm text-gray-600">{{ $supplierProducts->count() }} {{ Str::plural('product', $supplierProducts->count()) }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('business-profile.products', $supplier->id) }}"
                                           class="text-sm font-medium text-[#ff0808] hover:text-[#dd0606] flex items-center gap-1">
                                            View All
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                <!-- Supplier Products Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    @foreach($supplierProducts as $product)
                                        @php
                                            $featuredImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                            $firstPriceTier = $product->prices->first();
                                        @endphp
                                        <div class="group bg-white rounded-lg overflow-hidden transition-all duration-300 hover:shadow-lg border-2 border-transparent hover:border-[#ff0808]">
                                            <!-- Product Image -->
                                            <a href="{{ route('products.show', $product->slug) }}" class="block relative h-40 overflow-hidden">
                                                @if($featuredImage)
                                                    <img src="{{ $featuredImage->image_url }}"
                                                         alt="{{ $product->name }}"
                                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                         loading="lazy">
                                                @else
                                                    <div class="w-full h-full bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                                        <span class="text-5xl">📦</span>
                                                    </div>
                                                @endif

                                                <!-- Verified Badge -->
                                                @if($product->is_admin_verified)
                                                    <span class="absolute top-2 right-2 bg-green-600 text-white text-xs font-semibold px-2 py-0.5 rounded flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Verified
                                                    </span>
                                                @endif
                                            </a>

                                            <!-- Content -->
                                            <div class="p-3 bg-white">
                                                <!-- Product Name -->
                                                <a href="{{ route('products.show', $product->slug) }}">
                                                    <h3 class="text-sm font-bold text-gray-900 mb-2 group-hover:text-[#ff0808] transition-colors line-clamp-2 min-h-[2.5rem]">
                                                        {{ $product->name }}
                                                    </h3>
                                                </a>

                                                <!-- Category -->
                                                @if($product->productCategory)
                                                <div class="mb-2">
                                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="00 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
</svg>
{{ $product->productCategory->name }}
</span>
</div>
@endif
                                            <!-- Price -->
                                            @if($firstPriceTier)
                                                <div class="text-[#ff0808] font-bold text-base mb-2">
                                                    {{ number_format($firstPriceTier->price, 0) }} {{ $firstPriceTier->currency }}
                                                </div>
                                            @endif

                                            <!-- MOQ -->
                                            <div class="flex items-center justify-between text-xs text-gray-600 pt-2 border-t border-gray-100">
                                                <span class="font-medium">MOQ: {{ $product->min_order_quantity }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="mt-6">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg p-12 text-center border border-gray-200">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No Products Found</h3>
                        <p class="text-gray-600 mb-6">
                            Try adjusting your filters or search criteria.
                        </p>
                        <a href="{{ route('country.products', $country) }}"
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
