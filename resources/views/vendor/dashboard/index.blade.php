@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .tab-content { display: none; }
    .tab-content.show { display: block; animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-6">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->name }}</p>

        </div>
        <div class="flex flex-wrap gap-3">
            <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-50 to-green-100 border border-green-200 text-green-700 rounded-lg font-medium shadow-sm">
                <span class="w-2 h-2 bg-green-600 rounded-full animate-pulse"></span>
                <span class="text-sm font-semibold">Store Active</span>
            </div>
            <button onclick="window.open('{{ route('vendor.dashboard.print') }}' + window.location.search, '_blank')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    {{-- ── Date Filter ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('vendor.dashboard.home') }}" class="flex flex-wrap items-center gap-3">
            <label class="text-sm font-medium text-gray-700">Filter by:</label>

            <div class="flex gap-2">
                <button type="submit" name="filter" value="weekly"
                        class="px-3 py-1.5 text-xs font-medium {{ (!request('filter') || request('filter') == 'weekly') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all">
                    Weekly
                </button>
                <button type="submit" name="filter" value="monthly"
                        class="px-3 py-1.5 text-xs font-medium {{ request('filter') == 'monthly' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all">
                    Monthly
                </button>
                <button type="submit" name="filter" value="yearly"
                        class="px-3 py-1.5 text-xs font-medium {{ request('filter') == 'yearly' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all">
                    Yearly
                </button>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">or</span>
                <div class="relative">
                    <input type="text" id="dateRangePicker" name="date_range"
                           value="{{ request('date_range') }}" readonly
                           placeholder="Custom date range"
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-64 cursor-pointer bg-white text-sm">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
                </div>
                <input type="hidden" name="filter" value="custom" id="customFilterInput">
            </div>

            @if(request()->hasAny(['filter', 'date_range']))
                <a href="{{ route('vendor.dashboard.home') }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-xs transition-all">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>

    {{-- ── Tab Navigation ───────────────────────────────────────────────────── --}}
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview"
                class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            <i class="fas fa-chart-line mr-2"></i> Overview
        </button>
        <button onclick="switchTab('products')" id="tab-products"
                class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-box mr-2"></i> Products
        </button>
        <button onclick="switchTab('orders')" id="tab-orders"
                class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-shopping-bag mr-2"></i> Orders
        </button>
        <button onclick="switchTab('analytics')" id="tab-analytics"
                class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Analytics
            @if(!$planFeatures['has_basic_analytics'] && !$planFeatures['has_analytics'])
                <i class="fas fa-lock text-xs text-amber-500 ml-1"></i>
            @endif
        </button>
    </div>

{{-- ════════════════════════════════════════════════════════════════════════
         OVERVIEW TAB
    ════════════════════════════════════════════════════════════════════════ --}}
    <div id="tab-overview-content" class="tab-content">

        @if(!$planFeatures['has_basic_stats'] && !$planFeatures['has_basic_analytics'] && !$planFeatures['has_analytics'])
            @if(isset($activeTrial) && $activeTrial)
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3 mb-3">
                    <i class="fas fa-gift text-green-500 mt-0.5 flex-shrink-0"></i>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-green-900">Free Trial Active</h3>
                        <p class="text-xs text-green-700 mt-0.5">
                            You are on a free trial. All features are unlocked until
                            <span class="font-semibold">{{ $activeTrial->ends_at->format('M d, Y') }}</span>.
                        </p>
                    </div>
                    <a href="{{ route('vendor.subscriptions.index') }}" class="text-xs font-bold text-green-700 underline whitespace-nowrap">View Plans</a>
                </div>
            @else
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 mb-3">
                    <i class="fas fa-lock text-amber-500 mt-0.5 flex-shrink-0"></i>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-amber-900">Stats Locked</h3>
                        <p class="text-xs text-amber-700 mt-0.5">Upgrade your plan to unlock dashboard statistics and analytics.</p>
                    </div>
                    <a href="{{ route('vendor.subscriptions.index') }}" class="text-xs font-bold text-amber-700 underline">Upgrade</a>
                </div>
            @endif
        @endif

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

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
                    <div class="flex items-center justify-center w-14 h-14 bg-green-50 to-green-100 rounded-xl">
                        <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

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
                    <div class="flex items-center justify-center w-14 h-14 bg-purple-50 to-purple-100 rounded-xl">
                        <i class="fas fa-boxes text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>

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
                    <div class="flex items-center justify-center w-14 h-14 bg-blue-50 to-blue-100 rounded-xl">
                        <i class="fas fa-shopping-bag text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Pending Orders</p>
                        <p class="text-lg font-bold text-gray-900">{{ $pendingOrders }}</p>
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pendingOrders > 0 ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                <i class="fas fa-{{ $pendingOrders > 0 ? 'exclamation-circle' : 'check-circle' }} mr-1 text-[10px]"></i>
                                {{ $pendingOrders > 0 ? 'Action Needed' : 'All Clear' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-14 h-14 bg-orange-50 to-orange-100 rounded-xl">
                        <i class="fas fa-clock text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts & Recent Orders --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
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

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Recent Orders</h3>
                    <a href="{{ route('vendor.orders.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentOrders as $order)
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                        <div class="flex items-center justify-center w-10 h-10 bg-{{ $order['color'] }}-50 to-{{ $order['color'] }}-100 rounded-lg flex-shrink-0">
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

        {{-- Top Performing Products --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Top Performing Products</h3>
                <a href="{{ route('vendor.product.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All Products</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($topProducts as $product)
                <div class="p-5 bg-{{ $product['color'] }}-50 to-white rounded-xl border border-{{ $product['color'] }}-100">
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

        {{-- Order Status Overview --}}
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
                                    <div class="flex items-center justify-center w-10 h-10 bg-{{ $orderStatus['color'] }}-50 to-{{ $orderStatus['color'] }}-100 rounded-lg">
                                        <i class="fas fa-{{ $orderStatus['icon'] }} text-{{ $orderStatus['color'] }}-600"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $orderStatus['status'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4"><span class="text-sm font-bold text-gray-900">{{ number_format($orderStatus['count']) }}</span></td>
                            <td class="px-6 py-4"><span class="text-sm font-bold text-gray-900">${{ number_format($orderStatus['revenue'], 2) }}</span></td>
                            <td class="px-6 py-4"><span class="text-sm text-gray-700">${{ number_format($orderStatus['avg'], 2) }}</span></td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('vendor.orders.index', ['status' => strtolower($orderStatus['status'])]) }}"
                                   class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                                    View <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
</div>{{-- end tab-overview-content --}}

    {{-- ════════════════════════════════════════════════════════════════════════
         PRODUCTS TAB
    ════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-products-content" class="tab-content">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Products Summary</h3>
                <div class="flex gap-2">
                    <a href="{{ route('vendor.product.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-sm font-medium">
                        <i class="fas fa-plus"></i><span>Add Product</span>
                    </a>
                    <a href="{{ route('vendor.product.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all text-sm font-medium">
                        <i class="fas fa-list"></i><span>Manage Products</span>
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Product Status Distribution</h4>
                    <div class="h-64"><canvas id="productStatusChart"></canvas></div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Quick Actions</h4>
                    <div class="space-y-3">
                        <a href="{{ route('vendor.product.create') }}"
                           class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl hover:bg-blue-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-lg"><i class="fas fa-plus"></i></div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Create New Product</p>
                                <p class="text-xs text-gray-600">Add a new product to your catalog</p>
                            </div>
                        </a>
                        <a href="{{ route('vendor.product.index') }}"
                           class="flex items-center gap-3 p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl hover:bg-green-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-lg"><i class="fas fa-edit"></i></div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Manage Products</p>
                                <p class="text-xs text-gray-600">Edit, update or delete products</p>
                            </div>
                        </a>
                        <a href="{{ route('vendor.inventory.index') }}"
                           class="flex items-center gap-3 p-4 bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-xl hover:bg-purple-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-purple-600 text-white rounded-lg"><i class="fas fa-boxes"></i></div>
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

    {{-- ════════════════════════════════════════════════════════════════════════
         ORDERS TAB
    ════════════════════════════════════════════════════════════════════════ --}}
    <div id="tab-orders-content" class="tab-content">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Orders Summary</h3>
                <div class="flex gap-2">
                    <a href="{{ route('vendor.orders.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-sm font-medium">
                        <i class="fas fa-plus"></i><span>Create Order</span>
                    </a>
                    <a href="{{ route('vendor.orders.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all text-sm font-medium">
                        <i class="fas fa-list"></i><span>View All Orders</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-6">
                @foreach($orderStatuses as $orderStatus)
                <div class="p-4 bg-{{ $orderStatus['color'] }}-50 to-white rounded-xl border border-{{ $orderStatus['color'] }}-100">
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

    {{-- ════════════════════════════════════════════════════════════════════════
         ANALYTICS TAB
    ════════════════════════════════════════════════════════════════════════ --}}

    <div id="tab-analytics-content" class="tab-content">




        @if(!$planFeatures['has_basic_analytics'] && !$planFeatures['has_analytics'] && !$planFeatures['has_advanced_analytics'])

            {{-- Locked state --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lock text-3xl text-amber-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Analytics Locked</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
                    Your current plan does not include analytics. Upgrade to access store traffic, product views, page visits, video engagement, and more.
                </p>
                <div class="flex flex-wrap justify-center gap-3 mb-8">
                    @foreach(['Store Traffic','Product Views','Page Visits','Video Engagement','Comments & Interactions','ROI Dashboards','Conversion Tracking'] as $feat)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-400 rounded-full text-xs font-medium">
                            <i class="fas fa-lock text-[10px]"></i> {{ $feat }}
                        </span>
                    @endforeach
                </div>
                <a href="{{ route('vendor.subscriptions.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-md">
                    <i class="fas fa-arrow-circle-up"></i> Upgrade Plan
                </a>
            </div>

        @else

            {{-- ── Plan badge ────────────────────────────────────────────────── --}}
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Analytics Overview</h3>
                    <p class="text-xs text-gray-500 mt-0.5">All-time data from your store</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($planFeatures['has_advanced_analytics'])
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs font-bold">Advanced</span>
                    @elseif($planFeatures['has_analytics'])
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">Full</span>
                    @else
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold">Basic</span>
                    @endif
                </div>
            </div>

            {{-- ── Store Traffic cards ───────────────────────────────────────── --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-store text-blue-600 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Store Visits</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($vendorAnalytics->store_visits ?? 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Today: <span class="font-semibold text-blue-600">{{ number_format($todayVendorAnalytics->store_visits ?? 0) }}</span>
                    </p>
                </div>

                <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-purple-600 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Unique Visitors</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($vendorAnalytics->unique_visitors ?? 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Today: <span class="font-semibold text-purple-600">{{ number_format($todayVendorAnalytics->unique_visitors ?? 0) }}</span>
                    </p>
                </div>

                <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-eye text-green-600 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Product Views</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($vendorAnalytics->total_product_views ?? 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Today: <span class="font-semibold text-green-600">{{ number_format($todayVendorAnalytics->total_product_views ?? 0) }}</span>
                    </p>
                </div>

                <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bullhorn text-orange-600 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Impressions</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($vendorAnalytics->total_impressions ?? 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">All time</p>
                </div>
            </div>

            {{-- ── Engagement cards ──────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heart text-red-500 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Total Likes</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($vendorAnalytics->total_likes ?? 0) }}</p>
                </div>

                <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-share-alt text-indigo-600 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Total Shares</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($vendorAnalytics->total_shares ?? 0) }}</p>
                </div>

                <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-teal-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-plus text-teal-600 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Followers</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($vendorAnalytics->followers ?? 0) }}</p>
                </div>

                <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-invoice text-yellow-600 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">RFQ Requests</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($vendorAnalytics->rfq_count ?? 0) }}</p>
                </div>
            </div>

            {{-- ── Store traffic trend + Profile analytics ───────────────────── --}}
            @if($planFeatures['has_analytics'] || $planFeatures['has_advanced_analytics'])
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Store visits 14-day chart --}}
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Store Traffic — Last 14 Days</h4>
                    <div class="h-64"><canvas id="storeTrafficChart"></canvas></div>
                </div>

                {{-- Profile analytics summary --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Profile Analytics</h4>
                    @if($profileAnalytics)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-eye text-blue-500 text-sm w-5"></i>
                                <span class="text-sm text-gray-600">Profile Views</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($profileAnalytics->views) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users text-purple-500 text-sm w-5"></i>
                                <span class="text-sm text-gray-600">Unique Visitors</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($profileAnalytics->unique_visitors) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-phone text-green-500 text-sm w-5"></i>
                                <span class="text-sm text-gray-600">Contact Clicks</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($profileAnalytics->contact_clicks) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <i class="fab fa-whatsapp text-green-500 text-sm w-5"></i>
                                <span class="text-sm text-gray-600">WhatsApp Clicks</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($profileAnalytics->whatsapp_clicks) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-globe text-indigo-500 text-sm w-5"></i>
                                <span class="text-sm text-gray-600">Website Clicks</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($profileAnalytics->website_clicks) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-share-alt text-orange-500 text-sm w-5"></i>
                                <span class="text-sm text-gray-600">Profile Shares</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($profileAnalytics->shares) }}</span>
                        </div>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <i class="fas fa-chart-pie text-3xl text-gray-200 mb-3"></i>
                        <p class="text-sm text-gray-400">No profile data yet.<br>Visits will be tracked automatically.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ── Video engagement ─────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-play-circle text-pink-600 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Video Views</p>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($vendorAnalytics->video_views ?? 0) }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-red-500 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Watch Time</p>
                    </div>
                    @php
                        $watchSec = $vendorAnalytics->video_watch_time ?? 0;
                        $watchDisplay = $watchSec >= 3600
                            ? round($watchSec / 3600, 1) . 'h'
                            : ($watchSec >= 60 ? round($watchSec / 60) . 'm' : $watchSec . 's');
                    @endphp
                    <p class="text-2xl font-bold text-gray-900">{{ $watchDisplay }}</p>
                    <p class="text-xs text-gray-400 mt-1">Total across all videos</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 bg-cyan-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-percentage text-cyan-600 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Conversion Rate</p>
                    </div>
                    @php
                        $convRate = $vendorAnalytics->conversion_rate ?? 0;
                    @endphp
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($convRate, 1) }}%</p>
                    <p class="text-xs text-gray-400 mt-1">Visits → Orders</p>
                </div>
            </div>

            {{-- ── Top products by views ─────────────────────────────────────── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <h4 class="font-semibold text-gray-700 mb-4">Top Products by Views</h4>
                @if($topProductsByViews->isNotEmpty())
                <div class="space-y-3">
                    @foreach($topProductsByViews as $pa)
                    @php
                        $maxViews = $topProductsByViews->max('views') ?: 1;
                        $barPct   = min(100, round(($pa->views / $maxViews) * 100));
                    @endphp
                    <div class="flex items-center gap-4">
                        <div class="w-40 flex-shrink-0 truncate text-sm font-medium text-gray-700">
                            {{ $pa->product->name ?? 'Unknown' }}
                        </div>
                        <div class="flex-1">
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{ $barPct }}%"></div>
                            </div>
                        </div>
                        <div class="w-20 text-right text-sm font-bold text-gray-900">{{ number_format($pa->views) }}</div>
                        <div class="w-20 text-right text-xs text-gray-400">{{ number_format($pa->clicks) }} clicks</div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <i class="fas fa-chart-bar text-3xl text-gray-200 mb-3"></i>
                    <p class="text-sm text-gray-400">Product view data will appear here once<br>tracking is active.</p>
                </div>
                @endif
            </div>

            {{-- ── Conversion/ROI metrics (gated) ──────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="p-5 bg-blue-50 to-blue-100 rounded-xl border border-blue-200">
                    <p class="text-sm font-medium text-blue-600 mb-1">Avg. Order Value</p>
                    <p class="text-2xl font-bold text-gray-900">
                        ${{ $myOrders > 0 ? number_format($myRevenue / $myOrders, 2) : '0.00' }}
                    </p>
                </div>
                <div class="p-5 bg-green-50 to-green-100 rounded-xl border border-green-200">
                    <p class="text-sm font-medium text-green-600 mb-1">Active Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($activeProducts) }}</p>
                </div>

                {{-- Conversion tracking --}}
                <div class="p-5 bg-purple-50 to-purple-100 rounded-xl border border-purple-200 relative">
                    @if(!$planFeatures['has_advanced_conversion_tracking'])
                        <div class="absolute inset-0 bg-white/70 rounded-xl flex items-center justify-center">
                            <i class="fas fa-lock text-gray-400 text-xl"></i>
                        </div>
                    @endif
                    <p class="text-sm font-medium text-purple-600 mb-1">Click-to-Order Rate</p>
                    <p class="text-2xl font-bold text-gray-900">
                        @if(($vendorAnalytics->total_product_views ?? 0) > 0)
                            {{ number_format(($vendorAnalytics->total_orders / $vendorAnalytics->total_product_views) * 100, 1) }}%
                        @else
                            0.0%
                        @endif
                    </p>
                </div>

                {{-- ROI / Growth --}}
                <div class="p-5 bg-orange-50 to-orange-100 rounded-xl border border-orange-200 relative">
                    @if(!$planFeatures['has_roi_dashboards'])
                        <div class="absolute inset-0 bg-white/70 rounded-xl flex items-center justify-center">
                            <i class="fas fa-lock text-gray-400 text-xl"></i>
                        </div>
                    @endif
                    <p class="text-sm font-medium text-orange-600 mb-1">Revenue Growth</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $revenuePercentage >= 0 ? '+' : '' }}{{ $revenuePercentage }}%</p>
                </div>
            </div>

            {{-- Store performance block --}}
            @if($planFeatures['has_store_performance_tracking'])
            <div class="p-5 bg-indigo-50 to-indigo-100 rounded-xl border border-indigo-200 mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-store text-indigo-600"></i>
                    <h4 class="font-bold text-indigo-900 text-sm">Store Performance Summary</h4>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-indigo-600 font-medium">Total Revenue</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($myRevenue, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-indigo-600 font-medium">Total Orders</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($myOrders) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-indigo-600 font-medium">Completed Orders</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($vendorAnalytics->completed_orders ?? 0) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-indigo-600 font-medium">Total Customers</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($vendorAnalytics->total_customers ?? 0) }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Locked features upsell --}}
            @php
                $lockedFeatures = [];
                if (!$planFeatures['has_advanced_analytics'])           $lockedFeatures[] = 'Advanced Analytics';
                if (!$planFeatures['has_roi_dashboards'])               $lockedFeatures[] = 'ROI Dashboards';
                if (!$planFeatures['has_advanced_conversion_tracking']) $lockedFeatures[] = 'Conversion Tracking';
                if (!$planFeatures['has_performance_reports'])          $lockedFeatures[] = 'Performance Reports';
                if (!$planFeatures['has_weekly_analytics'])             $lockedFeatures[] = 'Weekly Reports';
                if (!$planFeatures['has_monthly_reports'])              $lockedFeatures[] = 'Monthly Reports';
                if (!$planFeatures['has_regional_analytics'])           $lockedFeatures[] = 'Regional Analytics';
            @endphp
            @if(count($lockedFeatures))
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                <i class="fas fa-star text-amber-500 mt-0.5 flex-shrink-0"></i>
                <div class="flex-1">
                    <p class="text-xs font-bold text-amber-900 mb-1">Unlock more with an upgrade:</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($lockedFeatures as $lf)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-semibold">
                                <i class="fas fa-lock" style="font-size:8px;"></i> {{ $lf }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('vendor.subscriptions.index') }}" class="text-xs font-bold text-amber-700 underline whitespace-nowrap">Upgrade</a>
            </div>
            @endif

        @endif
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ── Flatpickr ──────────────────────────────────────────────────────────────
    flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        showMonths: 2,
        locale: { rangeSeparator: " to " },
        onClose: function (dates, str, inst) {
            if (dates.length === 2) {
                document.getElementById('customFilterInput').value = 'custom';
                inst.element.closest('form').submit();
            }
        }
    });

