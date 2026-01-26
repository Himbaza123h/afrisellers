<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Total Administrators</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-users mr-1 text-[10px]"></i> All time
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                <i class="fas fa-user-shield text-xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Active Admins</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['active'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $stats['active_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">of total</span>
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
                <p class="text-xs font-medium text-gray-600 mb-1">Inactive</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $stats['inactive_percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-500">of total</span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg">
                <i class="fas fa-pause-circle text-xl text-gray-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Suspended</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['suspended'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-exclamation-triangle mr-1 text-[10px]"></i> Review
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                <i class="fas fa-ban text-xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Region Coverage Cards -->
<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 mt-3">
    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Regions Covered</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['regions_covered'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        <i class="fas fa-map-marked-alt mr-1 text-[10px]"></i> Active assignments
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg">
                <i class="fas fa-globe text-xl text-emerald-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-600 mb-1">Unassigned Regions</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['unassigned_regions'] }}</p>
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-exclamation-circle mr-1 text-[10px]"></i> Needs assignment
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                <i class="fas fa-map text-xl text-yellow-600"></i>
            </div>
        </div>
    </div>
</div>
