@extends('layouts.home')

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .print-full-width { width: 100% !important; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Country Reports</h1>
            {{-- <p class="text-sm text-gray-500">Analytics and insights for {{ Auth::user()->country->name }}</p> --}}
            <p class="text-sm text-gray-500">Analytics and insights for {{ Auth::user()->country?->name ?? 'N/A' }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button onclick="document.getElementById('exportModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium shadow-sm">
                <i class="fas fa-download"></i>
                <span>Export Report</span>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 no-print">
        <form method="GET" action="{{ route('country.reports.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-sm">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Vendors Card -->
        <div class="bg-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">Total Vendors</h3>
                <i class="fas fa-store text-2xl opacity-75"></i>
            </div>
            <p class="text-3xl font-bold mb-2">{{ number_format($vendorsStats['total']) }}</p>
            <div class="flex items-center gap-4 text-sm opacity-90">
                <span><i class="fas fa-check-circle mr-1"></i>{{ $vendorsStats['verified'] }}</span>
                <span><i class="fas fa-clock mr-1"></i>{{ $vendorsStats['pending'] }}</span>
            </div>
        </div>

        <!-- Products Card -->
        <div class="bg-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">Total Products</h3>
                <i class="fas fa-box text-2xl opacity-75"></i>
            </div>
            <p class="text-3xl font-bold mb-2">{{ number_format($productsStats['total']) }}</p>
            <div class="text-sm opacity-90">
                <span>{{ number_format($productsStats['total_views']) }} Total Views</span>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">Total Orders</h3>
                <i class="fas fa-shopping-cart text-2xl opacity-75"></i>
            </div>
            <p class="text-3xl font-bold mb-2">{{ number_format($ordersStats['total']) }}</p>
            <div class="text-sm opacity-90">
                <span>${{ number_format($ordersStats['total_value'], 2) }}</span>
            </div>
        </div>

        <!-- Transporters Card -->
        <div class="bg-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">Transporters</h3>
                <i class="fas fa-truck text-2xl opacity-75"></i>
            </div>
            <p class="text-3xl font-bold mb-2">{{ number_format($transportersStats['total']) }}</p>
            <div class="text-sm opacity-90">
                <span>{{ number_format($transportersStats['total_fleet']) }} Vehicles</span>
            </div>
        </div>
    </div>

    <!-- Period Performance -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Period Performance ({{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-blue-50 rounded-lg">
                <p class="text-sm font-medium text-blue-700 mb-1">New Vendors</p>
                <p class="text-2xl font-bold text-blue-900">{{ number_format($vendorsStats['new_this_period']) }}</p>
            </div>
            <div class="p-4 bg-purple-50 rounded-lg">
                <p class="text-sm font-medium text-purple-700 mb-1">New Products</p>
                <p class="text-2xl font-bold text-purple-900">{{ number_format($productsStats['new_this_period']) }}</p>
            </div>
            <div class="p-4 bg-green-50 rounded-lg">
                <p class="text-sm font-medium text-green-700 mb-1">Orders in Period</p>
                <p class="text-2xl font-bold text-green-900">{{ number_format($ordersStats['period_orders']) }}</p>
                <p class="text-xs text-green-700 mt-1">Value: ${{ number_format($ordersStats['period_value'], 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Vendors Detail -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendors Status</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Verified</span>
                    <span class="text-lg font-bold text-green-700">{{ number_format($vendorsStats['verified']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Pending</span>
                    <span class="text-lg font-bold text-yellow-700">{{ number_format($vendorsStats['pending']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Rejected</span>
                    <span class="text-lg font-bold text-red-700">{{ number_format($vendorsStats['rejected']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                    <span class="text-lg font-bold text-blue-700">{{ number_format($vendorsStats['active']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Inactive</span>
                    <span class="text-lg font-bold text-gray-700">{{ number_format($vendorsStats['inactive']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Suspended</span>
                    <span class="text-lg font-bold text-red-700">{{ number_format($vendorsStats['suspended']) }}</span>
                </div>
            </div>
        </div>

        <!-- Products Detail -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Products Status</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Approved</span>
                    <span class="text-lg font-bold text-green-700">{{ number_format($productsStats['approved']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Pending Approval</span>
                    <span class="text-lg font-bold text-yellow-700">{{ number_format($productsStats['pending']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                    <span class="text-lg font-bold text-blue-700">{{ number_format($productsStats['active']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Inactive</span>
                    <span class="text-lg font-bold text-gray-700">{{ number_format($productsStats['inactive']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Total Views</span>
                    <span class="text-lg font-bold text-purple-700">{{ number_format($productsStats['total_views']) }}</span>
                </div>
            </div>
        </div>

        <!-- Orders Detail -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Orders Status</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Pending</span>
                    <span class="text-lg font-bold text-yellow-700">{{ number_format($ordersStats['pending']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Confirmed</span>
                    <span class="text-lg font-bold text-blue-700">{{ number_format($ordersStats['confirmed']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Processing</span>
                    <span class="text-lg font-bold text-indigo-700">{{ number_format($ordersStats['processing']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Shipped</span>
                    <span class="text-lg font-bold text-purple-700">{{ number_format($ordersStats['shipped']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Delivered</span>
                    <span class="text-lg font-bold text-green-700">{{ number_format($ordersStats['delivered']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Cancelled</span>
                    <span class="text-lg font-bold text-red-700">{{ number_format($ordersStats['cancelled']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Showrooms & Transporters Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Showrooms Detail -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Showrooms Overview</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Total Showrooms</span>
                    <span class="text-lg font-bold text-blue-700">{{ number_format($showroomsStats['total']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Verified</span>
                    <span class="text-lg font-bold text-green-700">{{ number_format($showroomsStats['verified']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                    <span class="text-lg font-bold text-purple-700">{{ number_format($showroomsStats['active']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Featured</span>
                    <span class="text-lg font-bold text-yellow-700">{{ number_format($showroomsStats['featured']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-teal-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Authorized Dealers</span>
                    <span class="text-lg font-bold text-teal-700">{{ number_format($showroomsStats['authorized_dealers']) }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div class="p-2 bg-indigo-50 rounded text-center">
                        <p class="text-xs text-indigo-700">Total Views</p>
                        <p class="text-sm font-bold text-indigo-900">{{ number_format($showroomsStats['total_views']) }}</p>
                    </div>
                    <div class="p-2 bg-pink-50 rounded text-center">
                        <p class="text-xs text-pink-700">Inquiries</p>
                        <p class="text-sm font-bold text-pink-900">{{ number_format($showroomsStats['total_inquiries']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transporters & Loads Detail -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transportation Overview</h3>
            <div class="space-y-3">
                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs font-medium text-blue-700 mb-1">Total Transporters</p>
                    <p class="text-2xl font-bold text-blue-900">{{ number_format($transportersStats['total']) }}</p>
                    <div class="grid grid-cols-3 gap-2 mt-2 text-xs">
                        <span class="text-green-700">✓ {{ $transportersStats['verified'] }}</span>
                        <span class="text-blue-700">● {{ $transportersStats['active'] }}</span>
                        <span class="text-gray-700">○ {{ $transportersStats['inactive'] }}</span>
                    </div>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg">
                    <p class="text-xs font-medium text-purple-700 mb-1">Total Fleet Size</p>
                    <p class="text-2xl font-bold text-purple-900">{{ number_format($transportersStats['total_fleet']) }}</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg">
                    <p class="text-xs font-medium text-yellow-700 mb-1">Average Rating</p>
                    <div class="flex items-center gap-2">
                        <p class="text-2xl font-bold text-yellow-900">{{ number_format($transportersStats['average_rating'], 1) }}</p>
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-{{ $i <= $transportersStats['average_rating'] ? 'yellow' : 'gray' }}-400 text-sm"></i>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                    <p class="text-xs font-medium text-green-700 mb-1">Total Deliveries</p>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($transportersStats['total_deliveries']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loads Statistics -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Loads Status</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
            <div class="p-3 bg-blue-50 rounded-lg text-center">
                <p class="text-xs font-medium text-blue-700 mb-1">Posted</p>
                <p class="text-xl font-bold text-blue-900">{{ number_format($loadsStats['posted']) }}</p>
            </div>
            <div class="p-3 bg-yellow-50 rounded-lg text-center">
                <p class="text-xs font-medium text-yellow-700 mb-1">Bidding</p>
                <p class="text-xl font-bold text-yellow-900">{{ number_format($loadsStats['bidding']) }}</p>
            </div>
            <div class="p-3 bg-purple-50 rounded-lg text-center">
                <p class="text-xs font-medium text-purple-700 mb-1">Assigned</p>
                <p class="text-xl font-bold text-purple-900">{{ number_format($loadsStats['assigned']) }}</p>
            </div>
            <div class="p-3 bg-indigo-50 rounded-lg text-center">
                <p class="text-xs font-medium text-indigo-700 mb-1">In Transit</p>
                <p class="text-xl font-bold text-indigo-900">{{ number_format($loadsStats['in_transit']) }}</p>
            </div>
            <div class="p-3 bg-green-50 rounded-lg text-center">
                <p class="text-xs font-medium text-green-700 mb-1">Delivered</p>
                <p class="text-xl font-bold text-green-900">{{ number_format($loadsStats['delivered']) }}</p>
            </div>
            <div class="p-3 bg-red-50 rounded-lg text-center">
                <p class="text-xs font-medium text-red-700 mb-1">Cancelled</p>
                <p class="text-xl font-bold text-red-900">{{ number_format($loadsStats['cancelled']) }}</p>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg text-center">
                <p class="text-xs font-medium text-gray-700 mb-1">Total</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($loadsStats['total']) }}</p>
            </div>
        </div>
        <div class="mt-4 p-3 bg-teal-50 rounded-lg">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-teal-700">Total Weight Transported</span>
                <span class="text-lg font-bold text-teal-900">{{ number_format($loadsStats['total_weight'], 2) }} kg</span>
            </div>
        </div>
    </div>

    <!-- Top Vendors -->
    @if($topVendors->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Vendors by Revenue (Period)</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">#</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Vendor Name</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Business Name</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Orders</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topVendors as $index => $vendor)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 text-sm text-gray-900">{{ $vendor->name }}</td>
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $vendor->business_name }}</td>
                        <td class="py-3 px-4 text-sm text-right text-gray-900">{{ number_format($vendor->total_orders) }}</td>
                        <td class="py-3 px-4 text-sm font-semibold text-right text-gray-900">${{ number_format($vendor->total_revenue, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Top Products -->
    @if($topProducts->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Products by Views</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">#</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Product Name</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Category</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Views</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $index => $product)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 text-sm text-gray-900">{{ $product->name }}</td>
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $product->category }}</td>
                        <td class="py-3 px-4 text-sm font-semibold text-right text-gray-900">{{ number_format($product->views) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Monthly Trends -->
    @if($monthlyOrders->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Trends (Last 6 Months)</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Month</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Orders</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyOrders as $monthly)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($monthly->month . '-01')->format('F Y') }}</td>
                        <td class="py-3 px-4 text-sm text-right text-gray-900">{{ number_format($monthly->count) }}</td>
                        <td class="py-3 px-4 text-sm font-semibold text-right text-gray-900">${{ number_format($monthly->revenue, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Daily Activity -->
    @if($dailyActivity->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Activity in Period</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Date</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Orders</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyActivity as $daily)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($daily->date)->format('M d, Y') }}</td>
                        <td class="py-3 px-4 text-sm text-right text-gray-900">{{ number_format($daily->count) }}</td>
                        <td class="py-3 px-4 text-sm font-semibold text-right text-gray-900">${{ number_format($daily->revenue, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Export Modal -->
<div id="exportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center no-print">
    <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Export Report</h3>
        <form action="{{ route('country.reports.export') }}" method="GET">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">

            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
                    <select name="format" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="xlsx">Excel (XLSX)</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('exportModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-all font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
