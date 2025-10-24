<nav id="main-nav" class="bg-white shadow-md transition-all duration-300">
    <!-- Top Bar -->
    <div class="bg-[#ff0808] text-white py-2.5">
        <div class="container mx-auto px-4 flex justify-between items-center text-sm">
            <div class="hidden md:flex gap-4">
                <a href="{{ route('vendor.register') }}" class="hover:text-red-100 transition-colors">
                    <i class="fas fa-store mr-1"></i>Become Seller
                </a>
                <span class="text-red-200">|</span>
                <a href="#" class="hover:text-red-100 transition-colors">
                    <i class="fas fa-sign-in-alt mr-1"></i>Login to Seller
                </a>
            </div>
            <div class="flex gap-4 items-center ml-auto">
                <a href="#" class="hover:text-red-100 transition-colors">
                    <i class="fas fa-phone mr-1"></i>Helpline Call us: 250782179022
                </a>
                <span class="text-red-200 hidden md:inline">|</span>
                <select class="bg-transparent border-none text-white text-sm focus:outline-none cursor-pointer hidden md:block">
                    <option value="en">English</option>
                    <option value="fr">Français</option>
                    <option value="sw">Kiswahili</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <div class="container mx-auto px-4 py-5">
        <div class="flex items-center justify-between gap-4 lg:gap-6">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="/" class="flex items-center">
                    <img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png" alt="AfriSellers" class="h-12 lg:h-14">
                </a>
            </div>

            <!-- Search Bar - Hidden on mobile, shown on md+ -->
            <div class="hidden md:flex flex-1 max-w-2xl">
                <div class="relative w-full">
                    <input
                        type="text"
                        placeholder="Search products, suppliers, categories..."
                        class="w-full px-4 py-3 pr-32 rounded-lg border-2 border-gray-300 focus:border-[#ff0808] focus:outline-none font-medium"
                    >
                    <button class="absolute right-0 top-0 h-full px-6 bg-[#ff0808] text-white rounded-r-lg hover:bg-red-700 transition-colors font-bold">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Right Menu -->
            <div class="flex items-center gap-3 lg:gap-6">
                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-toggle" class="md:hidden text-gray-700 hover:text-[#ff0808]">
                    <i class="fas fa-bars text-2xl"></i>
                </button>

                <!-- Login/Registration -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center">
                        <i class="fas fa-user text-gray-400 text-lg"></i>
                    </div>
                    <div class="hidden lg:flex items-center gap-2 text-gray-700">
                        <a href="#" class="hover:text-[#ff0808] font-semibold transition-colors">Login</a>
                        <span class="text-gray-400">|</span>
                        <a href="#" class="hover:text-[#ff0808] font-semibold transition-colors">Registration</a>
                    </div>
                </div>

            <a href="{{ route('livestream') }}" class="text-gray-700 hover:text-[#ff0808] flex items-center gap-2 relative transition-colors">
                <div class="relative">
                    <i class="fas fa-video text-xl lg:text-2xl"></i>
                    <span class="absolute -top-2 -right-2 bg-[#ff0808] text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold shadow-md">7</span>
                </div>
                {{-- <span class="hidden lg:block font-semibold">Livestream</span> --}}
                <span class="hidden lg:flex items-center gap-1 bg-[#ff0808] text-white text-xs px-2 py-0.5 rounded-full font-bold">
                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                    LIVE
                </span>
            </a>
            </div>
        </div>

        <!-- Mobile Search Bar -->
        <div class="md:hidden mt-4">
            <div class="relative">
                <input
                    type="text"
                    placeholder="Search products..."
                    class="w-full px-4 py-3 pr-16 rounded-lg border-2 border-gray-300 focus:border-[#ff0808] focus:outline-none font-medium"
                >
                <button class="absolute right-0 top-0 h-full px-4 bg-[#ff0808] text-white rounded-r-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Category Navigation -->
    @if(Route::currentRouteName() === 'home')
    <div class="bg-gray-100 border-t border-gray-200 relative">
        <div class="container mx-auto px-4">
            <div class="flex items-center gap-4 lg:gap-8 py-3 overflow-x-auto scrollbar-hide">
                <!-- All Categories -->
                <div class="relative">
                    <button id="categories-btn" class="flex items-center gap-2 text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors bg-white px-4 py-2 rounded-lg shadow-sm">
                        <i class="fas fa-th text-lg"></i>
                        <span>All Categories</span>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>
                </div>

                <!-- Featured Suppliers -->
                <div class="relative">
                    <a href="#" class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-2" data-dropdown="suppliers-menu">
                        Featured Suppliers
                        <i class="fas fa-chevron-down text-sm"></i>
                    </a>
                </div>

                <!-- New Arrivals -->
                <div class="relative">
                    <a href="#" class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-2" data-dropdown="arrivals-menu">
                        New Arrivals
                        <i class="fas fa-chevron-down text-sm"></i>
                    </a>
                </div>

                <!-- Top RFQs -->
                <div class="relative">
                    <a href="#" class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-2" data-dropdown="rfq-menu">
                        Top RFQs
                        <i class="fas fa-chevron-down text-sm"></i>
                    </a>
                </div>

                <!-- Loadboard -->
                <div class="relative">
                    <a href="#" class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-2" data-dropdown="loadboard-menu">
                        Loadboard
                        <i class="fas fa-chevron-down text-sm"></i>
                    </a>
                </div>

                <!-- Trade Shows -->
                <div class="relative">
                    <a href="#" class="nav-dropdown-trigger text-gray-700 hover:text-[#ff0808] font-bold whitespace-nowrap transition-colors flex items-center gap-2" data-dropdown="tradeshows-menu">
                        Trade Shows
                        <i class="fas fa-chevron-down text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- All Categories Mega Menu -->
        <div id="categories-menu" class="hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-[#ff0808]">
            <div class="container mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                    <!-- Left Sidebar -->
                    <div class="lg:col-span-3 bg-gray-50 border-r border-gray-200 p-4 md:p-6">
                        <h3 class="text-lg md:text-xl font-black text-gray-900 mb-4 md:mb-6 flex items-center gap-2">
                            <i class="fas fa-layer-group text-[#ff0808]"></i>
                            Browse by Category
                        </h3>
                        <div class="space-y-2 md:space-y-3">
                            <button class="category-sidebar-btn w-full text-left px-3 md:px-4 py-2 md:py-3 rounded-lg bg-[#ff0808] text-white font-bold transition-all hover:bg-red-700 text-sm md:text-base" data-category="agriculture">
                                <i class="fas fa-seedling mr-2"></i>Agriculture
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-3 md:px-4 py-2 md:py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-red-50 hover:text-[#ff0808] text-sm md:text-base" data-category="electronics">
                                <i class="fas fa-laptop mr-2"></i>Electronics
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-3 md:px-4 py-2 md:py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-red-50 hover:text-[#ff0808] text-sm md:text-base" data-category="fashion">
                                <i class="fas fa-tshirt mr-2"></i>Fashion & Apparel
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-3 md:px-4 py-2 md:py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-red-50 hover:text-[#ff0808] text-sm md:text-base" data-category="industrial">
                                <i class="fas fa-industry mr-2"></i>Industrial
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-3 md:px-4 py-2 md:py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-red-50 hover:text-[#ff0808] text-sm md:text-base" data-category="home">
                                <i class="fas fa-home mr-2"></i>Home & Garden
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-3 md:px-4 py-2 md:py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-red-50 hover:text-[#ff0808] text-sm md:text-base" data-category="health">
                                <i class="fas fa-heartbeat mr-2"></i>Health & Beauty
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-3 md:px-4 py-2 md:py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-red-50 hover:text-[#ff0808] text-sm md:text-base" data-category="automotive">
                                <i class="fas fa-car mr-2"></i>Automotive
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-3 md:px-4 py-2 md:py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-red-50 hover:text-[#ff0808] text-sm md:text-base" data-category="food">
                                <i class="fas fa-utensils mr-2"></i>Food & Beverage
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Right Content Area -->
                    <div class="lg:col-span-9 p-4 md:p-8">
                        <!-- Agriculture Category -->
                        <div id="category-agriculture" class="category-content">
                            <div class="flex items-center justify-between mb-4 md:mb-6">
                                <h2 class="text-2xl md:text-3xl font-black text-gray-900 flex items-center gap-2 md:gap-3">
                                    <span class="text-3xl md:text-5xl">🌾</span>
                                    <span class="hidden sm:inline">Agriculture & Farming</span>
                                    <span class="sm:hidden">Agriculture</span>
                                </h2>
                                <a href="#" class="text-[#ff0808] hover:text-red-700 font-bold flex items-center gap-2 text-sm md:text-base">
                                    View All <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6">
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🌱</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Seeds & Crops</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">5,240 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🚜</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Farm Equipment</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">3,890 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">💧</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Irrigation</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">2,156 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🐄</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Livestock</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">1,678 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🧪</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Fertilizers</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">4,320 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🔧</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Tools</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">2,945 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🌽</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Harvesting</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">1,834 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🏡</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Greenhouses</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">967 products</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Electronics Category -->
                        <div id="category-electronics" class="category-content hidden">
                            <div class="flex items-center justify-between mb-4 md:mb-6">
                                <h2 class="text-2xl md:text-3xl font-black text-gray-900 flex items-center gap-2 md:gap-3">
                                    <span class="text-3xl md:text-5xl">💻</span>
                                    <span class="hidden sm:inline">Electronics & Technology</span>
                                    <span class="sm:hidden">Electronics</span>
                                </h2>
                                <a href="#" class="text-[#ff0808] hover:text-red-700 font-bold flex items-center gap-2 text-sm md:text-base">
                                    View All <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6">
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">💻</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Computers</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">8,450 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">📱</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Mobile Phones</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">12,340 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🎧</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Audio & Video</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">6,780 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">📷</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Cameras</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">3,290 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">⌚</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Smart Devices</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">5,670 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🔌</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Accessories</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">9,230 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🖨️</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Printers</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">2,145 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-4 md:p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-3xl md:text-5xl mb-2 md:mb-4">🎮</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#ff0808] text-sm md:text-base">Gaming</h4>
                                        <p class="text-xs md:text-sm text-gray-600 mt-1">4,567 products</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Other categories -->
                        <div id="category-fashion" class="category-content hidden">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl md:text-3xl font-black text-gray-900 flex items-center gap-3">
                                    <span class="text-3xl md:text-5xl">👔</span>
                                    Fashion & Apparel
                                </h2>
                            </div>
                            <p class="text-gray-600">Fashion categories coming soon...</p>
                        </div>

                        <div id="category-industrial" class="category-content hidden">
                            <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-4">🏭 Industrial Equipment</h2>
                            <p class="text-gray-600">Industrial categories coming soon...</p>
                        </div>

                        <div id="category-home" class="category-content hidden">
                            <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-4">🏡 Home & Garden</h2>
                            <p class="text-gray-600">Home & Garden categories coming soon...</p>
                        </div>

                        <div id="category-health" class="category-content hidden">
                            <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-4">💄 Health & Beauty</h2>
                            <p class="text-gray-600">Health & Beauty categories coming soon...</p>
                        </div>

                        <div id="category-automotive" class="category-content hidden">
                            <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-4">🚗 Automotive</h2>
                            <p class="text-gray-600">Automotive categories coming soon...</p>
                        </div>

                        <div id="category-food" class="category-content hidden">
                            <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-4">🍽️ Food & Beverage</h2>
                            <p class="text-gray-600">Food & Beverage categories coming soon...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Suppliers Dropdown -->
        <div id="suppliers-menu" class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-yellow-500">
            <div class="container mx-auto p-4 md:p-8">
                <div class="flex items-center justify-between mb-4 md:mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Featured Suppliers</h2>
                    <a href="#" class="text-[#ff0808] hover:text-red-700 font-semibold text-sm md:text-base flex items-center gap-2">
                        View All <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 md:gap-4">
                    <a href="#" class="group">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-3 md:p-4 hover:border-[#ff0808] hover:shadow-md transition-all text-center">
                            <div class="w-12 h-12 md:w-14 md:h-14 bg-gray-100 rounded-full mx-auto mb-2 md:mb-3 flex items-center justify-center text-gray-700 text-lg md:text-xl font-bold">F</div>
                            <h4 class="font-semibold text-gray-900 text-xs md:text-sm group-hover:text-[#ff0808] transition-colors">FashionMax</h4>
                            <p class="text-xs text-gray-600 mt-1">⭐ 4.6 (423)</p>
                            <span class="inline-block mt-2 text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">Verified</span>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-3 md:p-4 hover:border-[#ff0808] hover:shadow-md transition-all text-center">
                            <div class="w-12 h-12 md:w-14 md:h-14 bg-gray-100 rounded-full mx-auto mb-2 md:mb-3 flex items-center justify-center text-gray-700 text-lg md:text-xl font-bold">I</div>
                            <h4 class="font-semibold text-gray-900 text-xs md:text-sm group-hover:text-[#ff0808] transition-colors">IndustroPro</h4>
                            <p class="text-xs text-gray-600 mt-1">⭐ 4.8 (312)</p>
                            <span class="inline-block mt-2 text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">Verified</span>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-3 md:p-4 hover:border-[#ff0808] hover:shadow-md transition-all text-center">
                            <div class="w-12 h-12 md:w-14 md:h-14 bg-gray-100 rounded-full mx-auto mb-2 md:mb-3 flex items-center justify-center text-gray-700 text-lg md:text-xl font-bold">T</div>
                            <h4 class="font-semibold text-gray-900 text-xs md:text-sm group-hover:text-[#ff0808] transition-colors">TechVision</h4>
                            <p class="text-xs text-gray-600 mt-1">⭐ 4.9 (678)</p>
                            <span class="inline-block mt-2 text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">Verified</span>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-3 md:p-4 hover:border-[#ff0808] hover:shadow-md transition-all text-center">
                            <div class="w-12 h-12 md:w-14 md:h-14 bg-gray-100 rounded-full mx-auto mb-2 md:mb-3 flex items-center justify-center text-gray-700 text-lg md:text-xl font-bold">A</div>
                            <h4 class="font-semibold text-gray-900 text-xs md:text-sm group-hover:text-[#ff0808] transition-colors">AgriCorp</h4>
                            <p class="text-xs text-gray-600 mt-1">⭐ 4.7 (256)</p>
                            <span class="inline-block mt-2 text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">Verified</span>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-3 md:p-4 hover:border-[#ff0808] hover:shadow-md transition-all text-center">
                            <div class="w-12 h-12 md:w-14 md:h-14 bg-gray-100 rounded-full mx-auto mb-2 md:mb-3 flex items-center justify-center text-gray-700 text-lg md:text-xl font-bold">E</div>
                            <h4 class="font-semibold text-gray-900 text-xs md:text-sm group-hover:text-[#ff0808] transition-colors">ElectroHub</h4>
                            <p class="text-xs text-gray-600 mt-1">⭐ 4.5 (189)</p>
                            <span class="inline-block mt-2 text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">Verified</span>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-3 md:p-4 hover:border-[#ff0808] hover:shadow-md transition-all text-center">
                            <div class="w-12 h-12 md:w-14 md:h-14 bg-gray-100 rounded-full mx-auto mb-2 md:mb-3 flex items-center justify-center text-gray-700 text-lg md:text-xl font-bold">B</div>
                            <h4 class="font-semibold text-gray-900 text-xs md:text-sm group-hover:text-[#ff0808] transition-colors">BuildMaster</h4>
                            <p class="text-xs text-gray-600 mt-1">⭐ 4.8 (534)</p>
                            <span class="inline-block mt-2 text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">Verified</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- New Arrivals Dropdown -->
        <div id="arrivals-menu" class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-green-500">
            <div class="container mx-auto p-4 md:p-8">
                <div class="flex items-center justify-between mb-4 md:mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">New Arrivals</h2>
                    <a href="#" class="text-[#ff0808] hover:text-red-700 font-semibold text-sm md:text-base flex items-center gap-2">
                        View All <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4">
                    <a href="#" class="group">
                        <div class="bg-white rounded-lg p-3 md:p-4 border-2 border-gray-200 hover:border-green-500 hover:shadow-md transition-all">
                            <div class="w-full h-32 md:h-40 bg-gray-100 rounded-lg mb-2 md:mb-3 flex items-center justify-center">
                                <span class="text-4xl md:text-6xl">📱</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 group-hover:text-[#ff0808] text-xs md:text-sm">Smart Phone X</h4>
                            <p class="text-xs text-gray-600 mt-1">$299 - $599</p>
                            <p class="text-xs text-gray-500 mt-1">Added 2 days ago</p>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white rounded-lg p-3 md:p-4 border-2 border-gray-200 hover:border-green-500 hover:shadow-md transition-all">
                            <div class="w-full h-32 md:h-40 bg-gray-100 rounded-lg mb-2 md:mb-3 flex items-center justify-center">
                                <span class="text-4xl md:text-6xl">💻</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 group-hover:text-[#ff0808] text-xs md:text-sm">Laptop Pro</h4>
                            <p class="text-xs text-gray-600 mt-1">$899 - $1,499</p>
                            <p class="text-xs text-gray-500 mt-1">Added 3 days ago</p>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white rounded-lg p-3 md:p-4 border-2 border-gray-200 hover:border-green-500 hover:shadow-md transition-all">
                            <div class="w-full h-32 md:h-40 bg-gray-100 rounded-lg mb-2 md:mb-3 flex items-center justify-center">
                                <span class="text-4xl md:text-6xl">🎧</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 group-hover:text-[#ff0808] text-xs md:text-sm">Wireless Headphones</h4>
                            <p class="text-xs text-gray-600 mt-1">$79 - $199</p>
                            <p class="text-xs text-gray-500 mt-1">Added 1 day ago</p>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white rounded-lg p-3 md:p-4 border-2 border-gray-200 hover:border-green-500 hover:shadow-md transition-all">
                            <div class="w-full h-32 md:h-40 bg-gray-100 rounded-lg mb-2 md:mb-3 flex items-center justify-center">
                                <span class="text-4xl md:text-6xl">⌚</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 group-hover:text-[#ff0808] text-xs md:text-sm">Smart Watch</h4>
                            <p class="text-xs text-gray-600 mt-1">$149 - $399</p>
                            <p class="text-xs text-gray-500 mt-1">Added 5 days ago</p>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white rounded-lg p-3 md:p-4 border-2 border-gray-200 hover:border-green-500 hover:shadow-md transition-all">
                            <div class="w-full h-32 md:h-40 bg-gray-100 rounded-lg mb-2 md:mb-3 flex items-center justify-center">
                                <span class="text-4xl md:text-6xl">📷</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 group-hover:text-[#ff0808] text-xs md:text-sm">Digital Camera</h4>
                            <p class="text-xs text-gray-600 mt-1">$499 - $999</p>
                            <p class="text-xs text-gray-500 mt-1">Added 4 days ago</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Top RFQs Dropdown -->
        <div id="rfq-menu" class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-orange-500">
            <div class="container mx-auto p-4 md:p-8">
                <div class="flex items-center justify-between mb-4 md:mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Top RFQs</h2>
                    <a href="#" class="text-[#ff0808] hover:text-red-700 font-semibold text-sm md:text-base flex items-center gap-2">
                        View All <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <div class="space-y-3 md:space-y-4">
                    <a href="#" class="block bg-white rounded-lg p-4 md:p-6 hover:shadow-md transition-all border-2 border-gray-200 hover:border-orange-500">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-sm md:text-lg mb-2">🌾 Seeking 500 tons of Maize Seeds</h4>
                                <p class="text-gray-600 text-xs md:text-sm mb-2 md:mb-3">High-quality certified maize seeds for commercial farming. Required delivery by end of Q2.</p>
                                <div class="flex flex-wrap gap-2 md:gap-4 text-xs text-gray-600">
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>Kenya</span>
                                    <span><i class="fas fa-calendar mr-1"></i>Posted 2 hours ago</span>
                                    <span><i class="fas fa-tag mr-1"></i>Agriculture</span>
                                </div>
                            </div>
                            <div class="md:ml-4 flex md:flex-col items-center md:items-end gap-2">
                                <span class="inline-block bg-orange-500 text-white px-3 md:px-4 py-1 md:py-2 rounded-lg font-bold text-xs md:text-sm">15 Quotes</span>
                                <p class="text-xs text-gray-500">Expires in 5 days</p>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="block bg-white rounded-lg p-4 md:p-6 hover:shadow-md transition-all border-2 border-gray-200 hover:border-blue-500">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-sm md:text-lg mb-2">💻 Need 200 Laptops for Corporate Office</h4>
                                <p class="text-gray-600 text-xs md:text-sm mb-2 md:mb-3">Bulk order of business laptops with minimum 8GB RAM, 256GB SSD. Warranty required.</p>
                                <div class="flex flex-wrap gap-2 md:gap-4 text-xs text-gray-600">
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>Rwanda</span>
                                    <span><i class="fas fa-calendar mr-1"></i>Posted 5 hours ago</span>
                                    <span><i class="fas fa-tag mr-1"></i>Electronics</span>
                                </div>
                            </div>
                            <div class="md:ml-4 flex md:flex-col items-center md:items-end gap-2">
                                <span class="inline-block bg-blue-500 text-white px-3 md:px-4 py-1 md:py-2 rounded-lg font-bold text-xs md:text-sm">8 Quotes</span>
                                <p class="text-xs text-gray-500">Expires in 7 days</p>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="block bg-white rounded-lg p-4 md:p-6 hover:shadow-md transition-all border-2 border-gray-200 hover:border-green-500">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-sm md:text-lg mb-2">🚜 Industrial Tractor with Accessories</h4>
                                <p class="text-gray-600 text-xs md:text-sm mb-2 md:mb-3">Heavy-duty tractor 80HP+ with plowing and harvesting attachments. Training support preferred.</p>
                                <div class="flex flex-wrap gap-2 md:gap-4 text-xs text-gray-600">
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>Tanzania</span>
                                    <span><i class="fas fa-calendar mr-1"></i>Posted 1 day ago</span>
                                    <span><i class="fas fa-tag mr-1"></i>Industrial</span>
                                </div>
                            </div>
                            <div class="md:ml-4 flex md:flex-col items-center md:items-end gap-2">
                                <span class="inline-block bg-green-500 text-white px-3 md:px-4 py-1 md:py-2 rounded-lg font-bold text-xs md:text-sm">12 Quotes</span>
                                <p class="text-xs text-gray-500">Expires in 10 days</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Loadboard Dropdown -->
        <div id="loadboard-menu" class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-purple-500">
            <div class="container mx-auto p-4 md:p-8">
                <div class="flex items-center justify-between mb-4 md:mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Available Loads</h2>
                    <a href="#" class="text-[#ff0808] hover:text-red-700 font-semibold text-sm md:text-base flex items-center gap-2">
                        View All <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                    <a href="#" class="block bg-white rounded-lg p-4 md:p-6 hover:shadow-md transition-all border-2 border-gray-200 hover:border-purple-500">
                        <div class="flex items-start justify-between mb-3 md:mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm md:text-lg mb-1">Nairobi → Kampala</h4>
                                <p class="text-xs md:text-sm text-gray-600">Full Truckload (FTL)</p>
                            </div>
                            <span class="bg-purple-500 text-white px-2 md:px-3 py-1 rounded-full text-xs md:text-sm font-bold">$2,500</span>
                        </div>
                        <div class="space-y-1 md:space-y-2 text-xs md:text-sm text-gray-700">
                            <p><i class="fas fa-box mr-2 text-purple-500"></i>20 tons of Agricultural Products</p>
                            <p><i class="fas fa-calendar mr-2 text-purple-500"></i>Pickup: Oct 25, 2025</p>
                            <p><i class="fas fa-truck mr-2 text-purple-500"></i>Truck Type: Flatbed</p>
                        </div>
                        <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-200">
                            <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">Verified Shipper</span>
                        </div>
                    </a>
                    <a href="#" class="block bg-white rounded-lg p-4 md:p-6 hover:shadow-md transition-all border-2 border-gray-200 hover:border-blue-500">
                        <div class="flex items-start justify-between mb-3 md:mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm md:text-lg mb-1">Kigali → Dar es Salaam</h4>
                                <p class="text-xs md:text-sm text-gray-600">Less Than Truckload (LTL)</p>
                            </div>
                            <span class="bg-blue-500 text-white px-2 md:px-3 py-1 rounded-full text-xs md:text-sm font-bold">$800</span>
                        </div>
                        <div class="space-y-1 md:space-y-2 text-xs md:text-sm text-gray-700">
                            <p><i class="fas fa-box mr-2 text-blue-500"></i>5 tons of Electronics</p>
                            <p><i class="fas fa-calendar mr-2 text-blue-500"></i>Pickup: Oct 28, 2025</p>
                            <p><i class="fas fa-truck mr-2 text-blue-500"></i>Truck Type: Box Truck</p>
                        </div>
                        <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-200">
                            <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">Verified Shipper</span>
                        </div>
                    </a>
                    <a href="#" class="block bg-white rounded-lg p-4 md:p-6 hover:shadow-md transition-all border-2 border-gray-200 hover:border-green-500">
                        <div class="flex items-start justify-between mb-3 md:mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm md:text-lg mb-1">Addis Ababa → Nairobi</h4>
                                <p class="text-xs md:text-sm text-gray-600">Full Truckload (FTL)</p>
                            </div>
                            <span class="bg-green-500 text-white px-2 md:px-3 py-1 rounded-full text-xs md:text-sm font-bold">$3,200</span>
                        </div>
                        <div class="space-y-1 md:space-y-2 text-xs md:text-sm text-gray-700">
                            <p><i class="fas fa-box mr-2 text-green-500"></i>15 tons of Coffee Beans</p>
                            <p><i class="fas fa-calendar mr-2 text-green-500"></i>Pickup: Oct 30, 2025</p>
                            <p><i class="fas fa-truck mr-2 text-green-500"></i>Truck Type: Refrigerated</p>
                        </div>
                        <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-200">
                            <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">Verified Shipper</span>
                        </div>
                    </a>
                    <a href="#" class="block bg-white rounded-lg p-4 md:p-6 hover:shadow-md transition-all border-2 border-gray-200 hover:border-orange-500">
                        <div class="flex items-start justify-between mb-3 md:mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm md:text-lg mb-1">Lusaka → Harare</h4>
                                <p class="text-xs md:text-sm text-gray-600">Full Truckload (FTL)</p>
                            </div>
                            <span class="bg-orange-500 text-white px-2 md:px-3 py-1 rounded-full text-xs md:text-sm font-bold">$1,800</span>
                        </div>
                        <div class="space-y-1 md:space-y-2 text-xs md:text-sm text-gray-700">
                            <p><i class="fas fa-box mr-2 text-orange-500"></i>12 tons of Construction Materials</p>
                            <p><i class="fas fa-calendar mr-2 text-orange-500"></i>Pickup: Nov 2, 2025</p>
                            <p><i class="fas fa-truck mr-2 text-orange-500"></i>Truck Type: Flatbed</p>
                        </div>
                        <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-200">
                            <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">Verified Shipper</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Trade Shows Dropdown -->
        <div id="tradeshows-menu" class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-[#ff0808]">
            <div class="container mx-auto p-4 md:p-8">
                <div class="flex items-center justify-between mb-4 md:mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Upcoming Trade Shows</h2>
                    <a href="#" class="text-[#ff0808] hover:text-red-700 font-semibold text-sm md:text-base flex items-center gap-2">
                        View All <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
                    <a href="#" class="block bg-white rounded-lg overflow-hidden border-2 border-gray-200 hover:border-[#ff0808] hover:shadow-md transition-all">
                        <div class="h-32 md:h-40 bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white">
                            <div class="text-center">
                                <div class="text-3xl md:text-5xl font-black">NOV</div>
                                <div class="text-5xl md:text-7xl font-black">15</div>
                            </div>
                        </div>
                        <div class="p-4 md:p-6">
                            <h4 class="font-bold text-gray-900 text-sm md:text-lg mb-2">East Africa AgriExpo 2025</h4>
                            <p class="text-xs md:text-sm text-gray-600 mb-2 md:mb-3">The largest agriculture trade show featuring 500+ exhibitors</p>
                            <div class="space-y-1 text-xs md:text-sm text-gray-700">
                                <p><i class="fas fa-map-marker-alt mr-2 text-[#ff0808]"></i>Nairobi, Kenya</p>
                                <p><i class="fas fa-clock mr-2 text-[#ff0808]"></i>Nov 15-18, 2025</p>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="block bg-white rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 hover:shadow-md transition-all">
                        <div class="h-32 md:h-40 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white">
                            <div class="text-center">
                                <div class="text-3xl md:text-5xl font-black">DEC</div>
                                <div class="text-5xl md:text-7xl font-black">03</div>
                            </div>
                        </div>
                        <div class="p-4 md:p-6">
                            <h4 class="font-bold text-gray-900 text-sm md:text-lg mb-2">Africa Tech Summit 2025</h4>
                            <p class="text-xs md:text-sm text-gray-600 mb-2 md:mb-3">Technology and innovation showcase with 300+ tech companies</p>
                            <div class="space-y-1 text-xs md:text-sm text-gray-700">
                                <p><i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>Kigali, Rwanda</p>
                                <p><i class="fas fa-clock mr-2 text-blue-500"></i>Dec 3-5, 2025</p>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="block bg-white rounded-lg overflow-hidden border-2 border-gray-200 hover:border-purple-500 hover:shadow-md transition-all">
                        <div class="h-32 md:h-40 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white">
                            <div class="text-center">
                                <div class="text-3xl md:text-5xl font-black">JAN</div>
                                <div class="text-5xl md:text-7xl font-black">20</div>
                            </div>
                        </div>
                        <div class="p-4 md:p-6">
                            <h4 class="font-bold text-gray-900 text-sm md:text-lg mb-2">African Fashion Week 2026</h4>
                            <p class="text-xs md:text-sm text-gray-600 mb-2 md:mb-3">Premier fashion event showcasing African designers and textiles</p>
                            <div class="space-y-1 text-xs md:text-sm text-gray-700">
                                <p><i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>Lagos, Nigeria</p>
                                <p><i class="fas fa-clock mr-2 text-purple-500"></i>Jan 20-23, 2026</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
        <div class="container mx-auto px-4 py-4">
            <div class="space-y-2">
                <a href="#" class="block py-2 text-gray-700 hover:text-[#ff0808] font-semibold">
                    <i class="fas fa-store mr-2"></i>Sell on AfriSellers
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-[#ff0808] font-semibold">
                    <i class="fas fa-question-circle mr-2"></i>Help Center
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-[#ff0808] font-semibold">
                    <i class="fas fa-star mr-2"></i>Featured Suppliers
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-[#ff0808] font-semibold">
                    <i class="fas fa-certificate mr-2"></i>New Arrivals
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-[#ff0808] font-semibold">
                    <i class="fas fa-file-invoice mr-2"></i>Top RFQs
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-[#ff0808] font-semibold">
                    <i class="fas fa-truck mr-2"></i>Loadboard
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-[#ff0808] font-semibold">
                    <i class="fas fa-calendar-alt mr-2"></i>Trade Shows
                </a>
            </div>
        </div>
    </div>
</nav>

<style>
/* Fix dropdown positioning and scrolling */
.nav-dropdown-menu,
#categories-menu {
    max-height: calc(100vh - 150px);
    overflow-y: auto;
    overflow-x: hidden;
    scroll-behavior: smooth;
}

/* Custom scrollbar for dropdowns */
.nav-dropdown-menu::-webkit-scrollbar,
#categories-menu::-webkit-scrollbar {
    width: 8px;
}

.nav-dropdown-menu::-webkit-scrollbar-track,
#categories-menu::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.nav-dropdown-menu::-webkit-scrollbar-thumb,
#categories-menu::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.nav-dropdown-menu::-webkit-scrollbar-thumb:hover,
#categories-menu::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* CRITICAL: Ensure page stays scrollable */
body {
    overflow-y: scroll !important;
}

html {
    overflow-y: scroll !important;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

