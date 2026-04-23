@extends('layouts.app')

@section('title', $category->name . ' — Products')

@php
    $currencySymbols = [
        'USD' => '$', 'EUR' => '€', 'GBP' => '£',
        'RWF' => 'RF', 'KES' => 'KSh', 'UGX' => 'USh', 'TZS' => 'TSh',
    ];
@endphp

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- ── Breadcrumb ── --}}
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 sm:px-6 py-3">
            <nav class="flex text-xs text-gray-500 space-x-2 items-center overflow-x-auto whitespace-nowrap">
                <a href="{{ route('home') }}" class="hover:text-[#ff0808] transition-colors">Home</a>
                <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                <a href="{{ route('home') }}" class="hover:text-[#ff0808] transition-colors">Categories</a>
                <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                <span class="text-gray-800 font-medium">{{ $category->name }}</span>
            </nav>
        </div>
    </div>

    {{-- ── Category Hero Banner ── --}}
    <div class="bg-[#2d4a7a] text-white">
        <div class="container mx-auto px-4 sm:px-6 py-8 sm:py-12">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-[#ff0808] rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-th-large text-white text-base"></i>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight">{{ $category->name }}</h1>
                    </div>
                    <p class="text-white/70 text-sm">
                        {{ number_format($products->total()) }} products from verified suppliers across Africa
                    </p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <a href="{{ route('rfqs.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors shadow-lg">
                        <i class="fas fa-paper-plane"></i> Request Quote
                    </a>
                    <a href="{{ route('products.search', ['type' => 'category', 'slug' => \Illuminate\Support\Str::slug($category->name), 'tab' => 'suppliers']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-lg transition-colors border border-white/20">
                        <i class="fas fa-store"></i> View Suppliers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 py-6">
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ── Sidebar Filters ── --}}
            <aside class="lg:w-64 flex-shrink-0">

                {{-- Mobile Filter Toggle --}}
                <button id="mobileFilterToggle"
                    class="lg:hidden w-full flex items-center justify-between px-4 py-3 bg-white rounded-lg shadow-sm border border-gray-200 text-sm font-semibold text-gray-700 mb-4">
                    <span class="flex items-center gap-2"><i class="fas fa-sliders-h text-[#ff0808]"></i> Filters</span>
                    <i class="fas fa-chevron-down text-gray-400" id="mobileFilterIcon"></i>
                </button>

                <div id="filterSidebar" class="hidden lg:block space-y-4">

                    {{-- Active Filters --}}
                    @php
                        $activeFilters = array_filter(request()->except(['page']));
                    @endphp
                    @if(count($activeFilters))
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-gray-900">Active Filters</h3>
                            <a href="{{ route('categories.products', ['slug' => $category->slug ?? \Illuminate\Support\Str::slug($category->name)]) }}" class="text-xs text-[#ff0808] hover:underline">Clear all</a>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($activeFilters as $key => $value)
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 text-red-700 border border-red-200 rounded-full text-[10px] font-medium">
                                    {{ str_replace('_', ' ', $key) }}
                                    <a href="{{ request()->fullUrlWithoutQuery([$key]) }}" class="hover:text-red-900"><i class="fas fa-times text-[8px]"></i></a>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <form method="GET" action="{{ route('categories.products', ['slug' => $category->slug ?? \Illuminate\Support\Str::slug($category->name)]) }}" id="filterForm">

                        {{-- Sort --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Sort By</h3>
                            <div class="space-y-2">
                                @foreach(['latest' => 'Newest First', 'oldest' => 'Oldest First', 'price_low' => 'Price: Low to High', 'price_high' => 'Price: High to Low', 'name_asc' => 'Name A–Z', 'name_desc' => 'Name Z–A'] as $val => $label)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="sort" value="{{ $val }}" class="accent-[#ff0808]"
                                        {{ request('sort', 'latest') === $val ? 'checked' : '' }}
                                        onchange="document.getElementById('filterForm').submit()">
                                    <span class="text-xs text-gray-700 group-hover:text-[#ff0808] transition-colors">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Supplier Trust --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Supplier Trust</h3>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="verified_supplier" value="1" class="accent-[#ff0808]"
                                        {{ request('verified_supplier') ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-700 group-hover:text-[#ff0808]">
                                        <i class="fas fa-check-circle text-green-500 mr-1"></i> Verified Supplier
                                    </span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="verified_pro" value="1" class="accent-[#ff0808]"
                                        {{ request('verified_pro') ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-700 group-hover:text-[#ff0808]">
                                        <i class="fas fa-shield-alt text-blue-500 mr-1"></i> Verified PRO
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Price Range --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Price Range</h3>
                            <div class="space-y-2">
                                @foreach(['0-100' => 'Under $100', '100-500' => '$100 – $500', '500-1000' => '$500 – $1,000', '1000-5000' => '$1,000 – $5,000', '5000-plus' => '$5,000+'] as $val => $label)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="price_range" value="{{ $val }}" class="accent-[#ff0808]"
                                        {{ request('price_range') === $val ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-700 group-hover:text-[#ff0808]">{{ $label }}</span>
                                </label>
                                @endforeach
                                @if(request('price_range'))
                                <a href="{{ request()->fullUrlWithoutQuery(['price_range']) }}" class="text-[10px] text-gray-400 hover:text-[#ff0808]">Clear price</a>
                                @endif
                            </div>
                        </div>

                        {{-- Min Order Qty --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Min. Order Qty</h3>
                            <div class="space-y-2.5">
                                @foreach(['moq_1_10' => '1–10 pieces', 'moq_11_50' => '11–50 pieces', 'moq_51_100' => '51–100 pieces', 'moq_100_plus' => '100+ pieces'] as $name => $label)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="{{ $name }}" value="1" class="accent-[#ff0808]"
                                        {{ request($name) ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-700 group-hover:text-[#ff0808]">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Product Features --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Product Features</h3>
                            <div class="space-y-2.5">
                                @foreach(['ready_to_ship' => ['🚀', 'Ready to Ship'], 'customizable' => ['🎨', 'Customizable'], 'eco_friendly' => ['🌿', 'Eco-Friendly'], 'free_shipping' => ['📦', 'Free Shipping'], 'paid_samples' => ['🔬', 'Paid Samples']] as $name => [$icon, $label])
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="{{ $name }}" value="1" class="accent-[#ff0808]"
                                        {{ request($name) ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-700 group-hover:text-[#ff0808]">{{ $icon }} {{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Country Filter --}}
                        @if($availableCountries->count())
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Country of Origin</h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @foreach($availableCountries as $country)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="countries[]"
                                        value="{{ \Illuminate\Support\Str::slug($country->name, '_') }}"
                                        class="accent-[#ff0808]"
                                        {{ in_array(\Illuminate\Support\Str::slug($country->name, '_'), (array) request('countries', [])) ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-700 group-hover:text-[#ff0808] flex items-center gap-1">
                                        @if($country->flag_url)
                                            <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" class="w-4 h-3 object-cover rounded-sm">
                                        @endif
                                        {{ $country->name }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <button type="submit"
                            class="w-full py-2.5 bg-[#ff0808] hover:bg-red-700 mt-5 text-white text-sm font-bold rounded-lg transition-colors shadow-md">
                            <i class="fas fa-filter mr-2"></i> Apply Filters
                        </button>

                    </form>
                </div>
            </aside>

            {{-- ── Products Main Area ── --}}
            <div class="flex-1 min-w-0">

                {{-- Results bar --}}
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <p class="text-sm text-gray-600">
                        Showing <span class="font-bold text-gray-900">{{ number_format($products->firstItem() ?? 0) }}–{{ number_format($products->lastItem() ?? 0) }}</span>
                        of <span class="font-bold text-gray-900">{{ number_format($products->total()) }}</span> results
                    </p>
                    <div class="flex items-center gap-2">
                        {{-- Grid / List toggle --}}
                        <button id="gridViewBtn" class="p-2 rounded-md bg-[#ff0808] text-white" title="Grid view">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 8a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zm6-8a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2h-2zm0 8a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2z"/></svg>
                        </button>
                        <button id="listViewBtn" class="p-2 rounded-md bg-white border border-gray-200 text-gray-600 hover:bg-gray-50" title="List view">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Products Grid --}}
                @if($products->count())
                <div id="productsGrid" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                    @foreach($products as $product)
                    @php
                        $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        $firstPrice = $product->prices->sortBy('min_qty')->first();
                        $currency = $firstPrice->currency ?? 'USD';
                        $symbol = $currencySymbols[$currency] ?? $currency;
                        $finalPrice = $firstPrice ? ($firstPrice->price - ($firstPrice->discount ?? 0)) : null;
                        $hasDiscount = $firstPrice && ($firstPrice->discount ?? 0) > 0;
                        $businessProfile = optional(optional(optional($product->user)->vendor)->businessProfile);
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg hover:border-[#ff0808]/30 transition-all duration-300 group flex flex-col product-card">
                        <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden bg-gray-50" style="padding-bottom: 68%;">
                            @if($image)
                                <img src="{{ $image->image_url }}" alt="{{ $product->name }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-4xl bg-gray-100">📦</div>
                            @endif

                            {{-- Badges --}}
                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                @if($product->ready_to_ship)
                                    <span class="px-1.5 py-0.5 bg-green-500 text-white text-[9px] font-bold rounded">Ready</span>
                                @endif
                                @if($hasDiscount)
                                    <span class="px-1.5 py-0.5 bg-[#ff0808] text-white text-[9px] font-bold rounded">Sale</span>
                                @endif
                                @if($product->eco_friendly)
                                    <span class="px-1.5 py-0.5 bg-emerald-500 text-white text-[9px] font-bold rounded">Eco</span>
                                @endif
                            </div>

                            {{-- Wishlist --}}
                            <button class="wishlist-mini-btn absolute top-2 right-2 w-7 h-7 bg-white rounded-full shadow flex items-center justify-center hover:bg-red-50 transition-colors z-10"
                                    data-product-id="{{ $product->id }}"
                                    data-url="{{ route('wishlist.toggle', $product) }}">
                                <i class="far fa-heart text-gray-400 text-xs"></i>
                            </button>
                        </a>

                        <div class="p-3 flex flex-col flex-1">
                            <a href="{{ route('products.show', $product->slug) }}">
                                <h3 class="text-xs font-semibold text-gray-800 line-clamp-2 group-hover:text-[#ff0808] transition-colors mb-1.5">
                                    {{ $product->name }}
                                </h3>
                            </a>

                            {{-- Supplier --}}
                            @if($businessProfile->business_name)
                            <p class="text-[10px] text-gray-500 mb-1.5 flex items-center gap-1 truncate">
                                <i class="fas fa-store text-[8px]"></i>
                                {{ $businessProfile->business_name }}
                                @if($businessProfile->is_admin_verified)
                                    <i class="fas fa-check-circle text-green-500 text-[8px]"></i>
                                @endif
                            </p>
                            @endif

                            {{-- Country --}}
                            @if($product->country)
                            <p class="text-[10px] text-gray-400 mb-2 flex items-center gap-1">
                                @if($product->country->flag_url)
                                    <img src="{{ $product->country->flag_url }}" alt="{{ $product->country->name }}" class="w-3.5 h-2.5 object-cover">
                                @endif
                                {{ $product->country->name }}
                            </p>
                            @endif

                            <div class="mt-auto">
                                {{-- Price --}}
                                @if($finalPrice !== null)
                                <div class="flex items-baseline gap-1.5 mb-1.5">
                                    <span class="text-sm font-black text-gray-900">{{ $symbol }}{{ number_format($finalPrice, 2) }}</span>
                                    @if($hasDiscount)
                                        <span class="text-[10px] text-gray-400 line-through">{{ $symbol }}{{ number_format($firstPrice->price, 2) }}</span>
                                    @endif
                                </div>
                                @else
                                <p class="text-xs text-gray-400 mb-1.5 italic">Contact for price</p>
                                @endif

                                {{-- MOQ --}}
                                @if($product->min_order_quantity)
                                <p class="text-[10px] text-gray-500 mb-2">
                                    Min. <span class="font-semibold">{{ number_format($product->min_order_quantity) }}</span> pcs
                                    @if($firstPrice && $firstPrice->min_qty)
                                        / {{ number_format($firstPrice->min_qty) }}–{{ $firstPrice->max_qty ? number_format($firstPrice->max_qty) : '∞' }}
                                    @endif
                                </p>
                                @endif

                                <a href="{{ route('products.show', $product->slug) }}"
                                    class="block w-full text-center py-1.5 bg-[#1a2942] hover:bg-[#ff0808] text-white text-[10px] font-bold rounded-lg transition-colors">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- List view (hidden by default) --}}
                <div id="productsList" class="hidden space-y-3">
                    @foreach($products as $product)
                    @php
                        $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        $firstPrice = $product->prices->sortBy('min_qty')->first();
                        $currency = $firstPrice->currency ?? 'USD';
                        $symbol = $currencySymbols[$currency] ?? $currency;
                        $finalPrice = $firstPrice ? ($firstPrice->price - ($firstPrice->discount ?? 0)) : null;
                        $businessProfile = optional(optional(optional($product->user)->vendor)->businessProfile);
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-200 p-4 flex gap-4 hover:shadow-md hover:border-[#ff0808]/30 transition-all duration-300 group">
                        <a href="{{ route('products.show', $product->slug) }}" class="w-28 h-28 flex-shrink-0 rounded-lg overflow-hidden bg-gray-50">
                            @if($image)
                                <img src="{{ $image->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-3xl bg-gray-100">📦</div>
                            @endif
                        </a>
                        <div class="flex-1 min-w-0 flex flex-col">
                            <div class="flex items-start justify-between gap-2">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-[#ff0808] transition-colors line-clamp-2">{{ $product->name }}</h3>
                                </a>
                                @if($finalPrice !== null)
                                <div class="flex-shrink-0 text-right">
                                    <div class="text-base font-black text-gray-900">{{ $symbol }}{{ number_format($finalPrice, 2) }}</div>
                                    @if(($firstPrice->discount ?? 0) > 0)
                                        <div class="text-xs text-gray-400 line-through">{{ $symbol }}{{ number_format($firstPrice->price, 2) }}</div>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-3 mt-1.5 text-xs text-gray-500">
                                @if($businessProfile->business_name)
                                    <span class="flex items-center gap-1"><i class="fas fa-store text-[9px]"></i> {{ $businessProfile->business_name }}</span>
                                @endif
                                @if($product->country)
                                    <span class="flex items-center gap-1">
                                        @if($product->country->flag_url)<img src="{{ $product->country->flag_url }}" class="w-3.5 h-2.5">@endif
                                        {{ $product->country->name }}
                                    </span>
                                @endif
                                @if($product->min_order_quantity)
                                    <span>Min. {{ number_format($product->min_order_quantity) }} pcs</span>
                                @endif
                            </div>
                            @if($product->short_description)
                                <p class="text-xs text-gray-600 mt-1.5 line-clamp-2">{{ $product->short_description }}</p>
                            @endif
                            <div class="mt-auto pt-2 flex gap-2">
                                <a href="{{ route('products.show', $product->slug) }}"
                                    class="px-4 py-1.5 bg-[#1a2942] hover:bg-[#ff0808] text-white text-xs font-bold rounded-lg transition-colors">
                                    View Details
                                </a>
                                <button class="wishlist-mini-btn px-3 py-1.5 border border-gray-200 hover:border-red-300 text-gray-500 hover:text-red-500 text-xs rounded-lg transition-colors"
                                        data-product-id="{{ $product->id }}"
                                        data-url="{{ route('wishlist.toggle', $product) }}">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
                @endif

                @else
                {{-- Empty state --}}
                <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-xl border border-gray-200">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-4xl mb-5">📦</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">No products found</h3>
                    <p class="text-sm text-gray-500 mb-6 max-w-sm">
                        No products match your current filters in <strong>{{ $category->name }}</strong>. Try adjusting the filters.
                    </p>
                    <a href="{{ route('categories.products', ['slug' => $category->slug ?? \Illuminate\Support\Str::slug($category->name)]) }}"
                        class="px-6 py-2.5 bg-[#ff0808] hover:bg-red-700 text-white text-sm font-bold rounded-lg transition-colors">
                        Clear Filters
                    </a>
                </div>
                @endif

            </div>{{-- end main --}}
        </div>{{-- end flex --}}
    </div>{{-- end container --}}

    {{-- ── Related Categories ── --}}
    @if($relatedCategories->count())
    <div class="bg-white border-t border-gray-200 mt-8">
        <div class="container mx-auto px-4 sm:px-6 py-8">
            <h2 class="text-base font-bold text-gray-900 mb-4">Other Categories</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($relatedCategories as $related)
                <a href="{{ route('categories.products', ['slug' => $related->slug ?? \Illuminate\Support\Str::slug($related->name)]) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-50 hover:bg-[#fff5f5] hover:text-[#ff0808] border border-gray-200 hover:border-[#ff0808]/30 rounded-lg text-xs font-medium text-gray-700 transition-all">
                    {{ $related->name }}
                    <span class="text-[10px] text-gray-400">({{ number_format($related->products_count) }})</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Mobile filter sidebar toggle
    const toggle = document.getElementById('mobileFilterToggle');
    const sidebar = document.getElementById('filterSidebar');
    const icon = document.getElementById('mobileFilterIcon');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('hidden');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        });
    }

    // Grid / List view toggle
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    const grid    = document.getElementById('productsGrid');
    const list    = document.getElementById('productsList');

    if (gridBtn && listBtn) {
        gridBtn.addEventListener('click', function () {
            grid?.classList.remove('hidden');
            list?.classList.add('hidden');
            gridBtn.classList.add('bg-[#ff0808]', 'text-white');
            gridBtn.classList.remove('bg-white', 'border-gray-200', 'text-gray-600');
            listBtn.classList.remove('bg-[#ff0808]', 'text-white');
            listBtn.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-600');
        });

        listBtn.addEventListener('click', function () {
            list?.classList.remove('hidden');
            grid?.classList.add('hidden');
            listBtn.classList.add('bg-[#ff0808]', 'text-white');
            listBtn.classList.remove('bg-white', 'border-gray-200', 'text-gray-600');
            gridBtn.classList.remove('bg-[#ff0808]', 'text-white');
            gridBtn.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-600');
        });
    }

    // Wishlist mini buttons
    document.querySelectorAll('.wishlist-mini-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            fetch(this.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                const icon = this.querySelector('.fa-heart');
                if (icon) {
                    if (data.wishlisted) {
                        icon.classList.replace('far', 'fas');
                        icon.classList.add('text-red-500');
                    } else {
                        icon.classList.replace('fas', 'far');
                        icon.classList.remove('text-red-500');
                    }
                }
                const navBadge = document.getElementById('wishlistCount');
                if (navBadge && data.count !== undefined) navBadge.textContent = data.count;
            });
        });
    });

    // Auto-submit filter form on checkbox change
    document.querySelectorAll('#filterForm input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', () => document.getElementById('filterForm').submit());
    });

});
</script>
@endsection
