@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Platform Analytics</h1>
            <p class="mt-1 text-sm text-gray-500">Comprehensive overview of platform performance and metrics</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <form method="GET" class="flex gap-2">
                <input type="text" name="date_range" id="dateRangePicker" value="{{ request('date_range') }}" readonly placeholder="Select date range" class="px-4 py-2 border border-gray-300 rounded-lg cursor-pointer">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-sync"></i>Update
                </button>
            </form>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                <i class="fas fa-print"></i>Print
            </button>
        </div>
    </div>

    <!-- Key Metrics Overview -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">+{{ $stats['new_users'] }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ number_format($stats['active_users']) }} active</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-purple-600 text-xl"></i>
                </div>
                <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">+{{ $stats['new_vendors'] }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Vendors</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_vendors']) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ number_format($stats['verified_vendors']) }} verified</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-emerald-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-orange-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ number_format($stats['completed_orders']) }} completed</p>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Products</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-question-circle text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">RFQs</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_rfqs']) }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-home text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Showrooms</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_showrooms']) }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tradeshows</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_tradeshows']) }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-handshake text-amber-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Escrows</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($escrowStats['total_escrows']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-mouse-pointer text-blue-600 text-xl"></i>
                <p class="text-sm font-semibold text-blue-900">Total Clicks</p>
            </div>
            <p class="text-2xl font-bold text-blue-900">{{ number_format($performanceMetrics['total_clicks']) }}</p>
        </div>

        <div class="stat-card p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-eye text-purple-600 text-xl"></i>
                <p class="text-sm font-semibold text-purple-900">Total Impressions</p>
            </div>
            <p class="text-2xl font-bold text-purple-900">{{ number_format($performanceMetrics['total_impressions']) }}</p>
        </div>

        <div class="stat-card p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-percentage text-green-600 text-xl"></i>
                <p class="text-sm font-semibold text-green-900">Average CTR</p>
            </div>
            <p class="text-2xl font-bold text-green-900">{{ $performanceMetrics['ctr'] }}%</p>
        </div>

        <div class="stat-card p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                <p class="text-sm font-semibold text-orange-900">Activity Score</p>
            </div>
            <p class="text-2xl font-bold text-orange-900">{{ number_format($platformActivity['product_views'] + $platformActivity['product_clicks']) }}</p>
        </div>
    </div>

    <!-- Main Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Trend -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Revenue & Fees Trend</h3>
                <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Last 30 Days</span>
            </div>
            <div style="height: 300px;">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        <!-- Orders Trend -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Orders Trend</h3>
                <span class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Last 30 Days</span>
            </div>
            <div style="height: 300px;">
                <canvas id="ordersTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- User Growth & Transaction Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Growth -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Growth Trend</h3>
            <div style="height: 300px;">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Status Distribution</h3>
            <div style="height: 300px;">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Transaction & Escrow Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transaction Stats -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transaction Statistics</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Successful</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($transactionStats['successful']) }}</p>
                </div>
                <div class="p-4 bg-yellow-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($transactionStats['pending']) }}</p>
                </div>
                <div class="p-4 bg-red-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Failed</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($transactionStats['failed']) }}</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Refunded</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($transactionStats['refunded']) }}</p>
                </div>
            </div>
        </div>

        <!-- Escrow Stats -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Escrow Statistics</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Active</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($escrowStats['active']) }}</p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Released</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($escrowStats['released']) }}</p>
                </div>
                <div class="p-4 bg-red-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Disputed</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($escrowStats['disputed']) }}</p>
                </div>
                <div class="p-4 bg-amber-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Total Held</p>
                    <p class="text-lg font-bold text-amber-600">${{ number_format($escrowStats['total_held'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Products -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Products</h3>
            <div class="space-y-3">
                @forelse($topProducts as $index => $product)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg text-blue-700 font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ Str::limit($product->name, 25) }}</p>
                            <p class="text-xs text-gray-500">{{ $product->total_quantity }} sold</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-600">${{ number_format($product->total_revenue, 0) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- Top Vendors -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Vendors</h3>
            <div class="space-y-3">
                @forelse($topVendors as $index => $vendor)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-lg text-purple-700 font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ Str::limit($vendor->vendor->name ?? 'Unknown', 25) }}</p>
                            <p class="text-xs text-gray-500">{{ $vendor->order_count }} orders</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-600">${{ number_format($vendor->total_revenue, 0) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- Top Buyers -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Buyers</h3>
            <div class="space-y-3">
                @forelse($topBuyers as $index => $buyer)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-lg text-green-700 font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ Str::limit($buyer->buyer->name ?? 'Unknown', 25) }}</p>
                            <p class="text-xs text-gray-500">{{ $buyer->order_count }} orders</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-600">${{ number_format($buyer->total_spent, 0) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Regional Performance -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Regional Performance</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Country</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Vendors</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Products</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($regionalStats as $country)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">{{ $country->flag }}</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $country->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($country->vendors_count) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($country->products_count) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($country->orders_count) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">${{ number_format($country->total_revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No regional data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Category Distribution</h3>
        <div style="height: 300px;">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Date Range Picker
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    locale: { rangeSeparator: " to " }
});

// Revenue Trend Chart
const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
new Chart(revenueTrendCtx, {
    type: 'line',
    data: {
        labels: @json($revenueTrend->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))),
        datasets: [
            {
                label: 'Revenue',
                data: @json($revenueTrend->pluck('revenue')),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            },
            {
                label: 'Platform Fees',
                data: @json($revenueTrend->pluck('fees')),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': $' + ctx.parsed.y.toFixed(2)
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: value => '$' + value }
            }
        }
    }
});

// Orders Trend Chart
const ordersTrendCtx = document.getElementById('ordersTrendChart').getContext('2d');
new Chart(ordersTrendCtx, {
    type: 'bar',
    data: {
        labels: @json($ordersTrend->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))),
        datasets: [{
            label: 'Orders',
            data: @json($ordersTrend->pluck('count')),
            backgroundColor: '#3b82f6',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userGrowthCtx, {
    type: 'line',
    data: {
        labels: @json($userGrowthTrend->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))),
        datasets: [{
            label: 'New Users',
            data: @json($userGrowthTrend->pluck('count')),
            borderColor: '#8b5cf6',
            backgroundColor: 'rgba(139, 92, 246, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Order Status Chart
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: @json($orderStatusDistribution->keys()),
        datasets: [{
            data: @json($orderStatusDistribution->values()),
            backgroundColor: [
                '#fbbf24',
                '#3b82f6',
                '#8b5cf6',
                '#6366f1',
                '#10b981',
                '#ef4444'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: @json($categoryDistribution->pluck('name')),
        datasets: [{
            label: 'Products',
            data: @json($categoryDistribution->pluck('count')),
            backgroundColor: '#6366f1',
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>
@endpush

@endsection
