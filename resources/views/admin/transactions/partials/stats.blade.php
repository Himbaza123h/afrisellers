<!-- Statistics Cards - Main -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Total Transactions</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-exchange-alt mr-1 text-[8px]"></i> All time
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                <i class="fas fa-receipt text-xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Pending</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-1 text-[8px]"></i> Awaiting
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                <i class="fas fa-hourglass-half text-xl text-yellow-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Completed</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['completed']) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $stats['completed_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">success rate</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                <i class="fas fa-check-circle text-xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Failed</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['failed']) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ $stats['failed_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">failure rate</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                <i class="fas fa-times-circle text-xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Financial Statistics -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 mt-4">
    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Total Revenue</p>
                <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_amount'], 2) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        <i class="fas fa-dollar-sign mr-1 text-[8px]"></i> Completed
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg">
                <i class="fas fa-money-bill-wave text-xl text-emerald-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Pending Amount</p>
                <p class="text-lg font-bold text-gray-900">${{ number_format($stats['pending_amount'], 2) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                        <i class="fas fa-spinner mr-1 text-[8px]"></i> Pending
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg">
                <i class="fas fa-hand-holding-usd text-xl text-amber-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Avg Transaction</p>
                <p class="text-lg font-bold text-gray-900">${{ number_format($stats['avg_transaction'] ?? 0, 2) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                        <i class="fas fa-chart-line mr-1 text-[8px]"></i> Average
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-lg">
                <i class="fas fa-chart-bar text-xl text-cyan-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">This Month</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['this_month']) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        <i class="fas fa-calendar mr-1 text-[8px]"></i> Transactions
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                <i class="fas fa-calendar-alt text-xl text-indigo-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Month Revenue</p>
                <p class="text-lg font-bold text-gray-900">${{ number_format($stats['this_month_amount'], 2) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                        <i class="fas fa-trending-up mr-1 text-[8px]"></i> Monthly
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg">
                <i class="fas fa-chart-pie text-xl text-teal-600"></i>
            </div>
        </div>
    </div>
</div>
