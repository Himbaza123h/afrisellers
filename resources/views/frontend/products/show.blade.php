@extends('layouts.app')

@section('title', $product->name)

@php
    $currencySymbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'RWF' => 'RF',
        'KES' => 'KSh',
        'UGX' => 'USh',
        'TZS' => 'TSh',
    ];
@endphp

@section('content')
    <div class="py-8 min-h-screen bg-gray-50">
        <div class="container px-4 mx-auto">


            <!-- Breadcrumb -->
            <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600">Home</a>
                    </li>
                    @if ($product->productCategory)
                        <li>
                            <div class="flex items-center">
                                <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('products.search', ['type' => 'category', 'slug' => \Illuminate\Support\Str::slug($product->productCategory->name)]) }}"
                                    class="text-gray-700 hover:text-blue-600">{{ $product->productCategory->name }}</a>
                            </div>
                        </li>
                    @endif
                    <li>
                        <div class="flex items-center">
                            <svg class="mx-1 w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-500">{{ $product->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Product Title & Rating -->
            <div class="mb-6">
                <h1 class="mb-4 text-2xl font-bold text-gray-900 md:text-lg">
                    {{ $product->name }}
                </h1>

                <div class="flex flex-wrap gap-6 items-center">
                    @php
                        $avgRating = $allReviews->count() > 0 ? $allReviews->avg('mark') : 0;
                        $reviewsCount = $allReviews->count();
                    @endphp
                    @if ($avgRating > 0)
                        <div class="flex gap-2 items-center">
                            <div class="flex text-yellow-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 fill-current {{ $i <= round($avgRating) ? '' : 'text-gray-300' }}"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="font-semibold text-gray-900">{{ number_format($avgRating, 1) }}</span>
                            @if ($reviewsCount > 0)
                                <a href="#reviews"
                                    class="text-blue-600 hover:underline">({{ number_format($reviewsCount) }}
                                    reviews)</a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex gap-4 items-center mt-4 text-sm text-gray-600">
                    @php
                        $vendor = $product->user->vendor ?? null;
                        $businessProfile = $vendor->businessProfile ?? null;
                    @endphp
                    @if ($businessProfile)
                        <div class="flex gap-2 items-center">
                            <span>{{ $businessProfile->business_name ?? ($product->user->name ?? 'Vendor') }}</span>
                        </div>
                    @elseif($product->user)
                        <div class="flex gap-2 items-center">
                            <span>{{ $product->user->name ?? 'Vendor' }}</span>
                        </div>
                    @endif
                    @if ($product->user && $product->user->created_at)
                        <span>•</span>
                        @php
                            $yearsSince = round($product->user->created_at->diffInDays() / 365.25, 1);
                        @endphp
                        <span>
                            @if($yearsSince < 1)
                                Less than a year
                            @else
                                {{ $yearsSince }} {{ $yearsSince == 1.0 ? 'year' : 'years' }}
                            @endif
                        </span>
                    @endif
                    @if ($product->country)
                        <span>•</span>
                        <span class="flex gap-1 items-center">
                            @if ($product->country->flag_url)
                                <img src="{{ $product->country->flag_url }}" alt="{{ $product->country->name }}"
                                    class="w-5 h-4">
                            @endif
                            {{ $product->country->name }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                <!-- Left Column - Images -->
                <div class="lg:col-span-2">
                    <div class="p-6 bg-white rounded-md shadow-sm">
                        <div class="flex gap-4">
                            <!-- Thumbnail Column -->
                            <div class="flex flex-col gap-3 w-24" id="thumbnailContainer">
                                <!-- Thumbnails will be generated by JavaScript -->
                            </div>

                            <!-- Main Image -->
                            <div class="overflow-hidden relative flex-1 bg-gray-50 rounded-md">
                                <button
                                    class="absolute top-4 right-4 z-10 p-2 bg-white rounded-full shadow-md hover:bg-gray-50">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                        </path>
                                    </svg>
                                </button>

                                <button
                                    class="absolute right-4 top-16 z-10 p-2 bg-white rounded-full shadow-md hover:bg-gray-50">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </button>

                                @php
                                    $featuredImage =
                                        $product->images->where('is_primary', true)->first() ??
                                        $product->images->first();
                                    $defaultImage = $featuredImage
                                        ? $featuredImage->image_url
                                        : asset('images/placeholder-product.png');
                                @endphp
                                <img id="mainImage" src="{{ $defaultImage }}" alt="{{ $product->name }}"
                                    class="w-full h-[500px] object-cover">

                                <!-- Navigation Arrows -->
                                <button id="prevImage"
                                    class="absolute left-4 top-1/2 p-2 rounded-full shadow-md -translate-y-1/2 bg-white/90 hover:bg-white">
                                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button id="nextImage"
                                    class="absolute right-4 top-1/2 p-2 rounded-full shadow-md -translate-y-1/2 bg-white/90 hover:bg-white">
                                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Product Info & Purchase -->
                <div class="lg:col-span-1">
                    <div class="sticky top-4 p-6 bg-white rounded-md shadow-sm">

                        <!-- Price Badge -->
                        @if ($product->is_lower_priced)
                            <div class="bg-[#ff0808] text-white inline-block px-3 py-1 rounded text-sm font-semibold mb-4">
                                Lower priced than similar
                            </div>
                        @endif
                        <!-- Negotiable Badge -->
                        @if($product->is_negotiable)
                            <div class="p-4 mb-4 text-center bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl border-2 border-yellow-300">
                                <div class="flex justify-center items-center gap-2 mb-2">
                                    <i class="text-2xl text-yellow-600 fas fa-handshake"></i>
                                    <span class="text-xl font-bold text-gray-900">Negotiable Price</span>
                                </div>
                                <p class="text-sm text-gray-600">Contact supplier to discuss pricing</p>
                            </div>
                        @endif

                        <!-- Pricing Tiers -->
                        <div class="mb-6">
                            @if ($product->prices->count() > 0)
                                @php
                                    $sortedPrices = $product->prices->sortBy('min_qty');
                                    $firstPrice = $sortedPrices->first();
                                    $currency = $firstPrice->currency ?? 'USD';
                                    $symbol = $currencySymbols[$currency] ?? $currency;
                                @endphp

                                <div class="space-y-3">
                                    @foreach ($sortedPrices as $tier)
                                        @php
                                            $originalPrice = $tier->price;
                                            $discount = $tier->discount ?? 0;
                                            $finalPrice = $originalPrice - $discount;
                                        @endphp
                                        <div class="p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-md border border-green-200">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="text-sm font-medium text-gray-600">
                                                    {{ number_format($tier->min_qty) }}
                                                    @if ($tier->max_qty)
                                                        - {{ number_format($tier->max_qty) }}
                                                    @else
                                                        +
                                                    @endif
                                                    pieces
                                                </div>
                                                <div class="flex gap-2">
                                                @if($discount > 0)
                                                    <span class="px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded-full">
                                                        Save {{ $symbol }}{{ number_format($discount, 2) }}
                                                    </span>
                                                @endif
                                                @if($product->is_negotiable)
                                                    <span class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">
                                                        Negotiable
                                                    </span>
                                                @endif
                                            </div>
                                            </div>
                                            <div class="flex items-baseline gap-2">
                                                <span class="text-2xl font-bold text-gray-900">
                                                    {{ $symbol }}{{ number_format($finalPrice, 2) }}
                                                </span>
                                                @if($discount > 0)
                                                    <span class="text-lg text-gray-400 line-through">
                                                        {{ $symbol }}{{ number_format($originalPrice, 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-6 text-center bg-gray-50 rounded-md">
                                    <div class="mb-2 text-gray-400">
                                        <i class="text-4xl fas fa-tag"></i>
                                    </div>
                                    <p class="font-medium text-gray-600">Price not set</p>
                                    <p class="text-sm text-gray-500">Contact supplier for pricing</p>
                                </div>
                            @endif
                        </div>

                        <hr class="my-6">

                        <!-- Variations Section -->
                        @if ($product->variations->count() > 0)
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Variations</h3>
                                </div>

                                @php
                                    $variationsByType = $product->variations->groupBy('variation_type');
                                @endphp

                                @foreach ($variationsByType as $type => $variations)
                                    <div class="mb-6">
                                        <label class="block mb-3 text-sm font-medium text-gray-700 capitalize">
                                            {{ str_replace('_', ' ', $type) }}: <span
                                                class="text-gray-900 variation-selected-{{ $type }}">{{ $variations->first()->variation_value ?? '' }}</span>
                                        </label>
                                        <div class="flex flex-wrap gap-3 variation-options"
                                            data-type="{{ $type }}">
                                            @foreach ($variations as $variation)
                                                <button type="button"
                                                    class="px-4 py-2 border-2 {{ $loop->first ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }} rounded-full text-sm font-medium variation-option"
                                                    data-type="{{ $type }}"
                                                    data-value="{{ $variation->variation_value }}">
                                                    {{ $variation->variation_value }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <hr class="my-6">

                    <!-- Action Buttons -->
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button class="w-full bg-[#0088cc] hover:bg-[#006699] text-white font-bold py-4 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <i class="mr-2 fas fa-envelope"></i>
                        Contact Supplier
                    </button>
                    <div class="space-y-3">
                    <!-- Shop Now Button -->
                    <button id="shopNowBtn" class="w-full bg-[#3e07f4] hover:bg-[#270de8] text-white font-bold py-4 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <i class="mr-2 fas fa-shopping-bag"></i>
                        Shop Now
                    </button>

                    <!-- Quantity Selector (Hidden by default) -->
                    <div id="quantitySelector" class="hidden space-y-3">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="cartQuantity"
                                value="{{ $product->min_order_quantity ?? 1 }}"
                                min="{{ $product->min_order_quantity ?? 1 }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <!-- Add to Cart Button -->
                        <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" id="hiddenQuantity">
                            <input type="hidden" name="selected_variations" id="hiddenVariations">

                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="mr-2 fas fa-cart-plus"></i>
                                Add to Cart
                            </button>
                        </form>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button class="px-4 py-3 font-semibold text-gray-700 bg-white rounded-lg border-2 border-gray-300 transition-all duration-300 hover:border-[#25D366] hover:text-[#25D366] hover:shadow-md transform hover:scale-105">
                            <i class="mr-1 far fa-heart"></i>
                            Wishlist
                        </button>
                        <button class="px-4 py-3 font-semibold text-white bg-[#25D366] hover:bg-[#128C7E] rounded-lg transition-all duration-300 hover:shadow-md transform hover:scale-105">
                            <i class="mr-1 fas fa-comment"></i>
                            Chat
                        </button>
                    </div>

                </div>
                </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Tabs -->
            <div class="mt-8 bg-white rounded-md shadow-sm">
                <div class="border-b border-gray-200">
                    <nav class="flex px-6 space-x-8" aria-label="Tabs">
                        <button class="px-1 py-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600 tab-button"
                            data-tab="overview">
                            Overview
                        </button>
                        <button
                            class="px-1 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent tab-button hover:text-gray-700 hover:border-gray-300"
                            data-tab="specifications">
                            Specifications
                        </button>
                        <button
                            class="px-1 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent tab-button hover:text-gray-700 hover:border-gray-300"
                            data-tab="shipping">
                            Shipping & Payment
                        </button>
                        <button
                            class="px-1 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent tab-button hover:text-gray-700 hover:border-gray-300"
                            data-tab="reviews">
                            Reviews ({{ $reviewsCount ?? 0 }})
                        </button>
                        <button
                            class="px-1 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent tab-button hover:text-gray-700 hover:border-gray-300"
                            data-tab="youtube">
                            YouTube Video
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- Overview Tab -->
                    <div id="overview-tab" class="tab-content">
                        <h3 class="mb-4 text-xl font-bold text-gray-900">Product Description</h3>
                        <div class="space-y-4 max-w-none text-gray-700 prose">
                            @if ($product->description)
                                <div>
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            @elseif($product->overview)
                                <div>
                                    {!! nl2br(e($product->overview)) !!}
                                </div>
                            @else
                                <p>No description available for this product.</p>
                            @endif

                            @if ($product->short_description)
                                <p class="italic text-gray-600">
                                    {{ $product->short_description }}
                                </p>
                            @endif

                            @if ($product->images->count() > 1)
                                <div class="grid grid-cols-2 gap-4 mt-6 lg:grid-cols-3 xl:grid-cols-4">
                                    @foreach ($product->images->skip(1)->take(2) as $image)
                                        <img src="{{ $image->image_url }}"
                                            alt="{{ $image->alt_text ?? $product->name }}" class="rounded-md">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Specifications Tab -->
                    <div id="specifications-tab" class="hidden tab-content">
                        <h3 class="mb-4 text-xl font-bold text-gray-900">Technical Specifications</h3>
                        <div class="overflow-x-auto">
                            @if ($product->specifications && is_array($product->specifications) && count($product->specifications) > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($product->specifications as $key => $value)
                                            <tr>
                                                <td
                                                    class="px-6 py-4 text-sm font-medium text-gray-900 capitalize whitespace-nowrap bg-gray-50">
                                                    {{ str_replace('_', ' ', $key) }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ is_array($value) ? implode(', ', $value) : $value }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-600">No specifications available for this product.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Shipping Tab -->
                    <div id="shipping-tab" class="hidden tab-content">
                        <h3 class="mb-4 text-xl font-bold text-gray-900">Shipping & Payment Information</h3>
                        <div class="space-y-6 text-gray-700">
                            @if ($product->shipping_info && is_array($product->shipping_info) && count($product->shipping_info) > 0)
                                <div>
                                    <h4 class="mb-2 font-semibold text-gray-900">Shipping Options:</h4>
                                    @if (isset($product->shipping_info['options']) && is_array($product->shipping_info['options']))
                                        <ul class="pl-6 space-y-1 list-disc">
                                            @foreach ($product->shipping_info['options'] as $option)
                                                <li>{{ $option }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>{{ is_array($product->shipping_info) ? json_encode($product->shipping_info, JSON_PRETTY_PRINT) : $product->shipping_info }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if ($product->payment_info && is_array($product->payment_info) && count($product->payment_info) > 0)
                                <div>
                                    <h4 class="mb-2 font-semibold text-gray-900">Payment Methods:</h4>
                                    @if (isset($product->payment_info['methods']) && is_array($product->payment_info['methods']))
                                        <ul class="pl-6 space-y-1 list-disc">
                                            @foreach ($product->payment_info['methods'] as $method)
                                                <li>{{ $method }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>{{ is_array($product->payment_info) ? json_encode($product->payment_info, JSON_PRETTY_PRINT) : $product->payment_info }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if ($product->shipping_info || $product->payment_info)
                                <div>
                                    <h4 class="mb-2 font-semibold text-gray-900">Return Policy:</h4>
                                    <p>Please contact the vendor for return policy details.</p>
                                </div>
                            @else
                                <p>No shipping and payment information available. Please contact the vendor for details.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Reviews Tab -->
                    <div id="reviews-tab" class="hidden tab-content">
                        <!-- Reviews Section -->
                        <div class="p-6 bg-white rounded-md shadow">
                            <h2 class="mb-6 text-2xl font-bold text-gray-900">Reviews</h2>

                            @php
                                // Ensure $allReviews is always defined
                                $allReviews = $allReviews ?? collect();
                            @endphp

                            <!-- Success/Error Messages -->
                            @if(session('success'))
                                <div class="mb-6 p-4 bg-green-50 rounded-md border border-green-300">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="mb-6 p-4 bg-red-50 rounded-md border border-red-300">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="mb-6 p-4 bg-red-50 rounded-md border border-red-300">
                                    <p class="mb-2 text-sm font-medium text-red-900">Please fix the following errors:</p>
                                    <ul class="space-y-1 text-sm text-red-700">
                                        @foreach($errors->all() as $error)
                                            <li>• {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Review Summary -->
                            @if ($allReviews && $allReviews->count() > 0)
                                @php
                                    $averageRating = $allReviews->avg('mark') ?? 0;
                                    $totalReviews = $allReviews->count();
                                    $ratingCounts = $allReviews->groupBy('mark')->map->count();
                                @endphp
                                <div class="flex gap-8 items-center pb-6 mb-8 border-b">
                                    <div class="text-center">
                                        <div class="text-5xl font-bold text-gray-900">{{ number_format($averageRating, 1) }}</div>
                                        <div class="flex justify-center items-center mt-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">{{ $totalReviews }}
                                            {{ $totalReviews === 1 ? 'review' : 'reviews' }}</p>
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        @for ($star = 5; $star >= 1; $star--)
                                            @php
                                                $count = $ratingCounts->get($star, 0);
                                                $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                            @endphp
                                            <div class="flex gap-3 items-center">
                                                <span class="w-16 text-sm text-gray-600">{{ $star }}
                                                    {{ $star === 1 ? 'Star' : 'Stars' }}</span>
                                                <div class="flex-1 h-2 bg-gray-200 rounded-full">
                                                    <div class="h-2 bg-yellow-400 rounded-full"
                                                        style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <span class="w-8 text-sm text-gray-600">{{ $count }}</span>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            @else
                                <div class="py-8 text-center">
                                    <p class="text-gray-500">No reviews yet. Be the first to review this product!</p>
                                </div>
                            @endif

                            <div>
                                <!-- drawer init and toggle -->
                                <div class="text-end">
                                    @auth
                                        <button
                                            class="px-5 py-2.5 mb-2 text-sm font-medium text-white bg-blue-700 rounded-md hover:bg-blue-900 focus:ring-4 focus:ring-blue-300 dark:bg-yellow-400 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                            type="button" data-drawer-target="drawer-right-example"
                                            data-drawer-show="drawer-right-example" data-drawer-placement="right"
                                            aria-controls="drawer-right-example">
                                            Add Review
                                        </button>
                                    @else
                                        <button
                                            class="px-5 py-2.5 mb-2 text-sm font-medium text-white bg-gray-400 rounded-md cursor-not-allowed"
                                            type="button" disabled title="Please login to add a review">
                                            <i class="mr-2 fas fa-lock"></i> Login to Add Review
                                        </button>
                                    @endauth
                                </div>

                                <!-- drawer component -->
                                <div id="drawer-right-example"
                                    class="overflow-y-auto fixed top-0 right-0 z-40 p-4 w-80 h-screen bg-white transition-transform translate-x-full dark:bg-gray-800"
                                    tabindex="-1" aria-labelledby="drawer-right-label">
                                    <h5 id="drawer-right-label"
                                        class="inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                                        </svg>Add Review
                                    </h5>
                                    <button type="button" data-drawer-hide="drawer-right-example"
                                        aria-controls="drawer-right-example"
                                        class="inline-flex absolute top-2.5 justify-center items-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-md hover:bg-gray-200 hover:text-gray-900 end-2.5 dark:hover:bg-gray-600 dark:hover:text-white">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close menu</span>
                                    </button>
                                    <div class="bg-white shadow-sm dark:bg-gray-800 dark:border-gray-700">
                                        @auth
                                            {{-- Review Form --}}
                                            <form id="reviewForm" method="POST" action="{{ route('products.review') }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <div class="mb-4">
                                                    <label for="mark"
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                        Rating (1-5 stars)
                                                    </label>
                                                    <select name="mark" id="mark" required
                                                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                        <option value="">Select Rating</option>
                                                        <option value="1">1 Star</option>
                                                        <option value="2">2 Stars</option>
                                                        <option value="3">3 Stars</option>
                                                        <option value="4">4 Stars</option>
                                                        <option value="5">5 Stars</option>
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label for="message"
                                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                        Review Message
                                                    </label>
                                                    <textarea name="message" id="message" rows="3" required
                                                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                        placeholder="Write your review here..."></textarea>
                                                </div>
                                                <button type="submit" id="submitReview"
                                                    class="px-5 py-2.5 w-full text-sm font-medium text-white bg-yellow-400 rounded-md transition-colors hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-yellow-400 dark:focus:ring-blue-800">
                                                    <i class="mr-2 fas fa-star"></i> Submit Review
                                                </button>
                                            </form>
                                        @else
                                            {{-- Login Required Message --}}
                                            <div class="py-8 text-center">
                                                <div class="mb-4">
                                                    <i class="text-4xl text-gray-400 fas fa-lock"></i>
                                                </div>
                                                <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
                                                    Login Required
                                                </h3>
                                                <p class="mb-4 text-gray-600 dark:text-gray-400">
                                                    You need to be logged in to submit a review.
                                                </p>
                                                <div class="space-y-2">
                                                    <a href="{{ route('auth.signin') }}"
                                                        class="inline-block px-4 py-2 text-sm font-medium text-white bg-yellow-400 rounded-md hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-yellow-400 dark:focus:ring-blue-800">
                                                        <i class="mr-2 fas fa-sign-in-alt"></i> Login
                                                    </a>
                                                    <p class="text-sm text-gray-500">
                                                        Don't have an account?
                                                        <a href="{{ route('auth.register') }}"
                                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                                            Register here
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        @endauth
                                    </div>
                                </div>
                            </div>

                            <!-- Individual Reviews -->
                            @if ($allReviews && $allReviews->count() > 0)
                                <div class="mt-8 space-y-6">
                                    @foreach ($allReviews as $review)
                                        <div class="pb-6 border-b border-gray-200">
                                            <div class="flex gap-4 items-start">
                                                <div
                                                    class="flex justify-center items-center w-12 h-12 font-semibold text-white bg-blue-500 rounded-full">
                                                    {{ strtoupper(substr(($review->user && $review->user->name) ? $review->user->name : 'U', 0, 2)) }}
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <div>
                                                            <div class="font-semibold text-gray-900">{{ ($review->user && $review->user->name) ? $review->user->name : 'Anonymous' }}</div>
                                                            <div class="text-sm text-gray-500">Verified Review</div>
                                                        </div>
                                                        <div class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</div>
                                                    </div>
                                                    <div class="flex mb-2 text-yellow-400">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <svg class="w-4 h-4 fill-current {{ $i <= $review->mark ? '' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                                                <path
                                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                    <p class="mb-3 text-gray-700">
                                                        {{ $review->message }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- YouTube Video Tab -->
                <div id="youtube-tab" class="hidden tab-content">
                    <div class="aspect-video w-full rounded-lg overflow-hidden shadow-lg bg-black">
                        @php
                            $vendor = $product->user->vendor ?? null;
                            $businessProfile = $vendor->businessProfile ?? null;
                            $youtubeLink = $businessProfile->youtube_link ?? 'https://youtu.be/Xb8som_PBGc?list=RDXb8som_PBGc';

                            // Extract video ID from various YouTube URL formats
                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $youtubeLink, $matches)) {
                                $videoId = $matches[1];
                            } else {
                                $videoId = 'Xb8som_PBGc'; // fallback
                            }
                        @endphp
                        <iframe
                            class="w-full h-full"
                            src="https://www.youtube.com/embed/{{ $videoId }}"
                            title="Product Video"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
                </div>

            </div>



            <!-- Newsletter Section -->
            <div class="mt-8 rounded-md overflow-hidden bg-[#2B6A4A]">
                <div class="px-6 py-12 text-center md:px-12 md:py-16">
                    <h2 class="mb-4 text-lg font-bold text-white md:text-4xl">Stay Connected, Stay Informed</h2>
                    <p class="mx-auto mb-8 max-w-3xl text-base text-green-50 md:text-lg">
                        Subscribe to receive exclusive updates, tips, and promotions straight to your inbox.<br>
                        Join our community for expert advice and resources to support your care journey.
                    </p>

                    <form class="mx-auto max-w-2xl">
                        <div class="flex overflow-hidden relative items-center bg-white rounded-full shadow-lg">
                            <input type="email" placeholder="Enter your email address here"
                                class="flex-1 px-6 py-4 text-base placeholder-gray-400 text-gray-700 focus:outline-none"
                                required>
                            <button type="submit"
                                class="flex justify-center items-center p-4 m-1 text-white bg-green-800 rounded-full transition-colors duration-300 hover:bg-green-900"
                                aria-label="Subscribe">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Related Products -->
            <div class="mt-8">
                <!-- Title and Brands Carousel -->
                <div class="mb-6">
                    <div class="flex flex-col gap-4 justify-between items-start md:flex-row md:items-center">
                        <!-- Left: Title -->
                        <h2 class="text-xl font-bold text-gray-900 whitespace-nowrap md:text-2xl">You may also like</h2>

                        <!-- Right: Brands Carousel with Navigation -->
                        <div class="relative w-full md:w-auto md:flex-1 md:max-w-2xl">
                            <div class="flex gap-3 items-center">
                                <!-- Left Arrow -->
                                <button id="brandsPrev"
                                    class="flex-shrink-0 p-2 bg-white rounded-full shadow-md transition-all hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>

                                <!-- Brands Container -->
                                <div class="overflow-hidden flex-1">
                                    <div id="brandsCarousel"
                                        class="flex gap-3 items-center transition-transform duration-500 ease-in-out">
                                        <!-- Brand 1 - Sony -->
                                        <button
                                            class="flex flex-shrink-0 gap-2 items-center px-4 py-2 whitespace-nowrap bg-white rounded-md border-2 border-gray-200 transition-all brand-item hover:border-blue-500">
                                            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M17.5 12c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm-4.5-1.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zm-5 0c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z" />
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-700">Sony</span>
                                        </button>

                                        <!-- Brand 2 - Samsung -->
                                        <button
                                            class="flex flex-shrink-0 gap-2 items-center px-4 py-2 whitespace-nowrap bg-white rounded-md border-2 border-gray-200 transition-all brand-item hover:border-blue-500">
                                            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-700">Samsung</span>
                                        </button>

                                        <!-- Brand 3 - Bose -->
                                        <button
                                            class="flex flex-shrink-0 gap-2 items-center px-4 py-2 whitespace-nowrap bg-white rounded-md border-2 border-gray-200 transition-all brand-item hover:border-blue-500">
                                            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-700">Bose</span>
                                        </button>

                                        <!-- Brand 4 - JBL -->
                                        <button
                                            class="flex flex-shrink-0 gap-2 items-center px-4 py-2 whitespace-nowrap bg-white rounded-md border-2 border-gray-200 transition-all brand-item hover:border-blue-500">
                                            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 3v18m-9-9h18" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-700">JBL</span>
                                        </button>

                                        <!-- Brand 5 - Apple -->
                                        <button
                                            class="flex flex-shrink-0 gap-2 items-center px-4 py-2 whitespace-nowrap bg-white rounded-md border-2 border-gray-200 transition-all brand-item hover:border-blue-500">
                                            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z" />
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-700">Apple</span>
                                        </button>

                                        <!-- Brand 6 - Beats -->
                                        <button
                                            class="flex flex-shrink-0 gap-2 items-center px-4 py-2 whitespace-nowrap bg-white rounded-md border-2 border-gray-200 transition-all brand-item hover:border-blue-500">
                                            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z" />
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-700">Beats</span>
                                        </button>

                                        <!-- Brand 7 - Sennheiser -->
                                        <button
                                            class="flex flex-shrink-0 gap-2 items-center px-4 py-2 whitespace-nowrap bg-white rounded-md border-2 border-gray-200 transition-all brand-item hover:border-blue-500">
                                            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z" />
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-700">Sennheiser</span>
                                        </button>

                                        <!-- Brand 8 - LG -->
                                        <button
                                            class="flex flex-shrink-0 gap-2 items-center px-4 py-2 whitespace-nowrap bg-white rounded-md border-2 border-gray-200 transition-all brand-item hover:border-blue-500">
                                            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                                <circle cx="12" cy="12" r="4" />
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-700">LG</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Right Arrow -->
                                <button id="brandsNext"
                                    class="flex-shrink-0 p-2 bg-white rounded-full shadow-md transition-all hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 md:gap-4">
                    @forelse($relatedProducts ?? [] as $relatedProduct)
                        @php
                            $relatedImage =
                                $relatedProduct->images->where('is_primary', true)->first() ??
                                $relatedProduct->images->first();
                        @endphp
                        <div
                            class="overflow-hidden bg-white rounded-md border border-gray-200 transition-all duration-300 hover:border-gray-300 hover:shadow-lg group">
                            <a href="{{ route('products.show', $relatedProduct->slug) }}" class="block">
                                <div class="overflow-hidden relative bg-gray-50" style="height: 180px;">
                                    @if ($relatedImage)
                                        <img src="{{ $relatedImage->image_url }}" alt="{{ $relatedProduct->name }}"
                                            class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105">
                                    @else
                                        <div
                                            class="flex justify-center items-center w-full h-full bg-gradient-to-br from-gray-100 to-gray-200">
                                            <span class="text-4xl">📦</span>
                                        </div>
                                    @endif
                                    <button
                                        class="flex absolute top-2 right-2 z-10 justify-center items-center w-8 h-8 bg-white rounded-full shadow transition-all hover:bg-gray-50">
                                        <i class="text-sm text-gray-500 far fa-heart"></i>
                                    </button>
                                </div>
                                <div class="p-3">
                                    <h3
                                        class="text-sm font-medium text-gray-800 mb-2 line-clamp-2 group-hover:text-[#ff0808] transition-colors">
                                        {{ $relatedProduct->name }}</h3>
                                    <div class="flex gap-2 items-baseline mb-1">
                                        @php
                                            $relatedFirstTier = $relatedProduct->prices->first();
                                        @endphp
                                        @if($relatedFirstTier)
                                            @php
                                                $relatedCurrency = $relatedFirstTier->currency ?? 'USD';
                                                $relatedSymbol = $currencySymbols[$relatedCurrency] ?? $relatedCurrency;
                                                $relatedPrice = $relatedFirstTier->price - ($relatedFirstTier->discount ?? 0);
                                            @endphp
                                            <span class="text-lg font-bold text-gray-900">
                                                {{ $relatedSymbol }}{{ number_format($relatedPrice, 2) }}
                                            </span>
                                            @if(($relatedFirstTier->discount ?? 0) > 0)
                                                <span class="text-sm text-gray-400 line-through">
                                                    {{ $relatedSymbol }}{{ number_format($relatedFirstTier->price, 2) }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-lg font-bold text-gray-400">Price not set</span>
                                        @endif
                                    </div>
                                    <div class="mb-2 text-xs text-gray-500">Min. {{ $relatedProduct->min_order_quantity }}
                                        pieces</div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-span-5 py-12 text-center">
                            <p class="text-gray-500">No related products available.</p>
                        </div>
                    @endforelse

                </div>
            </div>

            <!-- Image Gallery Modal -->
            <div id="imageGalleryModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-90">
                <button onclick="closeGallery()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <button onclick="prevGalleryImage()" class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-50">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <button onclick="nextGalleryImage()" class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-50">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div class="flex items-center justify-center h-full p-4">
                    <img id="galleryImage" src="" alt="Gallery" class="max-w-full max-h-full object-contain">
                </div>

                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white text-sm">
                    <span id="galleryCounter"></span>
                </div>
            </div>

        </div>
    </div>
    <script>
                    let galleryIndex = 0;

            function openGallery(index) {
                galleryIndex = index;
                const modal = document.getElementById('imageGalleryModal');
                const img = document.getElementById('galleryImage');
                const counter = document.getElementById('galleryCounter');

                if (modal && img) {
                    img.src = productImages[galleryIndex];
                    counter.textContent = `${galleryIndex + 1} / ${productImages.length}`;
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeGallery() {
                const modal = document.getElementById('imageGalleryModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }

            function nextGalleryImage() {
                galleryIndex = (galleryIndex + 1) % productImages.length;
                openGallery(galleryIndex);
            }

            function prevGalleryImage() {
                galleryIndex = (galleryIndex - 1 + productImages.length) % productImages.length;
                openGallery(galleryIndex);
            }

            // Close gallery on Escape key
            // Close gallery on Escape key, navigate with arrow keys
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeGallery();
                } else if (e.key === 'ArrowLeft') {
                    const modal = document.getElementById('imageGalleryModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        prevGalleryImage();
                    }
                } else if (e.key === 'ArrowRight') {
                    const modal = document.getElementById('imageGalleryModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        nextGalleryImage();
                    }
                }
            });
        document.addEventListener('DOMContentLoaded', function() {
            // Product Images Data from Database
            window.productImages = @json($product->images->pluck('image_url')->toArray());

            // If no images, use placeholder
            if (productImages.length === 0) {
                productImages.push('{{ asset('images/placeholder-product.png') }}');
            }

            let currentImageIndex = 0;

            // Color Options Data
            const colorOptions = [{
                    name: 'Matte Black',
                    image: 'https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=100'
                },
                {
                    name: 'Rose Gold',
                    image: 'https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&cs=tinysrgb&w=100'
                },
                {
                    name: 'Silver',
                    image: 'https://images.pexels.com/photos/3587488/pexels-photo-3587488.jpeg?auto=compress&cs=tinysrgb&w=100'
                },
                {
                    name: 'Navy Blue',
                    image: 'https://images.pexels.com/photos/3587489/pexels-photo-3587489.jpeg?auto=compress&cs=tinysrgb&w=100'
                }
            ];

            // Connectivity Options Data
            const connectivityOptions = [{
                    name: 'Bluetooth 5.0',
                    selected: true
                },
                {
                    name: 'Wired USB-C',
                    selected: false
                },
                {
                    name: 'Hybrid (BT + Wired)',
                    selected: false
                }
            ];

            // Battery Options Data
            const batteryOptions = [{
                    name: '20 hours',
                    selected: false
                },
                {
                    name: '30 hours',
                    selected: true
                },
                {
                    name: '40 hours',
                    selected: false
                }
            ];



            // Initialize Image Gallery
            function initImageGallery() {
                const thumbnailContainer = document.getElementById('thumbnailContainer');
                const mainImage = document.getElementById('mainImage');

                // Make main image clickable
                if (mainImage) {
                    mainImage.style.cursor = 'pointer';
                    mainImage.addEventListener('click', () => openGallery(currentImageIndex));
                }

                if (!thumbnailContainer || !mainImage) return;

                // Generate thumbnails
                productImages.forEach((image, index) => {
                    const button = document.createElement('button');
                    button.className =
                        `border-2 ${index === 0 ? 'border-blue-500' : 'border-gray-200 hover:border-gray-300'} rounded-md overflow-hidden`;
                    button.innerHTML =
                        `<img src="${image}" alt="Thumbnail ${index + 1}" class="object-cover w-full h-20">`;
                    button.addEventListener('click', () => {
                        changeImage(index);
                        // openGallery(index);
                    });
                    thumbnailContainer.appendChild(button);
                });

                // Add more button
                const moreButton = document.createElement('button');
                moreButton.className =
                    'bg-gray-100 rounded-md h-20 flex items-center justify-center text-gray-600 hover:bg-gray-200';
                moreButton.innerHTML =
                    `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
                thumbnailContainer.appendChild(moreButton);

                // Previous/Next buttons
                const prevBtn = document.getElementById('prevImage');
                const nextBtn = document.getElementById('nextImage');

                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        currentImageIndex = (currentImageIndex - 1 + productImages.length) % productImages
                            .length;
                        changeImage(currentImageIndex);
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        currentImageIndex = (currentImageIndex + 1) % productImages.length;
                        changeImage(currentImageIndex);
                    });
                }
            }



            function changeImage(index) {
                currentImageIndex = index;
                const mainImage = document.getElementById('mainImage');
                if (!mainImage) return;

                mainImage.src = productImages[index];

                // Update thumbnail borders
                const thumbnails = document.querySelectorAll('#thumbnailContainer button');
                thumbnails.forEach((thumb, i) => {
                    if (i === index) {
                        thumb.className = 'border-2 border-blue-500 rounded-md overflow-hidden';
                    } else if (i < productImages.length) {
                        thumb.className =
                            'border-2 border-gray-200 hover:border-gray-300 rounded-md overflow-hidden';
                    }
                });
            }

            // Initialize Color Options
            function initColorOptions() {
                const colorContainer = document.getElementById('colorOptions');
                if (!colorContainer) return;

                colorOptions.forEach((color, index) => {
                    const button = document.createElement('button');
                    button.className =
                        `border-2 ${index === 0 ? 'border-blue-500' : 'border-gray-200 hover:border-gray-300'} rounded-md p-2 w-20 h-20 overflow-hidden`;
                    button.innerHTML =
                        `<img src="${color.image}" alt="${color.name}" class="object-cover w-full h-full rounded">`;
                    button.addEventListener('click', () => selectColor(index));
                    colorContainer.appendChild(button);
                });

                // Add +3 button
                const moreButton = document.createElement('button');
                moreButton.className =
                    'border-2 border-gray-200 hover:border-gray-300 rounded-md p-2 w-20 h-20 flex items-center justify-center bg-gray-50';
                moreButton.innerHTML = '<span class="text-sm font-medium text-gray-600">+3</span>';
                colorContainer.appendChild(moreButton);
            }

            function selectColor(index) {
                const selectedColorSpan = document.getElementById('selectedColor');
                if (selectedColorSpan) {
                    selectedColorSpan.textContent = colorOptions[index].name;
                }

                // Update button borders
                const buttons = document.querySelectorAll('#colorOptions button');
                buttons.forEach((button, i) => {
                    if (i === index) {
                        button.className =
                            'border-2 border-blue-500 rounded-md p-2 w-20 h-20 overflow-hidden';
                    } else if (i < colorOptions.length) {
                        button.className =
                            'border-2 border-gray-200 hover:border-gray-300 rounded-md p-2 w-20 h-20 overflow-hidden';
                    }
                });
            }

            // Initialize Connectivity Options
            function initConnectivityOptions() {
                const connectivityContainer = document.getElementById('connectivityOptions');
                if (!connectivityContainer) return;

                connectivityOptions.forEach((option, index) => {
                    const button = document.createElement('button');
                    button.className =
                        `px-5 py-2 border-2 ${option.selected ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'} rounded-full text-sm font-medium ${option.selected ? 'text-gray-900' : 'text-gray-700'}`;
                    button.textContent = option.name;
                    button.addEventListener('click', () => selectConnectivity(index));
                    connectivityContainer.appendChild(button);
                });
            }

            function selectConnectivity(index) {
                connectivityOptions.forEach(opt => opt.selected = false);
                connectivityOptions[index].selected = true;

                const buttons = document.querySelectorAll('#connectivityOptions button');
                buttons.forEach((button, i) => {
                    if (i === index) {
                        button.className =
                            'px-5 py-2 border-2 border-blue-500 bg-blue-50 rounded-full text-sm font-medium text-gray-900';
                    } else {
                        button.className =
                            'px-5 py-2 border-2 border-gray-200 hover:border-gray-300 rounded-full text-sm font-medium text-gray-700';
                    }
                });
            }

            // Initialize Battery Options
            function initBatteryOptions() {
                const batteryContainer = document.getElementById('batteryOptions');
                if (!batteryContainer) return;

                batteryOptions.forEach((option, index) => {
                    const button = document.createElement('button');
                    button.className =
                        `px-5 py-2 border-2 ${option.selected ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'} rounded-full text-sm font-medium ${option.selected ? 'text-gray-900' : 'text-gray-700'}`;
                    button.textContent = option.name;
                    button.addEventListener('click', () => selectBattery(index));
                    batteryContainer.appendChild(button);
                });

                // Add +12 button
                const moreButton = document.createElement('button');
                moreButton.className =
                    'px-5 py-2 border-2 border-gray-200 hover:border-gray-300 rounded-full text-sm font-medium text-gray-700';
                moreButton.textContent = '+12';
                batteryContainer.appendChild(moreButton);
            }

            function selectBattery(index) {
                batteryOptions.forEach(opt => opt.selected = false);
                batteryOptions[index].selected = true;

                const buttons = document.querySelectorAll('#batteryOptions button');
                buttons.forEach((button, i) => {
                    if (i === index) {
                        button.className =
                            'px-5 py-2 border-2 border-blue-500 bg-blue-50 rounded-full text-sm font-medium text-gray-900';
                    } else if (i < batteryOptions.length) {
                        button.className =
                            'px-5 py-2 border-2 border-gray-200 hover:border-gray-300 rounded-full text-sm font-medium text-gray-700';
                    }
                });
            }

            // Tab Navigation
            function initTabs() {
                const tabButtons = document.querySelectorAll('.tab-button');
                const tabContents = document.querySelectorAll('.tab-content');

                tabButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const tabName = button.getAttribute('data-tab');

                        // Remove active classes from all tabs
                        tabButtons.forEach(btn => {
                            btn.className =
                                'tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300';
                        });

                        // Hide all tab contents
                        tabContents.forEach(content => {
                            content.classList.add('hidden');
                        });

                        // Add active class to clicked tab
                        button.className =
                            'tab-button border-b-2 border-blue-600 py-4 px-1 text-sm font-medium text-blue-600';

                        // Show corresponding content
                        const activeContent = document.getElementById(`${tabName}-tab`);
                        if (activeContent) {
                            activeContent.classList.remove('hidden');
                        }
                    });
                });
            }

            // Brands Carousel - Improved Version
            function initBrandsCarousel() {
                const carousel = document.getElementById('brandsCarousel');
                const prevBtn = document.getElementById('brandsPrev');
                const nextBtn = document.getElementById('brandsNext');

                if (!carousel || !prevBtn || !nextBtn) return;

                let currentScroll = 0;
                const scrollAmount = 250; // Amount to scroll on each click

                function updateButtons() {
                    const maxScroll = carousel.scrollWidth - carousel.parentElement.clientWidth;

                    // Disable/enable buttons based on scroll position
                    if (currentScroll <= 0) {
                        prevBtn.disabled = true;
                        prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        prevBtn.disabled = false;
                        prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }

                    if (currentScroll >= maxScroll) {
                        nextBtn.disabled = true;
                        nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        nextBtn.disabled = false;
                        nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }

                // Next button click handler
                nextBtn.addEventListener('click', () => {
                    const maxScroll = carousel.scrollWidth - carousel.parentElement.clientWidth;
                    currentScroll = Math.min(currentScroll + scrollAmount, maxScroll);
                    carousel.style.transform = `translateX(-${currentScroll}px)`;
                    updateButtons();
                });

                // Previous button click handler
                prevBtn.addEventListener('click', () => {
                    currentScroll = Math.max(currentScroll - scrollAmount, 0);
                    carousel.style.transform = `translateX(-${currentScroll}px)`;
                    updateButtons();
                });

                // Initial button state
                updateButtons();

                // Update on window resize
                window.addEventListener('resize', () => {
                    currentScroll = 0;
                    carousel.style.transform = `translateX(0px)`;
                    updateButtons();
                });

                // Brand filter functionality
                const brandItems = document.querySelectorAll('.brand-item');
                brandItems.forEach(brand => {
                    brand.addEventListener('click', function(e) {
                        e.preventDefault();

                        // Remove active state from all brands
                        brandItems.forEach(b => {
                            b.classList.remove('border-blue-500', 'bg-blue-50');
                            b.classList.add('border-gray-200');
                        });

                        // Add active state to clicked brand
                        this.classList.remove('border-gray-200');
                        this.classList.add('border-blue-500', 'bg-blue-50');

                        // Here you can add filtering logic
                        console.log('Brand selected:', this.textContent.trim());
                    });
                });
            }

            // Wishlist functionality for related products
            function initWishlist() {
                document.querySelectorAll('.fa-heart').forEach(heart => {
                    const button = heart.closest('button');
                    if (button) {
                        button.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();

                            const icon = this.querySelector('.fa-heart');
                            if (!icon) return;

                            if (icon.classList.contains('far')) {
                                // Add to wishlist
                                icon.classList.remove('far', 'text-gray-500');
                                icon.classList.add('fas', 'text-red-500');
                                this.classList.add('bg-red-50');
                                console.log('Added to wishlist');
                            } else {
                                // Remove from wishlist
                                icon.classList.remove('fas', 'text-red-500');
                                icon.classList.add('far', 'text-gray-500');
                                this.classList.remove('bg-red-50');
                                console.log('Removed from wishlist');
                            }
                        });
                    }
                });
            }

            // Newsletter Form
            function initNewsletterForm() {
                const newsletterForm = document.querySelector('form');
                if (newsletterForm && newsletterForm.querySelector('input[type="email"]')) {
                    newsletterForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const emailInput = this.querySelector('input[type="email"]');
                        const email = emailInput.value.trim();

                        if (email) {
                            // Here you would send the email to your backend
                            console.log('Newsletter subscription:', email);
                            alert('Thank you for subscribing!');
                            emailInput.value = '';
                        } else {
                            alert('Please enter a valid email address.');
                        }
                    });
                }
            }

            // Drawer functionality
            function initDrawer() {
                const drawerElement = document.getElementById('drawer-right-example');
                const drawerToggle = document.querySelector('[data-drawer-target="drawer-right-example"]');
                const drawerHide = document.querySelector('[data-drawer-hide="drawer-right-example"]');
                const drawerBackdrop = document.createElement('div');
                drawerBackdrop.id = 'drawer-backdrop';
                drawerBackdrop.className = 'fixed inset-0 z-30 bg-gray-900 bg-opacity-50 hidden';
                document.body.appendChild(drawerBackdrop);

                function showDrawer() {
                    if (drawerElement) {
                        drawerElement.classList.remove('translate-x-full');
                        drawerBackdrop.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }
                }

                function hideDrawer() {
                    if (drawerElement) {
                        drawerElement.classList.add('translate-x-full');
                        drawerBackdrop.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                }

                if (drawerToggle) {
                    drawerToggle.addEventListener('click', showDrawer);
                }

                if (drawerHide) {
                    drawerHide.addEventListener('click', hideDrawer);
                }

                drawerBackdrop.addEventListener('click', hideDrawer);

                // Close drawer on Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !drawerElement.classList.contains('translate-x-full')) {
                        hideDrawer();
                    }
                });
            }

            // Review form submission
            function initReviewForm() {
                const reviewForm = document.getElementById('reviewForm');
                if (reviewForm) {
                    reviewForm.addEventListener('submit', function(e) {
                        const submitBtn = document.getElementById('submitReview');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="mr-2 fas fa-spinner fa-spin"></i> Submitting...';
                        }
                    });
                }
            }

            // Add this NEW function
function initVariationSelection() {
    console.log('🔧 Initializing variation selection...');
    const variationButtons = document.querySelectorAll('.variation-option');
    console.log('📦 Found variation buttons:', variationButtons.length);

    variationButtons.forEach(button => {
        button.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            const value = this.getAttribute('data-value');

            console.log('✅ Variation clicked:', { type, value });

            // Remove active class from all buttons of same type
            document.querySelectorAll(`.variation-option[data-type="${type}"]`).forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50');
                btn.classList.add('border-gray-200');
            });

            // Add active class to clicked button
            this.classList.remove('border-gray-200');
            this.classList.add('border-blue-500', 'bg-blue-50');

            console.log('🎨 Updated button classes');

            // Update the displayed selected value
            const selectedSpan = document.querySelector(`.variation-selected-${type}`);
            if (selectedSpan) {
                selectedSpan.textContent = value;
                console.log('📝 Updated display text to:', value);
            } else {
                console.warn('⚠️ Could not find span for type:', type);
            }
        });
    });
}

            // Shop Now and Add to Cart functionality
// Shop Now and Add to Cart functionality
const shopNowBtn = document.getElementById('shopNowBtn');
console.log('🛍️ Shop Now button found:', shopNowBtn !== null);

if (shopNowBtn) {
    shopNowBtn.addEventListener('click', function() {
        console.log('🛍️ Shop Now clicked');
        const quantitySelector = document.getElementById('quantitySelector');
        quantitySelector.classList.toggle('hidden');

        if (!quantitySelector.classList.contains('hidden')) {
            console.log('✅ Quantity selector shown');
            this.innerHTML = '<i class="mr-2 fas fa-times"></i> Cancel';
            this.classList.remove('bg-[#3e07f4]', 'hover:bg-[#270de8]');
            this.classList.add('bg-gray-500', 'hover:bg-gray-600');
        } else {
            console.log('❌ Quantity selector hidden');
            this.innerHTML = '<i class="mr-2 fas fa-shopping-bag"></i> Shop Now';
            this.classList.add('bg-[#3e07f4]', 'hover:bg-[#270de8]');
            this.classList.remove('bg-gray-500', 'hover:bg-gray-600');
        }
    });
}

// Add to Cart Form Submission
// Add to Cart Form Submission
const cartForm = document.getElementById('addToCartForm');
console.log('🛒 Cart form found:', cartForm !== null);

if (cartForm) {
    cartForm.addEventListener('submit', function(e) {
        console.log('🚀 Form submission started');

        const quantity = document.getElementById('cartQuantity').value;
        console.log('📊 Quantity:', quantity);

        document.getElementById('hiddenQuantity').value = quantity;

        // Collect selected variations
        const variations = {};
        const selectedButtons = document.querySelectorAll('.variation-option.border-blue-500');
        console.log('🔍 Selected variation buttons found:', selectedButtons.length);

        selectedButtons.forEach(option => {
            const type = option.getAttribute('data-type');
            const value = option.getAttribute('data-value');
            variations[type] = value;
            console.log('➕ Added variation:', { type, value });
        });

        const variationsJson = JSON.stringify(variations);
        console.log('📦 Final variations JSON:', variationsJson);

        document.getElementById('hiddenVariations').value = variationsJson;

        console.log('✅ Form data prepared, submitting...');
    });
}

// Update cart count on page load
function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const cartBadge = document.getElementById('cartCount');
            if (cartBadge) {
                cartBadge.textContent = data.count;
            }
        });
}





            // Initialize all features
            initImageGallery();
            initColorOptions();
            initConnectivityOptions();
            initBatteryOptions();
            initTabs();
            initBrandsCarousel();
            initWishlist();
            initNewsletterForm();
            initDrawer();
            initReviewForm();
            initVariationSelection();
            updateCartCount();

            console.log('✅ Product page initialized successfully!');
        });
    </script>
@endsection
