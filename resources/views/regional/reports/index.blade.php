@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .tab-content { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    @media print {
        .no-print { display: none !important; }
        .print-full-width { width: 100% !important; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Regional Reports</h1>
            <p class="mt-1 text-xs text-gray-500">Comprehensive analytics and insights for {{ $region->name }}</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('regional.reports.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button onclick="document.getElementById('exportModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-download"></i>
                <span>Export</span>
            </button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            <i class="fas fa-chart-line mr-2"></i> Overview
        </button>
        <button onclick="switchTab('vendors')" id="tab-vendors" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-store mr-2"></i> Vendors
        </button>
        <button onclick="switchTab('orders')" id="tab-orders" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-shopping-cart mr-2"></i> Orders
        </button>
        <button onclick="switchTab('logistics')" id="tab-logistics" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-truck mr-2"></i> Logistics
        </button>
        <button onclick="switchTab('trends')" id="tab-trends" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Trends
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" action="{{ route('regional.reports.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Country</label>
                        <select name="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ $countryFilter == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium text-sm">
                            <i class="fas fa-filter mr-2"></i>Apply
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Overview Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <!-- Vendors Card -->
            <div class="stat-card p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-sm text-white">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold opacity-90">Total Vendors</h3>
                    <i class="fas fa-store text-xl opacity-75"></i>
                </div>
                <p class="text-2xl font-bold mb-2">{{ number_format($vendorsStats['total']) }}</p>
                <div class="flex items-center gap-3 text-xs opacity-90">
                    <span><i class="fas fa-check-circle mr-1"></i>{{ $vendorsStats['verified'] }}</span>
                    <span><i class="fas fa-clock mr-1"></i>{{ $vendorsStats['pending'] }}</span>
                </div>
            </div>

            <!-- Products Card -->
            <div class="stat-card p-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-sm text-white">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold opacity-90">Total Products</h3>
                    <i class="fas fa-box text-xl opacity-75"></i>
                </div>
                <p class="text-2xl font-bold mb-2">{{ number_format($productsStats['total']) }}</p>
                <div class="flex items-center gap-3 text-xs opacity-90">
                    <span><i class="fas fa-check mr-1"></i>{{ $productsStats['approved'] }}</span>
                    <span><i class="fas fa-clock mr-1"></i>{{ $productsStats['pending'] }}</span>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="stat-card p-4 bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-sm text-white">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold opacity-90">Total Orders</h3>
                    <i class="fas fa-shopping-cart text-xl opacity-75"></i>
                </div>
                <p class="text-2xl font-bold mb-2">{{ number_format($ordersStats['total']) }}</p>
                <div class="text-xs opacity-90">
                    <span>Value: ${{ number_format($ordersStats['total_value'], 2) }}</span>
                </div>
            </div>

            <!-- Loads Card -->
            <div class="stat-card p-4 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-sm text-white">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold opacity-90">Total Loads</h3>
                    <i class="fas fa-truck text-xl opacity-75"></i>
                </div>
                <p class="text-2xl font-bold mb-2">{{ number_format($loadsStats['total']) }}</p>
                <div class="flex items-center gap-3 text-xs opacity-90">
                    <span><i class="fas fa-check-circle mr-1"></i>{{ $loadsStats['delivered'] }}</span>
                    <span><i class="fas fa-shipping-fast mr-1"></i>{{ $loadsStats['in_transit'] }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h4 class="text-xs font-semibold text-gray-600 mb-2">Showrooms</h4>
                <p class="text-xl font-bold text-gray-900 mb-1">{{ number_format($showroomsStats['total']) }}</p>
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full">{{ $showroomsStats['verified'] }} Verified</span>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h4 class="text-xs font-semibold text-gray-600 mb-2">Transporters</h4>
                <p class="text-xl font-bold text-gray-900 mb-1">{{ number_format($transportersStats['total']) }}</p>
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded-full">{{ $transportersStats['verified'] }} Verified</span>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h4 class="text-xs font-semibold text-gray-600 mb-2">Fleet Size</h4>
                <p class="text-xl font-bold text-gray-900 mb-1">{{ number_format($transportersStats['total_fleet']) }}</p>
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full">Total Vehicles</span>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h4 class="text-xs font-semibold text-gray-600 mb-2">Period Revenue</h4>
                <p class="text-xl font-bold text-gray-900 mb-1">${{ number_format($ordersStats['period_value'] ?? 0, 2) }}</p>
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full">{{ $ordersStats['period_orders'] ?? 0 }} Orders</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendors Tab Content -->
    <div id="tab-vendors-content" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Vendors Statistics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Vendors Overview</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Verified</span>
                        <span class="text-base font-bold text-green-700">{{ number_format($vendorsStats['verified']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Pending</span>
                        <span class="text-base font-bold text-yellow-700">{{ number_format($vendorsStats['pending']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                        <span class="text-base font-bold text-blue-700">{{ number_format($vendorsStats['active']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Suspended</span>
                        <span class="text-base font-bold text-red-700">{{ number_format($vendorsStats['suspended']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-indigo-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">New This Period</span>
                        <span class="text-base font-bold text-indigo-700">{{ number_format($vendorsStats['new_this_month']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Products Statistics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Products Overview</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-purple-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Total Products</span>
                        <span class="text-base font-bold text-purple-700">{{ number_format($productsStats['total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Approved</span>
                        <span class="text-base font-bold text-green-700">{{ number_format($productsStats['approved']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Pending Approval</span>
                        <span class="text-base font-bold text-yellow-700">{{ number_format($productsStats['pending']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">New This Period</span>
                        <span class="text-base font-bold text-blue-700">{{ number_format($productsStats['new_this_month']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-teal-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Approval Rate</span>
                        <span class="text-base font-bold text-teal-700">
                            {{ $productsStats['total'] > 0 ? round(($productsStats['approved'] / $productsStats['total']) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Showrooms Statistics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Showrooms Overview</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Total Showrooms</span>
                        <span class="text-base font-bold text-blue-700">{{ number_format($showroomsStats['total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Verified</span>
                        <span class="text-base font-bold text-green-700">{{ number_format($showroomsStats['verified']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-purple-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                        <span class="text-base font-bold text-purple-700">{{ number_format($showroomsStats['active']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Featured</span>
                        <span class="text-base font-bold text-yellow-700">{{ number_format($showroomsStats['featured']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Combined Summary -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Business Summary</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Total Businesses</span>
                        <span class="text-base font-bold text-indigo-700">
                            {{ number_format($vendorsStats['total'] + $showroomsStats['total']) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Verified Entities</span>
                        <span class="text-base font-bold text-emerald-700">
                            {{ number_format($vendorsStats['verified'] + $showroomsStats['verified'] + $transportersStats['verified']) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Total Listings</span>
                        <span class="text-base font-bold text-pink-700">{{ number_format($productsStats['total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gradient-to-r from-orange-50 to-red-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Pending Reviews</span>
                        <span class="text-base font-bold text-red-700">
                            {{ number_format($vendorsStats['pending'] + $productsStats['pending']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Tab Content -->
    <div id="tab-orders-content" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Orders Statistics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Orders Breakdown</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Pending</span>
                        <span class="text-base font-bold text-yellow-700">{{ number_format($ordersStats['pending']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Processing</span>
                        <span class="text-base font-bold text-blue-700">{{ number_format($ordersStats['processing']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-indigo-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Shipped</span>
                        <span class="text-base font-bold text-indigo-700">{{ number_format($ordersStats['shipped']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Delivered</span>
                        <span class="text-base font-bold text-green-700">{{ number_format($loadsStats['delivered']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Cancelled</span>
                        <span class="text-base font-bold text-red-700">{{ number_format($loadsStats['cancelled']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-indigo-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Period Loads</span>
                        <span class="text-base font-bold text-indigo-700">{{ number_format($loadsStats['period_loads']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Transporters Statistics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Transportation Overview</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Total Transporters</span>
                        <span class="text-base font-bold text-blue-700">{{ number_format($transportersStats['total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Verified</span>
                        <span class="text-base font-bold text-green-700">{{ number_format($transportersStats['verified']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-purple-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                        <span class="text-base font-bold text-purple-700">{{ number_format($transportersStats['active']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-indigo-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Total Fleet Size</span>
                        <span class="text-base font-bold text-indigo-700">{{ number_format($transportersStats['total_fleet']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-teal-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Avg Fleet per Transporter</span>
                        <span class="text-base font-bold text-teal-700">
                            {{ $transportersStats['total'] > 0 ? round($transportersStats['total_fleet'] / $transportersStats['total'], 1) : 0 }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-cyan-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Delivery Success Rate</span>
                        <span class="text-base font-bold text-cyan-700">
                            {{ $loadsStats['total'] > 0 ? round(($loadsStats['delivered'] / $loadsStats['total']) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trends Tab Content -->
    <div id="tab-trends-content" class="tab-content hidden">
        <!-- Monthly Trends -->
        @if($monthlyOrders->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Monthly Trends (Last 6 Months)</h3>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left py-2 px-3 text-xs font-semibold text-gray-700">Month</th>
                            <th class="text-right py-2 px-3 text-xs font-semibold text-gray-700">Orders</th>
                            <th class="text-right py-2 px-3 text-xs font-semibold text-gray-700">Revenue</th>
                            <th class="text-right py-2 px-3 text-xs font-semibold text-gray-700">Avg Order</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($monthlyOrders as $monthly)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($monthly->month . '-01')->format('F Y') }}</td>
                            <td class="py-2 px-3 text-sm text-right text-gray-900">{{ number_format($monthly->count) }}</td>
                            <td class="py-2 px-3 text-sm font-semibold text-right text-gray-900">${{ number_format($monthly->revenue, 2) }}</td>
                            <td class="py-2 px-3 text-sm text-right text-gray-600">
                                ${{ $monthly->count > 0 ? number_format($monthly->revenue / $monthly->count, 2) : '0.00' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t font-semibold">
                        <tr>
                            <td class="py-2 px-3 text-sm text-gray-900">Total</td>
                            <td class="py-2 px-3 text-sm text-right text-gray-900">{{ number_format($monthlyOrders->sum('count')) }}</td>
                            <td class="py-2 px-3 text-sm text-right text-gray-900">${{ number_format($monthlyOrders->sum('revenue'), 2) }}</td>
                            <td class="py-2 px-3 text-sm text-right text-gray-600">
                                ${{ $monthlyOrders->sum('count') > 0 ? number_format($monthlyOrders->sum('revenue') / $monthlyOrders->sum('count'), 2) : '0.00' }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 text-center">
            <i class="fas fa-chart-line text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500 font-medium">No monthly trend data available</p>
            <p class="text-xs text-gray-400 mt-1">Data will appear once orders are placed</p>
        </div>
        @endif
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center no-print">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Export Report</h3>
        <form action="{{ route('regional.reports.export') }}" method="GET">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            @if($countryFilter)
            <input type="hidden" name="country_id" value="{{ $countryFilter }}">
            @endif

            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
                    <select name="format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="xlsx">Excel (XLSX)</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('exportModal').classList.add('hidden')" class="flex-1 px-3 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-all font-medium text-sm">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium text-sm">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
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
    });
</script>
@endpush
@endsection
