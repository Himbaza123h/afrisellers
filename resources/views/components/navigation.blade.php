<nav id="main-nav" class="bg-white shadow-md transition-all duration-300">
    <!-- Top Bar -->
    <div class="bg-blue-600 text-white py-2">
        <div class="container mx-auto px-4 flex justify-between items-center text-sm">
            <div class="hidden md:flex gap-4">
                <a href="#" class="hover:text-blue-200 transition-colors">
                    <i class="fas fa-store mr-1"></i>Sell on AfriSellers
                </a>
                <a href="#" class="hover:text-blue-200 transition-colors">
                    <i class="fas fa-question-circle mr-1"></i>Help Center
                </a>
            </div>
            <div class="flex gap-4 items-center ml-auto">
                <select class="bg-transparent border-none text-white text-sm focus:outline-none cursor-pointer">
                    <option value="en">English</option>
                    <option value="fr">Français</option>
                    <option value="sw">Kiswahili</option>
                </select>
                <a href="#" class="hover:text-blue-200 transition-colors">
                    <i class="fas fa-sign-in-alt mr-1"></i>Sign In
                </a>
                <a href="#" class="hover:text-blue-200 transition-colors font-semibold">
                    <i class="fas fa-user-plus mr-1"></i>Join Free
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between gap-4 lg:gap-6">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="/" class="text-2xl lg:text-3xl font-black text-blue-600 font-display flex items-center gap-2">
                    <i class="fas fa-shopping-bag"></i>
                    <span>AfriSellers</span>
                </a>
            </div>

            <!-- Search Bar - Hidden on mobile, shown on md+ -->
            <div class="hidden md:flex flex-1 max-w-2xl">
                <div class="relative w-full">
                    <input
                        type="text"
                        placeholder="Search products, suppliers, categories..."
                        class="w-full px-4 py-3 pr-32 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:outline-none font-medium"
                    >
                    <button class="absolute right-0 top-0 h-full px-6 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors font-bold">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Right Menu -->
            <div class="flex items-center gap-3 lg:gap-6">
                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-toggle" class="md:hidden text-gray-700 hover:text-blue-600">
                    <i class="fas fa-bars text-2xl"></i>
                </button>

                <a href="#" class="text-gray-700 hover:text-blue-600 flex items-center gap-2 transition-colors">
                    <i class="fas fa-user text-xl lg:text-2xl"></i>
                    <span class="hidden lg:block font-semibold">Account</span>
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 flex items-center gap-2 transition-colors">
                    <i class="fas fa-heart text-xl lg:text-2xl"></i>
                    <span class="hidden lg:block font-semibold">Favorites</span>
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 flex items-center gap-2 relative transition-colors">
                    <i class="fas fa-shopping-cart text-xl lg:text-2xl"></i>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">3</span>
                    <span class="hidden lg:block font-semibold">Cart</span>
                </a>
            </div>
        </div>

        <!-- Mobile Search Bar -->
        <div class="md:hidden mt-4">
            <div class="relative">
                <input
                    type="text"
                    placeholder="Search products..."
                    class="w-full px-4 py-3 pr-16 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:outline-none font-medium"
                >
                <button class="absolute right-0 top-0 h-full px-4 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Category Navigation -->
    <div class="bg-gray-100 border-t border-gray-200 relative">
        <div class="container mx-auto px-4">
            <div class="flex items-center gap-4 lg:gap-8 py-3 overflow-x-auto scrollbar-hide">
                <!-- Categories Dropdown -->
                <div class="relative">
                    <button id="categories-btn" class="flex items-center gap-2 text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors bg-white px-4 py-2 rounded-lg shadow-sm">
                        <i class="fas fa-th text-lg"></i>
                        <span>All Categories</span>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>
                </div>

                <!-- Quick Links -->
                <a href="#" class="text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors flex items-center gap-2">
                    <i class="fas fa-star text-yellow-500"></i>Featured Suppliers
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors flex items-center gap-2">
                    <i class="fas fa-certificate text-green-500"></i>New Arrivals
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors flex items-center gap-2">
                    <i class="fas fa-file-invoice text-orange-500"></i>Top RFQs
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors flex items-center gap-2">
                    <i class="fas fa-truck text-purple-500"></i>Loadboard
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-red-500"></i>Trade Shows
                </a>
            </div>
        </div>

        <!-- Full Width Mega Dropdown Menu -->
        <div id="categories-menu" class="hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-blue-600">
            <div class="container mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                    <!-- Left Sidebar -->
                    <div class="lg:col-span-3 bg-gray-50 border-r border-gray-200 p-6">
                        <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
                            <i class="fas fa-layer-group text-blue-600"></i>
                            Browse by Category
                        </h3>
                        <div class="space-y-3">
                            <button class="category-sidebar-btn w-full text-left px-4 py-3 rounded-lg bg-blue-600 text-white font-bold transition-all hover:bg-blue-700" data-category="agriculture">
                                <i class="fas fa-seedling mr-2"></i>Agriculture
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-4 py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-blue-50 hover:text-blue-600" data-category="electronics">
                                <i class="fas fa-laptop mr-2"></i>Electronics
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-4 py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-blue-50 hover:text-blue-600" data-category="fashion">
                                <i class="fas fa-tshirt mr-2"></i>Fashion & Apparel
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-4 py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-blue-50 hover:text-blue-600" data-category="industrial">
                                <i class="fas fa-industry mr-2"></i>Industrial
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-4 py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-blue-50 hover:text-blue-600" data-category="home">
                                <i class="fas fa-home mr-2"></i>Home & Garden
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-4 py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-blue-50 hover:text-blue-600" data-category="health">
                                <i class="fas fa-heartbeat mr-2"></i>Health & Beauty
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-4 py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-blue-50 hover:text-blue-600" data-category="automotive">
                                <i class="fas fa-car mr-2"></i>Automotive
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                            <button class="category-sidebar-btn w-full text-left px-4 py-3 rounded-lg bg-white text-gray-700 font-semibold transition-all hover:bg-blue-50 hover:text-blue-600" data-category="food">
                                <i class="fas fa-utensils mr-2"></i>Food & Beverage
                                <i class="fas fa-chevron-right float-right mt-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Right Content Area -->
                    <div class="lg:col-span-9 p-8">
                        <!-- Agriculture Category -->
                        <div id="category-agriculture" class="category-content">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                                    <span class="text-5xl">🌾</span>
                                    Agriculture & Farming
                                </h2>
                                <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🌱</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Seeds & Crops</h4>
                                        <p class="text-sm text-gray-600 mt-1">5,240 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🚜</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Farm Equipment</h4>
                                        <p class="text-sm text-gray-600 mt-1">3,890 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">💧</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Irrigation</h4>
                                        <p class="text-sm text-gray-600 mt-1">2,156 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🐄</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Livestock</h4>
                                        <p class="text-sm text-gray-600 mt-1">1,678 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🧪</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Fertilizers</h4>
                                        <p class="text-sm text-gray-600 mt-1">4,320 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🔧</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Tools</h4>
                                        <p class="text-sm text-gray-600 mt-1">2,945 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🌽</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Harvesting</h4>
                                        <p class="text-sm text-gray-600 mt-1">1,834 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🏡</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Greenhouses</h4>
                                        <p class="text-sm text-gray-600 mt-1">967 products</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Electronics Category -->
                        <div id="category-electronics" class="category-content hidden">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                                    <span class="text-5xl">💻</span>
                                    Electronics & Technology
                                </h2>
                                <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">💻</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Computers</h4>
                                        <p class="text-sm text-gray-600 mt-1">8,450 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">📱</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Mobile Phones</h4>
                                        <p class="text-sm text-gray-600 mt-1">12,340 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🎧</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Audio & Video</h4>
                                        <p class="text-sm text-gray-600 mt-1">6,780 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">📷</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Cameras</h4>
                                        <p class="text-sm text-gray-600 mt-1">3,290 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">⌚</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Smart Devices</h4>
                                        <p class="text-sm text-gray-600 mt-1">5,670 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🔌</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Accessories</h4>
                                        <p class="text-sm text-gray-600 mt-1">9,230 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🖨️</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Printers</h4>
                                        <p class="text-sm text-gray-600 mt-1">2,145 products</p>
                                    </div>
                                </a>
                                <a href="#" class="group">
                                    <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-6 text-center hover:shadow-lg transition-all">
                                        <div class="text-5xl mb-4">🎮</div>
                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Gaming</h4>
                                        <p class="text-sm text-gray-600 mt-1">4,567 products</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Other categories hidden by default -->
                        <div id="category-fashion" class="category-content hidden">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                                    <span class="text-5xl">👔</span>
                                    Fashion & Apparel
                                </h2>
                                <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <p class="text-gray-600">Fashion categories coming soon...</p>
                        </div>

                        <div id="category-industrial" class="category-content hidden">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                                    <span class="text-5xl">🏭</span>
                                    Industrial Equipment
                                </h2>
                                <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <p class="text-gray-600">Industrial categories coming soon...</p>
                        </div>

                        <div id="category-home" class="category-content hidden">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                                    <span class="text-5xl">🏡</span>
                                    Home & Garden
                                </h2>
                                <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <p class="text-gray-600">Home & Garden categories coming soon...</p>
                        </div>

                        <div id="category-health" class="category-content hidden">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                                    <span class="text-5xl">💄</span>
                                    Health & Beauty
                                </h2>
                                <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <p class="text-gray-600">Health & Beauty categories coming soon...</p>
                        </div>

                        <div id="category-automotive" class="category-content hidden">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                                    <span class="text-5xl">🚗</span>
                                    Automotive
                                </h2>
                                <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <p class="text-gray-600">Automotive categories coming soon...</p>
                        </div>

                        <div id="category-food" class="category-content hidden">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                                    <span class="text-5xl">🍽️</span>
                                    Food & Beverage
                                </h2>
                                <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <p class="text-gray-600">Food & Beverage categories coming soon...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
        <div class="container mx-auto px-4 py-4">
            <div class="space-y-2">
                <a href="#" class="block py-2 text-gray-700 hover:text-blue-600 font-semibold">
                    <i class="fas fa-store mr-2"></i>Sell on AfriSellers
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-blue-600 font-semibold">
                    <i class="fas fa-question-circle mr-2"></i>Help Center
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-blue-600 font-semibold">
                    <i class="fas fa-star mr-2"></i>Featured Suppliers
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-blue-600 font-semibold">
                    <i class="fas fa-certificate mr-2"></i>New Arrivals
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-blue-600 font-semibold">
                    <i class="fas fa-file-invoice mr-2"></i>Top RFQs
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-blue-600 font-semibold">
                    <i class="fas fa-truck mr-2"></i>Loadboard
                </a>
                <a href="#" class="block py-2 text-gray-700 hover:text-blue-600 font-semibold">
                    <i class="fas fa-calendar-alt mr-2"></i>Trade Shows
                </a>
            </div>
        </div>
    </div>
</nav>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
