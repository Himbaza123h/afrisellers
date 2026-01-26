<!-- Quick Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
    <!-- Global Revenue -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-globe text-lg text-green-600"></i>
            </div>
            <span class="text-green-600 text-xs font-bold">This Month</span>
        </div>
        <p class="text-gray-600 text-xs mb-1">Global Revenue</p>
        <p class="text-2xl font-black text-gray-900">${{ number_format($globalRevenue, 0) }}</p>
    </div>

    <!-- Active Vendors -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-store text-lg text-blue-600"></i>
            </div>
            <span class="text-blue-600 text-xs font-bold">Active</span>
        </div>
        <p class="text-gray-600 text-xs mb-1">Total Vendors</p>
        <p class="text-2xl font-black text-gray-900">{{ number_format($activeVendors) }}</p>
    </div>

    <!-- Total Orders -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-shopping-cart text-lg text-purple-600"></i>
            </div>
            <span class="text-purple-600 text-xs font-bold">This Month</span>
        </div>
        <p class="text-gray-600 text-xs mb-1">Total Orders</p>
        <p class="text-2xl font-black text-gray-900">{{ number_format($totalOrders) }}</p>
    </div>

    <!-- Pending Approvals -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-lg text-orange-600"></i>
            </div>
            <span class="text-orange-600 text-xs font-bold">Action Needed</span>
        </div>
        <p class="text-gray-600 text-xs mb-1">Pending Approvals</p>
        <p class="text-2xl font-black text-gray-900">{{ $pendingApprovals }}</p>
    </div>
</div>

<!-- Regional Statistics -->
<div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-gray-900">Regional Statistics</h3>
        <a href="{{ route('admin.regional-admins.index') }}" class="text-[#ff0808] font-bold hover:underline text-xs">View Details</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach($regionalStats as $region)
        <div class="p-3 bg-gradient-to-br from-{{ $region['color'] }}-50 to-white rounded-lg border border-{{ $region['color'] }}-100">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-900">{{ $region['name'] }}</span>
                <span class="text-sm font-black text-gray-900">${{ number_format($region['revenue']/1000) }}k</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                <div class="bg-{{ $region['color'] }}-600 h-1.5 rounded-full" style="width: {{ $region['percentage'] }}%"></div>
            </div>
            <span class="inline-block text-[10px] bg-{{ $region['badge_color'] ?? $region['color'] }}-600 text-white px-2 py-0.5 rounded-full font-bold mb-1.5">{{ $region['status'] }}</span>
            <p class="text-[10px] text-gray-600">{{ number_format($region['vendors']) }} Vendors • {{ number_format($region['orders']) }} Orders</p>
        </div>
        @endforeach
    </div>
</div>
