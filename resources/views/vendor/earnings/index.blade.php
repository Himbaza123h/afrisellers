@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .tab-button { transition: all 0.3s; }
    .tab-button.active { border-bottom: 3px solid #ff0808; color: #ff0808; font-weight: 600; }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Earnings Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Track your revenue and completed transactions</p>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Earnings</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_earnings'], 2) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        @if($stats['earnings_change'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-arrow-up mr-1 text-[10px]"></i> {{ number_format(abs($stats['earnings_change']), 1) }}%
                            </span>
                        @elseif($stats['earnings_change'] < 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-arrow-down mr-1 text-[10px]"></i> {{ number_format(abs($stats['earnings_change']), 1) }}%
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-minus mr-1 text-[10px]"></i> 0%
                            </span>
                        @endif
                        <span class="text-xs text-gray-500">vs last period</span>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Transactions</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_transactions']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-check-circle mr-1 text-[10px]"></i> Completed
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-receipt text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Average Transaction</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($stats['average_transaction'], 2) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-chart-line mr-1 text-[10px]"></i> Per order
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-chart-bar text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Top Earning Day</p>
                    @if($stats['top_days']->isNotEmpty())
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['top_days']->first()->total, 2) }}</p>
                        <div class="mt-3">
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($stats['top_days']->first()->date)->format('M d, Y') }}</span>
                        </div>
                    @else
                        <p class="text-2xl font-bold text-gray-900">$0.00</p>
                        <div class="mt-3">
                            <span class="text-xs text-gray-500">No data</span>
                        </div>
                    @endif
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl">
                    <i class="fas fa-trophy text-2xl text-amber-600"></i>
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

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('vendor.earnings.index', array_merge(request()->except('tab'), ['tab' => 'list'])) }}"
                   class="tab-button py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'list' ? 'active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-list mr-2"></i>
                    List View
                </a>
                <a href="{{ route('vendor.earnings.index', array_merge(request()->except('tab'), ['tab' => 'statistics'])) }}"
                   class="tab-button py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'statistics' ? 'active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-chart-line mr-2"></i>
                    Statistics
                </a>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            @if($activeTab === 'list')
                @include('vendor.earnings.partials.list-view')
            @else
                @include('vendor.earnings.partials.statistics-view')
            @endif
        </div>
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
