@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .tab-content { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
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
            <button onclick="window.open('{{ route('vendor.dashboard.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
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
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
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

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            <i class="fas fa-chart-line mr-2"></i> Overview
        </button>
        <button onclick="switchTab('products')" id="tab-products" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-box mr-2"></i> Products
        </button>
        <button onclick="switchTab('orders')" id="tab-orders" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-shopping-bag mr-2"></i> Orders
        </button>
        <button onclick="switchTab('analytics')" id="tab-analytics" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Analytics
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
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
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-6">
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
        <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm mt-6">
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

    <!-- Products Tab Content (Hidden by default) -->
    <div id="tab-products-content" class="tab-content hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Products Summary</h3>
                <div class="flex gap-2">
                    <a href="{{ route('vendor.product.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-sm font-medium">
                        <i class="fas fa-plus"></i>
                        <span>Add Product</span>
                    </a>
                    <a href="{{ route('vendor.product.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all text-sm font-medium">
                        <i class="fas fa-list"></i>
                        <span>Manage Products</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="p-5 bg-blue-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                            <i class="fas fa-box text-xl text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-600">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalProducts) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-green-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-xl text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-600">Active Products</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($activeProducts) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-orange-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg">
                            <i class="fas fa-exclamation-circle text-xl text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-orange-600">Inactive Products</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalProducts - $activeProducts) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-purple-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                            <i class="fas fa-chart-line text-xl text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-600">Top Selling</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $topProducts->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Status Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Product Status Distribution</h4>
                    <div class="h-64">
                        <canvas id="productStatusChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Quick Actions</h4>
                    <div class="space-y-3">
                        <a href="{{ route('vendor.product.create') }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl hover:bg-blue-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-lg">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Create New Product</p>
                                <p class="text-xs text-gray-600">Add a new product to your catalog</p>
                            </div>
                        </a>

                        <a href="{{ route('vendor.product.index') }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl hover:bg-green-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-lg">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Manage Products</p>
                                <p class="text-xs text-gray-600">Edit, update or delete products</p>
                            </div>
                        </a>

                        <a href="{{ route('vendor.inventory.index') }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-xl hover:bg-purple-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-purple-600 text-white rounded-lg">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Inventory Management</p>
                                <p class="text-xs text-gray-600">Update stock levels and alerts</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Tab Content (Hidden by default) -->
    <div id="tab-orders-content" class="tab-content hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Orders Summary</h3>
                <div class="flex gap-2">
                    <a href="{{ route('vendor.orders.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-sm font-medium">
                        <i class="fas fa-plus"></i>
                        <span>Create Order</span>
                    </a>
                    <a href="{{ route('vendor.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all text-sm font-medium">
                        <i class="fas fa-list"></i>
                        <span>View All Orders</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-6">
                @foreach($orderStatuses as $orderStatus)
                <div class="p-4 bg-gradient-to-br from-{{ $orderStatus['color'] }}-50 to-white rounded-xl border border-{{ $orderStatus['color'] }}-100">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-{{ $orderStatus['color'] }}-100 rounded-lg">
                            <i class="fas fa-{{ $orderStatus['icon'] }} text-{{ $orderStatus['color'] }}-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-gray-600">{{ $orderStatus['status'] }}</p>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($orderStatus['count']) }}</p>
                            <p class="text-xs text-gray-500">${{ number_format($orderStatus['revenue'], 0) }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Recent Orders Table -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="font-semibold text-gray-700">Recent Orders</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $order['id'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ now()->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">Customer</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($order['amount'], 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-{{ $order['status_color'] }}-100 text-{{ $order['status_color'] }}-800">
                                        {{ $order['status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-shopping-cart text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">No orders found</p>
                                        <p class="text-xs text-gray-500 mt-1">Orders will appear here</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Tab Content (Hidden by default) -->
    <div id="tab-analytics-content" class="tab-content hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Analytics Overview</h3>
                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                    {{ ucfirst(request('filter', 'weekly')) }} Period
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sales Chart -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Sales Trend</h4>
                    <div class="h-64">
                        <canvas id="analyticsSalesChart"></canvas>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Revenue Breakdown</h4>
                    <div class="h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Analytics Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                <div class="p-5 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                    <p class="text-sm font-medium text-blue-600 mb-1">Avg. Order Value</p>
                    <p class="text-2xl font-bold text-gray-900">
                        ${{ $myOrders > 0 ? number_format($myRevenue / $myOrders, 2) : '0.00' }}
                    </p>
                </div>

                <div class="p-5 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                    <p class="text-sm font-medium text-green-600 mb-1">Conversion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $myOrders > 0 ? number_format(($myOrders / max($totalProducts, 1)) * 100, 1) : '0.0' }}%
                    </p>
                </div>

                <div class="p-5 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                    <p class="text-sm font-medium text-purple-600 mb-1">Active Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($activeProducts) }}</p>
                </div>

                <div class="p-5 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200">
                    <p class="text-sm font-medium text-orange-600 mb-1">Growth Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $revenuePercentage >= 0 ? '+' : '' }}{{ $revenuePercentage }}%</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    // Tab Switching Function
    function switchTab(tabName) {
        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            btn.classList.add('text-gray-600');
        });

        // Add active state to selected tab
        const activeTab = document.getElementById(`tab-${tabName}`);
        activeTab.classList.remove('text-gray-600');
        activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Show selected tab content
        document.getElementById(`tab-${tabName}-content`).classList.remove('hidden');
    }

    // Initialize with Overview tab active
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('overview');

        // Initialize charts if they exist
        initializeCharts();
    });

    // Sales Chart
    function initializeCharts() {
        // Main Sales Chart
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            const salesChart = new Chart(salesCtx, {
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
        }

        // Product Status Chart
        const productStatusCtx = document.getElementById('productStatusChart');
        if (productStatusCtx) {
            const productStatusChart = new Chart(productStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Inactive'],
                    datasets: [{
                        data: [{{ $activeProducts }}, {{ $totalProducts - $activeProducts }}],
                        backgroundColor: ['#10b981', '#f59e0b'],
                        borderWidth: 2,
                        borderColor: '#fff'
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

        // Analytics Sales Chart
        const analyticsCtx = document.getElementById('analyticsSalesChart');
        if (analyticsCtx) {
            const analyticsChart = new Chart(analyticsCtx, {
                type: 'bar',
                data: {
                    labels: @json($salesChartData['labels']),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($salesChartData['sales']),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: '#3b82f6',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            // Calculate revenue by status
            const statusRevenue = @json(array_column($orderStatuses, 'revenue'));
            const statusLabels = @json(array_column($orderStatuses, 'status'));
            const colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#6b7280'];

            const revenueChart = new Chart(revenueCtx, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusRevenue,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    }
</script>
@endpush
@endsection
