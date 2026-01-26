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
            <h1 class="text-xl font-bold text-gray-900">Platform Analytics</h1>
            <p class="mt-1 text-xs text-gray-500">Comprehensive overview of platform performance</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('admin.analytics.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <form method="GET" class="flex gap-2">
                <input type="text" name="date_range" id="dateRangePicker" value="{{ request('date_range') }}" readonly placeholder="Select date range" class="px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    <i class="fas fa-sync text-sm"></i>Update
                </button>
            </form>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            Overview
        </button>
        <button onclick="switchTab('stats')" id="tab-stats" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Stats
        </button>
        <button onclick="switchTab('trends')" id="tab-trends" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Trends
        </button>
        <button onclick="switchTab('performers')" id="tab-performers" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Top Performers
        </button>
        <button onclick="switchTab('regional')" id="tab-regional" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Regional
        </button>
    </div>

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container hidden">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Users</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-users mr-1 text-[8px]"></i> +{{ $stats['new_users'] }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-users text-xl text-blue-600"></i>
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
                                <i class="fas fa-store mr-1 text-[8px]"></i> {{ $stats['verified_vendors'] }} verified
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
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Revenue</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</p>
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
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-shopping-cart mr-1 text-[8px]"></i> {{ $stats['completed_orders'] }} completed
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                        <i class="fas fa-shopping-cart text-xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Metrics -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 mt-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-question-circle text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">RFQs</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_rfqs']) }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-home text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Showrooms</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_showrooms']) }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tradeshows</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_tradeshows']) }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2">
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
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mt-4">
            <div class="stat-card p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-mouse-pointer text-blue-600"></i>
                    <p class="text-sm font-semibold text-blue-900">Total Clicks</p>
                </div>
                <p class="text-xl font-bold text-blue-900">{{ number_format($performanceMetrics['total_clicks']) }}</p>
            </div>

            <div class="stat-card p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-eye text-purple-600"></i>
                    <p class="text-sm font-semibold text-purple-900">Total Impressions</p>
                </div>
                <p class="text-xl font-bold text-purple-900">{{ number_format($performanceMetrics['total_impressions']) }}</p>
            </div>

            <div class="stat-card p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-percentage text-green-600"></i>
                    <p class="text-sm font-semibold text-green-900">Average CTR</p>
                </div>
                <p class="text-xl font-bold text-green-900">{{ $performanceMetrics['ctr'] }}%</p>
            </div>

            <div class="stat-card p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg border border-orange-200 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-chart-line text-orange-600"></i>
                    <p class="text-sm font-semibold text-orange-900">Activity Score</p>
                </div>
                <p class="text-xl font-bold text-orange-900">{{ number_format($platformActivity['product_views'] + $platformActivity['product_clicks']) }}</p>
            </div>
        </div>

        <!-- Transaction & Escrow Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <!-- Transaction Stats -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Transaction Statistics</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-green-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Successful</p>
                        <p class="text-lg font-bold text-green-600">{{ number_format($transactionStats['successful']) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Pending</p>
                        <p class="text-lg font-bold text-yellow-600">{{ number_format($transactionStats['pending']) }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Failed</p>
                        <p class="text-lg font-bold text-red-600">{{ number_format($transactionStats['failed']) }}</p>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Refunded</p>
                        <p class="text-lg font-bold text-orange-600">{{ number_format($transactionStats['refunded']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Escrow Stats -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Escrow Statistics</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Active</p>
                        <p class="text-lg font-bold text-blue-600">{{ number_format($escrowStats['active']) }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Released</p>
                        <p class="text-lg font-bold text-green-600">{{ number_format($escrowStats['released']) }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Disputed</p>
                        <p class="text-lg font-bold text-red-600">{{ number_format($escrowStats['disputed']) }}</p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Total Held</p>
                        <p class="text-base font-bold text-amber-600">${{ number_format($escrowStats['total_held'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Section -->
    <div id="overview-section" class="overview-container">
        <!-- Main Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Revenue Trend -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Revenue & Fees Trend</h3>
                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Last 30 Days</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>

            <!-- Orders Trend -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Orders Trend</h3>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Last 30 Days</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="ordersTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- User Growth & Order Status -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <!-- User Growth -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">User Growth Trend</h3>
                <div style="height: 250px;">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>

            <!-- Order Status Distribution -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Order Status Distribution</h3>
                <div style="height: 250px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Trends Section -->
    <div id="trends-section" class="trends-container hidden">
        <!-- Category Distribution -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Product Category Distribution</h3>
            <div style="height: 300px;">
                <canvas id="categoryChart"></canvas>
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

            <!-- Top Buyers -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Top Buyers</h3>
                <div class="space-y-2">
                    @forelse($topBuyers as $index => $buyer)
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center justify-center w-6 h-6 bg-green-100 rounded text-green-700 font-bold text-xs">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Str::limit($buyer->buyer->name ?? 'Unknown', 22) }}</p>
                                <p class="text-xs text-gray-500">{{ $buyer->order_count }} orders</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-600">${{ number_format($buyer->total_spent, 0) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Regional Section -->
    <div id="regional-section" class="regional-container hidden">
        <!-- Regional Performance -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Regional Performance</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Country</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Vendors</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Products</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($regionalStats as $country)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl">{{ $country->flag ?? '🌍' }}</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $country->name }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($country->vendors_count) }}</td>
                                <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($country->products_count) }}</td>
                                <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($country->orders_count) }}</td>
                                <td class="px-3 py-2 text-sm text-right font-semibold text-green-600">${{ number_format($country->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-gray-500">No regional data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
    const sections = ['overview', 'stats', 'trends', 'performers', 'regional'];
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

// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart')?.getContext('2d');
if (userGrowthCtx) {
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
}

// Order Status Chart
const orderStatusCtx = document.getElementById('orderStatusChart')?.getContext('2d');
if (orderStatusCtx) {
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
}

// Category Chart
const categoryCtx = document.getElementById('categoryChart')?.getContext('2d');
if (categoryCtx) {
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: @json($categoryDistribution->pluck('name')),
            datasets: [{
                label: 'Products',
                data: @json($categoryDistribution->pluck('count')),
                backgroundColor: '#6366f1',
                borderRadius: 4
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
}
</script>
@endpush

@endsection
