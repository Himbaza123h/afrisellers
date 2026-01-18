@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Country Orders Management</h1>
            <p class="mt-1 text-sm text-gray-500">Monitor orders in {{ $country->name }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-download"></i>
                <span>Export</span>
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-shopping-cart mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-shopping-cart text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $stats['pending_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Processing</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['processing']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ $stats['processing_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-sync text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['completed']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['completed_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                            <i class="fas fa-arrow-up mr-1 text-[10px]"></i> From completed orders
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl">
                    <i class="fas fa-dollar-sign text-2xl text-emerald-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Average Order Value</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['avg_order_value'], 2) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            <i class="fas fa-chart-line mr-1 text-[10px]"></i> Per order
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl">
                    <i class="fas fa-chart-bar text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('country.orders.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order number, customer or vendor..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <div class="relative">
                    <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Date range" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg w-56 cursor-pointer bg-white">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
                </div>

                <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <select name="payment_status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Payment Status</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                    <option value="order_number" {{ request('sort_by') == 'order_number' ? 'selected' : '' }}>Order Number</option>
                    <option value="total" {{ request('sort_by') == 'total' ? 'selected' : '' }}>Total Amount</option>
                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'date_range', 'status', 'payment_status', 'sort_by']))
                    <a href="{{ route('country.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Order Details</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Customer</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Vendor</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Items</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Total</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Payment</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">#{{ $order->order_number }}</span>
                                    <span class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full">
                                        <span class="text-sm font-semibold text-blue-700">{{ substr($order->buyer->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ $order->buyer->name ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500">{{ $order->buyer->email ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $order->vendor->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">{{ $order->vendor->email ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900">{{ $order->formatted_total }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $paymentColors = [
                                        'paid' => ['Paid', 'bg-green-100 text-green-800'],
                                        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                        'failed' => ['Failed', 'bg-red-100 text-red-800'],
                                        'refunded' => ['Refunded', 'bg-purple-100 text-purple-800'],
                                    ];
                                    $payment = $paymentColors[$order->payment_status ?? 'pending'] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-xs font-medium {{ $payment[1] }}">{{ $payment[0] }}</span>
                            </td>
                            <td class="px-6 py-4">
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
                                <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('country.orders.show', $order->id) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-shopping-cart text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No orders found</p>
                                    <p class="text-sm text-gray-500 mb-6">Orders from your country will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($orders, 'hasPages') && $orders->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }}</span>
                    <div>{{ $orders->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    locale: { rangeSeparator: " to " },
    onClose: function(dates, str, inst) {
        if (dates.length === 2) inst.element.closest('form').submit();
    }
});
</script>
@endsection
