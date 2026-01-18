@extends('layouts.app')

@section('title', ($product ? $product->name : $businessProfile->business_name) . ' - Request Quote')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">

        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to {{ $product ? 'Product' : 'Business Profile' }}
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column - Product/Company Details -->
            <div class="lg:col-span-2 space-y-6">

                @if($product)
                <!-- Product Details Card -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $product->name }}</h1>

                        <!-- Product Image Gallery -->
                        <div class="mb-6">
                            @php
                                $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                $thumbnails = $product->images->take(4);
                            @endphp

                            @if($primaryImage)
                            <div class="mb-4">
                                <img src="{{ $primaryImage->image_url }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-96 object-cover rounded-lg"
                                     id="main-image">
                            </div>

                            <!-- Thumbnails -->
                            @if($thumbnails->count() > 1)
                            <div class="flex gap-3">
                                @foreach($thumbnails as $thumb)
                                <img src="{{ $thumb->image_url }}"
                                     alt="{{ $product->name }}"
                                     class="w-24 h-24 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-green-600 transition-colors"
                                     onclick="document.getElementById('main-image').src='{{ $thumb->image_url }}'">
                                @endforeach
                            </div>
                            @endif
                            @endif
                        </div>

                        <!-- Product Details Section -->
                        <div class="border-t pt-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Product Details</h2>

                            <div class="space-y-3">
                                @if($product->productCategory)
                                <div class="flex">
                                    <span class="font-semibold text-gray-700 w-40">Category:</span>
                                    <span class="text-gray-600">{{ $product->productCategory->name }}</span>
                                </div>
                                @endif

                                @if($product->specifications && is_array($product->specifications))
                                    @foreach($product->specifications as $key => $value)
                                    <div class="flex">
                                        <span class="font-semibold text-gray-700 w-40">{{ ucfirst($key) }}:</span>
                                        <span class="text-gray-600">{{ $value }}</span>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Packaging & Shipping -->
                        @if($product->packaging || $product->shipping_info)
                        <div class="border-t pt-6 mt-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Packaging</h2>
                            <p class="text-gray-700">{{ $product->packaging ?? '60 kg jute bags' }}</p>

                            @if($product->shelf_life)
                            <div class="mt-3">
                                <span class="font-semibold text-gray-700">Shelf Life:</span>
                                <span class="text-gray-600">{{ $product->shelf_life }}</span>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Export Info -->
                        @if($product->export_markets || $product->shipping_terms)
                        <div class="border-t pt-6 mt-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Export Info</h2>

                            @if($product->export_markets)
                            <div class="mb-3">
                                <span class="font-semibold text-gray-700">Export Markets:</span>
                                <span class="text-gray-600">{{ $product->export_markets }}</span>
                            </div>
                            @endif

                            @if($product->shipping_terms)
                            <div>
                                <span class="font-semibold text-gray-700">Shipping:</span>
                                <span class="text-gray-600">{{ $product->shipping_terms }}</span>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <!-- Business Profile Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $businessProfile->business_name }}</h1>

                    <div class="flex items-center gap-2 text-gray-600 mb-4">
                        <span class="text-2xl">{{ $businessProfile->country->flag ?? '🌍' }}</span>
                        <span>{{ $businessProfile->city }}, {{ $businessProfile->country->name ?? '' }}</span>
                    </div>

                    @if($businessProfile->description)
                    <p class="text-gray-700 leading-relaxed">{{ $businessProfile->description }}</p>
                    @endif
                </div>
                @endif

            </div>

            <!-- Right Column - Request Quote Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Request a Quote</h2>

                    <form action="{{ route('request-quote.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="business_profile_id" value="{{ $businessProfile->id }}">
                        @if($product)
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        @endif

                        <!-- Your Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Your Name</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                                   placeholder="Enter your name">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                                   placeholder="your@email.com">
                        </div>

                        <!-- Phone / WhatsApp -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone / WhatsApp</label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                                   placeholder="+250 XXX XXX XXX">
                        </div>

                        <!-- Quantity (if product) -->
                        @if($product)
                        <div>
                            <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Quantity Needed</label>
                            <input type="text"
                                   id="quantity"
                                   name="quantity"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                                   placeholder="e.g., 1000 kg">
                            <p class="text-xs text-gray-500 mt-1">MOQ: {{ number_format($product->min_order_quantity) }} {{ $product->unit ?? 'units' }}</p>
                        </div>
                        @endif

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                            <textarea id="message"
                                      name="message"
                                      rows="5"
                                      required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all resize-none"
                                      placeholder="Tell us about your requirements..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition-colors transform hover:scale-105 duration-200 shadow-lg">
                            Submit Inquiry
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            By submitting this form, you agree to our terms and conditions
                        </p>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
