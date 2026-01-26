@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Platform Reports</h1>
            <p class="mt-1 text-xs text-gray-500">Comprehensive sales and performance reports</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('admin.reports.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
        <form method="GET" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Report Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="product" {{ $reportType == 'product' ? 'selected' : '' }}>Product</option>
                        <option value="vendor" {{ $reportType == 'vendor' ? 'selected' : '' }}>Vendor</option>
                        <option value="customer" {{ $reportType == 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                    <input type="text" name="date_range" id="dateRangePicker" value="{{ request('date_range') }}" readonly placeholder="Select date range" class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-sync text-sm"></i> Generate
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            Overview
        </button>
        <button onclick="switchTab('stats')" id="tab-stats" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Stats
        </button>
        <button onclick="switchTab('details')" id="tab-details" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Details
        </button>
        <button onclick="switchTab('performers')" id="tab-performers" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Top Performers
        </button>
    </div>

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Revenue</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                <i class="fas fa-dollar-sign mr-1 text-[8px]"></i> Revenue
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-xl text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Orders</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-shopping-cart mr-1 text-[8px]"></i> Orders
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-shopping-cart text-xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Vendors</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_vendors']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-store mr-1 text-[8px]"></i> Vendors
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-store text-xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Customers</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_customers']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-users mr-1 text-[8px]"></i> Customers
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                        <i class="fas fa-users text-xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Section -->
    <div id="overview-section" class="overview-container">
        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <!-- Revenue Trend -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Revenue Trend (Last 30 Days)</h3>
                <div style="height: 250px;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>

            <!-- Orders Trend -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Orders Trend (Last 30 Days)</h3>
                <div style="height: 250px;">
                    <canvas id="ordersTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Order Status & Payment Methods -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <!-- Order Status Distribution -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Order Status Distribution</h3>
                <div style="height: 250px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>

            <!-- Sales by Payment Method -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Sales by Payment Method</h3>
                <div class="space-y-2">
                    @forelse($salesByPaymentMethod as $payment)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ ucfirst($payment->payment_method ?? 'Unknown') }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->count }} transactions</p>
                            </div>
                            <p class="text-sm font-bold text-green-600">${{ number_format($payment->total, 2) }}</p>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">No payment data available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Details Section -->
    <div id="details-section" class="details-container hidden">
        <!-- Report Data Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">{{ ucfirst($reportType) }} Report Data</h3>
                <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                    {{ $reportData->count() }} {{ Str::plural('record', $reportData->count()) }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Period</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                            @if(!in_array($reportType, ['product', 'vendor', 'customer']))
                                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Transactions</th>
                            @else
                                @if($reportType == 'product')
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Quantity Sold</th>
                                @endif
                                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                                @if(in_array($reportType, ['vendor', 'customer']))
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Avg Order Value</th>
                                @endif
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($reportData as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-sm text-gray-900">{{ $data->period_label }}</td>
                                <td class="px-3 py-2 text-sm text-right font-semibold text-green-600">
                                    ${{ number_format($data->revenue, 2) }}
                                </td>
                                @if(!in_array($reportType, ['product', 'vendor', 'customer']))
                                    <td class="px-3 py-2 text-sm text-right text-gray-700">{{ $data->orders ?? 0 }}</td>
                                    <td class="px-3 py-2 text-sm text-right text-gray-700">{{ $data->transaction_count }}</td>
                                @else
                                    @if($reportType == 'product')
                                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ $data->quantity_sold ?? 0 }}</td>
                                    @endif
                                    <td class="px-3 py-2 text-sm text-right text-gray-700">{{ $data->order_count }}</td>
                                    @if(in_array($reportType, ['vendor', 'customer']))
                                        <td class="px-3 py-2 text-sm text-right text-gray-700">
                                            ${{ number_format($data->average_order_value ?? 0, 2) }}
                                        </td>
                                    @endif
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-gray-500">No data available for this period</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Performers Section -->
    <div id="performers-section" class="performers-container hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Top Products -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Top Products</h3>
                <div class="space-y-2">
                    @forelse($topProducts as $index => $product)
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center justify-center w-6 h-6 bg-blue-100 rounded text-blue-700 font-bold text-xs">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Str::limit($product->name, 22) }}</p>
                                <p class="text-xs text-gray-500">{{ $product->total_quantity }} sold</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-600">${{ number_format($product->total_revenue, 0) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Top Vendors -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Top Vendors</h3>
                <div class="space-y-2">
                    @forelse($topVendors as $index => $vendor)
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center justify-center w-6 h-6 bg-purple-100 rounded text-purple-700 font-bold text-xs">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Str::limit($vendor->vendor->name ?? 'Unknown', 22) }}</p>
                                <p class="text-xs text-gray-500">{{ $vendor->order_count }} orders</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-600">${{ number_format($vendor->total_revenue, 0) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Top Customers -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Top Customers</h3>
                <div class="space-y-2">
                    @forelse($topCustomers as $index => $customer)
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center justify-center w-6 h-6 bg-green-100 rounded text-green-700 font-bold text-xs">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Str::limit($customer->buyer->name ?? 'Unknown', 22) }}</p>
                                <p class="text-xs text-gray-500">{{ $customer->order_count }} orders</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-600">${{ number_format($customer->total_spent, 0) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">No data available</p>
                    @endforelse
                </div>
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

// Tab Switching
function switchTab(tab) {
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        btn.classList.add('text-gray-600');
    });

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

    // Show/hide sections
    const sections = ['stats', 'overview', 'details', 'performers'];
    sections.forEach(section => {
        const element = document.getElementById(`${section}-section`);
        if (element) {
            element.classList.add('hidden');
        }
    });

    const activeSection = document.getElementById(`${tab}-section`);
    if (activeSection) {
        activeSection.classList.remove('hidden');
    }
}

// Initialize with Overview tab active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('overview');
});

// Revenue Trend Chart
const revenueTrendCtx = document.getElementById('revenueTrendChart')?.getContext('2d');
if (revenueTrendCtx) {
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
                },
                legend: {
                    display: false
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
}

// Orders Trend Chart
const ordersTrendCtx = document.getElementById('ordersTrendChart')?.getContext('2d');
if (ordersTrendCtx) {
    new Chart(ordersTrendCtx, {
        type: 'bar',
        data: {
            labels: @json($ordersTrend->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))),
            datasets: [{
                label: 'Orders',
                data: @json($ordersTrend->pluck('count')),
                backgroundColor: '#3b82f6',
                borderRadius: 4
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
}

// Order Status Chart
const orderStatusCtx = document.getElementById('orderStatusChart')?.getContext('2d');
if (orderStatusCtx) {
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
}
</script>
@endpush

@endsection
