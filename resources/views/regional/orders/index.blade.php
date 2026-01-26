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
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Regional Orders Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor orders across {{ $region->name }} region</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('regional.orders.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
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
        <button onclick="switchTab('orders')" id="tab-orders" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-shopping-cart mr-2"></i> Orders
        </button>
        <button onclick="switchTab('revenue')" id="tab-revenue" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-dollar-sign mr-2"></i> Revenue
        </button>
        <button onclick="switchTab('analytics')" id="tab-analytics" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Analytics
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Orders</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-shopping-cart mr-1 text-[8px]"></i> All time
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Pending Orders</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $stats['pending_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Processing</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['processing']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $stats['processing_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-sync text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Completed</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['completed']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $stats['completed_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Revenue</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                <i class="fas fa-arrow-up mr-1 text-[8px]"></i> Completed
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Avg Order Value</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($stats['avg_order_value'], 2) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <i class="fas fa-chart-line mr-1 text-[8px]"></i> Per order
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                        <i class="fas fa-chart-bar text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Cancelled</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['cancelled']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1 text-[8px]"></i> Cancelled
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                        <i class="fas fa-ban text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Countries</p>
                        <p class="text-lg font-bold text-gray-900">{{ $countries->count() }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                <i class="fas fa-globe mr-1 text-[8px]"></i> Region
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg">
                        <i class="fas fa-flag text-teal-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-2 mt-4">
                <i class="fas fa-check-circle text-green-600 mt-0.5 text-sm"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times text-sm"></i></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <form method="GET" action="{{ route('regional.orders.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Country</label>
                        <select name="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRangePicker" placeholder="Select dates" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="dateTo" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Payment Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                            <option value="order_number" {{ request('sort_by') == 'order_number' ? 'selected' : '' }}>Order Number</option>
                            <option value="total" {{ request('sort_by') == 'total' ? 'selected' : '' }}>Total Amount</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Order</label>
                        <select name="sort_order" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('regional.orders.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Tab Content -->
    <div id="tab-orders-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Orders List</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ $orders->total() }} {{ Str::plural('order', $orders->total()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Order Details</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Customer</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Vendor</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Items</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Total</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Payment</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-semibold text-gray-900">#{{ $order->order_number }}</span>
                                        <span class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $order->buyer->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ Str::limit($order->buyer->email ?? 'N/A', 20) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $order->vendor->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ Str::limit($order->vendor->email ?? 'N/A', 20) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $order->items->count() }} items
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-bold text-gray-900">{{ $order->formatted_total }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $paymentColors = [
                                            'paid' => ['Paid', 'bg-green-100 text-green-800'],
                                            'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                            'failed' => ['Failed', 'bg-red-100 text-red-800'],
                                            'refunded' => ['Refunded', 'bg-purple-100 text-purple-800'],
                                        ];
                                        $payment = $paymentColors[$order->payment_status ?? 'pending'] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $payment[1] }}">
                                        {{ $payment[0] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                            'confirmed' => ['Confirmed', 'bg-blue-100 text-blue-800'],
                                            'processing' => ['Processing', 'bg-purple-100 text-purple-800'],
                                            'shipped' => ['Shipped', 'bg-indigo-100 text-indigo-800'],
                                            'delivered' => ['Delivered', 'bg-green-100 text-green-800'],
                                            'cancelled' => ['Cancelled', 'bg-red-100 text-red-800'],
                                        ];
                                        $status = $statusColors[$order->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">
                                        {{ $status[0] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('regional.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-shopping-cart text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No orders found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-700">Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }}</span>
                        <div class="text-sm">{{ $orders->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Revenue Tab Content -->
    <div id="tab-revenue-content" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Revenue Overview -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Revenue Overview</h3>
                <div class="space-y-4">
                    <div class="p-3 bg-emerald-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Total Revenue</span>
                            <i class="fas fa-dollar-sign text-emerald-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($stats['total_revenue'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">From completed orders</p>
                    </div>

                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Average Order Value</span>
                            <i class="fas fa-chart-line text-indigo-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($stats['avg_order_value'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Per order average</p>
                    </div>
                </div>
            </div>

            <!-- Payment Status Distribution -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Payment Status</h3>
                <div class="space-y-3">
                    @php
                        $paidCount = $orders->where('payment_status', 'paid')->count();
                        $pendingCount = $orders->where('payment_status', 'pending')->count();
                        $failedCount = $orders->where('payment_status', 'failed')->count();
                    @endphp

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Paid Orders</span>
                            <span class="text-sm font-bold text-green-700">{{ $paidCount }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full" style="width: {{ $stats['total'] > 0 ? round(($paidCount / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Pending Payment</span>
                            <span class="text-sm font-bold text-yellow-700">{{ $pendingCount }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-yellow-600 h-3 rounded-full" style="width: {{ $stats['total'] > 0 ? round(($pendingCount / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Failed Payment</span>
                            <span class="text-sm font-bold text-red-700">{{ $failedCount }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-red-600 h-3 rounded-full" style="width: {{ $stats['total'] > 0 ? round(($failedCount / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Tab Content -->
    <div id="tab-analytics-content" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Status Distribution -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Order Status Distribution</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Pending</span>
                            <span class="text-sm font-bold text-yellow-700">{{ number_format($stats['pending']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-yellow-600 h-3 rounded-full" style="width: {{ $stats['pending_percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['pending_percentage'] }}% of total</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Processing</span>
                            <span class="text-sm font-bold text-purple-700">{{ number_format($stats['processing']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-purple-600 h-3 rounded-full" style="width: {{ $stats['processing_percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['processing_percentage'] }}% of total</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Completed</span>
                            <span class="text-sm font-bold text-green-700">{{ number_format($stats['completed']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full" style="width: {{ $stats['completed_percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['completed_percentage'] }}% of total</p>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Orders</h3>
                <div class="space-y-3">
                    @php
                        $recentOrders = $orders->take(5);
                    @endphp

                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">#{{ $order->order_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-900">{{ $order->formatted_total }}</span>
                                <a href="{{ route('regional.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">No recent orders</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Date Range Picker
    flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        showMonths: 2,
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                document.getElementById('dateFrom').value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                document.getElementById('dateTo').value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
            }
        },
        defaultDate: [
            document.getElementById('dateFrom').value,
            document.getElementById('dateTo').value
        ].filter(d => d)
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
    });
</script>
@endpush
@endsection