// ── Tab switching ──────────────────────────────────────────────────────────
function switchTab(tabName) {
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            btn.classList.add('text-gray-600');
        });

        const activeBtn = document.getElementById('tab-' + tabName);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-600');
            activeBtn.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        }

        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('show'));

        const activeContent = document.getElementById('tab-' + tabName + '-content');
        if (activeContent) {
            activeContent.classList.add('show');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        switchTab('overview');
        initializeCharts();
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Make sure all tab contents start hidden except overview
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        switchTab('overview');
        initializeCharts();
    });

    // ── Charts ─────────────────────────────────────────────────────────────────
    function initializeCharts() {

        // Main sales chart
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: @json($salesChartData['labels']),
                    datasets: [{
                        label: 'Sales ($)',
                        data: @json($salesChartData['sales']),
                        backgroundColor: 'rgba(59,130,246,0.1)',
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
                        backgroundColor: 'rgba(147,51,234,0.1)',
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
                        legend: { display: true, position: 'bottom',
                            labels: { padding: 15, font: { size: 12 }, boxWidth: 40, boxHeight: 12, usePointStyle: true }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    }
                }
            });
        }

        // Product status doughnut
        const productStatusCtx = document.getElementById('productStatusChart');
        if (productStatusCtx) {
            new Chart(productStatusCtx, {
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
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        // Analytics tab — store traffic 14-day chart
        const trafficCtx = document.getElementById('storeTrafficChart');
        if (trafficCtx) {
            new Chart(trafficCtx, {
                type: 'line',
                data: {
                    labels: @json($analyticsChartData['labels']),
                    datasets: [{
                        label: 'Store Visits',
                        data: @json($analyticsChartData['visits']),
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        borderColor: '#3b82f6',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Product Views',
                        data: @json($analyticsChartData['views']),
                        backgroundColor: 'rgba(16,185,129,0.1)',
                        borderColor: '#10b981',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'bottom',
                            labels: { font: { size: 11 }, padding: 12, usePointStyle: true }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45 } }
                    }
                }
            });
        }

        // Analytics tab — revenue breakdown pie
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            const statusRevenue = @json(array_column($orderStatuses, 'revenue'));
            const statusLabels  = @json(array_column($orderStatuses, 'status'));
            const colors = ['#3b82f6','#10b981','#8b5cf6','#f59e0b','#ef4444','#6b7280'];
            new Chart(revenueCtx, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{ data: statusRevenue, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right' } }
                }
            });
        }

        // Analytics tab — sales bar chart
        const analyticsCtx = document.getElementById('analyticsSalesChart');
        if (analyticsCtx) {
            new Chart(analyticsCtx, {
                type: 'bar',
                data: {
                    labels: @json($salesChartData['labels']),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($salesChartData['sales']),
                        backgroundColor: 'rgba(59,130,246,0.7)',
                        borderColor: '#3b82f6',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }
</script>
@endpush
@endsection
