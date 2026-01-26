@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $country->name }} Dashboard</h1>
            <p class="mt-1 text-xs text-gray-500">Country performance overview and analytics</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <form method="GET" action="{{ route('country.dashboard.home') }}" class="inline-flex">
                <select name="period" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] text-sm">
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $period === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="this_week" {{ $period === 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="last_week" {{ $period === 'last_week' ? 'selected' : '' }}>Last Week</option>
                    <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_quarter" {{ $period === 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                    <option value="last_quarter" {{ $period === 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
                    <option value="this_year" {{ $period === 'this_year' ? 'selected' : '' }}>This Year</option>
                    <option value="last_year" {{ $period === 'last_year' ? 'selected' : '' }}>Last Year</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Vendors -->
        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Vendors</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_vendors']) }}</p>
                    <div class="mt-2 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['active_vendors'] }} Active
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            {{ $stats['pending_vendors'] }} Pending
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                    <i class="fas fa-store text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Products</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                    <i class="fas fa-box text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Showrooms -->
        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Showrooms</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_showrooms']) }}</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                    <i class="fas fa-building text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Period Revenue -->
        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Period Revenue</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($stats['period_revenue'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst(str_replace('_', ' ', $period)) }}</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                    <i class="fas fa-dollar-sign text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Total Transporters -->
        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Transporters</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_transporters']) }}</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                    <i class="fas fa-truck text-yellow-600"></i>
                </div>
            </div>
        </div>

        <!-- Period Orders -->
        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Period Orders</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['period_orders']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst(str_replace('_', ' ', $period)) }}</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                    <i class="fas fa-shopping-cart text-indigo-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Loads -->
        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Loads</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_loads']) }}</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg">
                    <i class="fas fa-boxes text-teal-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Vendor Growth Chart -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Vendor Growth</h3>
                <span class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                    {{ ucfirst(str_replace('_', ' ', $period)) }}
                </span>
            </div>
            <div class="h-64">
                <canvas id="vendorGrowthChart"></canvas>
            </div>
        </div>

        <!-- Product Growth Chart -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Product Growth</h3>
                <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                    {{ ucfirst(str_replace('_', ' ', $period)) }}
                </span>
            </div>
            <div class="h-64">
                <canvas id="productGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Revenue Trend</h3>
                <span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                    {{ ucfirst(str_replace('_', ' ', $period)) }}
                </span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Order Status Chart -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Order Status Distribution</h3>
            </div>
            <div class="h-64">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Top Categories -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Top Product Categories</h3>
                <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-full">
                    {{ $topCategories->count() }} categories
                </span>
            </div>
            @if($topCategories->count() > 0)
                <div class="space-y-3">
                    @foreach($topCategories as $category)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tag text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $category['name'] }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                {{ $category['count'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-tag text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No categories data</p>
                    <p class="text-xs text-gray-400 mt-1">Try selecting a different period</p>
                </div>
            @endif
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Pending Vendor Approvals</h3>
                <span class="px-2 py-1 text-xs font-medium text-orange-700 bg-orange-100 rounded-full">
                    {{ $pendingApprovals->count() }} pending
                </span>
            </div>
            @if($pendingApprovals->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingApprovals->take(5) as $profile)
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg hover:bg-orange-100">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-orange-600 text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $profile->business_name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $profile->user->email }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-medium">
                                Pending
                            </span>
                        </div>
                    @endforeach
                    @if($pendingApprovals->count() > 5)
                        <a href="#" class="block text-center text-sm font-medium text-blue-600 hover:text-blue-700 py-2">
                            View all {{ $pendingApprovals->count() }} pending approvals →
                        </a>
                    @endif
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-check text-2xl text-green-600"></i>
                    </div>
                    <p class="text-gray-500 font-medium">All caught up!</p>
                    <p class="text-xs text-gray-400 mt-1">No pending vendor approvals</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Recent Vendors -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Recent Vendors</h3>
                <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-full">
                    Last 30 days
                </span>
            </div>
            @if($recentVendors->count() > 0)
                <div class="space-y-3">
                    @foreach($recentVendors->take(5) as $vendor)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-store text-gray-600 text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $vendor->businessProfile->business_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $vendor->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $vendor->account_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($vendor->account_status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-store text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No recent vendors</p>
                    <p class="text-xs text-gray-400 mt-1">Vendors will appear here</p>
                </div>
            @endif
        </div>

        <!-- Recent Products -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Recent Products</h3>
                <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-full">
                    Last 30 days
                </span>
            </div>
            @if($recentProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($recentProducts->take(5) as $product)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                @php
                                    $img = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                @endphp
                                @if($img)
                                    <img src="{{ $img->thumbnail_url ?? $img->image_url }}" alt="{{ $product->name }}"
                                        class="w-8 h-8 rounded-md object-cover border border-gray-200">
                                @else
                                    <div class="w-8 h-8 bg-gray-100 rounded-md flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-xs"></i>
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->productCategory->name ?? 'Uncategorized' }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $product->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-box text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No recent products</p>
                    <p class="text-xs text-gray-400 mt-1">Products will appear here</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Vendor Growth Chart
    const vendorCtx = document.getElementById('vendorGrowthChart');
    if (vendorCtx) {
        new Chart(vendorCtx, {
            type: 'line',
            data: {
                labels: @json($vendorGrowth['labels']),
                datasets: [{
                    label: 'New Vendors',
                    data: @json($vendorGrowth['data']),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 11 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // Product Growth Chart
    const productCtx = document.getElementById('productGrowthChart');
    if (productCtx) {
        new Chart(productCtx, {
            type: 'line',
            data: {
                labels: @json($productGrowth['labels']),
                datasets: [{
                    label: 'New Products',
                    data: @json($productGrowth['data']),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 11 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: @json($revenueChart['labels']),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json($revenueChart['data']),
                    backgroundColor: '#ff0808',
                    borderColor: '#ff0808',
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverBackgroundColor: '#e60000'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 11 },
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 11 },
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart');
    if (orderStatusCtx) {
        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($orderStatusChart['labels']),
                datasets: [{
                    data: @json($orderStatusChart['data']),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#6b7280'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 11 },
                            boxWidth: 12,
                            boxHeight: 12
                        }
                    },
                    tooltip: {
                        padding: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 11 }
                    }
                },
                cutout: '60%'
            }
        });
    }
</script>
@endpush
@endsection
