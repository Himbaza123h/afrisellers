<!-- Statistics Cards - Main -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Total Transporters</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-truck mr-1 text-[10px]"></i> All time
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                <i class="fas fa-truck text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Active</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['active'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $stats['active_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">of total</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Verified</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['verified'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        {{ $stats['verified_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">verified</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl">
                <i class="fas fa-certificate text-2xl text-emerald-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Suspended</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['suspended'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ $stats['suspended_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">of total</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                <i class="fas fa-ban text-2xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Performance Statistics -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 mt-4">
    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Total Deliveries</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_deliveries']) }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                        <i class="fas fa-box mr-1 text-[10px]"></i> All time
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl">
                <i class="fas fa-boxes text-2xl text-violet-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Success Rate</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['success_rate'] }}%</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check mr-1 text-[10px]"></i> Delivered
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                <i class="fas fa-chart-line text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Avg Rating</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['avg_rating'] }}/5</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                        <i class="fas fa-star mr-1 text-[10px]"></i> Rating
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl">
                <i class="fas fa-star text-2xl text-amber-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Total Fleet</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_fleet_size']) }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                        <i class="fas fa-shipping-fast mr-1 text-[10px]"></i> Vehicles
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl">
                <i class="fas fa-truck-loading text-2xl text-cyan-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">This Month</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['this_month'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                        <i class="fas fa-calendar-alt mr-1 text-[10px]"></i> New
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl">
                <i class="fas fa-calendar-check text-2xl text-teal-600"></i>
            </div>
        </div>
    </div>
</div>
