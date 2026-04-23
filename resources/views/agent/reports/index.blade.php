@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .bar-wrap { display: flex; align-items: flex-end; gap: 5px; height: 110px; }
    .bar-col  { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px; }
    @media print { .no-print { display: none !important; } }
</style>
@endpush

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Reports</h1>
            <p class="mt-1 text-xs text-gray-500">Generate, filter and export detailed reports</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="{{ route('agent.reports.print') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i> Print
            </a>
            <form action="{{ route('agent.reports.export') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="type"      value="{{ $type }}">
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to"   value="{{ request('date_to') }}">
                <input type="hidden" name="status"    value="{{ request('status') }}">
                <input type="hidden" name="vendor_id" value="{{ request('vendor_id') }}">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-sm">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="stat-card col-span-2 bg-emerald-500 to-green-600 rounded-xl p-5 text-white shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-100 uppercase tracking-wider">Total Earned</p>
                    <p class="text-3xl font-bold mt-1">${{ number_format($summary['total_earned'], 2) }}</p>
                    @php $diff = $summary['this_month'] - $summary['last_month']; $pos = $diff >= 0; @endphp
                    <p class="text-xs text-green-100 mt-1">
                        <i class="fas fa-arrow-{{ $pos ? 'up' : 'down' }} text-[10px]"></i>
                        ${{ number_format(abs($diff), 2) }} vs last month
                    </p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-chart-line text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Pending</p>
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">${{ number_format($summary['total_pending'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting payout</p>
        </div>

        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Active Vendors</p>
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-purple-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ $summary['active_vendors'] }}</p>
            <p class="text-xs text-gray-400 mt-1">of {{ $summary['total_vendors'] }} total</p>
        </div>
    </div>

    {{-- Report Type Tabs --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 no-print">
        <form method="GET" action="{{ route('agent.reports.index') }}" id="reportForm">

            {{-- Type Selector --}}
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach([
                    ['earnings',     'Earnings',     'fa-dollar-sign', 'emerald'],
                    ['vendors',      'Vendors',      'fa-store',       'purple'],
                    ['transactions', 'Transactions', 'fa-wallet',      'teal'],
                ] as [$val, $label, $icon, $color])
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="{{ $val }}"
                           {{ $type == $val ? 'checked' : '' }}
                           class="sr-only peer" onchange="document.getElementById('reportForm').submit()">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border text-sm font-medium transition-all
                        peer-checked:bg-{{ $color }}-50 peer-checked:border-{{ $color }}-300 peer-checked:text-{{ $color }}-700
                        border-gray-200 text-gray-600 hover:bg-gray-50">
                        <i class="fas {{ $icon }} text-xs"></i>
                        {{ $label }}
                    </span>
                </label>
                @endforeach
            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap gap-3">

                {{-- Date Range --}}
                <div class="flex gap-1">
                    <input type="text" id="dateRange" placeholder="Date range"
                        class="w-48 px-3 py-2 border border-gray-300 rounded-lg text-sm cursor-pointer focus:ring-2 focus:ring-blue-500" readonly>
                    <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to"   id="dateTo"   value="{{ request('date_to') }}">
                </div>

                {{-- Status --}}
                <div>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        @if($type === 'earnings')
                            <option value="paid"     {{ request('status') == 'paid'     ? 'selected' : '' }}>Paid</option>
                            <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        @elseif($type === 'vendors')
                            <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
                            <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        @elseif($type === 'transactions')
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                            <option value="failed"    {{ request('status') == 'failed'    ? 'selected' : '' }}>Failed</option>
                        @endif
                    </select>
                </div>

                {{-- Vendor filter (only for earnings / transactions) --}}
                @if($type !== 'vendors')
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
                @endif

                {{-- Period --}}
                <div>
                    <select name="period" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="3"  {{ $period == 3  ? 'selected' : '' }}>Last 3 Months</option>
                        <option value="6"  {{ $period == 6  ? 'selected' : '' }}>Last 6 Months</option>
                        <option value="12" {{ $period == 12 ? 'selected' : '' }}>Last 12 Months</option>
                    </select>
                </div>

                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-filter mr-1"></i> Apply
                </button>
                <a href="{{ route('agent.reports.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Earnings Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Monthly Earnings</h3>
                <span class="text-xs text-gray-400">Last {{ $period }} months</span>
            </div>
            @if(count($earningsChart) > 0)
                @php $maxE = max($earningsChart) ?: 1; @endphp
                <div class="bar-wrap">
                    @foreach($earningsChart as $month => $total)
                        @php
                            $pct   = round(($total / $maxE) * 100);
                            $label = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M');
                        @endphp
                        <div class="bar-col">
                            <span style="font-size:9px;font-weight:600;color:#6b7280;">${{ number_format($total,0) }}</span>
                            <div class="w-full bg-gray-100 rounded-t-md relative flex-1">
                                <div class="absolute bottom-0 left-0 right-0 bg-emerald-500 rounded-t-md"
                                     style="height: {{ $pct }}%"></div>
                            </div>
                            <span style="font-size:9px;color:#9ca3af;">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-28 text-gray-300">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p class="text-sm">No earnings data yet</p>
                </div>
            @endif
        </div>

        {{-- Commission Status Donut-style --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Commission Status</h3>
            @php
                $statusColors = [
                    'paid'     => ['bg-emerald-500', 'bg-emerald-100', 'text-emerald-700'],
                    'pending'  => ['bg-amber-400',   'bg-amber-100',   'text-amber-700'],
                    'rejected' => ['bg-red-500',     'bg-red-100',     'text-red-700'],
                ];
                $totalC = $commissionStatusChart->sum('count') ?: 1;
            @endphp
            <div class="space-y-3">
                @forelse($commissionStatusChart as $status => $row)
                    @php
                        $colors = $statusColors[$status] ?? ['bg-gray-400', 'bg-gray-100', 'text-gray-600'];
                        $pct    = round(($row->count / $totalC) * 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold {{ $colors[2] }} capitalize">{{ $status }}</span>
                            <span class="text-xs text-gray-500">{{ $row->count }} &bull; ${{ number_format($row->total,2) }}</span>
                        </div>
                        <div class="w-full {{ $colors[1] }} rounded-full h-2">
                            <div class="{{ $colors[0] }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center mt-6">No commission data yet</p>
                @endforelse
            </div>

            {{-- Vendor Growth mini --}}
            @if(count($vendorGrowthChart) > 0)
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <h4 class="text-xs font-bold text-gray-700 mb-2">Vendor Growth</h4>
                    @php $maxVG = max($vendorGrowthChart) ?: 1; @endphp
                    <div class="flex items-end gap-1" style="height:40px;">
                        @foreach($vendorGrowthChart as $month => $count)
                            @php $pct = round(($count / $maxVG) * 100); @endphp
                            <div class="flex-1 bg-gray-100 rounded-sm relative" style="height:40px;">
                                <div class="absolute bottom-0 left-0 right-0 bg-purple-400 rounded-sm"
                                     style="height: {{ $pct }}%"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Vendor Breakdown Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-800">Earnings by Vendor</h3>
            <span class="px-2 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full">
                {{ $vendorBreakdown->count() }} {{ Str::plural('vendor', $vendorBreakdown->count()) }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Paid</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pending</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Commissions</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Share</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $grandTotal = $vendorBreakdown->sum('total') ?: 1; @endphp
                    @forelse($vendorBreakdown as $row)
                        @php $pct = round(($row->total / $grandTotal) * 100); @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-store text-purple-600 text-xs"></i>
                                    </div>
                                    <span class="font-semibold text-gray-900 text-sm">
                                        {{ $row->vendor?->businessProfile?->business_name ?? 'N/A' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-bold text-gray-900">${{ number_format($row->total, 2) }}</td>
                            <td class="px-4 py-3 font-semibold text-emerald-700">${{ number_format($row->paid, 2) }}</td>
                            <td class="px-4 py-3 font-semibold text-amber-600">${{ number_format($row->pending, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                    {{ $row->count }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2 w-20">
                                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">No vendor data yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Main Records Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-800">
                @if($type === 'earnings') Commission Records
                @elseif($type === 'vendors') Vendor Records
                @else Transaction Records
                @endif
            </h3>
            <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                {{ $records->total() }} {{ Str::plural('record', $records->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">

            {{-- EARNINGS TABLE --}}
            @if($type === 'earnings')
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
                    @forelse($records as $r)
                        @php
                            $map = ['paid'=>'bg-green-100 text-green-700','pending'=>'bg-amber-100 text-amber-700','rejected'=>'bg-red-100 text-red-700'];
                            $cls = $map[$r->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-xs font-mono font-semibold text-gray-700">
                                {{ $r->reference ?? 'COM-' . str_pad($r->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $r->vendor?->businessProfile?->business_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-xs font-mono text-blue-600">
                                {{ $r->order?->order_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3 font-bold text-emerald-700">${{ number_format($r->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                    <i class="fas fa-circle text-[6px]"></i> {{ ucfirst($r->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $r->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">No commission records found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- VENDORS TABLE --}}
            @elseif($type === 'vendors')
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Business</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($records as $r)
                        @php
                            $map = ['active'=>'bg-green-100 text-green-700','pending'=>'bg-amber-100 text-amber-700','suspended'=>'bg-red-100 text-red-700','rejected'=>'bg-gray-100 text-gray-600'];
                            $cls = $map[$r->account_status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900 text-sm">{{ $r->businessProfile?->business_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $r->businessProfile?->business_type ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-800">{{ $r->user?->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $r->user?->email }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ $r->businessProfile?->city ? $r->businessProfile->city . ', ' : '' }}
                                {{ $r->businessProfile?->country?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                    <i class="fas fa-circle text-[6px]"></i> {{ ucfirst($r->account_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $r->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm">No vendor records found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- TRANSACTIONS TABLE --}}
            @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Transaction #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($records as $r)
                        @php
                            $map = ['completed'=>'bg-green-100 text-green-700','pending'=>'bg-amber-100 text-amber-700','failed'=>'bg-red-100 text-red-700'];
                            $cls = $map[$r->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-xs font-mono font-semibold text-gray-700">
                                {{ $r->transaction_number ?? 'TXN-' . str_pad($r->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3 text-xs font-mono text-blue-600">
                                {{ $r->order?->order_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3 font-bold text-teal-700">${{ number_format($r->amount, 2) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $r->payment_method ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                    <i class="fas fa-circle text-[6px]"></i> {{ ucfirst($r->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $r->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">No transaction records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @endif
        </div>

        @if($records->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500">
                    Showing {{ $records->firstItem() }}–{{ $records->lastItem() }} of {{ $records->total() }}
                </span>
                <div class="text-sm">{{ $records->links() }}</div>
            </div>
        @endif
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
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
</script>
@endpush
