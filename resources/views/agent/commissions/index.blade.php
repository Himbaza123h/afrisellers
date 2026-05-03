@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print { .no-print { display: none !important; } }
</style>
@endpush

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}k
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Credits Management</h1>
            <p class="mt-1 text-xs text-gray-500">Track your credits, transactions and performance targets</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="{{ route('agent.rewards.index') }}"
                class="inline-flex items-center gap-2 px-3 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 text-sm font-medium shadow-sm">
                <i class="fas fa-gift"></i> Rewards
            </a>

            <button onclick="printReport()"
                class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-gray-200 no-print overflow-x-auto">
        <button onclick="switchTab('overview')" id="tab-overview"
            class="tab-button px-4 py-2 text-sm font-semibold text-green-600 border-b-2 border-green-600 whitespace-nowrap">
            Overview
        </button>
        <button onclick="switchTab('graph')" id="tab-graph"
            class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 whitespace-nowrap">
            Graph
        </button>
        <button onclick="switchTab('catalog')" id="tab-catalog"
            class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 whitespace-nowrap">
            Catalog
        </button>
        <button onclick="switchTab('table')" id="tab-table"
            class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 whitespace-nowrap">
            Transactions
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3 no-print">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- ── OVERVIEW TAB ─────────────────────────────────────────────── --}}
    <div id="section-overview">

        {{-- Top credit cards --}}
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
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['this_month_credits'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">${{ number_format($stats['this_month_value'], 2) }}</p>
                @php $diff = $stats['this_month_credits'] - $stats['last_month_credits']; @endphp
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
                <p class="text-xs text-gray-400 mt-1">{{ $stats['total_count'] }} transactions</p>
            </div>
        </div>

        {{-- Type Breakdown + Mini chart --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">

            {{-- Mini bar chart --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Credits — Last 6 Months</h3>
                @if(array_sum($chartCredits) > 0)
                    <div class="flex items-end gap-2 h-36">
                        @php $maxVal = max($chartCredits) ?: 1; @endphp
                        @foreach($chartCredits as $i => $total)
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

            {{-- Type breakdown --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Credits by Type</h3>
                @forelse($typeBreakdown->take(6) as $row)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 bg-blue-100 rounded-md flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tag text-blue-600 text-[9px]"></i>
                            </div>
                            <span class="text-xs text-gray-700 font-medium truncate capitalize">
                                {{ str_replace('_', ' ', $row->transaction_type) }}
                            </span>
                        </div>
                        <div class="text-right ml-2 flex-shrink-0">
                            <p class="text-xs font-bold text-gray-900">{{ number_format($row->total, 2) }}</p>
                            <p class="text-[10px] text-gray-400">{{ $row->count }}×</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center mt-6">No transactions yet</p>
                @endforelse
            </div>
        </div>

        {{-- Targets --}}
        @if($targets->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mt-4">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-bullseye text-red-500"></i>
                Performance Targets
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($targets as $target)
                <div class="p-4 rounded-lg border {{ $target->reached ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-600 capitalize">
                            <i class="fas fa-{{ $target->target_type === 'monthly' ? 'calendar-alt' : ($target->target_type === 'yearly' ? 'calendar' : 'calendar-week') }} mr-1"></i>
                            {{ ucfirst($target->target_type) }} Target
                        </span>
                        @if($target->reached)
                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">
                                <i class="fas fa-check mr-1"></i>Reached!
                            </span>
                        @else
                            <span class="text-xs text-gray-500">{{ $target->percentage }}%</span>
                        @endif
                    </div>
                    <div class="flex items-baseline justify-between mb-2">
                        <span class="text-lg font-bold text-gray-900">${{ number_format($target->progress, 2) }}</span>
                        <span class="text-xs text-gray-400">/ ${{ number_format($target->target_amount, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="h-2 rounded-full {{ $target->reached ? 'bg-green-500' : 'bg-blue-500' }}"
                             style="width:{{ $target->percentage }}%"></div>
                    </div>
                    @if($target->prize)
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-gift text-amber-500 mr-1"></i>Prize: {{ $target->prize }}
                        </p>
                    @endif
                    @if($target->end_at)
                        <p class="text-[10px] text-gray-400 mt-0.5">
                            <i class="fas fa-clock mr-1"></i>Ends {{ $target->end_at->format('M d, Y') }}
                        </p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- ── GRAPH TAB ─────────────────────────────────────────────────── --}}
    <div id="section-graph" class="hidden space-y-4">

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Credits & Monetary Value — Last 6 Months</h3>
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-sm bg-blue-500"></span> Credits
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-sm bg-emerald-500"></span> Value ($)
                    </span>
                </div>
            </div>
            <div style="position:relative; height:300px;">
                <canvas id="creditsChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Type doughnut --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Credits by Transaction Type</h3>
                <div style="position:relative; height:260px;">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>

            {{-- Summary --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Credit balance</span>
                        <span class="text-sm font-bold text-blue-600">{{ number_format($stats['total_credits'], 2) }} cr</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Monetary value</span>
                        <span class="text-sm font-bold text-emerald-600">${{ number_format($stats['monetary_value'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">This month</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($stats['this_month_credits'], 2) }} cr</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">This week</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($stats['this_week_credits'], 2) }} cr</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">This year</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($stats['this_year_credits'], 2) }} cr</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-gray-500">Total transactions</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stats['total_count'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Targets on graph tab too --}}
        @if($targets->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-bullseye text-red-500"></i> Performance Targets
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($targets as $target)
                <div class="p-4 rounded-lg border {{ $target->reached ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-600 capitalize">
                            {{ ucfirst($target->target_type) }} Target
                        </span>
                        @if($target->reached)
                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">
                                <i class="fas fa-check mr-1"></i>Reached!
                            </span>
                        @else
                            <span class="text-xs text-gray-500">{{ $target->percentage }}%</span>
                        @endif
                    </div>
                    <div class="flex items-baseline justify-between mb-2">
                        <span class="text-lg font-bold text-gray-900">${{ number_format($target->progress, 2) }}</span>
                        <span class="text-xs text-gray-400">/ ${{ number_format($target->target_amount, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="h-2 rounded-full {{ $target->reached ? 'bg-green-500' : 'bg-blue-500' }}"
                             style="width:{{ $target->percentage }}%"></div>
                    </div>
                    @if($target->prize)
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-gift text-amber-500 mr-1"></i>Prize: {{ $target->prize }}
                        </p>
                    @endif
                    @if($target->end_at)
                        <p class="text-[10px] text-gray-400 mt-0.5">
                            <i class="fas fa-clock mr-1"></i>Ends {{ $target->end_at->format('M d, Y') }}
                        </p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── CATALOG TAB ──────────────────────────────────────────────── --}}
    <div id="section-catalog" class="hidden space-y-4">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-blue-600 rounded-xl p-5 text-white shadow-md">
                <p class="text-xs font-semibold text-blue-100 uppercase tracking-wider mb-1">Total Credits</p>
                <p class="text-3xl font-bold">{{ number_format($stats['total_credits'], 2) }}</p>
                <p class="text-xs text-blue-100 mt-1">current balance</p>
            </div>
            <div class="bg-emerald-600 rounded-xl p-5 text-white shadow-md">
                <p class="text-xs font-semibold text-emerald-100 uppercase tracking-wider mb-1">Monetary Value</p>
                <p class="text-3xl font-bold">${{ number_format($stats['monetary_value'], 2) }}</p>
                <p class="text-xs text-emerald-100 mt-1">at ${{ $stats['multiplier'] }} per credit</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Credit Rate</p>
                <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['multiplier'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">per 1 credit</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-800">Credits Catalog</h2>
                <p class="text-xs text-gray-400 mt-0.5">All credit types available and how much each action earns you</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Credit Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Credits Earned</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Monetary Value</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Times Earned</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($creditsCatalog as $credit)
                            @php
                                $earned = $typeBreakdown->firstWhere('transaction_type', $credit->type);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-xs text-gray-400">{{ str_pad($credit->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full capitalize">
                                        <i class="fas fa-tag text-[9px]"></i>
                                        {{ str_replace('_', ' ', $credit->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-bold text-gray-900">{{ number_format($credit->value, 2) }}</span>
                                    <span class="text-xs text-gray-400 ml-1">credits</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-bold text-emerald-700">
                                        ${{ number_format($credit->value * $multiplier, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($earned)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                            {{ $earned->count }} times
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Not yet</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm">
                                    No credit types defined yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── TRANSACTIONS TABLE TAB ───────────────────────────────────── --}}
    <div id="section-table" class="hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- Filters --}}
            <div class="p-4 border-b border-gray-200 no-print">
                <form method="GET" action="{{ route('agent.commissions.index') }}" class="flex flex-wrap gap-3">
                    <input type="hidden" name="tab" value="table">

                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Search transaction type…"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                    </div>

                    <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                        <option value="all">All Types</option>
                        @foreach($availableTypes as $type)
                            <option value="{{ $type }}" {{ $typeFilter == $type ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="date_filter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                        <option value="all"        {{ $dateFilter == 'all'        ? 'selected' : '' }}>All Time</option>
                        <option value="today"      {{ $dateFilter == 'today'      ? 'selected' : '' }}>Today</option>
                        <option value="this_week"  {{ $dateFilter == 'this_week'  ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="this_year"  {{ $dateFilter == 'this_year'  ? 'selected' : '' }}>This Year</option>
                    </select>

                    <select name="sort_order" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                        <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc"  {{ $sortOrder == 'asc'  ? 'selected' : '' }}>Oldest First</option>
                    </select>

                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    @if($search || $typeFilter != 'all' || $dateFilter != 'all')
                        <a href="{{ route('agent.commissions.index') }}?tab=table"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Transaction Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Credits</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Value ($)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-gray-50">
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
                                    <span class="text-sm font-bold text-blue-700">{{ number_format($tx->credits, 2) }}</span>
                                    <span class="text-xs text-gray-400 ml-1">cr</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-bold text-emerald-700">
                                        ${{ number_format($tx->credits * $multiplier, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-900">{{ $tx->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $tx->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-coins text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 mb-1">No transactions found</p>
                                        <p class="text-xs text-gray-500">Credit transactions appear here when you earn them</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($transactions->count() > 0)
                        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                            <tr class="font-bold">
                                <td colspan="2" class="px-4 py-3 text-sm text-gray-900">Page total:</td>
                                <td class="px-4 py-3 text-base text-blue-700">
                                    {{ number_format($transactions->sum('credits'), 2) }} cr
                                </td>
                                <td class="px-4 py-3 text-base text-emerald-700">
                                    ${{ number_format($transactions->sum('credits') * $multiplier, 2) }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $transactions->count() }} record(s)</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 no-print">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ALL_TABS = ['overview', 'graph', 'catalog', 'table'];

function switchTab(tab) {
    ALL_TABS.forEach(t => {
        document.getElementById('section-' + t).style.display = 'none';
        const btn = document.getElementById('tab-' + t);
        btn.classList.remove('text-green-600', 'border-b-2', 'border-green-600');
        btn.classList.add('text-gray-600');
    });

    document.getElementById('section-' + tab).style.display = 'block';
    const active = document.getElementById('tab-' + tab);
    active.classList.remove('text-gray-600');
    active.classList.add('text-green-600', 'border-b-2', 'border-green-600');
}

const chartMonths  = @json($chartMonths);
const chartCredits = @json($chartCredits);
const chartValues  = @json($chartValues);
const typeLabels   = @json($typeBreakdown->pluck('transaction_type')->map(fn($t) => ucwords(str_replace('_', ' ', $t))));
const typeValues   = @json($typeBreakdown->pluck('total'));

const COLORS = [
    'rgba(59,130,246,0.85)',
    'rgba(16,185,129,0.85)',
    'rgba(245,158,11,0.85)',
    'rgba(139,92,246,0.85)',
    'rgba(239,68,68,0.80)',
    'rgba(20,184,166,0.85)',
    'rgba(249,115,22,0.85)',
];

document.addEventListener('DOMContentLoaded', () => {
    // Bar chart — credits + value
    const barCtx = document.getElementById('creditsChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: chartMonths,
                datasets: [
                    {
                        label: 'Credits',
                        data: chartCredits,
                        backgroundColor: 'rgba(59,130,246,0.85)',
                        borderRadius: 4,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Value ($)',
                        data: chartValues,
                        backgroundColor: 'rgba(16,185,129,0.80)',
                        borderRadius: 4,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.datasetIndex === 0
                                ? ctx.parsed.y.toFixed(2) + ' cr'
                                : '$' + ctx.parsed.y.toFixed(2),
                        },
                    },
                },
                scales: {
                    x:  { grid: { display: false } },
                    y:  {
                        beginAtZero: true,
                        position: 'left',
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { callback: v => v + ' cr' },
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { display: false },
                        ticks: { callback: v => '$' + v },
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
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.label + ': ' + ctx.parsed.toFixed(2) + ' cr',
                        },
                    },
                },
            },
        });
    }

    const tab = new URLSearchParams(window.location.search).get('tab') || 'overview';
    switchTab(tab);
});

function printReport() {
    const params = new URLSearchParams(window.location.search);
    window.open('{{ route("agent.commissions.print") }}?' + params.toString(), '_blank');
}
</script>
@endpush
