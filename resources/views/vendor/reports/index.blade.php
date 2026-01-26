@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .badge-action {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    @media print {
        .no-print { display: none !important; }
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
            <h1 class="text-xl font-bold text-gray-900">Sales Reports</h1>
            <p class="mt-1 text-xs text-gray-500">Detailed sales analysis and reporting</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('vendor.reports.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button onclick="exportReport()" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-download"></i>
                <span>Export</span>
            </button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            All
        </button>
        <button onclick="switchTab('stats')" id="tab-stats" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Stats
        </button>
        <button onclick="switchTab('reports')" id="tab-reports" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Reports
        </button>
    </div>

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container hidden">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Sales</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_sales'], 2) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                <i class="fas fa-dollar-sign mr-1 text-[8px]"></i> Revenue
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-lg text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Orders</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-shopping-cart mr-1 text-[8px]"></i> All orders
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-shopping-cart text-lg text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Avg Order Value</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($stats['average_order_value'], 2) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-chart-line mr-1 text-[8px]"></i> Average
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-chart-line text-lg text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                <i class="fas fa-box mr-1 text-[8px]"></i> Products
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg">
                        <i class="fas fa-box text-lg text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Active Products</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['active_products']) }}</p>
                        <div class="mt-2">
                            @php
                                $activePercentage = $stats['total_products'] > 0 ? round(($stats['active_products'] / $stats['total_products']) * 100, 1) : 0;
                            @endphp
                            <span class="text-xs text-gray-500">{{ $activePercentage }}% of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-lg text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Customers</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_customers']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                <i class="fas fa-users mr-1 text-[8px]"></i> Customers
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-lg">
                        <i class="fas fa-users text-lg text-cyan-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Section -->
    <div id="reports-section" class="reports-container">
        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" action="{{ route('vendor.reports.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <!-- Report Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Report Type</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Daily Report</option>
                            <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                            <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                            <option value="product" {{ $reportType == 'product' ? 'selected' : '' }}>Product Report</option>
                            <option value="customer" {{ $reportType == 'customer' ? 'selected' : '' }}>Customer Report</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRangePicker" placeholder="Select dates" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_range" value="{{ request('date_range') }}">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-sync text-sm"></i> Generate Report
                    </button>
                    @if(request()->hasAny(['type', 'date_range']))
                        <a href="{{ route('vendor.reports.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                            <i class="fas fa-undo text-sm"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Report Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-4">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">{{ ucfirst($reportType) }} Report</h2>
                        <p class="text-xs text-gray-500 mt-1">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ $reportData->count() }} {{ Str::plural('record', $reportData->count()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">
                                @if($reportType == 'product')
                                    Product
                                @elseif($reportType == 'customer')
                                    Customer
                                @else
                                    Period
                                @endif
                            </th>
                            @if(in_array($reportType, ['daily', 'weekly', 'monthly']))
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Transactions</th>
                            @endif
                            @if($reportType == 'product')
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Units Sold</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                            @endif
                            @if($reportType == 'customer')
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Avg Order</th>
                            @endif
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($reportData as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $item->period_label ?? 'N/A' }}
                                </td>
                                @if(in_array($reportType, ['daily', 'weekly', 'monthly']))
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $item->orders ?? 0 }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $item->transaction_count }}</td>
                                @endif
                                @if($reportType == 'product')
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($item->quantity_sold) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $item->order_count }}</td>
                                @endif
                                @if($reportType == 'customer')
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $item->order_count }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">${{ number_format($item->average_order_value, 2) }}</td>
                                @endif
                                <td class="px-4 py-3 text-sm text-right font-semibold text-emerald-600">${{ number_format($item->revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-chart-bar text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No data available</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($reportData->isNotEmpty())
                    <tfoot class="bg-gray-50 border-t">
                        <tr>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900">TOTAL</td>
                            @if(in_array($reportType, ['daily', 'weekly', 'monthly']))
                                <td class="px-4 py-3 text-sm text-right font-bold text-gray-900">{{ $reportData->sum('orders') }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold text-gray-900">{{ $reportData->sum('transaction_count') }}</td>
                            @endif
                            @if($reportType == 'product')
                                <td class="px-4 py-3 text-sm text-right font-bold text-gray-900">{{ number_format($reportData->sum('quantity_sold')) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold text-gray-900">{{ $reportData->sum('order_count') }}</td>
                            @endif
                            @if($reportType == 'customer')
                                <td class="px-4 py-3 text-sm text-right font-bold text-gray-900">{{ $reportData->sum('order_count') }}</td>
                                <td class="px-4 py-3 text-sm text-right font-bold text-gray-900">-</td>
                            @endif
                            <td class="px-4 py-3 text-sm text-right font-bold text-emerald-600">${{ number_format($reportData->sum('revenue'), 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Date Range Picker
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    locale: { rangeSeparator: " to " },
    defaultDate: "{{ request('date_range') }}"
});

// Tab Switching
function switchTab(tab) {
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        btn.classList.add('text-gray-600');
    });

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

    // Show/hide sections
    const statsSection = document.getElementById('stats-section');
    const reportsSection = document.getElementById('reports-section');

    switch(tab) {
        case 'all':
            statsSection.style.display = 'block';
            reportsSection.style.display = 'block';
            break;
        case 'stats':
            statsSection.style.display = 'block';
            reportsSection.style.display = 'none';
            break;
        case 'reports':
            statsSection.style.display = 'none';
            reportsSection.style.display = 'block';
            break;
    }
}

// Initialize with All tab active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('all');
});

function exportReport() {
    alert('Export functionality will be implemented soon!');
}
</script>
@endpush
