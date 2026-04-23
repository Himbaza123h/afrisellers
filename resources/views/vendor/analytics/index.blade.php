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
            <h1 class="text-2xl font-bold text-gray-900">Analytics Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Comprehensive overview of your business metrics</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <form method="GET" class="flex gap-2">
                <input type="text" name="date_range" id="dateRangePicker" value="{{ request('date_range') }}" readonly placeholder="Select date range" class="px-4 py-2 border border-gray-300 rounded-lg cursor-pointer">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-sync mr-2"></i>Update
                </button>
            </form>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
            <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $stats['active_products'] }} active</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
            <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
            <p class="text-xs text-gray-500 mt-2">This period</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-emerald-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
            <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">This period</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Customers</p>
            <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_customers']) }}</p>
            <p class="text-xs text-gray-500 mt-2">Unique buyers</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-amber-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Avg Order Value</p>
            <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_orders'] > 0 ? $stats['total_revenue'] / $stats['total_orders'] : 0, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">Per order</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Trend -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend (Last 30 Days)</h3>
            <div style="height: 300px;">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        <!-- Orders Trend -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Orders Trend (Last 30 Days)</h3>
            <div style="height: 300px;">
                <canvas id="ordersTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Order Status Breakdown -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Status Distribution</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $statusData = [
                    'pending' => ['label' => 'Pending', 'color' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-clock'],
                    'confirmed' => ['label' => 'Confirmed', 'color' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-check-circle'],
                    'processing' => ['label' => 'Processing', 'color' => 'bg-purple-100 text-purple-800', 'icon' => 'fa-sync'],
                    'shipped' => ['label' => 'Shipped', 'color' => 'bg-indigo-100 text-indigo-800', 'icon' => 'fa-shipping-fast'],
                    'delivered' => ['label' => 'Delivered', 'color' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-double'],
                    'cancelled' => ['label' => 'Cancelled', 'color' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle'],
                ];
            @endphp

            @foreach($statusData as $key => $data)
                <div class="p-4 {{ $data['color'] }} rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas {{ $data['icon'] }}"></i>
                        <span class="text-xs font-semibold uppercase">{{ $data['label'] }}</span>
                    </div>
                    <p class="text-2xl font-bold">{{ $orderStatusBreakdown[$key] ?? 0 }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Products -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Selling Products</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Product</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Sold</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($topProducts as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ Str::limit($product->name, 30) }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($product->total_quantity) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">${{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Customers</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Customer</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Total Spent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($topCustomers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-semibold text-blue-700">{{ substr($customer->buyer->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ Str::limit($customer->buyer->name ?? 'Unknown', 20) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $customer->order_count }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">${{ number_format($customer->total_spent, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
        datasets: [{
            label: 'Revenue',
            data: @json($revenueTrend->pluck('revenue')),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' $' + ctx.parsed.y.toFixed(2)
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
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>
@endsection
