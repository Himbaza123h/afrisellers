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
    @if(Route::currentRouteName() === 'home')
    <div class="bg-gray-100 border-t border-gray-200 relative">
        <div class="container mx-auto px-4">
            <div class="flex items-center gap-4 lg:gap-8 py-3 overflow-x-auto scrollbar-hide">
                <!-- All Categories -->
                <div class="relative">
                    <button id="categories-btn" class="flex items-center gap-2 text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors bg-white px-4 py-2 rounded-lg shadow-sm">
                        <i class="fas fa-th text-lg"></i>
                        <span>All Categories</span>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>
                </div>

                <!-- Featured Suppliers -->
                <div class="relative">
                    <a href="#" class="nav-dropdown-trigger text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors flex items-center gap-2" data-dropdown="suppliers-menu">
                        <i class="fas fa-star text-yellow-500"></i>Featured Suppliers
                        <i class="fas fa-chevron-down text-sm"></i>
                    </a>
                </div>

                <!-- New Arrivals -->
                <div class="relative">
                    <a href="#" class="nav-dropdown-trigger text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors flex items-center gap-2" data-dropdown="arrivals-menu">
                        <i class="fas fa-certificate text-green-500"></i>New Arrivals
                        <i class="fas fa-chevron-down text-sm"></i>
                    </a>
                </div>

                <!-- Top RFQs -->
                <div class="relative">
                    <a href="#" class="nav-dropdown-trigger text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors flex items-center gap-2" data-dropdown="rfq-menu">
                        <i class="fas fa-file-invoice text-orange-500"></i>Top RFQs
                        <i class="fas fa-chevron-down text-sm"></i>
                    </a>
                </div>

                <!-- Loadboard -->
                <div class="relative">
                    <a href="#" class="nav-dropdown-trigger text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors flex items-center gap-2" data-dropdown="loadboard-menu">
                        <i class="fas fa-truck text-purple-500"></i>Loadboard
                        <i class="fas fa-chevron-down text-sm"></i>
                    </a>
                </div>

                <!-- Trade Shows -->
                <div class="relative">
                    <a href="#" class="nav-dropdown-trigger text-gray-700 hover:text-blue-600 font-bold whitespace-nowrap transition-colors flex items-center gap-2" data-dropdown="tradeshows-menu">
                        <i class="fas fa-calendar-alt text-red-500"></i>Trade Shows
                        <i class="fas fa-chevron-down text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- All Categories Mega Menu -->
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

                        <!-- Other categories (abbreviated for space) -->
                        <div id="category-fashion" class="category-content hidden">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                                    <span class="text-5xl">👔</span>
                                    Fashion & Apparel
                                </h2>
                            </div>
                            <p class="text-gray-600">Fashion categories coming soon...</p>
                        </div>

                        <div id="category-industrial" class="category-content hidden">
                            <h2 class="text-3xl font-black text-gray-900 mb-4">🏭 Industrial Equipment</h2>
                            <p class="text-gray-600">Industrial categories coming soon...</p>
                        </div>

                        <div id="category-home" class="category-content hidden">
                            <h2 class="text-3xl font-black text-gray-900 mb-4">🏡 Home & Garden</h2>
                            <p class="text-gray-600">Home & Garden categories coming soon...</p>
                        </div>

                        <div id="category-health" class="category-content hidden">
                            <h2 class="text-3xl font-black text-gray-900 mb-4">💄 Health & Beauty</h2>
                            <p class="text-gray-600">Health & Beauty categories coming soon...</p>
                        </div>

                        <div id="category-automotive" class="category-content hidden">
                            <h2 class="text-3xl font-black text-gray-900 mb-4">🚗 Automotive</h2>
                            <p class="text-gray-600">Automotive categories coming soon...</p>
                        </div>

                        <div id="category-food" class="category-content hidden">
                            <h2 class="text-3xl font-black text-gray-900 mb-4">🍽️ Food & Beverage</h2>
                            <p class="text-gray-600">Food & Beverage categories coming soon...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Suppliers Dropdown -->
        <div id="suppliers-menu" class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-yellow-500">
            <div class="container mx-auto p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                        <i class="fas fa-star text-yellow-500 text-4xl"></i>
                        Featured Suppliers
                    </h2>
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                        View All Suppliers <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    <a href="#" class="group text-center">
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 hover:shadow-lg transition-all">
                            <div class="w-16 h-16 bg-yellow-500 rounded-full mx-auto mb-3 flex items-center justify-center text-white text-2xl font-bold">A</div>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">AgriTech Co.</h4>
                            <p class="text-sm text-gray-600 mt-1">⭐ 4.8 (234)</p>
                            <span class="inline-block mt-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Verified</span>
                        </div>
                    </a>
                    <a href="#" class="group text-center">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 hover:shadow-lg transition-all">
                            <div class="w-16 h-16 bg-blue-500 rounded-full mx-auto mb-3 flex items-center justify-center text-white text-2xl font-bold">E</div>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">ElectroHub</h4>
                            <p class="text-sm text-gray-600 mt-1">⭐ 4.9 (567)</p>
                            <span class="inline-block mt-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Verified</span>
                        </div>
                    </a>
                    <a href="#" class="group text-center">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 hover:shadow-lg transition-all">
                            <div class="w-16 h-16 bg-green-500 rounded-full mx-auto mb-3 flex items-center justify-center text-white text-2xl font-bold">G</div>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">GreenFarms</h4>
                            <p class="text-sm text-gray-600 mt-1">⭐ 4.7 (189)</p>
                            <span class="inline-block mt-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Verified</span>
                        </div>
                    </a>
                    <a href="#" class="group text-center">
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 hover:shadow-lg transition-all">
                            <div class="w-16 h-16 bg-purple-500 rounded-full mx-auto mb-3 flex items-center justify-center text-white text-2xl font-bold">F</div>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">FashionMax</h4>
                            <p class="text-sm text-gray-600 mt-1">⭐ 4.6 (423)</p>
                            <span class="inline-block mt-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Verified</span>
                        </div>
                    </a>
                    <a href="#" class="group text-center">
                        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 hover:shadow-lg transition-all">
                            <div class="w-16 h-16 bg-red-500 rounded-full mx-auto mb-3 flex items-center justify-center text-white text-2xl font-bold">I</div>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">IndustroPro</h4>
                            <p class="text-sm text-gray-600 mt-1">⭐ 4.8 (312)</p>
                            <span class="inline-block mt-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Verified</span>
                        </div>
                    </a>
                    <a href="#" class="group text-center">
                        <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl p-6 hover:shadow-lg transition-all">
                            <div class="w-16 h-16 bg-teal-500 rounded-full mx-auto mb-3 flex items-center justify-center text-white text-2xl font-bold">T</div>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">TechVision</h4>
                            <p class="text-sm text-gray-600 mt-1">⭐ 4.9 (678)</p>
                            <span class="inline-block mt-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Verified</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- New Arrivals Dropdown -->
        <div id="arrivals-menu" class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-green-500">
            <div class="container mx-auto p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                        <i class="fas fa-certificate text-green-500 text-4xl"></i>
                        New Arrivals
                    </h2>
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                        View All Products <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    <a href="#" class="group">
                        <div class="bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-green-500 hover:shadow-lg transition-all">
                            <div class="w-full h-40 bg-gradient-to-br from-green-100 to-green-200 rounded-lg mb-3 flex items-center justify-center">
                                <span class="text-6xl">📱</span>
                            </div>
                            <span class="inline-block text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full mb-2">New</span>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Smart Phone X</h4>
                            <p class="text-sm text-gray-600 mt-1">$299 - $599</p>
                            <p class="text-xs text-gray-500 mt-1">Added 2 days ago</p>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-green-500 hover:shadow-lg transition-all">
                            <div class="w-full h-40 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg mb-3 flex items-center justify-center">
                                <span class="text-6xl">💻</span>
                            </div>
                            <span class="inline-block text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full mb-2">New</span>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Laptop Pro</h4>
                            <p class="text-sm text-gray-600 mt-1">$899 - $1,499</p>
                            <p class="text-xs text-gray-500 mt-1">Added 3 days ago</p>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-green-500 hover:shadow-lg transition-all">
                            <div class="w-full h-40 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg mb-3 flex items-center justify-center">
                                <span class="text-6xl">🎧</span>
                            </div>
                            <span class="inline-block text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full mb-2">New</span>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Wireless Headphones</h4>
                            <p class="text-sm text-gray-600 mt-1">$79 - $199</p>
                            <p class="text-xs text-gray-500 mt-1">Added 1 day ago</p>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-green-500 hover:shadow-lg transition-all">
                            <div class="w-full h-40 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-lg mb-3 flex items-center justify-center">
                                <span class="text-6xl">⌚</span>
                            </div>
                            <span class="inline-block text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full mb-2">New</span>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Smart Watch</h4>
                            <p class="text-sm text-gray-600 mt-1">$149 - $399</p>
                            <p class="text-xs text-gray-500 mt-1">Added 5 days ago</p>
                        </div>
                    </a>
                    <a href="#" class="group">
                        <div class="bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-green-500 hover:shadow-lg transition-all">
                            <div class="w-full h-40 bg-gradient-to-br from-red-100 to-red-200 rounded-lg mb-3 flex items-center justify-center">
                                <span class="text-6xl">📷</span>
                            </div>
                            <span class="inline-block text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full mb-2">New</span>
                            <h4 class="font-bold text-gray-900 group-hover:text-blue-600">Digital Camera</h4>
                            <p class="text-sm text-gray-600 mt-1">$499 - $999</p>
                            <p class="text-xs text-gray-500 mt-1">Added 4 days ago</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Top RFQs Dropdown -->
        <div id="rfq-menu" class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-orange-500">
            <div class="container mx-auto p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                        <i class="fas fa-file-invoice text-orange-500 text-4xl"></i>
                        Top RFQs (Request for Quotation)
                    </h2>
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                        View All RFQs <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    <a href="#" class="block bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-6 hover:shadow-lg transition-all border-l-4 border-orange-500">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-lg mb-2">🌾 Seeking 500 tons of Maize Seeds</h4>
                                <p class="text-gray-600 text-sm mb-3">High-quality certified maize seeds for commercial farming. Required delivery by end of Q2.</p>
                                <div class="flex gap-4 text-sm text-gray-600">
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>Kenya</span>
                                    <span><i class="fas fa-calendar mr-1"></i>Posted 2 hours ago</span>
                                    <span><i class="fas fa-tag mr-1"></i>Agriculture</span>
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <span class="inline-block bg-orange-500 text-white px-4 py-2 rounded-lg font-bold">15 Quotes</span>
                                <p class="text-xs text-gray-500 mt-2">Expires in 5 days</p>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="block bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 hover:shadow-lg transition-all border-l-4 border-blue-500">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-lg mb-2">💻 Need 200 Laptops for Corporate Office</h4>
                                <p class="text-gray-600 text-sm mb-3">Bulk order of business laptops with minimum 8GB RAM, 256GB SSD. Warranty required.</p>
                                <div class="flex gap-4 text-sm text-gray-600">
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>Rwanda</span>
                                    <span><i class="fas fa-calendar mr-1"></i>Posted 5 hours ago</span>
                                    <span><i class="fas fa-tag mr-1"></i>Electronics</span>
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <span class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg font-bold">8 Quotes</span>
                                <p class="text-xs text-gray-500 mt-2">Expires in 7 days</p>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="block bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 hover:shadow-lg transition-all border-l-4 border-green-500">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-lg mb-2">🚜 Industrial Tractor with Accessories</h4>
                                <p class="text-gray-600 text-sm mb-3">Heavy-duty tractor 80HP+ with plowing and harvesting attachments. Training support preferred.</p>
                                <div class="flex gap-4 text-sm text-gray-600">
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>Tanzania</span>
                                    <span><i class="fas fa-calendar mr-1"></i>Posted 1 day ago</span>
                                    <span><i class="fas fa-tag mr-1"></i>Industrial</span>
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <span class="inline-block bg-green-500 text-white px-4 py-2 rounded-lg font-bold">12 Quotes</span>
                                <p class="text-xs text-gray-500 mt-2">Expires in 10 days</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Loadboard Dropdown -->
        <div id="loadboard-menu" class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-purple-500">
            <div class="container mx-auto p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                        <i class="fas fa-truck text-purple-500 text-4xl"></i>
                        Available Loads & Freight
                    </h2>
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                        View All Loads <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="#" class="block bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-6 hover:shadow-lg transition-all border-2 border-purple-200">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg mb-1">Nairobi → Kampala</h4>
                                <p class="text-sm text-gray-600">Full Truckload (FTL)</p>
                            </div>
                            <span class="bg-purple-500 text-white px-3 py-1 rounded-full text-sm font-bold">$2,500</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-700">
                            <p><i class="fas fa-box mr-2 text-purple-500"></i>20 tons of Agricultural Products</p>
                            <p><i class="fas fa-calendar mr-2 text-purple-500"></i>Pickup: Oct 25, 2025</p>
                            <p><i class="fas fa-truck mr-2 text-purple-500"></i>Truck Type: Flatbed</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-purple-200">
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Verified Shipper</span>
                        </div>
                    </a>
                    <a href="#" class="block bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 hover:shadow-lg transition-all border-2 border-blue-200">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg mb-1">Kigali → Dar es Salaam</h4>
                                <p class="text-sm text-gray-600">Less Than Truckload (LTL)</p>
                            </div>
                            <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-bold">$800</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-700">
                            <p><i class="fas fa-box mr-2 text-blue-500"></i>5 tons of Electronics</p>
                            <p><i class="fas fa-calendar mr-2 text-blue-500"></i>Pickup: Oct 28, 2025</p>
                            <p><i class="fas fa-truck mr-2 text-blue-500"></i>Truck Type: Box Truck</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-blue-200">
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Verified Shipper</span>
                        </div>
                    </a>
                    <a href="#" class="block bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 hover:shadow-lg transition-all border-2 border-green-200">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg mb-1">Addis Ababa → Nairobi</h4>
                                <p class="text-sm text-gray-600">Full Truckload (FTL)</p>
                            </div>
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-bold">$3,200</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-700">
                            <p><i class="fas fa-box mr-2 text-green-500"></i>15 tons of Coffee Beans</p>
                            <p><i class="fas fa-calendar mr-2 text-green-500"></i>Pickup: Oct 30, 2025</p>
                            <p><i class="fas fa-truck mr-2 text-green-500"></i>Truck Type: Refrigerated</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-green-200">
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Verified Shipper</span>
                        </div>
                    </a>
                    <a href="#" class="block bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-6 hover:shadow-lg transition-all border-2 border-orange-200">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg mb-1">Lusaka → Harare</h4>
                                <p class="text-sm text-gray-600">Full Truckload (FTL)</p>
                            </div>
                            <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-bold">$1,800</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-700">
                            <p><i class="fas fa-box mr-2 text-orange-500"></i>12 tons of Construction Materials</p>
                            <p><i class="fas fa-calendar mr-2 text-orange-500"></i>Pickup: Nov 2, 2025</p>
                            <p><i class="fas fa-truck mr-2 text-orange-500"></i>Truck Type: Flatbed</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-orange-200">
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Verified Shipper</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Trade Shows Dropdown -->
        <div id="tradeshows-menu" class="nav-dropdown-menu hidden absolute left-0 right-0 top-full bg-white shadow-2xl z-[9999] border-t-4 border-red-500">
            <div class="container mx-auto p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                        <i class="fas fa-calendar-alt text-red-500 text-4xl"></i>
                        Upcoming Trade Shows & Events
                    </h2>
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-2">
                        View All Events <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="#" class="block bg-white rounded-xl overflow-hidden border-2 border-gray-200 hover:border-red-500 hover:shadow-lg transition-all">
                        <div class="h-40 bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white">
                            <div class="text-center">
                                <div class="text-5xl font-black">NOV</div>
                                <div class="text-7xl font-black">15</div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-900 text-lg mb-2">East Africa AgriExpo 2025</h4>
                            <p class="text-sm text-gray-600 mb-3">The largest agriculture trade show featuring 500+ exhibitors</p>
                            <div class="space-y-1 text-sm text-gray-700">
                                <p><i class="fas fa-map-marker-alt mr-2 text-red-500"></i>Nairobi, Kenya</p>
                                <p><i class="fas fa-clock mr-2 text-red-500"></i>Nov 15-18, 2025</p>
                            </div>
                            <div class="mt-4">
                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Agriculture</span>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="block bg-white rounded-xl overflow-hidden border-2 border-gray-200 hover:border-blue-500 hover:shadow-lg transition-all">
                        <div class="h-40 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white">
                            <div class="text-center">
                                <div class="text-5xl font-black">DEC</div>
                                <div class="text-7xl font-black">03</div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-900 text-lg mb-2">Africa Tech Summit 2025</h4>
                            <p class="text-sm text-gray-600 mb-3">Technology and innovation showcase with 300+ tech companies</p>
                            <div class="space-y-1 text-sm text-gray-700">
                                <p><i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>Kigali, Rwanda</p>
                                <p><i class="fas fa-clock mr-2 text-blue-500"></i>Dec 3-5, 2025</p>
                            </div>
                            <div class="mt-4">
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Technology</span>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="block bg-white rounded-xl overflow-hidden border-2 border-gray-200 hover:border-purple-500 hover:shadow-lg transition-all">
                        <div class="h-40 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white">
                            <div class="text-center">
                                <div class="text-5xl font-black">JAN</div>
                                <div class="text-7xl font-black">20</div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="font-bold text-gray-900 text-lg mb-2">African Fashion Week 2026</h4>
                            <p class="text-sm text-gray-600 mb-3">Premier fashion event showcasing African designers and textiles</p>
                            <div class="space-y-1 text-sm text-gray-700">
                                <p><i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>Lagos, Nigeria</p>
                                <p><i class="fas fa-clock mr-2 text-purple-500"></i>Jan 20-23, 2026</p>
                            </div>
                            <div class="mt-4">
                                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">Fashion</span>
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
