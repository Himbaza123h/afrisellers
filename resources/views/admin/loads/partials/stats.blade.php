<!-- Statistics Cards - Main -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Total Loads</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-truck mr-1 text-[8px]"></i> All time
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                <i class="fas fa-truck-loading text-xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Posted</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['posted'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        <i class="fas fa-bullhorn mr-1 text-[8px]"></i> Active
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                <i class="fas fa-bullhorn text-xl text-indigo-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">In Transit</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['in_transit'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        {{ $stats['in_transit_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">of total</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                <i class="fas fa-shipping-fast text-xl text-orange-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Delivered</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['delivered'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $stats['delivered_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">completed</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                <i class="fas fa-check-circle text-xl text-green-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Performance Statistics -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 mt-4">
    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Cancelled</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['cancelled'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-ban mr-1 text-[8px]"></i> Lost
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                <i class="fas fa-times-circle text-xl text-red-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Total Bids</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_bids']) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-gavel mr-1 text-[8px]"></i> Bids
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                <i class="fas fa-gavel text-xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Avg Bids/Load</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['avg_bids'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                        <i class="fas fa-chart-line mr-1 text-[8px]"></i> Average
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-lg">
                <i class="fas fa-chart-line text-xl text-cyan-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Total Weight</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['total_weight'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                        <i class="fas fa-weight mr-1 text-[8px]"></i> KG
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg">
                <i class="fas fa-weight-hanging text-xl text-teal-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">This Month</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['this_month'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                        <i class="fas fa-calendar-check mr-1 text-[8px]"></i> New
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg">
                <i class="fas fa-calendar-check text-xl text-pink-600"></i>
            </div>
        </div>
    </div>
</div>
