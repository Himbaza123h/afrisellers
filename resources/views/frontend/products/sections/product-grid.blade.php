
<main class="flex-1">
    <!-- Header with Results Count and Controls -->
    <div class="bg-white rounded-lg border p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-gray-900">
                Showing 9+ products from global suppliers for "electronics"
            </h1>

            <div class="flex items-center gap-4">
                <!-- Sort Dropdown -->
                <div class="relative">
                    <button class="sort-dropdown-btn flex items-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="text-gray-700 font-medium">Sort by relevance</span>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>
                    <div class="sort-dropdown absolute right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg hidden z-50">
                        <a href="#" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Relevance</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Price: Low to High</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Price: High to Low</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Newest Arrivals</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-50 text-gray-700">Best Sellers</a>
                    </div>
                </div>

                <!-- View Toggle -->
                <div class="flex items-center gap-2 border rounded-lg p-1">
                    <button class="view-toggle active p-2 rounded hover:bg-gray-100 transition-colors" data-view="grid">
                        <i class="fas fa-th text-gray-700"></i>
                    </button>
                    <button class="view-toggle p-2 rounded hover:bg-gray-100 transition-colors" data-view="list">
                        <i class="fas fa-bars text-gray-700"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid - Initially empty, will be populated by JavaScript -->
    <div id="products-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <!-- Products will be dynamically loaded here -->
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        <div class="flex items-center gap-2">
            <button class="px-4 py-2 border rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold">1</button>
            <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">2</button>
            <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">3</button>
            <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">4</button>
            <span class="px-2">...</span>
            <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">10</button>
            <button class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scroll-top" class="fixed bottom-8 right-8 w-12 h-12 bg-orange-500 text-white rounded-full shadow-lg hover:bg-orange-600 transition-all opacity-0 pointer-events-none z-50">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Mobile Messenger Button -->
    <div class="fixed bottom-20 right-8 lg:hidden z-40">
        <button id="messenger-btn" class="w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg flex items-center justify-center hover:bg-blue-700 transition-all">
            <i class="fas fa-comment-dots text-xl"></i>
        </button>
    </div>
</main>
