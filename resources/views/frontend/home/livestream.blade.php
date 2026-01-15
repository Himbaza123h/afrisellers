@extends('layouts.app')

@section('title', 'Live Streaming - Watch Live Product Showcases')

@push('styles')
<style>
    .live-badge {
        animation: pulse-live 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-live {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .7;
        }
    }
    .viewer-count {
        animation: count-update 3s ease-in-out infinite;
    }
    @keyframes count-update {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
    .scroll-smooth {
        scroll-behavior: smooth;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #ff0808;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cc0606;
    }
</style>
@endpush

@section('content')
<div class="bg-[#ff0808] min-h-screen">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-[#ff0808] to-red-600 text-white py-6 md:py-10 lg:py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 md:gap-6">
                <div class="text-center md:text-left w-full md:w-auto">
                    <h1 class="text-2xl sm:text-lg md:text-4xl lg:text-5xl font-bold mb-2">Live Streaming</h1>
                    <p class="text-red-100 text-xs sm:text-sm md:text-base">Watch live product demonstrations from verified suppliers</p>
                </div>
                <div class="flex items-center gap-2 sm:gap-3 w-full md:w-auto justify-center md:justify-end">
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-center flex-1 sm:flex-initial">
                        <div class="text-xl sm:text-2xl md:text-lg font-bold">24</div>
                        <div class="text-xs text-red-100">Live Now</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-center flex-1 sm:flex-initial">
                        <div class="text-xl sm:text-2xl md:text-lg font-bold">1.2K</div>
                        <div class="text-xs text-red-100">Watching</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 md:py-8">
        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm p-3 sm:p-4 mb-4 sm:mb-6 overflow-x-auto">
            <div class="flex gap-2 sm:gap-3 min-w-max sm:flex-wrap">
                <button class="filter-tab active px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm transition-all whitespace-nowrap">
                    <i class="fas fa-fire mr-1 sm:mr-2"></i>All Streams
                </button>
                <button class="filter-tab px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm transition-all whitespace-nowrap">
                    <i class="fas fa-laptop mr-1 sm:mr-2"></i>Electronics
                </button>
                <button class="filter-tab px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm transition-all whitespace-nowrap">
                    <i class="fas fa-tshirt mr-1 sm:mr-2"></i>Fashion
                </button>
                <button class="filter-tab px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm transition-all whitespace-nowrap">
                    <i class="fas fa-home mr-1 sm:mr-2"></i>Home & Garden
                </button>
                <button class="filter-tab px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm transition-all whitespace-nowrap">
                    <i class="fas fa-heartbeat mr-1 sm:mr-2"></i>Health & Beauty
                </button>
                <button class="filter-tab px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm transition-all whitespace-nowrap">
                    <i class="fas fa-seedling mr-1 sm:mr-2"></i>Agriculture
                </button>
            </div>
        </div>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Main Stream (Featured) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Video Player -->
                    <div class="relative aspect-video bg-gray-900">
                        <img src="https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=1200"
                             alt="Live Stream"
                             class="w-full h-full object-cover">

                        <!-- Live Badge -->
                        <div class="absolute top-2 sm:top-4 left-2 sm:left-4 flex items-center gap-2 sm:gap-3 flex-wrap">
                            <span class="live-badge bg-[#ff0808] text-white px-2 sm:px-3 py-1 sm:py-1.5 rounded-full text-xs sm:text-sm font-bold flex items-center gap-1 sm:gap-2">
                                <span class="w-1.5 sm:w-2 h-1.5 sm:h-2 bg-white rounded-full animate-pulse"></span>
                                LIVE
                            </span>
                            <span class="viewer-count bg-black/70 backdrop-blur-sm text-white px-2 sm:px-3 py-1 sm:py-1.5 rounded-full text-xs sm:text-sm font-semibold">
                                <i class="fas fa-eye mr-1"></i><span class="viewer-number">3,542</span> watching
                            </span>
                        </div>

                        <!-- Play Button Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <button class="bg-white/90 hover:bg-white rounded-full p-4 sm:p-6 transition-all hover:scale-110 shadow-lg">
                                <i class="fas fa-play text-[#ff0808] text-2xl sm:text-lg ml-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Stream Info -->
                    <div class="p-3 sm:p-4 md:p-6">
                        <div class="flex flex-col sm:flex-row items-start justify-between gap-3 sm:gap-4 mb-4">
                            <div class="flex-1 w-full sm:w-auto">
                                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-2 leading-tight">
                                    Premium Wireless Headphones - Live Product Demo & Special Offers
                                </h2>
                                <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs sm:text-sm text-gray-600">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-clock text-[#ff0808]"></i>
                                        Started 25 min ago
                                    </span>
                                    <span class="hidden sm:inline">•</span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-tag text-[#ff0808]"></i>
                                        Electronics
                                    </span>
                                </div>
                            </div>
                            <button class="w-full sm:w-auto flex-shrink-0 bg-[#ff0808] hover:bg-red-700 text-white px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg font-semibold transition-colors text-sm sm:text-base">
                                <i class="fas fa-heart mr-2"></i>Follow
                            </button>
                        </div>

                        <!-- Supplier Info -->
                        <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 bg-gray-50 rounded-lg mb-4">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-lg sm:text-2xl font-bold flex-shrink-0">
                                T
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 text-sm sm:text-base lg:text-lg truncate">TechGear Electronics Co., Ltd</h3>
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs sm:text-sm text-gray-600 mt-1">
                                    <div class="flex items-center gap-1">
                                        <div class="flex text-yellow-400">
                                            <i class="fas fa-star text-xs"></i>
                                            <i class="fas fa-star text-xs"></i>
                                            <i class="fas fa-star text-xs"></i>
                                            <i class="fas fa-star text-xs"></i>
                                            <i class="fas fa-star text-xs"></i>
                                        </div>
                                        <span class="font-semibold">4.9</span>
                                    </div>
                                    <span class="hidden sm:inline">•</span>
                                    <span>8 years</span>
                                    <span class="hidden sm:inline">•</span>
                                    <span class="flex items-center gap-1">
                                        <img src="https://flagcdn.com/w20/cn.png" alt="CN" class="w-4 h-3">
                                        China
                                    </span>
                                </div>
                            </div>
                            <button class="hidden md:block bg-[#ff0808] hover:bg-red-700 text-white px-4 lg:px-6 py-2 rounded-lg font-semibold transition-colors text-sm">
                                Contact Supplier
                            </button>
                        </div>

                        <!-- Mobile Contact Button -->
                        <button class="md:hidden w-full bg-[#ff0808] hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-semibold transition-colors mb-4 text-sm">
                            <i class="fas fa-envelope mr-2"></i>Contact Supplier
                        </button>

                        <!-- Featured Products -->
                        <div class="border-t pt-4">
                            <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2 text-sm sm:text-base">
                                <i class="fas fa-shopping-bag text-[#ff0808]"></i>
                                Featured Products in This Stream
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 sm:gap-3">
                                <div class="border border-gray-200 rounded-lg p-2 sm:p-3 hover:border-[#ff0808] hover:shadow-md transition-all cursor-pointer group">
                                    <img src="https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&cs=tinysrgb&w=200"
                                         alt="Product"
                                         class="w-full aspect-square object-cover rounded mb-2 group-hover:scale-105 transition-transform">
                                    <p class="text-xs font-semibold text-gray-900 line-clamp-2 mb-1">Wireless Headphones Pro</p>
                                    <p class="text-sm font-bold text-[#ff0808]">$45.99</p>
                                    <p class="text-xs text-gray-500 mt-0.5">MOQ: 50 pcs</p>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-2 sm:p-3 hover:border-[#ff0808] hover:shadow-md transition-all cursor-pointer group">
                                    <img src="https://images.pexels.com/photos/3587488/pexels-photo-3587488.jpeg?auto=compress&cs=tinysrgb&w=200"
                                         alt="Product"
                                         class="w-full aspect-square object-cover rounded mb-2 group-hover:scale-105 transition-transform">
                                    <p class="text-xs font-semibold text-gray-900 line-clamp-2 mb-1">Bluetooth Speaker</p>
                                    <p class="text-sm font-bold text-[#ff0808]">$29.50</p>
                                    <p class="text-xs text-gray-500 mt-0.5">MOQ: 100 pcs</p>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-2 sm:p-3 hover:border-[#ff0808] hover:shadow-md transition-all cursor-pointer group">
                                    <img src="https://images.pexels.com/photos/3587489/pexels-photo-3587489.jpeg?auto=compress&cs=tinysrgb&w=200"
                                         alt="Product"
                                         class="w-full aspect-square object-cover rounded mb-2 group-hover:scale-105 transition-transform">
                                    <p class="text-xs font-semibold text-gray-900 line-clamp-2 mb-1">USB-C Cable</p>
                                    <p class="text-sm font-bold text-[#ff0808]">$8.99</p>
                                    <p class="text-xs text-gray-500 mt-0.5">MOQ: 200 pcs</p>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-2 sm:p-3 hover:border-[#ff0808] hover:shadow-md transition-all cursor-pointer group">
                                    <img src="https://images.pexels.com/photos/4223030/pexels-photo-4223030.jpeg?auto=compress&cs=tinysrgb&w=200"
                                         alt="Product"
                                         class="w-full aspect-square object-cover rounded mb-2 group-hover:scale-105 transition-transform">
                                    <p class="text-xs font-semibold text-gray-900 line-clamp-2 mb-1">Power Bank 20000mAh</p>
                                    <p class="text-sm font-bold text-[#ff0808]">$19.99</p>
                                    <p class="text-xs text-gray-500 mt-0.5">MOQ: 50 pcs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Chat Section (Mobile Toggle) -->
                <div class="lg:hidden mt-4 sm:mt-6 bg-white rounded-lg shadow-lg p-3 sm:p-4">
                    <button id="mobile-chat-toggle" class="w-full bg-[#ff0808] hover:bg-red-700 text-white py-2.5 sm:py-3 rounded-lg font-semibold transition-colors text-sm sm:text-base">
                        <i class="fas fa-comments mr-2"></i>Show Live Chat
                    </button>
                </div>
            </div>

            <!-- Right Sidebar - Live Chat & Other Streams -->
            <div class="lg:col-span-1 space-y-4 sm:space-y-6">
                <!-- Live Chat -->
                <div id="live-chat-section" class="bg-white rounded-lg shadow-lg overflow-hidden hidden lg:block">
                    <div class="bg-gradient-to-r from-[#ff0808] to-red-600 text-white p-3 sm:p-4">
                        <h3 class="font-bold flex items-center gap-2 text-sm sm:text-base">
                            <i class="fas fa-comments"></i>
                            Live Chat
                            <span class="ml-auto bg-white/20 px-2 py-0.5 rounded-full text-xs">234 online</span>
                        </h3>
                    </div>

                    <div class="h-80 sm:h-96 overflow-y-auto p-3 sm:p-4 space-y-2 sm:space-y-3 bg-gray-50 custom-scrollbar" id="chat-messages">
                        <!-- Chat Message -->
                        <div class="flex gap-2">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                J
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-xs sm:text-sm text-gray-900 truncate">John_Buyer</span>
                                    <span class="text-xs text-gray-500 flex-shrink-0">2m ago</span>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700 bg-white p-2 rounded-lg break-words">What's the minimum order quantity?</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                S
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-xs sm:text-sm text-gray-900 truncate">Sarah_K</span>
                                    <span class="text-xs text-gray-500 flex-shrink-0">3m ago</span>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700 bg-white p-2 rounded-lg break-words">Great product! Can you ship to Kenya?</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 border-2 border-yellow-400">
                                T
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="font-semibold text-xs sm:text-sm text-gray-900">TechGear</span>
                                    <span class="bg-yellow-400 text-xs px-2 py-0.5 rounded-full font-bold">HOST</span>
                                    <span class="text-xs text-gray-500">4m ago</span>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700 bg-blue-50 p-2 rounded-lg border-l-4 border-blue-500 break-words">Yes! We ship worldwide. MOQ is 50 pieces. Special discount today!</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                M
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-xs sm:text-sm text-gray-900 truncate">Mike_Tech</span>
                                    <span class="text-xs text-gray-500 flex-shrink-0">5m ago</span>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700 bg-white p-2 rounded-lg">🔥 Amazing quality!</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-pink-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                A
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-xs sm:text-sm text-gray-900 truncate">Anna_R</span>
                                    <span class="text-xs text-gray-500 flex-shrink-0">6m ago</span>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700 bg-white p-2 rounded-lg break-words">How long is the warranty?</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 sm:p-4 border-t border-gray-200 bg-white">
                        <div class="flex gap-2">
                            <input type="text"
                                   placeholder="Type a message..."
                                   class="flex-1 px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] text-sm">
                            <button class="bg-[#ff0808] hover:bg-red-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors flex-shrink-0">
                                <i class="fas fa-paper-plane text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Other Live Streams -->
                <div class="bg-white rounded-lg shadow-lg p-3 sm:p-4">
                    <h3 class="font-bold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2 text-sm sm:text-base">
                        <i class="fas fa-fire text-[#ff0808]"></i>
                        Other Live Streams
                    </h3>
                    <div class="space-y-3">
                        <!-- Stream Card -->
                        <a href="#" class="block group">
                            <div class="relative overflow-hidden rounded-lg">
                                <img src="https://images.pexels.com/photos/3587489/pexels-photo-3587489.jpeg?auto=compress&cs=tinysrgb&w=400"
                                     alt="Stream"
                                     class="w-full aspect-video object-cover rounded-lg group-hover:scale-105 transition-transform">
                                <div class="absolute top-2 left-2 flex items-center gap-2">
                                    <span class="live-badge bg-[#ff0808] text-white px-2 py-1 rounded text-xs font-bold">
                                    LIVE
                                </span>
                                <span class="bg-black/70 backdrop-blur-sm text-white px-2 py-1 rounded text-xs">
                                    <i class="fas fa-eye mr-1"></i>432
                                </span>
                            </div>
                        </div>
                        <div class="p-2 sm:p-3">
                            <h4 class="font-semibold text-xs sm:text-sm text-gray-900 group-hover:text-[#ff0808] line-clamp-2 mb-2 transition-colors">
                                Kitchen Appliances Mega Sale
                            </h4>
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 sm:w-6 sm:h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    K
                                </div>
                                <span class="text-xs text-gray-600 truncate">KitchenPro Co.</span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Stream Card 2 -->
                <a href="#" class="block group">
                    <div class="bg-white rounded-lg overflow-hidden shadow hover:shadow-lg transition-all">
                        <div class="relative overflow-hidden">
                            <img src="https://images.pexels.com/photos/2582937/pexels-photo-2582937.jpeg?auto=compress&cs=tinysrgb&w=400"
                                 alt="Stream"
                                 class="w-full aspect-video object-cover group-hover:scale-105 transition-transform">
                            <div class="absolute top-2 left-2 flex items-center gap-2">
                                <span class="live-badge bg-[#ff0808] text-white px-2 py-1 rounded text-xs font-bold">
                                    LIVE
                                </span>
                                <span class="bg-black/70 backdrop-blur-sm text-white px-2 py-1 rounded text-xs">
                                    <i class="fas fa-eye mr-1"></i>287
                                </span>
                            </div>
                        </div>
                        <div class="p-2 sm:p-3">
                            <h4 class="font-semibold text-xs sm:text-sm text-gray-900 group-hover:text-[#ff0808] line-clamp-2 mb-2 transition-colors">
                                Professional Camera Equipment
                            </h4>
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 sm:w-6 sm:h-6 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    V
                                </div>
                                <span class="text-xs text-gray-600 truncate">VideoTech Solutions</span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Stream Card 3 -->
                <a href="#" class="block group m-2">
                    <div class="bg-white rounded-lg overflow-hidden shadow hover:shadow-lg transition-all">
                        <div class="relative overflow-hidden">
                            <img src="https://images.pexels.com/photos/20285555/pexels-photo-20285555.jpeg?auto=compress&cs=tinysrgb&w=400"
                                 alt="Stream"
                                 class="w-full aspect-video object-cover group-hover:scale-105 transition-transform">
                            <div class="absolute top-2 left-2 flex items-center gap-2">
                                <span class="live-badge bg-[#ff0808] text-white px-2 py-1 rounded text-xs font-bold">
                                    LIVE
                                </span>
                                <span class="bg-black/70 backdrop-blur-sm text-white px-2 py-1 rounded text-xs">
                                    <i class="fas fa-eye mr-1"></i>198
                                </span>
                            </div>
                        </div>
                        <div class="p-2 sm:p-3">
                            <h4 class="font-semibold text-xs sm:text-sm text-gray-900 group-hover:text-[#ff0808] line-clamp-2 mb-2 transition-colors">
                                Portable Bluetooth Speakers
                            </h4>
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 sm:w-6 sm:h-6 bg-orange-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    A
                                </div>
                                <span class="text-xs text-gray-600 truncate">AudioMax Industries</span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Stream Card 4 -->
                <a href="#" class="block group">
                    <div class="bg-white rounded-lg overflow-hidden shadow hover:shadow-lg transition-all">
                        <div class="relative overflow-hidden">
                            <img src="https://images.pexels.com/photos/4553111/pexels-photo-4553111.jpeg?auto=compress&cs=tinysrgb&w=400"
                                 alt="Stream"
                                 class="w-full aspect-video object-cover group-hover:scale-105 transition-transform">
                            <div class="absolute top-2 left-2 flex items-center gap-2">
                                <span class="live-badge bg-[#ff0808] text-white px-2 py-1 rounded text-xs font-bold">
                                    LIVE
                                </span>
                                <span class="bg-black/70 backdrop-blur-sm text-white px-2 py-1 rounded text-xs">
                                    <i class="fas fa-eye mr-1"></i>345
                                </span>
                            </div>
                        </div>
                        <div class="p-2 sm:p-3">
                            <h4 class="font-semibold text-xs sm:text-sm text-gray-900 group-hover:text-[#ff0808] line-clamp-2 mb-2 transition-colors">
                                LED Ring Lights & Studio Equipment
                            </h4>
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 sm:w-6 sm:h-6 bg-pink-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    L
                                </div>
                                <span class="text-xs text-gray-600 truncate">LightPro Studios</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter Tabs
    const filterTabs = document.querySelectorAll('.filter-tab');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => {
                t.classList.remove('active', 'bg-[#ff0808]', 'text-white');
                t.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            });
            this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            this.classList.add('active', 'bg-[#ff0808]', 'text-white');
        });
    });

    // Initialize first tab as active
    if (filterTabs.length > 0) {
        filterTabs[0].classList.add('bg-[#ff0808]', 'text-white');
        filterTabs[0].classList.remove('bg-gray-100', 'text-gray-700');
    }

    // Mobile Chat Toggle
    const mobileChatToggle = document.getElementById('mobile-chat-toggle');
    const liveChatSection = document.getElementById('live-chat-section');

    if (mobileChatToggle && liveChatSection) {
        mobileChatToggle.addEventListener('click', function() {
            liveChatSection.classList.toggle('hidden');
            liveChatSection.classList.toggle('block');

            if (liveChatSection.classList.contains('hidden')) {
                this.innerHTML = '<i class="fas fa-comments mr-2"></i>Show Live Chat';
            } else {
                this.innerHTML = '<i class="fas fa-times mr-2"></i>Hide Live Chat';
                // Scroll chat to bottom when opened
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    setTimeout(() => {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }, 100);
                }
            }
        });
    }

    // Auto-scroll chat to bottom on load
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Simulate new chat messages (for demo)
    const chatNames = ['Alice_B', 'Tom_S', 'Emma_W', 'David_K', 'Lisa_M', 'James_P', 'Sofia_R', 'Mark_T'];
    const chatMessages_demo = [
        'This looks amazing! 😍',
        'What\'s the delivery time?',
        'Can I get a sample?',
        'Great price!',
        'Do you ship to Nigeria?',
        'MOQ for this product?',
        'Amazing quality! 🔥',
        'Is there a discount for bulk orders?',
        'What payment methods do you accept?',
        'How long is the warranty period?',
        'Can you customize the packaging?',
        'Do you have certifications?'
    ];

    function addRandomMessage() {
        if (!chatMessages) return;

        const name = chatNames[Math.floor(Math.random() * chatNames.length)];
        const message = chatMessages_demo[Math.floor(Math.random() * chatMessages_demo.length)];
        const colors = ['bg-blue-500', 'bg-purple-500', 'bg-green-500', 'bg-pink-500', 'bg-orange-500', 'bg-indigo-500', 'bg-teal-500'];
        const color = colors[Math.floor(Math.random() * colors.length)];
        const initial = name.charAt(0);

        const messageHTML = `
            <div class="flex gap-2 animate-fade-in">
                <div class="w-7 h-7 sm:w-8 sm:h-8 ${color} rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    ${initial}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-semibold text-xs sm:text-sm text-gray-900 truncate">${name}</span>
                        <span class="text-xs text-gray-500 flex-shrink-0">Just now</span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-700 bg-white p-2 rounded-lg break-words">${message}</p>
                </div>
            </div>
        `;

        chatMessages.insertAdjacentHTML('beforeend', messageHTML);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Remove old messages if too many
        const messages = chatMessages.querySelectorAll('.flex.gap-2');
        if (messages.length > 12) {
            messages[0].remove();
        }
    }

    // Add a new message every 8-15 seconds
    setInterval(addRandomMessage, Math.random() * 7000 + 8000);

    // Simulate viewer count updates
    function updateViewerCount() {
        const viewerNumber = document.querySelector('.viewer-number');
        if (viewerNumber) {
            let count = parseInt(viewerNumber.textContent.replace(',', ''));
            const change = Math.floor(Math.random() * 30) - 10; // -10 to +20
            count = Math.max(100, count + change);
            viewerNumber.textContent = count.toLocaleString();
        }
    }

    setInterval(updateViewerCount, 5000);

    // Handle chat input enter key
    const chatInput = document.querySelector('input[placeholder="Type a message..."]');
    const chatSendBtn = chatInput?.nextElementSibling;

    if (chatInput && chatSendBtn) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.trim()) {
                // Simulate sending message
                const message = this.value.trim();
                const messageHTML = `
                    <div class="flex gap-2 animate-fade-in">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            Y
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-xs sm:text-sm text-gray-900">You</span>
                                <span class="text-xs text-gray-500 flex-shrink-0">Just now</span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-700 bg-blue-50 p-2 rounded-lg border-l-4 border-blue-500 break-words">${message}</p>
                        </div>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', messageHTML);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                this.value = '';
            }
        });

        chatSendBtn.addEventListener('click', function() {
            if (chatInput.value.trim()) {
                const event = new KeyboardEvent('keypress', { key: 'Enter' });
                chatInput.dispatchEvent(event);
            }
        });
    }

    console.log('✅ Enhanced livestream page initialized successfully!');
});
</script>
@endpush
