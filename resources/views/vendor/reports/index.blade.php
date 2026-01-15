@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sales Reports</h1>
            <p class="mt-1 text-sm text-gray-500">Detailed sales analysis and reporting</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                <i class="fas fa-print"></i> Print
            </button>
            <button onclick="exportReport()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                <i class="fas fa-file-excel"></i> Export
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-emerald-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Sales</p>
            <p class="text-lg font-bold text-gray-900">${{ number_format($summary['total_sales'], 2) }}</p>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
            <p class="text-lg font-bold text-gray-900">{{ number_format($summary['total_orders']) }}</p>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Avg Order Value</p>
            <p class="text-lg font-bold text-gray-900">${{ number_format($summary['average_order_value'], 2) }}</p>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-amber-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Items Sold</p>
            <p class="text-lg font-bold text-gray-900">{{ number_format($summary['total_items_sold']) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                <select name="type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Daily Report</option>
                    <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                    <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                    <option value="product" {{ $reportType == 'product' ? 'selected' : '' }}>Product Report</option>
                    <option value="customer" {{ $reportType == 'customer' ? 'selected' : '' }}>Customer Report</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <input type="text" name="date_range" id="dateRangePicker" value="{{ request('date_range') }}" readonly placeholder="Select date range" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg cursor-pointer">
            </div>

            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-sync mr-2"></i>Generate Report
            </button>
        </form>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">{{ ucfirst($reportType) }} Report</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">
                            @if($reportType == 'product')
                                Product
                            @elseif($reportType == 'customer')
                                Customer
                            @else
                                Period
                            @endif
                        </th>
                        @if(in_array($reportType, ['daily', 'weekly', 'monthly']))
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Transactions</th>
                        @endif
                        @if($reportType == 'product')
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Units Sold</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                        @endif
                        @if($reportType == 'customer')
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Avg Order</th>
                        @endif
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($reportData as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $item->period_label ?? 'N/A' }}
                            </td>
                            @if(in_array($reportType, ['daily', 'weekly', 'monthly']))
                                <td class="px-6 py-4 text-sm text-right text-gray-700">{{ $item->orders ?? 0 }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-700">{{ $item->transaction_count }}</td>
                            @endif
                            @if($reportType == 'product')
                                <td class="px-6 py-4 text-sm text-right text-gray-700">{{ number_format($item->quantity_sold) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-700">{{ $item->order_count }}</td>
                            @endif
                            @if($reportType == 'customer')
                                <td class="px-6 py-4 text-sm text-right text-gray-700">{{ $item->order_count }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-700">${{ number_format($item->average_order_value, 2) }}</td>
                            @endif
                            <td class="px-6 py-4 text-sm text-right font-semibold text-green-600">${{ number_format($item->revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-chart-bar text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No data available for this period</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($reportData->isNotEmpty())
                <tfoot class="bg-gray-50 border-t-2">
                    <tr>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">TOTAL</td>
                        @if(in_array($reportType, ['daily', 'weekly', 'monthly']))
                            <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">{{ $reportData->sum('orders') }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">{{ $reportData->sum('transaction_count') }}</td>
                        @endif
                        @if($reportType == 'product')
                            <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">{{ number_format($reportData->sum('quantity_sold')) }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">{{ $reportData->sum('order_count') }}</td>
                        @endif
                        @if($reportType == 'customer')
                            <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">{{ $reportData->sum('order_count') }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">-</td>
                        @endif
                        <td class="px-6 py-4 text-base text-right font-bold text-green-600">${{ number_format($reportData->sum('revenue'), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Additional Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales by Payment Method -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales by Payment Method</h3>
            <div class="space-y-3">
                @foreach($salesByPaymentMethod as $method)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-credit-card text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $method->payment_method ?? 'Unknown')) }}</p>
                                <p class="text-xs text-gray-500">{{ $method->count }} transactions</p>
                            </div>
                        </div>
                        <span class="text-base font-bold text-gray-900">${{ number_format($method->total, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Sales by Status -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales by Order Status</h3>
            <div class="space-y-3">
                @foreach($salesByStatus as $status)
                    @php
                        $statusColors = [
                            'pending' => ['bg-yellow-100', 'text-yellow-600'],
                            'confirmed' => ['bg-blue-100', 'text-blue-600'],
                            'processing' => ['bg-purple-100', 'text-purple-600'],
                            'shipped' => ['bg-indigo-100', 'text-indigo-600'],
                            'delivered' => ['bg-green-100', 'text-green-600'],
                            'cancelled' => ['bg-red-100', 'text-red-600'],
                        ];
                        $color = $statusColors[$status->status] ?? ['bg-gray-100', 'text-gray-600'];
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 {{ $color[0] }} rounded-lg flex items-center justify-center">
                                <i class="fas fa-shopping-cart {{ $color[1] }}"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst($status->status) }}</p>
                                <p class="text-xs text-gray-500">{{ $status->count }} orders</p>
                            </div>
                        </div>
                        <span class="text-base font-bold text-gray-900">${{ number_format($status->total, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    locale: { rangeSeparator: " to " }
});

function exportReport() {
    alert('Export functionality will be implemented soon!');
}
</script>
@endsection
