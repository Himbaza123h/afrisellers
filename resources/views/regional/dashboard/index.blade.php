@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $region->name }} Regional Dashboard</h1>
            <p class="text-gray-600">Welcome back, {{ auth()->user()->name }}!</p>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('regional.dashboard.home') }}" class="flex gap-2">
            <select name="period" onchange="this.form.submit()" class="rounded-lg border-gray-300 focus:border-[#ff0808] focus:ring-[#ff0808]">
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
            <select name="country_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 focus:border-[#ff0808] focus:ring-[#ff0808]">
                <option value="">All Countries</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ $countryFilter == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Countries -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Countries</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_countries'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-globe text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Vendors -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Vendors</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_vendors']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">{{ $stats['active_vendors'] }} Active</span>
                <span class="text-gray-400 mx-2">•</span>
                <span class="text-orange-600 font-medium">{{ $stats['pending_vendors'] }} Pending</span>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Showrooms -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Showrooms</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_showrooms']) }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Transporters -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Transporters</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_transporters']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Period Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Period Revenue</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['period_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Period Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Period Orders</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['period_orders']) }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-teal-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Loads -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Loads</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_loads']) }}</p>
                </div>
                <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-pink-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Vendor Growth Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendor Growth</h3>
            <canvas id="vendorGrowthChart"></canvas>
        </div>

        <!-- Product Growth Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Growth</h3>
            <canvas id="productGrowthChart"></canvas>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend</h3>
            <canvas id="revenueChart"></canvas>
        </div>

        <!-- Country Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendors by Country</h3>
            <canvas id="countryDistributionChart"></canvas>
        </div>
    </div>

    <!-- Order Status Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Status Distribution</h3>
        <div class="max-w-md mx-auto">
            <canvas id="orderStatusChart"></canvas>
        </div>
    </div>

    <!-- Country Performance Table -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Country Performance</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendors</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($countryPerformance as $performance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $performance['name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($performance['vendors']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($performance['products']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${{ number_format($performance['revenue'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Categories & Pending Approvals -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Top Categories -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Product Categories</h3>
            @if($topCategories->count() > 0)
                <div class="space-y-3">
                    @foreach($topCategories as $category)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $category['name'] }}</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                {{ $category['count'] }} products
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No data available for this period</p>
            @endif
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pending Vendor Approvals</h3>
            @if($pendingApprovals->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingApprovals as $profile)
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $profile->business_name }}</p>
                                <p class="text-xs text-gray-500">{{ $profile->country->name }} • {{ $profile->user->email }}</p>
                            </div>
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs">Pending</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No pending approvals</p>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Vendors -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Vendors</h3>
            @if($recentVendors->count() > 0)
                <div class="space-y-3">
                    @foreach($recentVendors as $vendor)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $vendor->businessProfile->business_name }}</p>
                                <p class="text-xs text-gray-500">{{ $vendor->businessProfile->country->name }} • {{ $vendor->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="px-2 py-1 bg-{{ $vendor->account_status === 'active' ? 'green' : 'gray' }}-100 text-{{ $vendor->account_status === 'active' ? 'green' : 'gray' }}-800 rounded text-xs">
                                {{ ucfirst($vendor->account_status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No recent vendors</p>
            @endif
        </div>

        <!-- Recent Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Products</h3>
            @if($recentProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($recentProducts as $product)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ Str::limit($product->name, 30) }}</p>
                                <p class="text-xs text-gray-500">{{ $product->country->name }} • {{ $product->productCategory->name ?? 'Uncategorized' }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $product->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No recent products</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Vendor Growth Chart
    const vendorCtx = document.getElementById('vendorGrowthChart').getContext('2d');
    new Chart(vendorCtx, {
        type: 'line',
        data: {
            labels: @json($vendorGrowth['labels']),
            datasets: [{
                label: 'New Vendors',
                data: @json($vendorGrowth['data']),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Product Growth Chart
    const productCtx = document.getElementById('productGrowthChart').getContext('2d');
    new Chart(productCtx, {
        type: 'line',
        data: {
            labels: @json($productGrowth['labels']),
            datasets: [{
                label: 'New Products',
                data: @json($productGrowth['data']),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: @json($revenueChart['labels']),
            datasets: [{
                label: 'Revenue ($)',
                data: @json($revenueChart['data']),
                backgroundColor: '#ff0808'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Country Distribution Chart
    const countryDistCtx = document.getElementById('countryDistributionChart').getContext('2d');
    new Chart(countryDistCtx, {
        type: 'bar',
        data: {
            labels: @json($countryDistribution['labels']),
            datasets: [{
                label: 'Vendors',
                data: @json($countryDistribution['data']),
                backgroundColor: '#8b5cf6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($orderStatusChart['labels']),
            datasets: [{
                data: @json($orderStatusChart['data']),
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
</script>
@endpush
@endsection
