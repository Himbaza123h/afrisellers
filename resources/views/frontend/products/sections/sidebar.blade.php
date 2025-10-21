<aside class="w-full lg:w-64 flex-shrink-0">
    <div class="bg-white rounded-lg border p-6 sticky top-24">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Filters</h2>

        <!-- Trade Assurance -->
        <div class="mb-6 pb-6 border-b">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-5 h-5 bg-yellow-400 rounded flex items-center justify-center mt-0.5">
                    <i class="fas fa-shield-alt text-white text-xs"></i>
                </div>
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" class="mr-2 filter-checkbox">
                        <span class="font-semibold text-gray-900">Trade Assurance</span>
                    </label>
                    <p class="text-sm text-gray-600 mt-1">Protects your orders on Alibaba.com</p>
                </div>
            </div>
        </div>

        <!-- Supplier Features -->
        <div class="mb-6 pb-6 border-b">
            <h3 class="font-bold text-gray-900 mb-4">Supplier features</h3>
            <div class="space-y-3">
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" class="mr-2 filter-checkbox">
                    <span class="text-blue-600 font-medium group-hover:underline">Verified Supplier</span>
                    <i class="fas fa-info-circle text-gray-400 ml-1 text-xs"></i>
                </label>
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" class="mr-2 filter-checkbox">
                    <span class="text-blue-600 font-medium group-hover:underline">Verified <span class="bg-blue-600 text-white text-xs px-1 rounded">PRO</span> Supplier</span>
                    <i class="fas fa-info-circle text-gray-400 ml-1 text-xs"></i>
                </label>
            </div>
        </div>

        <!-- Merge Results -->
        <div class="mb-6 pb-6 border-b">
            <h3 class="font-bold text-gray-900 mb-4">Merge results</h3>
            <label class="flex items-start cursor-pointer">
                <input type="checkbox" class="mr-2 mt-1 filter-checkbox">
                <div>
                    <span class="font-medium text-gray-900 block">Merge by supplier</span>
                    <span class="text-sm text-gray-600">Only the most relevant item from each supplier will be shown</span>
                </div>
            </label>
        </div>

        <!-- Store Reviews -->
        <div class="mb-6 pb-6 border-b">
            <h3 class="font-bold text-gray-900 mb-4">Store reviews</h3>
            <p class="text-sm text-gray-600 mb-3">Based on a 5-star rating system</p>
            <div class="space-y-2">
                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                    <input type="radio" name="rating" class="mr-2 filter-radio">
                    <span class="text-gray-700">4.0 & up</span>
                </label>
                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                    <input type="radio" name="rating" class="mr-2 filter-radio">
                    <span class="text-gray-700">4.5 & up</span>
                </label>
                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                    <input type="radio" name="rating" class="mr-2 filter-radio">
                    <span class="text-gray-700">5.0</span>
                </label>
            </div>
        </div>

        <!-- Product Features -->
        <div class="mb-6 pb-6 border-b">
            <h3 class="font-bold text-gray-900 mb-4">Product features</h3>
            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                <input type="checkbox" class="mr-2 filter-checkbox">
                <span class="text-gray-700">Paid samples</span>
            </label>
        </div>

        <!-- Clear Filters Button -->
        <button class="clear-filters-btn w-full bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold py-2 px-4 rounded-lg transition-colors">
            Clear all filters
        </button>
    </div>
</aside>
