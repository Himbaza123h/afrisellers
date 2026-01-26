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
            <h1 class="text-xl font-bold text-gray-900">Country Orders Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor orders in {{ $country->name }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('country.orders.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
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
        <button onclick="switchTab('analytics')" id="tab-analytics" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Analytics
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Orders -->
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

            <!-- Pending Orders -->
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

            <!-- Processing -->
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

            <!-- Completed -->
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
        </div>

        <!-- Revenue Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Revenue</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                <i class="fas fa-arrow-up mr-1 text-[8px]"></i> From completed
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
                        <p class="text-xs font-medium text-gray-600 mb-1">Average Order Value</p>
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
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-green-50 rounded-md border border-green-200 flex items-start gap-3 mt-4">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <form method="GET" action="{{ route('country.orders.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRangePicker" placeholder="Select dates" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="dateTo" value="{{ request('date_to') }}">
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

                    <!-- Payment Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Payment</label>
                        <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
<option value="">All</option>
<option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
<option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
<option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
<option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
</select>
</div>
</div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    <i class="fas fa-filter text-sm"></i> Apply
                </button>
                <a href="{{ route('country.orders.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                    <i class="fas fa-undo text-sm"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Orders Tab Content (Hidden by default) -->
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
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Order</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Customer</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Vendor</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Items</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Payment</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">#{{ $order->order_number }}</span>
                                    <span class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-400">{{ $order->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex-shrink-0">
                                        <span class="text-xs font-semibold text-blue-700">{{ substr($order->buyer->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ Str::limit($order->buyer->name ?? 'N/A', 20) }}</span>
                                        <span class="text-xs text-gray-500">{{ Str::limit($order->buyer->email ?? 'N/A', 25) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ Str::limit($order->vendor->name ?? 'N/A', 20) }}</span>
                                    <span class="text-xs text-gray-500">{{ Str::limit($order->vendor->email ?? 'N/A', 25) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $order->items->count() }}
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
                                <span class="px-2 py-1 rounded-md text-xs font-medium {{ $payment[1] }}">{{ $payment[0] }}</span>
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
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('country.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="fas fa-shopping-cart text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-base font-medium">No orders found</p>
                                    <p class="text-sm">Orders from your country will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($orders, 'hasPages') && $orders->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }}
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Analytics Tab Content (Hidden by default) -->
<div id="tab-analytics-content" class="tab-content hidden">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Analytics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Cancelled Orders</p>
                        <p class="text-xs text-gray-500 mt-1">Total cancelled</p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-lg">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['cancelled']) }}</p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Total Revenue</p>
                        <p class="text-xs text-gray-500 mt-1">From completed orders</p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-emerald-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-emerald-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    window.switchTab = function(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            button.classList.add('text-gray-600');
        });

        // Show selected tab content
        document.getElementById('tab-' + tabName + '-content').classList.remove('hidden');

        // Add active state to selected tab
        const activeTab = document.getElementById('tab-' + tabName);
        activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        activeTab.classList.remove('text-gray-600');
    };

    // Initialize Flatpickr for date range
    const dateRangePicker = flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        maxDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                document.getElementById('dateFrom').value = flatpickr.formatDate(selectedDates[0], "Y-m-d");
                document.getElementById('dateTo').value = flatpickr.formatDate(selectedDates[1], "Y-m-d");
            }
        }
    });

    // Set initial date range if exists
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    if (dateFrom && dateTo) {
        dateRangePicker.setDate([dateFrom, dateTo]);
    }

    // Auto-dismiss success messages
    const successAlert = document.querySelector('.bg-green-50');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.transition = 'opacity 0.3s';
            successAlert.style.opacity = '0';
            setTimeout(function() {
                successAlert.remove();
            }, 300);
        }, 5000);
    }
});
</script>
@endpush

