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
            <h1 class="text-xl font-bold text-gray-900">Performance</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor your business performance and growth</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('vendor.performance.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
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
        <button onclick="switchTab('performance')" id="tab-performance" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Performance
        </button>
    </div>

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container hidden">
        <!-- Key Performance Indicators -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-lg"></i>
                    </div>
                    @if($comparison['revenue_change'] > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                            <i class="fas fa-arrow-up text-[8px] mr-1"></i> {{ number_format(abs($comparison['revenue_change']), 1) }}%
                        </span>
                    @elseif($comparison['revenue_change'] < 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                            <i class="fas fa-arrow-down text-[8px] mr-1"></i> {{ number_format(abs($comparison['revenue_change']), 1) }}%
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                            <i class="fas fa-minus text-[8px] mr-1"></i> 0%
                        </span>
                    @endif
                </div>
                <p class="text-xs font-medium text-blue-900 mt-3 mb-1">Revenue Growth</p>
                <p class="text-lg font-bold text-blue-900">${{ number_format($metrics['total_revenue'], 2) }}</p>
                <p class="text-xs text-blue-700 mt-1">vs previous period</p>
            </div>

            <div class="stat-card p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-white text-lg"></i>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                        Success
                    </span>
                </div>
                <p class="text-xs font-medium text-green-900 mt-3 mb-1">Conversion Rate</p>
                <p class="text-lg font-bold text-green-900">{{ number_format($metrics['conversion_rate'], 1) }}%</p>
                <p class="text-xs text-green-700 mt-1">Orders completed</p>
            </div>

            <div class="stat-card p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-white text-lg"></i>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                        {{ number_format($metrics['repeat_customer_rate'], 1) }}%
                    </span>
                </div>
                <p class="text-xs font-medium text-purple-900 mt-3 mb-1">Repeat Customers</p>
                <p class="text-lg font-bold text-purple-900">{{ number_format($metrics['repeat_customers']) }}</p>
                <p class="text-xs text-purple-700 mt-1">of {{ $metrics['total_customers'] }} total</p>
            </div>

            <div class="stat-card p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg border border-amber-200">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white text-lg"></i>
                    </div>
                    @if($comparison['orders_change'] > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                            <i class="fas fa-arrow-up text-[8px] mr-1"></i> {{ number_format(abs($comparison['orders_change']), 1) }}%
                        </span>
                    @elseif($comparison['orders_change'] < 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                            <i class="fas fa-arrow-down text-[8px] mr-1"></i> {{ number_format(abs($comparison['orders_change']), 1) }}%
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                            <i class="fas fa-minus text-[8px] mr-1"></i> 0%
                        </span>
                    @endif
                </div>
                <p class="text-xs font-medium text-amber-900 mt-3 mb-1">Avg Order Value</p>
                <p class="text-lg font-bold text-amber-900">${{ number_format($metrics['average_order_value'], 2) }}</p>
                <p class="text-xs text-amber-700 mt-1">Per transaction</p>
            </div>
        </div>

        <!-- Performance Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-blue-600 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600">Total Orders</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($metrics['total_orders']) }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600">Completed Orders</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($metrics['completed_orders']) }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600">Cancellation Rate</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($metrics['cancellation_rate'], 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Section -->
    <div id="performance-section" class="performance-container">
        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" action="{{ route('vendor.performance.index') }}" class="space-y-3">
                <div class="flex flex-col md:flex-row md:items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRangePicker" placeholder="Select dates" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_range" value="{{ request('date_range') }}">
                    </div>
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-sync text-sm"></i> Update
                    </button>
                </div>
            </form>
        </div>

        <!-- Product Performance -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-4">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Top Performing Products</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ $productPerformance->count() }} {{ Str::plural('product', $productPerformance->count()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Product</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Units Sold</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Avg Price</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($productPerformance as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ Str::limit($product->name, 40) }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($product->units_sold) }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $product->order_count }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">${{ number_format($product->average_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-emerald-600">${{ number_format($product->revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-box text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No product performance data</p>
                                        <p class="text-xs text-gray-400 mt-1">Data will appear here</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Additional Performance Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <!-- Customer Metrics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Customer Metrics</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-cyan-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Total Customers</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($metrics['total_customers']) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-redo text-purple-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Repeat Rate</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($metrics['repeat_customer_rate'], 1) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Metrics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Product Metrics</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-boxes text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Total Products</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($metrics['total_products']) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Active Products</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($metrics['active_products']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
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
    const performanceSection = document.getElementById('performance-section');

    switch(tab) {
        case 'all':
            statsSection.style.display = 'block';
            performanceSection.style.display = 'block';
            break;
        case 'stats':
            statsSection.style.display = 'block';
            performanceSection.style.display = 'none';
            break;
        case 'performance':
            statsSection.style.display = 'none';
            performanceSection.style.display = 'block';
            break;
    }
}

// Initialize with All tab active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('all');
});
</script>
@endpush
