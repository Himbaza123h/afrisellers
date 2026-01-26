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
            <h1 class="text-xl font-bold text-gray-900">Country Reports</h1>
            <p class="mt-1 text-xs text-gray-500">Analytics and insights for {{ Auth::user()->country?->name ?? 'N/A' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('country.reports.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            <i class="fas fa-chart-line mr-2"></i> Overview
        </button>
        <button onclick="switchTab('detailed')" id="tab-detailed" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-list-alt mr-2"></i> Detailed Stats
        </button>
        <button onclick="switchTab('trends')" id="tab-trends" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Trends
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" action="{{ route('country.reports.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium text-sm">
                            <i class="fas fa-filter mr-1"></i>Apply
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Vendors Card -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Vendors</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($vendorsStats['total']) }}</p>
                        <div class="mt-2 flex items-center gap-2 text-xs">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1 text-[8px]"></i>{{ $vendorsStats['verified'] }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1 text-[8px]"></i>{{ $vendorsStats['pending'] }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-store text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Products Card -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($productsStats['total']) }}</p>
                        <div class="mt-2">
                            <span class="text-xs text-gray-500">{{ number_format($productsStats['total_views']) }} Views</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-box text-purple-600"></i>
                    </div>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Orders</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($ordersStats['total']) }}</p>
                        <div class="mt-2">
                            <span class="text-xs font-bold text-green-600">${{ number_format($ordersStats['total_value'], 2) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-shopping-cart text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Transporters Card -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Transporters</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($transportersStats['total']) }}</p>
                        <div class="mt-2">
                            <span class="text-xs text-gray-500">{{ number_format($transportersStats['total_fleet']) }} Vehicles</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                        <i class="fas fa-truck text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Performance -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Period Performance</h3>
            <p class="text-xs text-gray-500 mb-3">{{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs font-medium text-blue-700 mb-1">New Vendors</p>
                    <p class="text-xl font-bold text-blue-900">{{ number_format($vendorsStats['new_this_period']) }}</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg">
                    <p class="text-xs font-medium text-purple-700 mb-1">New Products</p>
                    <p class="text-xl font-bold text-purple-900">{{ number_format($productsStats['new_this_period']) }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                    <p class="text-xs font-medium text-green-700 mb-1">Orders in Period</p>
                    <p class="text-xl font-bold text-green-900">{{ number_format($ordersStats['period_orders']) }}</p>
                    <p class="text-xs text-green-700 mt-1">Value: ${{ number_format($ordersStats['period_value'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Showrooms & Loads Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Showrooms -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Showrooms Overview</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded text-sm">
                        <span class="text-gray-700">Total</span>
                        <span class="font-bold text-blue-700">{{ number_format($showroomsStats['total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded text-sm">
                        <span class="text-gray-700">Verified</span>
                        <span class="font-bold text-green-700">{{ number_format($showroomsStats['verified']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-purple-50 rounded text-sm">
                        <span class="text-gray-700">Active</span>
                        <span class="font-bold text-purple-700">{{ number_format($showroomsStats['active']) }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <div class="p-2 bg-indigo-50 rounded text-center">
                            <p class="text-xs text-indigo-700">Views</p>
                            <p class="text-sm font-bold text-indigo-900">{{ number_format($showroomsStats['total_views']) }}</p>
                        </div>
                        <div class="p-2 bg-pink-50 rounded text-center">
                            <p class="text-xs text-pink-700">Inquiries</p>
                            <p class="text-sm font-bold text-pink-900">{{ number_format($showroomsStats['total_inquiries']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loads -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Loads Status</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="p-2 bg-blue-50 rounded text-center">
                        <p class="text-xs text-blue-700">Posted</p>
                        <p class="text-lg font-bold text-blue-900">{{ number_format($loadsStats['posted']) }}</p>
                    </div>
                    <div class="p-2 bg-yellow-50 rounded text-center">
                        <p class="text-xs text-yellow-700">Bidding</p>
                        <p class="text-lg font-bold text-yellow-900">{{ number_format($loadsStats['bidding']) }}</p>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded text-center">
                        <p class="text-xs text-indigo-700">In Transit</p>
                        <p class="text-lg font-bold text-indigo-900">{{ number_format($loadsStats['in_transit']) }}</p>
                    </div>
                    <div class="p-2 bg-green-50 rounded text-center">
                        <p class="text-xs text-green-700">Delivered</p>
                        <p class="text-lg font-bold text-green-900">{{ number_format($loadsStats['delivered']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats Tab Content (Hidden by default) -->
    <div id="tab-detailed-content" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Vendors Detail -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Vendors Status</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded text-sm">
                        <span class="text-gray-700">Verified</span>
                        <span class="font-bold text-green-700">{{ number_format($vendorsStats['verified']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-yellow-50 rounded text-sm">
                        <span class="text-gray-700">Pending</span>
                        <span class="font-bold text-yellow-700">{{ number_format($vendorsStats['pending']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-red-50 rounded text-sm">
                        <span class="text-gray-700">Rejected</span>
                        <span class="font-bold text-red-700">{{ number_format($vendorsStats['rejected']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded text-sm">
                        <span class="text-gray-700">Active</span>
                        <span class="font-bold text-blue-700">{{ number_format($vendorsStats['active']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-sm">
                        <span class="text-gray-700">Inactive</span>
                        <span class="font-bold text-gray-700">{{ number_format($vendorsStats['inactive']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-red-50 rounded text-sm">
                        <span class="text-gray-700">Suspended</span>
                        <span class="font-bold text-red-700">{{ number_format($vendorsStats['suspended']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Products Detail -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Products Status</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded text-sm">
                        <span class="text-gray-700">Approved</span>
                        <span class="font-bold text-green-700">{{ number_format($productsStats['approved']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-yellow-50 rounded text-sm">
                        <span class="text-gray-700">Pending</span>
                        <span class="font-bold text-yellow-700">{{ number_format($productsStats['pending']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded text-sm">
                        <span class="text-gray-700">Active</span>
                        <span class="font-bold text-blue-700">{{ number_format($productsStats['active']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-sm">
                        <span class="text-gray-700">Inactive</span>
                        <span class="font-bold text-gray-700">{{ number_format($productsStats['inactive']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-purple-50 rounded text-sm">
                        <span class="text-gray-700">Total Views</span>
                        <span class="font-bold text-purple-700">{{ number_format($productsStats['total_views']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Orders Detail -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Orders Status</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-yellow-50 rounded text-sm">
                        <span class="text-gray-700">Pending</span>
                        <span class="font-bold text-yellow-700">{{ number_format($ordersStats['pending']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded text-sm">
                        <span class="text-gray-700">Confirmed</span>
                        <span class="font-bold text-blue-700">{{ number_format($ordersStats['confirmed']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-indigo-50 rounded text-sm">
                        <span class="text-gray-700">Processing</span>
                        <span class="font-bold text-indigo-700">{{ number_format($ordersStats['processing']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-purple-50 rounded text-sm">
                        <span class="text-gray-700">Shipped</span>
                        <span class="font-bold text-purple-700">{{ number_format($ordersStats['shipped']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded text-sm">
                        <span class="text-gray-700">Delivered</span>
                        <span class="font-bold text-green-700">{{ number_format($ordersStats['delivered']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-red-50 rounded text-sm">
                        <span class="text-gray-700">Cancelled</span>
                        <span class="font-bold text-red-700">{{ number_format($ordersStats['cancelled']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transportation Overview -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Transportation Overview</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="p-3 bg-blue-50 rounded-lg text-center">
                    <p class="text-xs font-medium text-blue-700 mb-1">Total Transporters</p>
                    <p class="text-xl font-bold text-blue-900">{{ number_format($transportersStats['total']) }}</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg text-center">
                    <p class="text-xs font-medium text-purple-700 mb-1">Total Fleet</p>
                    <p class="text-xl font-bold text-purple-900">{{ number_format($transportersStats['total_fleet']) }}</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg text-center">
                    <p class="text-xs font-medium text-yellow-700 mb-1">Avg Rating</p>
                    <p class="text-xl font-bold text-yellow-900">{{ number_format($transportersStats['average_rating'], 1) }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg text-center">
                    <p class="text-xs font-medium text-green-700 mb-1">Deliveries</p>
                    <p class="text-xl font-bold text-green-900">{{ number_format($transportersStats['total_deliveries']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trends Tab Content (Hidden by default) -->
    <div id="tab-trends-content" class="tab-content hidden">
        <!-- Top Vendors -->
        @if($topVendors->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Top 10 Vendors by Revenue</h3>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">#</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Vendor Name</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Business</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Orders</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($topVendors as $index => $vendor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 text-gray-900">{{ Str::limit($vendor->name, 20) }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ Str::limit($vendor->business_name, 20) }}</td>
                            <td class="px-3 py-2 text-right text-gray-900">{{ number_format($vendor->total_orders) }}</td>
                            <td class="px-3 py-2 font-semibold text-right text-gray-900">${{ number_format($vendor->total_revenue, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Top Products -->
        @if($topProducts->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Top 10 Products by Views</h3>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">#</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Product Name</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Category</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Views</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($topProducts as $index => $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 text-gray-900">{{ Str::limit($product->name, 30) }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $product->category }}</td>
                            <td class="px-3 py-2 font-semibold text-right text-gray-900">{{ number_format($product->views) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Monthly Trends -->
        @if($monthlyOrders->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Monthly Trends (Last 6 Months)</h3>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Month</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Orders</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($monthlyOrders as $monthly)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-gray-900">{{ \Carbon\Carbon::parse($monthly->month . '-01')->format('F Y') }}</td>
                            <td class="px-3 py-2 text-right text-gray-900">{{ number_format($monthly->count) }}</td>
                            <td class="px-3 py-2 font-semibold text-right text-gray-900">${{ number_format($monthly->revenue, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Daily Activity -->
        @if($dailyActivity->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Daily Activity in Period</h3>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Date</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Orders</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($dailyActivity as $daily)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-gray-900">{{ \Carbon\Carbon::parse($daily->date)->format('M d, Y') }}</td>
                            <td class="px-3 py-2 text-right text-gray-900">{{ number_format($daily->orders_count) }}</td>
                            <td class="px-3 py-2 font-semibold text-right text-gray-900">${{ number_format($daily->daily_revenue, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    window.switchTab = function(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            button.classList.add('text-gray-600');
        });

        document.getElementById('tab-' + tabName + '-content').classList.remove('hidden');

        const activeTab = document.getElementById('tab-' + tabName);
        activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        activeTab.classList.remove('text-gray-600');
    };
});
</script>
@endpush
