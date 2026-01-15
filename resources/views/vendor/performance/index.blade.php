@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Performance Metrics</h1>
            <p class="mt-1 text-sm text-gray-500">Monitor your business performance and growth</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input type="text" name="date_range" id="dateRangePicker" value="{{ request('date_range') }}" readonly placeholder="Select date range" class="px-4 py-2 border border-gray-300 rounded-lg cursor-pointer">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-sync mr-2"></i>Update
            </button>
        </form>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                @if($comparison['revenue_change'] > 0)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                        <i class="fas fa-arrow-up mr-1"></i> {{ number_format(abs($comparison['revenue_change']), 1) }}%
                    </span>
                @elseif($comparison['revenue_change'] < 0)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                        <i class="fas fa-arrow-down mr-1"></i> {{ number_format(abs($comparison['revenue_change']), 1) }}%
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                        <i class="fas fa-minus mr-1"></i> 0%
                    </span>
                @endif
            </div>
            <p class="text-sm font-medium text-blue-900 mb-1">Revenue Growth</p>
            <p class="text-lg font-bold text-blue-900">${{ number_format($metrics['total_revenue'], 2) }}</p>
            <p class="text-xs text-blue-700 mt-2">vs previous period</p>
        </div>

        <div class="p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percentage text-white text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                    Success Rate
                </span>
            </div>
            <p class="text-sm font-medium text-green-900 mb-1">Conversion Rate</p>
            <p class="text-lg font-bold text-green-900">{{ number_format($metrics['conversion_rate'], 1) }}%</p>
            <p class="text-xs text-green-700 mt-2">Orders completed</p>
        </div>

        <div class="p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                    {{ number_format($metrics['repeat_customer_rate'], 1) }}%
                </span>
            </div>
            <p class="text-sm font-medium text-purple-900 mb-1">Repeat Customers</p>
            <p class="text-lg font-bold text-purple-900">{{ number_format($metrics['repeat_customers']) }}</p>
            <p class="text-xs text-purple-700 mt-2">of {{ $metrics['total_customers'] }} total</p>
        </div>

        <div class="p-6 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl border border-amber-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-white text-xl"></i>
                </div>
                @if($comparison['orders_change'] > 0)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                        <i class="fas fa-arrow-up mr-1"></i> {{ number_format(abs($comparison['orders_change']), 1) }}%
                    </span>
                @elseif($comparison['orders_change'] < 0)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                        <i class="fas fa-arrow-down mr-1"></i> {{ number_format(abs($comparison['orders_change']), 1) }}%
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                        <i class="fas fa-minus mr-1"></i> 0%
                    </span>
                @endif
            </div>
            <p class="text-sm font-medium text-amber-900 mb-1">Avg Order Value</p>
            <p class="text-lg font-bold text-amber-900">${{ number_format($metrics['average_order_value'], 2) }}</p>
            <p class="text-xs text-amber-700 mt-2">Per transaction</p>
        </div>
    </div>

    <!-- Performance Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['total_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Completed Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['completed_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Cancellation Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['cancellation_rate'], 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Trends -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Performance Trends</h3>
        <div class="mb-4 flex justify-end gap-2">
            <button onclick="toggleTrendChart('orders')" id="ordersBtn" class="trend-toggle-btn active px-4 py-2 text-sm font-medium rounded-lg border transition-all">
                Orders
            </button>
            <button onclick="toggleTrendChart('revenue')" id="revenueBtn" class="trend-toggle-btn px-4 py-2 text-sm font-medium rounded-lg border transition-all">
                Revenue
            </button>
        </div>
        <div style="height: 400px;">
            <canvas id="performanceTrendChart"></canvas>
        </div>
    </div>

    <!-- Product Performance -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Top Performing Products</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Product</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Units Sold</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Orders</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Avg Price</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($productPerformance as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ Str::limit($product->name, 40) }}</td>
                            <td class="px-4 py-4 text-sm text-right text-gray-700">{{ number_format($product->units_sold) }}</td>
                            <td class="px-4 py-4 text-sm text-right text-gray-700">{{ $product->order_count }}</td>
                            <td class="px-4 py-4 text-sm text-right text-gray-700">${{ number_format($product->average_price, 2) }}</td>
                            <td class="px-4 py-4 text-sm text-right font-semibold text-green-600">${{ number_format($product->revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-box text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No product performance data available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    locale: { rangeSeparator: " to " }
});

const trendData = @json($trends);
let currentTrendView = 'orders';

const trendCtx = document.getElementById('performanceTrendChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: trendData.labels,
        datasets: [{
            label: 'Orders',
            data: trendData.orders,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                mode: 'index',
                intersect: false,
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: { grid: { display: false } }
        }
    }
});

function toggleTrendChart(view) {
    currentTrendView = view;

    document.querySelectorAll('.trend-toggle-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white', 'border-blue-600');
        btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
    });

    if (view === 'orders') {
        document.getElementById('ordersBtn').classList.add('active', 'bg-blue-600', 'text-white', 'border-blue-600');
        document.getElementById('ordersBtn').classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
        trendChart.data.datasets[0].data = trendData.orders;
        trendChart.data.datasets[0].label = 'Orders';
        trendChart.data.datasets[0].borderColor = '#3b82f6';
        trendChart.data.datasets[0].backgroundColor = 'rgba(59, 130, 246, 0.1)';
        trendChart.data.datasets[0].pointBackgroundColor = '#3b82f6';
    } else {
        document.getElementById('revenueBtn').classList.add('active', 'bg-blue-600', 'text-white', 'border-blue-600');
        document.getElementById('revenueBtn').classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
        trendChart.data.datasets[0].data = trendData.revenue;
        trendChart.data.datasets[0].label = 'Revenue ($)';
        trendChart.data.datasets[0].borderColor = '#10b981';
        trendChart.data.datasets[0].backgroundColor = 'rgba(16, 185, 129, 0.1)';
        trendChart.data.datasets[0].pointBackgroundColor = '#10b981';
    }

    trendChart.update();
}
</script>

<style>
.trend-toggle-btn.active {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}
</style>
@endsection
