@extends('layouts.app')

@section('title', $businessProfile->business_name . ' - Business Profile')

@section('content')
<div class="bg-gray-50 min-h-screen py-6">
    <div class="container mx-auto px-4">

        <!-- Business Profile Header - Compact -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <!-- Cover Image / Banner - Reduced Height -->
            <div class="h-40 bg-cover bg-center relative" style="background-image: url('https://images.pexels.com/photos/346529/pexels-photo-346529.jpeg');">
                <div class="absolute inset-0 bg-gradient-to-r from-green-700 to-green-500 opacity-70"></div>
            </div>

            <!-- Profile Info - Compact -->
            <div class="px-4 py-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Business Logo - Smaller -->
                    <div class="-mt-12 flex-shrink-0 relative z-10">
                        <div class="w-20 h-20 rounded-lg bg-white border-4 border-white shadow-lg flex items-center justify-center overflow-hidden">
                            @if($businessProfile->logo)
                                <img src="{{ $businessProfile->logo }}" alt="{{ $businessProfile->business_name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Business Details - Compact -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between flex-wrap gap-2">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h1 class="text-xl font-bold text-gray-900">{{ $businessProfile->business_name }}</h1>
                                    @if($businessProfile->is_admin_verified)
                                    <span class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-medium px-2 py-0.5 rounded-full">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Afrisellers Verified
                                    </span>
                                    @endif
                                </div>

                                <!-- Location - Compact -->
                                <div class="flex items-center gap-1 text-gray-600 text-sm">
                                    <span class="text-lg">{{ $businessProfile->country->flag ?? '🌍' }}</span>
                                    <span>{{ $businessProfile->city }}, {{ $businessProfile->country->name ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons - Compact -->
                        <div class="flex flex-wrap gap-2 mt-3">
                            <a href="{{ route('request-quote.show', ['businessProfileId' => $businessProfile->id]) }}" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Request Information
                            </a>
                            <button class="inline-flex items-center gap-1 border border-gray-300 hover:border-gray-400 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Contact Company
                            </button>
                            <button class="inline-flex items-center gap-1 border border-gray-300 hover:border-gray-400 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                                Save F.Supplier
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Two Column Layout: About Us and Company Info - Compact -->
                <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- About Us - Takes 2 columns -->
                    <div class="lg:col-span-2">
                        <h3 class="text-base font-bold text-gray-900 mb-2">About Us</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 text-sm leading-relaxed mb-2">
                                {{ $businessProfile->description ?? 'Leading manufacturer and exporter of premium agricultural products in Rwanda with over 15 years of experience.' }}
                            </p>
                            @if($businessProfile->certifications)
                            <p class="text-gray-700 text-sm">
                                <span class="font-semibold">Certified:</span> {{ $businessProfile->certifications }}
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- Company Info - Takes 1 column -->
                    <div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-3">
                            @if($businessProfile->year_established)
                            <div>
                                <span class="text-xs text-gray-500 block mb-0.5">Year Established</span>
                                <p class="text-sm font-semibold text-gray-900">{{ $businessProfile->year_established }}</p>
                            </div>
                            @endif

                            @if($businessProfile->business_type)
                            <div>
                                <span class="text-xs text-gray-500 block mb-0.5">Business Type</span>
                                <p class="text-sm font-semibold text-gray-900">{{ $businessProfile->business_type }}</p>
                            </div>
                            @endif

                            @if($businessProfile->certifications)
                            <div>
                                <span class="text-xs text-gray-500 block mb-0.5">Certifications</span>
                                <p class="text-sm font-semibold text-gray-900">{{ $businessProfile->certifications }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Products Section - 6 Columns -->
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Featured Products</h2>

            @if($businessProfile->user->products->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($businessProfile->user->products as $product)
                    @php
                        $image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        $price = $product->prices->first();
                    @endphp

                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300">
                        <!-- Product Image - Compact -->
                        <a href="{{ route('products.show', $product->slug) }}" class="block relative h-32 overflow-hidden group">
                            @if($image)
                                <img src="{{ $image->image_url }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                     loading="lazy">
                            @else
                                <div class="flex justify-center items-center w-full h-full bg-gray-100">
                                    <span class="text-3xl">📦</span>
                                </div>
                            @endif
                        </a>

                        <!-- Product Info - Compact -->
                        <div class="p-2">
                            <a href="{{ route('products.show', $product->slug) }}">
                                <h3 class="text-xs font-semibold text-gray-900 mb-1 line-clamp-2 hover:text-green-600 transition-colors min-h-[2rem]">
                                    {{ $product->name }}
                                </h3>
                            </a>

                            <p class="text-xs text-gray-600 mb-2">
                                MOQ: {{ number_format($product->min_order_quantity) }} {{ $product->unit ?? 'kg' }}
                            </p>

                            <a href="{{ route('request-quote.show', ['businessProfileId' => $businessProfile->id, 'productId' => $product->id]) }}" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-1.5 px-2 rounded transition-colors">
                                Request Quote
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-gray-600 text-sm">{{ __('messages.no_products_available') }}</p>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
