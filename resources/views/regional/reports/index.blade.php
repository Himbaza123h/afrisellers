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
            <h1 class="text-2xl font-bold text-gray-900">Regional Reports</h1>
            <p class="text-sm text-gray-500">Comprehensive analytics and insights for your region</p>
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
        <form method="GET" action="{{ route('regional.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                <select name="country_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ $countryFilter == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
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
                <span><i class="fas fa-check-circle mr-1"></i>{{ $vendorsStats['verified'] }} Verified</span>
                <span><i class="fas fa-clock mr-1"></i>{{ $vendorsStats['pending'] }} Pending</span>
            </div>
        </div>

        <!-- Products Card -->
        <div class="bg-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">Total Products</h3>
                <i class="fas fa-box text-2xl opacity-75"></i>
            </div>
            <p class="text-3xl font-bold mb-2">{{ number_format($productsStats['total']) }}</p>
            <div class="flex items-center gap-4 text-sm opacity-90">
                <span><i class="fas fa-check mr-1"></i>{{ $productsStats['approved'] }} Approved</span>
                <span><i class="fas fa-clock mr-1"></i>{{ $productsStats['pending'] }} Pending</span>
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
                <span>Value: ${{ number_format($ordersStats['total_value'], 2) }}</span>
            </div>
        </div>

        <!-- Loads Card -->
        <div class="bg-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold opacity-90">Total Loads</h3>
                <i class="fas fa-truck text-2xl opacity-75"></i>
            </div>
            <p class="text-3xl font-bold mb-2">{{ number_format($loadsStats['total']) }}</p>
            <div class="flex items-center gap-4 text-sm opacity-90">
                <span><i class="fas fa-check-circle mr-1"></i>{{ $loadsStats['delivered'] }} Delivered</span>
                <span><i class="fas fa-shipping-fast mr-1"></i>{{ $loadsStats['in_transit'] }} In Transit</span>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Vendors Statistics -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendors Overview</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Verified</span>
                    <span class="text-lg font-bold text-green-700">{{ number_format($vendorsStats['verified']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Pending Verification</span>
                    <span class="text-lg font-bold text-yellow-700">{{ number_format($vendorsStats['pending']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Active Accounts</span>
                    <span class="text-lg font-bold text-blue-700">{{ number_format($vendorsStats['active']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Suspended</span>
                    <span class="text-lg font-bold text-red-700">{{ number_format($vendorsStats['suspended']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">New This Period</span>
                    <span class="text-lg font-bold text-indigo-700">{{ number_format($vendorsStats['new_this_month']) }}</span>
                </div>
            </div>
        </div>

        <!-- Orders Statistics -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Orders Breakdown</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Pending</span>
                    <span class="text-lg font-bold text-yellow-700">{{ number_format($ordersStats['pending']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Processing</span>
                    <span class="text-lg font-bold text-blue-700">{{ number_format($ordersStats['processing']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Shipped</span>
                    <span class="text-lg font-bold text-indigo-700">{{ number_format($ordersStats['shipped']) }}</span>
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

        <!-- Showrooms Statistics -->
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
            </div>
        </div>

        <!-- Transporters Statistics -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transportation Overview</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Total Transporters</span>
                    <span class="text-lg font-bold text-blue-700">{{ number_format($transportersStats['total']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Verified</span>
                    <span class="text-lg font-bold text-green-700">{{ number_format($transportersStats['verified']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                    <span class="text-lg font-bold text-purple-700">{{ number_format($transportersStats['active']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Total Fleet Size</span>
                    <span class="text-lg font-bold text-indigo-700">{{ number_format($transportersStats['total_fleet']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Country -->
    @if($revenueByCountry->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue by Country</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Country</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revenueByCountry as $revenue)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-900">{{ $revenue->country_name }}</td>
                        <td class="py-3 px-4 text-sm font-semibold text-right text-gray-900">${{ number_format($revenue->total_revenue, 2) }}</td>
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
</div>

<!-- Export Modal -->
<div id="exportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center no-print">
    <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Export Report</h3>
        <form action="{{ route('regional.reports.export') }}" method="GET">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            @if($countryFilter)
            <input type="hidden" name="country_id" value="{{ $countryFilter }}">
            @endif

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
