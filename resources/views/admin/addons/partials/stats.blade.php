<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Total Addons</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-layer-group mr-1 text-[8px]"></i> All
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                <i class="fas fa-layer-group text-xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Global Addons</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['global'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-globe mr-1 text-[8px]"></i> Worldwide
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                <i class="fas fa-globe text-xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Country Specific</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['country_specific'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-map-marker-alt mr-1 text-[8px]"></i> Targeted
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                <i class="fas fa-map-marker-alt text-xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Active Subscriptions</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['active_subscriptions'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        <i class="fas fa-users mr-1 text-[8px]"></i> Active
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                <i class="fas fa-users text-xl text-orange-600"></i>
            </div>
        </div>
    </div>
</div>
