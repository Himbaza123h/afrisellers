<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Total Commissions</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-percentage mr-1 text-[8px]"></i> All time
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                <i class="fas fa-hand-holding-usd text-xl text-blue-600"></i>
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
                <p class="text-xs font-medium text-gray-600 mb-1">Approved</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['approved']) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-check mr-1 text-[8px]"></i> Ready
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                <i class="fas fa-check-circle text-xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Paid</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['paid']) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-dollar-sign mr-1 text-[8px]"></i> Complete
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                <i class="fas fa-money-check-alt text-xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Cancelled</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($stats['cancelled']) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times mr-1 text-[8px]"></i> Voided
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                <i class="fas fa-ban text-xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Financial Statistics -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mt-4">
    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Total Paid Out</p>
                <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_amount'], 2) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        <i class="fas fa-check-double mr-1 text-[8px]"></i> Completed
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
                <i class="fas fa-coins text-xl text-amber-600"></i>
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
                        <i class="fas fa-calendar mr-1 text-[8px]"></i> Count
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
                <p class="text-xs font-medium text-gray-600 mb-1">Month Total</p>
                <p class="text-lg font-bold text-gray-900">${{ number_format($stats['this_month_amount'], 2) }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                        <i class="fas fa-trending-up mr-1 text-[8px]"></i> Monthly
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg">
                <i class="fas fa-chart-line text-xl text-teal-600"></i>
            </div>
        </div>
    </div>
</div>
