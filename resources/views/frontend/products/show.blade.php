@extends('layouts.app')

@section('title', 'Premium Wireless Headphones - AudioTech Pro')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">

        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="#" class="text-gray-700 hover:text-blue-600">Consumer Electronics</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="#" class="text-gray-700 hover:text-blue-600">Audio & Headphones</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500">Wireless Headphones</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Product Title & Rating -->
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                Premium Wireless Headphones with Active Noise Cancellation - AudioTech Pro X1
            </h1>

            <div class="flex items-center gap-6 flex-wrap">
                <div class="flex items-center gap-2">
                    <div class="flex text-yellow-400">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                    </div>
                    <span class="font-semibold text-gray-900">4.8</span>
                    <a href="#reviews" class="text-blue-600 hover:underline">(156 reviews)</a>
                </div>

                <div class="text-gray-600">
                    <span class="font-semibold">15,847</span> sold
                </div>

                <div class="text-gray-600">
                    #2 <span class="text-blue-600 hover:underline cursor-pointer">hot selling in Wireless Headphones</span>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <img src="https://flagcdn.com/w20/us.png" alt="US" class="w-5 h-4">
                    <span>AudioTech Industries Ltd.</span>
                </div>
                <span>•</span>
                <span>2 yr</span>
                <span>•</span>
                <span class="flex items-center gap-1">
                    <img src="https://flagcdn.com/w20/cn.png" alt="CN" class="w-5 h-4">
                    CN
                </span>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column - Images -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex gap-4">
                        <!-- Thumbnail Column -->
                        <div class="flex flex-col gap-3 w-24" id="thumbnailContainer">
                            <!-- Thumbnails will be generated by JavaScript -->
                        </div>

                        <!-- Main Image -->
                        <div class="flex-1 relative bg-gray-50 rounded-lg overflow-hidden">
                            <button class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-md hover:bg-gray-50 z-10">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>

                            <button class="absolute top-16 right-4 bg-white rounded-full p-2 shadow-md hover:bg-gray-50 z-10">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </button>

                            <img id="mainImage" src="https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Premium Wireless Headphones" class="w-full h-[500px] object-cover">

                            <!-- Navigation Arrows -->
                            <button id="prevImage" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white rounded-full p-2 shadow-md">
                                <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button id="nextImage" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white rounded-full p-2 shadow-md">
                                <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Product Info & Purchase -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">

                    <!-- Price Badge -->
                    <div class="bg-orange-500 text-white inline-block px-3 py-1 rounded text-sm font-semibold mb-4">
                        Lower priced than similar
                    </div>

                    <!-- Pricing Tiers -->
                    <div class="mb-6">
                        <div class="grid grid-cols-2 gap-4 mb-2">
                            <div>
                                <div class="text-sm text-gray-600 mb-1">20 - 99 pieces</div>
                                <div class="text-2xl font-bold text-gray-900">RF 4,850</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">100 - 199 pieces</div>
                                <div class="text-2xl font-bold text-gray-900">RF 4,620</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-600 mb-1">200 - 499 pieces</div>
                                <div class="text-2xl font-bold text-gray-900">RF 4,380</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">>= 500 pieces</div>
                                <div class="text-2xl font-bold text-gray-900">RF 4,150</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Variations Section -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Variations</h3>
                            <button class="text-blue-600 hover:underline font-medium">Select now</button>
                        </div>

                        <!-- Color Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                color: <span id="selectedColor" class="text-gray-900">Matte Black</span>
                            </label>
                            <div class="flex flex-wrap gap-3" id="colorOptions">
                                <!-- Color options will be generated by JavaScript -->
                            </div>
                        </div>

                        <!-- Connectivity Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">connectivity type</label>
                            <div class="flex flex-wrap gap-3" id="connectivityOptions">
                                <!-- Connectivity options will be generated by JavaScript -->
                            </div>
                        </div>

                        <!-- Battery Life -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">battery life</label>
                            <div class="flex flex-wrap gap-3" id="batteryOptions">
                                <!-- Battery options will be generated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <hr class="my-6">

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <button class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                            Start order
                        </button>
                        <div class="grid grid-cols-2 gap-3">
                            <button class="bg-white hover:bg-gray-50 text-gray-900 font-semibold py-3 px-6 rounded-lg border-2 border-gray-300 transition-colors">
                                Add to cart
                            </button>
                            <button class="bg-white hover:bg-gray-50 text-gray-900 font-semibold py-3 px-6 rounded-lg border-2 border-gray-300 transition-colors">
                                Chat now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details Tabs -->
        <div class="mt-8 bg-white rounded-lg shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button class="tab-button border-b-2 border-blue-600 py-4 px-1 text-sm font-medium text-blue-600" data-tab="overview">
                        Overview
                    </button>
                    <button class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="specifications">
                        Specifications
                    </button>
                    <button class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="shipping">
                        Shipping & Payment
                    </button>
                    <button class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="reviews">
                        Reviews (156)
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Overview Tab -->
                <div id="overview-tab" class="tab-content">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Product Description</h3>
                    <div class="prose max-w-none text-gray-700 space-y-4">
                        <p>
                            Experience premium audio quality with the AudioTech Pro X1 Wireless Headphones. Featuring industry-leading active noise cancellation technology, these headphones deliver crystal-clear sound in any environment.
                        </p>
                        <p>
                            With up to 30 hours of battery life, Bluetooth 5.0 connectivity, and comfortable over-ear cushions, the Pro X1 is perfect for long listening sessions, travel, or professional use.
                        </p>

                        <h4 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Key Features:</h4>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>Active Noise Cancellation (ANC) technology blocks out ambient noise</li>
                            <li>Premium 40mm drivers for rich, detailed sound</li>
                            <li>30-hour battery life with quick charge support (10 min = 5 hours)</li>
                            <li>Bluetooth 5.0 for stable, low-latency wireless connection</li>
                            <li>Comfortable memory foam ear cushions for all-day wear</li>
                            <li>Built-in microphone with CVC 8.0 noise reduction for clear calls</li>
                            <li>Foldable design with premium carrying case included</li>
                            <li>Multi-device connectivity - connect to 2 devices simultaneously</li>
                        </ul>

                        <h4 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Package Includes:</h4>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>1x AudioTech Pro X1 Wireless Headphones</li>
                            <li>1x USB-C Charging Cable</li>
                            <li>1x 3.5mm Audio Cable (for wired use)</li>
                            <li>1x Premium Carrying Case</li>
                            <li>1x User Manual</li>
                            <li>1x Airplane Adapter</li>
                        </ul>

                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <img src="https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Product feature 1" class="rounded-lg">
                            <img src="https://images.pexels.com/photos/3587488/pexels-photo-3587488.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Product feature 2" class="rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div id="specifications-tab" class="tab-content hidden">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Technical Specifications</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Driver Size</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">40mm Neodymium Drivers</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Frequency Response</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">20Hz - 20kHz</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Impedance</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">32 Ohms</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Bluetooth Version</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">Bluetooth 5.0</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Battery Capacity</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">800mAh Lithium-ion</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Charging Time</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">2 hours (Full charge)</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Weight</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">280g</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Dimensions</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">19 x 17 x 8 cm</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Shipping Tab -->
                <div id="shipping-tab" class="tab-content hidden">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Shipping & Payment Information</h3>
                    <div class="space-y-6 text-gray-700">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Shipping Options:</h4>
                            <ul class="list-disc pl-6 space-y-1">
                                <li>Standard Shipping: 7-15 business days</li>
                                <li>Express Shipping: 3-5 business days</li>
                                <li>Overnight Shipping: 1-2 business days</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Payment Methods:</h4>
                            <ul class="list-disc pl-6 space-y-1">
                                <li>Credit/Debit Cards (Visa, Mastercard, Amex)</li>
                                <li>PayPal</li>
                                <li>Bank Transfer</li>
                                <li>Trade Assurance</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Return Policy:</h4>
                            <p>30-day money-back guarantee. Items must be returned in original condition with all accessories.</p>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div id="reviews-tab" class="tab-content hidden">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Customer Reviews</h3>

                    <!-- Rating Summary -->
                    <div class="flex flex-col md:flex-row gap-8 mb-8 pb-8 border-b border-gray-200">
                        <div class="text-center md:text-left">
                            <div class="text-5xl font-bold text-gray-900 mb-2">4.8</div>
                            <div class="flex justify-center md:justify-start text-yellow-400 mb-2">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            </div>
                            <div class="text-gray-600">Based on 156 reviews</div>
                        </div>

                        <div class="flex-1">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 w-12">5 star</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: 78%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 w-12 text-right">122</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 w-12">4 star</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: 15%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 w-12 text-right">23</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 w-12">3 star</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: 5%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 w-12 text-right">8</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 w-12">2 star</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: 1%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 w-12 text-right">2</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 w-12">1 star</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: 1%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 w-12 text-right">1</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Reviews -->
                    <div class="space-y-6">
                        <!-- Review 1 -->
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    JD
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <div class="font-semibold text-gray-900">John Davidson</div>
                                            <div class="text-sm text-gray-500">Verified Purchase</div>
                                        </div>
                                        <div class="text-sm text-gray-500">2 weeks ago</div>
                                    </div>
                                    <div class="flex text-yellow-400 mb-2">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Excellent sound quality and comfort!</h4>
                                    <p class="text-gray-700 mb-3">
                                        These headphones exceeded my expectations. The noise cancellation is superb, and I can wear them for hours without any discomfort. The battery life is exactly as advertised. Highly recommend for anyone looking for premium wireless headphones.
                                    </p>
                                    <div class="flex gap-2 mb-3">
                                        <img src="https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&cs=tinysrgb&w=100" alt="Review image" class="w-20 h-20 object-cover rounded-lg">
                                        <img src="https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=100" alt="Review image" class="w-20 h-20 object-cover rounded-lg">
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <button class="hover:text-blue-600">Helpful (24)</button>
                                        <button class="hover:text-blue-600">Reply</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review 2 -->
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    SM
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <div class="font-semibold text-gray-900">Sarah Martinez</div>
                                            <div class="text-sm text-gray-500">Verified Purchase</div>
                                        </div>
                                        <div class="text-sm text-gray-500">1 month ago</div>
                                    </div>
                                    <div class="flex text-yellow-400 mb-2">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Perfect for work from home</h4>
                                    <p class="text-gray-700 mb-3">
                                        I use these daily for video calls and music while working. The microphone quality is crystal clear, and my colleagues say they can hear me perfectly. The ANC blocks out all the background noise. Worth every penny!
                                    </p>
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <button class="hover:text-blue-600">Helpful (18)</button>
                                        <button class="hover:text-blue-600">Reply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">You may also like</h2>
                <a href="{{ route('products.search', ['type' => 'category', 'slug' => 'audio-headphones']) }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                    View All
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                <!-- Product Card 1 -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                        <img src="https://images.pexels.com/photos/3587489/pexels-photo-3587489.jpeg?auto=compress&cs=tinysrgb&w=300" alt="Related product" class="w-full h-full object-cover hover:scale-105 transition-transform">
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">Premium Wireless Earbuds Pro</h3>
                        <div class="text-lg font-bold text-gray-900">RF 2,850</div>
                        <div class="text-xs text-gray-500 mt-1">100 pieces (Min. order)</div>
                    </div>
                </div>

                <!-- Product Card 2 -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                        <img src="https://images.pexels.com/photos/8000619/pexels-photo-8000619.jpeg?auto=compress&cs=tinysrgb&w=300" alt="Related product" class="w-full h-full object-cover hover:scale-105 transition-transform">
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">Studio Monitor Headphones</h3>
                        <div class="text-lg font-bold text-gray-900">RF 5,200</div>
                        <div class="text-xs text-gray-500 mt-1">50 pieces (Min. order)</div>
                    </div>
                </div>

                <!-- Product Card 3 -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                        <img src="https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&cs=tinysrgb&w=300" alt="Related product" class="w-full h-full object-cover hover:scale-105 transition-transform">
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">Gaming Headset RGB Edition</h3>
                        <div class="text-lg font-bold text-gray-900">RF 3,650</div>
                        <div class="text-xs text-gray-500 mt-1">200 pieces (Min. order)</div>
                    </div>
                </div>

                <!-- Product Card 4 -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                        <img src="https://images.pexels.com/photos/3587488/pexels-photo-3587488.jpeg?auto=compress&cs=tinysrgb&w=300" alt="Related product" class="w-full h-full object-cover hover:scale-105 transition-transform">
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">Portable Speaker Bluetooth 5.0</h3>
                        <div class="text-lg font-bold text-gray-900">RF 1,950</div>
                        <div class="text-xs text-gray-500 mt-1">500 pieces (Min. order)</div>
                    </div>
                </div>

                <!-- Product Card 5 -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                        <img src="https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=300" alt="Related product" class="w-full h-full object-cover hover:scale-105 transition-transform">
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">On-Ear Headphones Compact</h3>
                        <div class="text-lg font-bold text-gray-900">RF 2,450</div>
                        <div class="text-xs text-gray-500 mt-1">150 pieces (Min. order)</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Product Images Data
    const productImages = [
        'https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=800',
        'https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&cs=tinysrgb&w=800',
        'https://images.pexels.com/photos/3587488/pexels-photo-3587488.jpeg?auto=compress&cs=tinysrgb&w=800',
        'https://images.pexels.com/photos/3587489/pexels-photo-3587489.jpeg?auto=compress&cs=tinysrgb&w=800',
    ];

    let currentImageIndex = 0;

    // Color Options Data
    const colorOptions = [
        { name: 'Matte Black', image: 'https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=100' },
        { name: 'Rose Gold', image: 'https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&cs=tinysrgb&w=100' },
        { name: 'Silver', image: 'https://images.pexels.com/photos/3587488/pexels-photo-3587488.jpeg?auto=compress&cs=tinysrgb&w=100' },
        { name: 'Navy Blue', image: 'https://images.pexels.com/photos/3587489/pexels-photo-3587489.jpeg?auto=compress&cs=tinysrgb&w=100' }
    ];

    // Connectivity Options Data
    const connectivityOptions = [
        { name: 'Bluetooth 5.0', selected: true },
        { name: 'Wired USB-C', selected: false },
        { name: 'Hybrid (BT + Wired)', selected: false }
    ];

    // Battery Options Data
    const batteryOptions = [
        { name: '20 hours', selected: false },
        { name: '30 hours', selected: true },
        { name: '40 hours', selected: false }
    ];

    // Initialize Image Gallery
    function initImageGallery() {
        const thumbnailContainer = document.getElementById('thumbnailContainer');
        const mainImage = document.getElementById('mainImage');

        // Generate thumbnails
        productImages.forEach((image, index) => {
            const button = document.createElement('button');
            button.className = `border-2 ${index === 0 ? 'border-blue-500' : 'border-gray-200 hover:border-gray-300'} rounded-lg overflow-hidden`;
            button.innerHTML = `<img src="${image}" alt="Thumbnail ${index + 1}" class="w-full h-20 object-cover">`;
            button.addEventListener('click', () => changeImage(index));
            thumbnailContainer.appendChild(button);
        });

        // Add more button
        const moreButton = document.createElement('button');
        moreButton.className = 'bg-gray-100 rounded-lg h-20 flex items-center justify-center text-gray-600 hover:bg-gray-200';
        moreButton.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
        thumbnailContainer.appendChild(moreButton);

        // Previous/Next buttons
        document.getElementById('prevImage').addEventListener('click', () => {
            currentImageIndex = (currentImageIndex - 1 + productImages.length) % productImages.length;
            changeImage(currentImageIndex);
        });

        document.getElementById('nextImage').addEventListener('click', () => {
            currentImageIndex = (currentImageIndex + 1) % productImages.length;
            changeImage(currentImageIndex);
        });
    }

    function changeImage(index) {
        currentImageIndex = index;
        const mainImage = document.getElementById('mainImage');
        mainImage.src = productImages[index];

        // Update thumbnail borders
        const thumbnails = document.querySelectorAll('#thumbnailContainer button');
        thumbnails.forEach((thumb, i) => {
            if (i === index) {
                thumb.className = 'border-2 border-blue-500 rounded-lg overflow-hidden';
            } else if (i < productImages.length) {
                thumb.className = 'border-2 border-gray-200 hover:border-gray-300 rounded-lg overflow-hidden';
            }
        });
    }

    // Initialize Color Options
    function initColorOptions() {
        const colorContainer = document.getElementById('colorOptions');
        colorOptions.forEach((color, index) => {
            const button = document.createElement('button');
            button.className = `border-2 ${index === 0 ? 'border-blue-500' : 'border-gray-200 hover:border-gray-300'} rounded-lg p-2 w-20 h-20 overflow-hidden`;
            button.innerHTML = `<img src="${color.image}" alt="${color.name}" class="w-full h-full object-cover rounded">`;
            button.addEventListener('click', () => selectColor(index));
            colorContainer.appendChild(button);
        });

        // Add +3 button
        const moreButton = document.createElement('button');
        moreButton.className = 'border-2 border-gray-200 hover:border-gray-300 rounded-lg p-2 w-20 h-20 flex items-center justify-center bg-gray-50';
        moreButton.innerHTML = '<span class="text-gray-600 text-sm font-medium">+3</span>';
        colorContainer.appendChild(moreButton);
    }

    function selectColor(index) {
        const selectedColorSpan = document.getElementById('selectedColor');
        selectedColorSpan.textContent = colorOptions[index].name;

        // Update button borders
        const buttons = document.querySelectorAll('#colorOptions button');
        buttons.forEach((button, i) => {
            if (i === index) {
                button.className = 'border-2 border-blue-500 rounded-lg p-2 w-20 h-20 overflow-hidden';
            } else if (i < colorOptions.length) {
                button.className = 'border-2 border-gray-200 hover:border-gray-300 rounded-lg p-2 w-20 h-20 overflow-hidden';
            }
        });
    }

    // Initialize Connectivity Options
    function initConnectivityOptions() {
        const connectivityContainer = document.getElementById('connectivityOptions');
        connectivityOptions.forEach((option, index) => {
            const button = document.createElement('button');
            button.className = `px-5 py-2 border-2 ${option.selected ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'} rounded-full text-sm font-medium ${option.selected ? 'text-gray-900' : 'text-gray-700'}`;
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
                button.className = 'px-5 py-2 border-2 border-blue-500 bg-blue-50 rounded-full text-sm font-medium text-gray-900';
            } else {
                button.className = 'px-5 py-2 border-2 border-gray-200 hover:border-gray-300 rounded-full text-sm font-medium text-gray-700';
            }
        });
    }

    // Initialize Battery Options
    function initBatteryOptions() {
        const batteryContainer = document.getElementById('batteryOptions');
        batteryOptions.forEach((option, index) => {
            const button = document.createElement('button');
            button.className = `px-5 py-2 border-2 ${option.selected ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'} rounded-full text-sm font-medium ${option.selected ? 'text-gray-900' : 'text-gray-700'}`;
            button.textContent = option.name;
            button.addEventListener('click', () => selectBattery(index));
            batteryContainer.appendChild(button);
        });

        // Add +12 button
        const moreButton = document.createElement('button');
        moreButton.className = 'px-5 py-2 border-2 border-gray-200 hover:border-gray-300 rounded-full text-sm font-medium text-gray-700';
        moreButton.textContent = '+12';
        batteryContainer.appendChild(moreButton);
    }

    function selectBattery(index) {
        batteryOptions.forEach(opt => opt.selected = false);
        batteryOptions[index].selected = true;

        const buttons = document.querySelectorAll('#batteryOptions button');
        buttons.forEach((button, i) => {
            if (i === index) {
                button.className = 'px-5 py-2 border-2 border-blue-500 bg-blue-50 rounded-full text-sm font-medium text-gray-900';
            } else if (i < batteryOptions.length) {
                button.className = 'px-5 py-2 border-2 border-gray-200 hover:border-gray-300 rounded-full text-sm font-medium text-gray-700';
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
                    btn.className = 'tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300';
                });

                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                // Add active class to clicked tab
                button.className = 'tab-button border-b-2 border-blue-600 py-4 px-1 text-sm font-medium text-blue-600';

                // Show corresponding content
                const activeContent = document.getElementById(`${tabName}-tab`);
                if (activeContent) {
                    activeContent.classList.remove('hidden');
                }
            });
        });
    }

    // Initialize all features
    initImageGallery();
    initColorOptions();
    initConnectivityOptions();
    initBatteryOptions();
    initTabs();
});
</script>
@endsection
