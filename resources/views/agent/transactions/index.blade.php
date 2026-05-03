@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print { .no-print { display: none !important; } }
</style>
@endpush

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Credit Transactions</h1>
            <p class="mt-1 text-xs text-gray-500">All your credit transaction history and statistics</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="{{ route('agent.transactions.print') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i> Print
            </a>
            <form action="{{ route('agent.transactions.export') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to"   value="{{ request('date_to') }}">
                <input type="hidden" name="type"      value="{{ request('type') }}">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-sm">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </form>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-gray-200 no-print overflow-x-auto">
        <button onclick="switchTab('overview')" id="tab-overview"
            class="tab-btn px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 whitespace-nowrap">
            Overview
        </button>
        <button onclick="switchTab('graph')" id="tab-graph"
            class="tab-btn px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-900 whitespace-nowrap">
            Graph
        </button>
        <button onclick="switchTab('breakdown')" id="tab-breakdown"
            class="tab-btn px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-900 whitespace-nowrap">
            By Type
        </button>
        <button onclick="switchTab('table')" id="tab-table"
            class="tab-btn px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-900 whitespace-nowrap">
            Transactions
        </button>
    </div>

    {{-- ── OVERVIEW ──────────────────────────────────────────────────── --}}
    <div id="section-overview">

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

            <div class="stat-card col-span-2 lg:col-span-1 bg-blue-600 rounded-xl p-5 text-white shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-100 uppercase tracking-wider">Credit Balance</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($stats['total_credits'], 2) }}</p>
                        <p class="text-xs text-blue-100 mt-1">credits</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-coins text-xl text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card col-span-2 lg:col-span-1 bg-emerald-600 rounded-xl p-5 text-white shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Monetary Value</p>
                        <p class="text-3xl font-bold mt-1">${{ number_format($stats['monetary_value'], 2) }}</p>
                        <p class="text-xs text-emerald-100 mt-1">× ${{ $stats['multiplier'] }} per credit</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-xl text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-500">This Month</p>
                    <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar text-purple-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['this_month'], 2) }}</p>
                @php $diff = $stats['this_month'] - $stats['last_month']; @endphp
                <p class="text-xs mt-1 {{ $diff >= 0 ? 'text-green-600' : 'text-red-500' }}">
                    <i class="fas fa-arrow-{{ $diff >= 0 ? 'up' : 'down' }} text-[10px]"></i>
                    {{ number_format(abs($diff), 2) }} vs last month
                </p>
            </div>

            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-500">Total Transacted</p>
                    <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-layer-group text-amber-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_transacted'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['total_count'] }} total records</p>
            </div>
        </div>

        {{-- Mini Chart + Top Types --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">

            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Credits — Last 6 Months</h3>
                @if(array_sum($chartData) > 0)
                    <div class="flex items-end gap-2 h-36">
                        @php $maxVal = max($chartData) ?: 1; @endphp
                        @foreach($chartData as $i => $total)
                            @php $pct = round(($total / $maxVal) * 100); @endphp
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <span class="text-[10px] text-gray-500 font-medium">{{ number_format($total, 0) }}</span>
                                <div class="w-full bg-gray-100 rounded-t-md relative" style="height:100px">
                                    <div class="absolute bottom-0 left-0 right-0 bg-blue-500 rounded-t-md"
                                         style="height:{{ $pct }}%"></div>
                                </div>
                                <span class="text-[10px] text-gray-500">{{ $chartMonths[$i] }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-36 text-gray-300">
                        <i class="fas fa-chart-bar text-4xl mb-2"></i>
                        <p class="text-sm">No data yet</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Top Transaction Types</h3>
                @forelse($typeBreakdown->take(5) as $row)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tag text-blue-600 text-xs"></i>
                            </div>
                            <span class="text-xs text-gray-700 font-medium truncate capitalize">
                                {{ str_replace('_', ' ', $row->transaction_type) }}
                            </span>
                        </div>
                        <div class="text-right ml-2 flex-shrink-0">
                            <p class="text-xs font-bold text-gray-900">{{ number_format($row->total, 2) }}</p>
                            <p class="text-[10px] text-gray-400">{{ $row->count }} entries</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center mt-6">No data yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── GRAPH TAB ─────────────────────────────────────────────────── --}}
    <div id="section-graph" class="hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Credits per Month — Last 6 Months</h3>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span class="inline-block w-3 h-3 rounded-sm bg-blue-500"></span> Credits
                </div>
            </div>
            <div style="position:relative; height:300px;">
                <canvas id="txChart"></canvas>
            </div>
        </div>

        {{-- Type breakdown chart --}}
        <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Credits by Type</h3>
                <div style="position:relative; height:260px;">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Total records</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stats['total_count'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Total credits transacted</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($stats['total_transacted'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">This month</span>
                        <span class="text-sm font-bold text-blue-600">{{ number_format($stats['this_month'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Last month</span>
                        <span class="text-sm font-bold text-gray-600">{{ number_format($stats['last_month'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Current balance</span>
                        <span class="text-sm font-bold text-emerald-600">{{ number_format($stats['total_credits'], 2) }} credits</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-gray-500">Monetary value</span>
                        <span class="text-sm font-bold text-emerald-600">${{ number_format($stats['monetary_value'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── BY TYPE TAB ──────────────────────────────────────────────── --}}
    <div id="section-breakdown" class="hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-800">Credits Breakdown by Transaction Type</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total Credits</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Count</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php $grandTotal = $typeBreakdown->sum('total') ?: 1; @endphp
                        @forelse($typeBreakdown as $row)
                            @php $pct = round(($row->total / $grandTotal) * 100); @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-tag text-blue-600 text-sm"></i>
                                        </div>
                                        <span class="font-semibold text-gray-900 text-sm capitalize">
                                            {{ str_replace('_', ' ', $row->transaction_type) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-base font-bold text-blue-700">{{ number_format($row->total, 2) }}</span>
                                    <span class="text-xs text-gray-400 ml-1">credits</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                                        {{ $row->count }} {{ Str::plural('entry', $row->count) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 w-24">
                                            <div class="bg-blue-500 h-2 rounded-full" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-600">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-gray-400 text-sm">No transaction data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── TRANSACTIONS TABLE TAB ───────────────────────────────────── --}}
    <div id="section-table" class="hidden">

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" action="{{ route('agent.transactions.index') }}" class="flex flex-wrap gap-3">
                <input type="hidden" name="tab" value="table">

                <div class="flex-1 min-w-[180px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search transaction type…"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($stats['types'] as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-1">
                    <input type="text" id="dateRange" placeholder="Date range"
                        class="w-44 px-3 py-2 border border-gray-300 rounded-lg text-sm cursor-pointer" readonly>
                    <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to"   id="dateTo"   value="{{ request('date_to') }}">
                </div>

                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('agent.transactions.index') }}?tab=table"
                   class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mt-4">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-800">All Credit Transactions</h2>
                <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                    {{ $transactions->total() }} {{ Str::plural('record', $transactions->total()) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Transaction Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Credits</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="text-xs font-mono font-semibold text-gray-400">
                                        #{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full capitalize">
                                        <i class="fas fa-tag text-[9px]"></i>
                                        {{ str_replace('_', ' ', $tx->transaction_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-bold text-blue-700">
                                        {{ number_format($tx->credits, 2) }}
                                    </span>
                                    <span class="text-xs text-gray-400 ml-1">credits</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ $tx->created_at->format('M d, Y') }}
                                    <p class="text-[10px] text-gray-300">{{ $tx->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-exchange-alt text-3xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No transactions found</p>
                                        <p class="text-xs text-gray-400 mt-1">Your credit transactions will appear here</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($transactions->count() > 0)
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-xs font-semibold text-gray-600">Page total:</td>
                                <td class="px-4 py-3 text-sm font-bold text-blue-700">
                                    {{ number_format($transactions->sum('credits'), 2) }} credits
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $transactions->count() }} records</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-500">
                        Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}
                    </span>
                    <div class="text-sm">{{ $transactions->links() }}</div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Flatpickr ──────────────────────────────────────────────────────────
flatpickr('#dateRange', {
    mode: 'range',
    dateFormat: 'Y-m-d',
    onChange(dates) {
        if (dates.length === 2) {
            document.getElementById('dateFrom').value = flatpickr.formatDate(dates[0], 'Y-m-d');
            document.getElementById('dateTo').value   = flatpickr.formatDate(dates[1], 'Y-m-d');
        }
    },
    defaultDate: [
        document.getElementById('dateFrom')?.value,
        document.getElementById('dateTo')?.value,
    ].filter(Boolean),
});

// ── Tab Switching ──────────────────────────────────────────────────────
const TABS = ['overview', 'graph', 'breakdown', 'table'];

function switchTab(name) {
    TABS.forEach(t => {
        document.getElementById('section-' + t).classList.add('hidden');
        const btn = document.getElementById('tab-' + t);
        btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        btn.classList.add('text-gray-500');
    });
    document.getElementById('section-' + name).classList.remove('hidden');
    const active = document.getElementById('tab-' + name);
    active.classList.remove('text-gray-500');
    active.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
}

// ── Charts ─────────────────────────────────────────────────────────────
const chartMonths = @json($chartMonths);
const chartData   = @json($chartData);
const typeLabels  = @json($typeBreakdown->pluck('transaction_type')->map(fn($t) => ucwords(str_replace('_', ' ', $t))));
const typeValues  = @json($typeBreakdown->pluck('total'));

const COLORS = [
    'rgba(59,130,246,0.85)',
    'rgba(16,185,129,0.85)',
    'rgba(245,158,11,0.85)',
    'rgba(139,92,246,0.85)',
    'rgba(239,68,68,0.85)',
    'rgba(20,184,166,0.85)',
];

document.addEventListener('DOMContentLoaded', () => {
    // Monthly bar chart
    const barCtx = document.getElementById('txChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: chartMonths,
                datasets: [{
                    label: 'Credits',
                    data: chartData,
                    backgroundColor: 'rgba(59,130,246,0.85)',
                    borderRadius: 5,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ctx.parsed.y.toFixed(2) + ' credits' } },
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { callback: v => v + ' cr' },
                    },
                },
            },
        });
    }

    // Doughnut by type
    const doughCtx = document.getElementById('typeChart');
    if (doughCtx && typeValues.length > 0) {
        new Chart(doughCtx, {
            type: 'doughnut',
            data: {
                labels: typeLabels,
                datasets: [{
                    data: typeValues,
                    backgroundColor: COLORS,
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 } } },
                    tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed.toFixed(2) + ' credits' } },
                },
            },
        });
    }

    // Restore tab from URL
    const tab = new URLSearchParams(window.location.search).get('tab') || 'overview';
    switchTab(tab);
});
</script>
@endpush
