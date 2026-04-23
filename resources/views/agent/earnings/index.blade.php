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
            <h1 class="text-xl font-bold text-gray-900">My Earnings</h1>
            <p class="mt-1 text-xs text-gray-500">Commission earnings from your vendors' activity</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="{{ route('agent.earnings.print') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i> Print
            </a>
            <form action="{{ route('agent.earnings.export') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-sm">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </form>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview"
            class="tab-btn px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600">
            Overview
        </button>
        <button onclick="switchTab('transactions')" id="tab-transactions"
            class="tab-btn px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-900">
            Transactions
        </button>
        <button onclick="switchTab('vendors')" id="tab-vendors"
            class="tab-btn px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-900">
            By Vendor
        </button>
    </div>

    {{-- ── OVERVIEW TAB ────────────────────────────────────────────── --}}
    <div id="section-overview">

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            {{-- Total Earned --}}
            <div class="stat-card col-span-2 bg-green-600 rounded-xl p-5 text-white shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-100 uppercase tracking-wider">Total Earned</p>
                        <p class="text-3xl font-bold mt-1">${{ number_format($stats['total_earned'], 2) }}</p>
                        <p class="text-xs text-green-100 mt-1">{{ $stats['paid_count'] }} paid commissions</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-2xl text-white"></i>
                    </div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-500">Pending</p>
                    <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-amber-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-xl font-bold text-gray-900">${{ number_format($stats['pending'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['pending_count'] }} awaiting</p>
            </div>

            {{-- This Month --}}
            <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-500">This Month</p>
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar text-blue-600 text-sm"></i>
                    </div>
                </div>
                <p class="text-xl font-bold text-gray-900">${{ number_format($stats['this_month'], 2) }}</p>
                @php
                    $diff = $stats['this_month'] - $stats['last_month'];
                    $positive = $diff >= 0;
                @endphp
                <p class="text-xs mt-1 {{ $positive ? 'text-green-600' : 'text-red-500' }}">
                    <i class="fas fa-arrow-{{ $positive ? 'up' : 'down' }} text-[10px]"></i>
                    ${{ number_format(abs($diff), 2) }} vs last month
                </p>
            </div>
        </div>

        {{-- Chart + Breakdown --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">

            {{-- Monthly Chart --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Earnings – Last 6 Months</h3>
                @if(count($chartData) > 0)
                    <div class="flex items-end gap-2 h-36">
                        @php $maxVal = max($chartData) ?: 1; @endphp
                        @foreach($chartData as $month => $total)
                            @php
                                $pct = round(($total / $maxVal) * 100);
                                $label = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M');
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <span class="text-[10px] text-gray-500 font-medium">${{ number_format($total, 0) }}</span>
                                <div class="w-full bg-gray-100 rounded-t-md relative" style="height: 100px;">
                                    <div class="absolute bottom-0 left-0 right-0 bg-emerald-500 rounded-t-md transition-all"
                                         style="height: {{ $pct }}%"></div>
                                </div>
                                <span class="text-[10px] text-gray-500">{{ $label }}</span>
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

            {{-- Top Vendors --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Top Vendors by Earnings</h3>
                @forelse($vendorBreakdown as $row)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-store text-purple-600 text-xs"></i>
                            </div>
                            <span class="text-xs text-gray-700 font-medium truncate">
                                {{ $row->vendor?->businessProfile?->business_name ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="text-right ml-2 flex-shrink-0">
                            <p class="text-xs font-bold text-gray-900">${{ number_format($row->total, 2) }}</p>
                            <p class="text-[10px] text-gray-400">{{ $row->count }} orders</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center mt-6">No data yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── TRANSACTIONS TAB ────────────────────────────────────────── --}}
    <div id="section-transactions" class="hidden">

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" action="{{ route('agent.earnings.index') }}" class="flex flex-wrap gap-3">
                <input type="hidden" name="tab" value="transactions">

                <div class="flex-1 min-w-[180px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search reference or order #…"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <select name="vendor_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">All Vendors</option>
                        @foreach($myVendors as $v)
                            <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->businessProfile?->business_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="paid"     {{ request('status') == 'paid'     ? 'selected' : '' }}>Paid</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="flex gap-1">
                    <input type="text" id="dateRange" placeholder="Date range"
                        class="w-44 px-3 py-2 border border-gray-300 rounded-lg text-sm cursor-pointer focus:ring-2 focus:ring-blue-500" readonly>
                    <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to"   id="dateTo"   value="{{ request('date_to') }}">
                </div>

                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('agent.earnings.index') }}?tab=transactions"
                   class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </form>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mt-4">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-800">Commission Transactions</h2>
                <span class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-100 rounded-full">
                    {{ $earnings->total() }} {{ Str::plural('record', $earnings->total()) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reference</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($earnings as $earning)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="text-xs font-mono font-semibold text-gray-700">
                                        {{ $earning->reference ?? 'COM-' . str_pad($earning->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-store text-purple-500 text-xs"></i>
                                        </div>
                                        <span class="text-sm text-gray-800">
                                            {{ $earning->vendor?->businessProfile?->business_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($earning->order)
                                        <span class="text-xs font-mono text-blue-600">{{ $earning->order->order_number }}</span>
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            ${{ number_format($earning->order->total, 2) }} order total
                                        </p>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-bold text-emerald-700">
                                        ${{ number_format($earning->amount, 2) }}
                                    </span>
                                    @if($earning->rate)
                                        <p class="text-[10px] text-gray-400">{{ $earning->rate }}% rate</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $map = [
                                            'paid'     => 'bg-green-100 text-green-700',
                                            'pending'  => 'bg-amber-100 text-amber-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                        ];
                                        $cls = $map[$earning->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                        <i class="fas fa-circle text-[6px]"></i>
                                        {{ ucfirst($earning->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ $earning->created_at->format('M d, Y') }}
                                    <p class="text-[10px] text-gray-300">{{ $earning->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-dollar-sign text-3xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No earnings found</p>
                                        <p class="text-xs text-gray-400 mt-1">Earnings appear here as your vendors make sales</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($earnings->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-500">
                        Showing {{ $earnings->firstItem() }}–{{ $earnings->lastItem() }} of {{ $earnings->total() }}
                    </span>
                    <div class="text-sm">{{ $earnings->links() }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── BY VENDOR TAB ────────────────────────────────────────────── --}}
    <div id="section-vendors" class="hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-800">Earnings Breakdown by Vendor</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total Earned</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Commissions</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php $grandTotal = $vendorBreakdown->sum('total') ?: 1; @endphp
                        @forelse($vendorBreakdown as $row)
                            @php $pct = round(($row->total / $grandTotal) * 100); @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-store text-purple-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm">
                                                {{ $row->vendor?->businessProfile?->business_name ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $row->vendor?->businessProfile?->country?->name ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-base font-bold text-emerald-700">
                                        ${{ number_format($row->total, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                        {{ $row->count }} {{ Str::plural('commission', $row->count) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 w-24">
                                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-600">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-gray-400 text-sm">
                                    No vendor earnings data yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// ── Date Range ─────────────────────────────────────────────────────────
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
        document.getElementById('dateFrom').value,
        document.getElementById('dateTo').value,
    ].filter(Boolean),
});

// ── Tab Switching ──────────────────────────────────────────────────────
function switchTab(name) {
    ['overview', 'transactions', 'vendors'].forEach(t => {
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

document.addEventListener('DOMContentLoaded', () => {
    const tab = new URLSearchParams(window.location.search).get('tab') || 'overview';
    switchTab(tab);
});
</script>
@endpush
