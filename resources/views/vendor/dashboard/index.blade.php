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
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-br from-green-50 to-green-100 border border-green-200 text-green-700 rounded-lg font-medium shadow-sm">
                <span class="w-2 h-2 bg-green-600 rounded-full animate-pulse"></span>
                <span class="text-sm font-semibold">Store Active</span>
            </div>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-download"></i>
                <span>Export</span>
            </button>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('vendor.dashboard.home') }}" class="flex flex-wrap items-center gap-3">
            <label class="text-sm font-medium text-gray-700">Filter by:</label>

            <!-- Period Buttons -->
            <div class="flex gap-2">
                <button type="submit" name="filter" value="weekly" class="px-3 py-1.5 text-xs font-medium {{ (!request('filter') || request('filter') == 'weekly') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all">
                    Weekly
                </button>
                <button type="submit" name="filter" value="monthly" class="px-3 py-1.5 text-xs font-medium {{ request('filter') == 'monthly' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all">
                    Monthly
                </button>
                <button type="submit" name="filter" value="yearly" class="px-3 py-1.5 text-xs font-medium {{ request('filter') == 'yearly' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all">
                    Yearly
                </button>
            </div>

            <!-- Custom Date Range -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">or</span>
                <div class="relative">
                    <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Custom date range" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-64 cursor-pointer bg-white text-sm">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>
                <input type="hidden" name="filter" value="custom" id="customFilterInput">
            </div>

            @if(request()->hasAny(['filter', 'date_range']))
                <a href="{{ route('vendor.dashboard.home') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-xs transition-all">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- My Revenue -->
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">My Revenue</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($myRevenue, 2) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $revenuePercentage >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas fa-arrow-{{ $revenuePercentage >= 0 ? 'up' : 'down' }} mr-1 text-[10px]"></i> {{ abs($revenuePercentage) }}%
                        </span>
                        <span class="text-xs text-gray-500">vs previous period</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                    <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- My Products -->
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">My Products</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($totalProducts) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-check-circle mr-1 text-[10px]"></i> {{ $activeProducts }} Active
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-boxes text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- My Orders -->
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">My Orders</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($myOrders) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ordersPercentage >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas fa-arrow-{{ $ordersPercentage >= 0 ? 'up' : 'down' }} mr-1 text-[10px]"></i> {{ abs($ordersPercentage) }}%
                        </span>
                        <span class="text-xs text-gray-500">vs previous period</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-shopping-bag text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending Orders</p>
                    <p class="text-lg font-bold text-gray-900">{{ $pendingOrders }}</p>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pendingOrders > 0 ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                            <i class="fas fa-{{ $pendingOrders > 0 ? 'exclamation-circle' : 'check-circle' }} mr-1 text-[10px]"></i> {{ $pendingOrders > 0 ? 'Action Needed' : 'All Clear' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                    <i class="fas fa-clock text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Performance Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Sales Performance</h3>
                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                    {{ ucfirst(request('filter', 'weekly')) }} View
                </span>
            </div>
            <div class="h-[320px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Recent Orders</h3>
                <a href="{{ route('vendor.orders.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All</a>
            </div>
            <div class="space-y-4">
                @forelse($recentOrders as $order)
                <div class="flex items-start gap-3 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-{{ $order['color'] }}-50 to-{{ $order['color'] }}-100 rounded-lg flex-shrink-0">
                        <i class="fas fa-{{ $order['icon'] }} text-{{ $order['color'] }}-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $order['product'] }}</p>
                        <p class="text-xs text-gray-500 mb-1">Order #{{ $order['id'] }}</p>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-bold text-gray-900">${{ number_format($order['amount'], 2) }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $order['status_color'] }}-100 text-{{ $order['status_color'] }}-800">
                                {{ $order['status'] }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-shopping-cart text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-900 mb-1">No recent orders</p>
                    <p class="text-xs text-gray-500">Orders will appear here</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Performing Products -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Top Performing Products</h3>
            <a href="{{ route('vendor.product.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All Products</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($topProducts as $product)
            <div class="p-5 bg-gradient-to-br from-{{ $product['color'] }}-50 to-white rounded-xl border border-{{ $product['color'] }}-100">
                <div class="mb-3">
                    <p class="text-sm font-bold text-gray-900 mb-1 truncate">{{ $product['name'] }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($product['revenue'], 2) }}</p>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                    <div class="bg-{{ $product['color'] }}-600 h-2 rounded-full transition-all duration-500" style="width: {{ $product['percentage'] }}%"></div>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $product['badge_color'] ?? $product['color'] }}-100 text-{{ $product['badge_color'] ?? $product['color'] }}-800">
                        <i class="fas fa-box mr-1 text-[10px]"></i> {{ $product['status'] }}
                    </span>
                </div>
                <p class="text-xs text-gray-600">{{ number_format($product['sales']) }} Sales • {{ number_format($product['stock']) }} Stock</p>
            </div>
            @empty
            <div class="col-span-full flex flex-col items-center justify-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-box text-2xl text-gray-300"></i>
                </div>
                <p class="text-sm font-medium text-gray-900 mb-1">No product sales yet</p>
                <p class="text-xs text-gray-500">Start selling to see top products</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Order Status Overview -->
    <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Order Status Overview</h3>
                <a href="{{ route('vendor.orders.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All Orders</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Count</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Revenue</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Avg. Value</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($orderStatuses as $orderStatus)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-{{ $orderStatus['color'] }}-50 to-{{ $orderStatus['color'] }}-100 rounded-lg">
                                    <i class="fas fa-{{ $orderStatus['icon'] }} text-{{ $orderStatus['color'] }}-600"></i>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $orderStatus['status'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-900">{{ number_format($orderStatus['count']) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-900">${{ number_format($orderStatus['revenue'], 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">${{ number_format($orderStatus['avg'], 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('vendor.orders.index', ['status' => strtolower($orderStatus['status'])]) }}" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                                View <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize Flatpickr for custom date range
    flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        showMonths: 2,
        locale: { rangeSeparator: " to " },
        onClose: function(dates, str, inst) {
            if (dates.length === 2) {
                // Set the filter to custom and submit
                document.getElementById('customFilterInput').value = 'custom';
                inst.element.closest('form').submit();
            }
        }
    });

    // Sales Chart
    const ctx = document.getElementById('salesChart');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($salesChartData['labels']),
            datasets: [{
                label: 'Sales ($)',
                data: @json($salesChartData['sales']),
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderColor: '#3b82f6',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }, {
                label: 'Orders',
                data: @json($salesChartData['orders']),
                backgroundColor: 'rgba(147, 51, 234, 0.1)',
                borderColor: '#9333ea',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#9333ea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 },
                        boxWidth: 40,
                        boxHeight: 12,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    enabled: true,
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 12 }
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
</script>
@endpush
@endsection
