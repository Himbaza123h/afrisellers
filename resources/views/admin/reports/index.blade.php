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
            <h1 class="text-2xl font-bold text-gray-900">Platform Reports</h1>
            <p class="mt-1 text-sm text-gray-500">Comprehensive sales and performance reports</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                <i class="fas fa-print"></i>Print
            </button>
            <a href="{{ route('admin.reports.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                <i class="fas fa-download"></i>Export
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Daily Report</option>
                    <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                    <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                    <option value="product" {{ $reportType == 'product' ? 'selected' : '' }}>Product Report</option>
                    <option value="vendor" {{ $reportType == 'vendor' ? 'selected' : '' }}>Vendor Report</option>
                    <option value="customer" {{ $reportType == 'customer' ? 'selected' : '' }}>Customer Report</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <input type="text" name="date_range" id="dateRangePicker" value="{{ request('date_range') }}" readonly placeholder="Select date range" class="w-full px-4 py-2 border border-gray-300 rounded-lg cursor-pointer">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-sync"></i>Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-emerald-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">
                {{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            </p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
            <p class="text-xs text-gray-500 mt-2">
                Avg: ${{ number_format($stats['average_order_value'], 2) }}
            </p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Vendors</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_vendors']) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ number_format($stats['active_vendors']) }} active</p>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-orange-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Customers</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_customers']) }}</p>
            <p class="text-xs text-gray-500 mt-2">In selected period</p>
        </div>
    </div>

    <!-- Charts -->
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

    <!-- Order Status & Payment Methods -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Status Distribution -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Status Distribution</h3>
            <div style="height: 300px;">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>

        <!-- Sales by Payment Method -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales by Payment Method</h3>
            <div class="space-y-3">
                @forelse($salesByPaymentMethod as $payment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst($payment->payment_method ?? 'Unknown') }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->count }} transactions</p>
                        </div>
                        <p class="text-lg font-bold text-green-600">${{ number_format($payment->total, 2) }}</p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">No payment data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Report Data Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ ucfirst($reportType) }} Report Data</h3>
            <span class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                {{ $reportData->count() }} {{ Str::plural('record', $reportData->count()) }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Period</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                        @if(!in_array($reportType, ['product', 'vendor', 'customer']))
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Transactions</th>
                        @else
                            @if($reportType == 'product')
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Quantity Sold</th>
                            @endif
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                            @if(in_array($reportType, ['vendor', 'customer']))
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Avg Order Value</th>
                            @endif
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($reportData as $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $data->period_label }}</td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                                ${{ number_format($data->revenue, 2) }}
                            </td>
                            @if(!in_array($reportType, ['product', 'vendor', 'customer']))
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $data->orders ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $data->transaction_count }}</td>
                            @else
                                @if($reportType == 'product')
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $data->quantity_sold ?? 0 }}</td>
                                @endif
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $data->order_count }}</td>
                                @if(in_array($reportType, ['vendor', 'customer']))
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">
                                        ${{ number_format($data->average_order_value ?? 0, 2) }}
                                    </td>
                                @endif
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No data available for this period</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Performers -->
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

        <!-- Top Customers -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Customers</h3>
            <div class="space-y-3">
                @forelse($topCustomers as $index => $customer)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-lg text-green-700 font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ Str::limit($customer->buyer->name ?? 'Unknown', 25) }}</p>
                            <p class="text-xs text-gray-500">{{ $customer->order_count }} orders</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-600">${{ number_format($customer->total_spent, 0) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">No data available</p>
                @endforelse
            </div>
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
            tooltip: {
                callbacks: {
                    label: ctx => 'Revenue: $' + ctx.parsed.y.toFixed(2)
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

// Order Status Chart
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: @json(array_keys($orderStatusBreakdown)),
        datasets: [{
            data: @json(array_values($orderStatusBreakdown)),
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
</script>
@endpush

@endsection
