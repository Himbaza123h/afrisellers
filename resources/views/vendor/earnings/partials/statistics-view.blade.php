<!-- Date Range Selector -->
<div class="mb-6">
    <form method="GET" action="{{ route('vendor.earnings.index') }}" class="flex flex-wrap gap-3 items-center">
        <input type="hidden" name="tab" value="statistics">

        <label class="text-sm font-medium text-gray-700">Date Range:</label>

        <div class="relative">
            <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Select date range" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg w-56 cursor-pointer bg-white">
            <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
        </div>

        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
            <i class="fas fa-sync"></i> Update
        </button>
    </form>
</div>

<!-- Main Chart -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Earnings Overview</h3>
            <p class="text-sm text-gray-500 mt-1">Revenue trends over time</p>
        </div>
        <div class="flex gap-2">
            <button onclick="toggleChart('amount')" id="amountBtn" class="chart-toggle-btn active px-4 py-2 text-sm font-medium rounded-lg border transition-all">
                Amount
            </button>
            <button onclick="toggleChart('count')" id="countBtn" class="chart-toggle-btn px-4 py-2 text-sm font-medium rounded-lg border transition-all">
                Transactions
            </button>
        </div>
    </div>
    <div class="relative" style="height: 400px;">
        <canvas id="earningsChart"></canvas>
    </div>
</div>

<!-- Secondary Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Payment Methods Breakdown -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Payment Methods</h3>
        <div class="relative" style="height: 300px;">
            <canvas id="paymentMethodsChart"></canvas>
        </div>

        <!-- Legend -->
        <div class="mt-6 space-y-3">
            @foreach($stats['payment_methods'] as $method)
                @php
                    $colors = [
                        'cash' => ['bg-green-100', 'text-green-800'],
                        'credit_card' => ['bg-blue-100', 'text-blue-800'],
                        'bank_transfer' => ['bg-purple-100', 'text-purple-800'],
                        'paypal' => ['bg-indigo-100', 'text-indigo-800'],
                        'stripe' => ['bg-pink-100', 'text-pink-800'],
                        'other' => ['bg-gray-100', 'text-gray-800'],
                    ];
                    $color = $colors[$method->payment_method] ?? ['bg-gray-100', 'text-gray-800'];
                @endphp
                <div class="flex items-center justify-between p-3 {{ $color[0] }} rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-current {{ $color[1] }}"></span>
                        <span class="text-sm font-medium {{ $color[1] }}">
                            {{ ucfirst(str_replace('_', ' ', $method->payment_method ?? 'Unknown')) }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold {{ $color[1] }}">${{ number_format($method->total, 2) }}</p>
                        <p class="text-xs {{ $color[1] }} opacity-75">{{ $method->count }} transactions</p>
                    </div>
                </div>
            @endforeach

            @if($stats['payment_methods']->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-pie text-lg mb-2"></i>
                    <p class="text-sm">No payment data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Top Earning Days -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Top Earning Days</h3>

        <div class="space-y-4">
            @foreach($stats['top_days'] as $index => $day)
                @php
                    $maxAmount = $stats['top_days']->first()->total;
                    $percentage = ($day->total / $maxAmount) * 100;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-600') }}">
                                <span class="text-sm font-bold">{{ $index + 1 }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($day->date)->format('l') }}</p>
                            </div>
                        </div>
                        <span class="text-base font-bold text-gray-900">${{ number_format($day->total, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $index === 0 ? 'bg-yellow-500' : ($index === 1 ? 'bg-gray-500' : 'bg-orange-500') }}"
                             style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach

            @if($stats['top_days']->isEmpty())
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-calendar-day text-4xl mb-3"></i>
                    <p class="text-sm font-medium">No earnings data available</p>
                    <p class="text-xs mt-1">Complete transactions will appear here</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Additional Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-week text-white text-xl"></i>
            </div>
            <span class="text-xs font-semibold text-blue-600 uppercase">This Period</span>
        </div>
        <p class="text-2xl font-bold text-blue-900 mb-1">${{ number_format($stats['total_earnings'], 2) }}</p>
        <p class="text-sm text-blue-700">Total earnings collected</p>
    </div>

    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-percentage text-white text-xl"></i>
            </div>
            <span class="text-xs font-semibold text-green-600 uppercase">Growth</span>
        </div>
        <p class="text-2xl font-bold text-green-900 mb-1">
            {{ $stats['earnings_change'] > 0 ? '+' : '' }}{{ number_format($stats['earnings_change'], 1) }}%
        </p>
        <p class="text-sm text-green-700">Compared to last period</p>
    </div>

    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
            <span class="text-xs font-semibold text-purple-600 uppercase">Average</span>
        </div>
        <p class="text-2xl font-bold text-purple-900 mb-1">${{ number_format($stats['average_transaction'], 2) }}</p>
        <p class="text-sm text-purple-700">Per transaction</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    let currentView = 'amount';

    // Main Earnings Chart
    const earningsCtx = document.getElementById('earningsChart').getContext('2d');
    const earningsChart = new Chart(earningsCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Earnings ($)',
                data: chartData.amounts,
                borderColor: '#ff0808',
                backgroundColor: 'rgba(255, 8, 8, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#ff0808',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            if (currentView === 'amount') {
                                return ' $' + context.parsed.y.toFixed(2);
                            } else {
                                return ' ' + context.parsed.y + ' transactions';
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            if (currentView === 'amount') {
                                return '$' + value.toFixed(0);
                            } else {
                                return value;
                            }
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // Payment Methods Pie Chart
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    const paymentMethods = @json($stats['payment_methods']);

    const paymentMethodsChart = new Chart(paymentMethodsCtx, {
        type: 'doughnut',
        data: {
            labels: paymentMethods.map(m => m.payment_method ? m.payment_method.replace('_', ' ').toUpperCase() : 'UNKNOWN'),
            datasets: [{
                data: paymentMethods.map(m => m.total),
                backgroundColor: [
                    '#10b981', // green
                    '#3b82f6', // blue
                    '#8b5cf6', // purple
                    '#6366f1', // indigo
                    '#ec4899', // pink
                    '#6b7280'  // gray
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return ` ${label}: $${value.toFixed(2)} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Toggle Chart View
    window.toggleChart = function(view) {
        currentView = view;

        // Update button styles
        document.querySelectorAll('.chart-toggle-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white', 'border-blue-600');
            btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
        });

        if (view === 'amount') {
            document.getElementById('amountBtn').classList.add('active', 'bg-blue-600', 'text-white', 'border-blue-600');
            document.getElementById('amountBtn').classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
            earningsChart.data.datasets[0].data = chartData.amounts;
            earningsChart.data.datasets[0].label = 'Earnings ($)';
        } else {
            document.getElementById('countBtn').classList.add('active', 'bg-blue-600', 'text-white', 'border-blue-600');
            document.getElementById('countBtn').classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
            earningsChart.data.datasets[0].data = chartData.counts;
            earningsChart.data.datasets[0].label = 'Transactions';
        }

        earningsChart.update();
    };
});
</script>

<style>
.chart-toggle-btn.active {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}
</style>
