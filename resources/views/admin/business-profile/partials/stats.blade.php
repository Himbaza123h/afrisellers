<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Total Applications</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-file-alt mr-1 text-[10px]"></i> All time
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                <i class="fas fa-store text-xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Pending Review</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ $stats['pending_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">of total</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                <i class="fas fa-clock text-xl text-yellow-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Verified</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['verified'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $stats['verified_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">of total</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                <i class="fas fa-check-circle text-xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Rejected</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['rejected'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times mr-1 text-[10px]"></i> Review
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                <i class="fas fa-times-circle text-xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Time-based Statistics -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mt-4">
    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">Today's Applications</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['today'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        <i class="fas fa-calendar-day mr-1 text-[10px]"></i> Today
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl">
                <i class="fas fa-calendar-check text-xl text-indigo-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">This Week</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['this_week'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-calendar-week mr-1 text-[10px]"></i> 7 days
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                <i class="fas fa-chart-line text-xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">This Month</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month'] }}</p>
                <div class="mt-3 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                        <i class="fas fa-calendar-alt mr-1 text-[10px]"></i> 30 days
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl">
                <i class="fas fa-chart-bar text-xl text-teal-600"></i>
            </div>
        </div>
    </div>
</div>
