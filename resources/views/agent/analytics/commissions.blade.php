@extends('layouts.home')

@push('styles')
<style>
    .bar-wrap { display: flex; align-items: flex-end; gap: 5px; height: 120px; }
    .bar-col  { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
</style>
@endpush

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('agent.analytics.index') }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Commission Analytics</h1>
                <p class="mt-1 text-xs text-gray-500">Deep dive into your commission earnings</p>
            </div>
        </div>
        <form method="GET" class="flex gap-2">
            <select name="period" onchange="this.form.submit()"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="3"  {{ $period == 3  ? 'selected' : '' }}>Last 3 Months</option>
                <option value="6"  {{ $period == 6  ? 'selected' : '' }}>Last 6 Months</option>
                <option value="12" {{ $period == 12 ? 'selected' : '' }}>Last 12 Months</option>
            </select>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="col-span-2 bg-emerald-500 to-green-600 rounded-xl p-5 text-white shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-100 uppercase tracking-wider">Total Paid Out</p>
                    <p class="text-3xl font-bold mt-1">${{ number_format($stats['total_paid'], 2) }}</p>
                    <p class="text-xs text-green-100 mt-1">{{ $stats['count_paid'] }} paid commissions</p>
                    @php
                        $diff = $stats['this_month'] - $stats['last_month'];
                        $positive = $diff >= 0;
                    @endphp
                    <p class="text-xs mt-1 {{ $positive ? 'text-green-200' : 'text-red-200' }}">
                        <i class="fas fa-arrow-{{ $positive ? 'up' : 'down' }} text-[10px]"></i>
                        ${{ number_format(abs($diff), 2) }} vs last month
                    </p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-coins text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Pending</p>
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">${{ number_format($stats['total_pending'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['count_pending'] }} awaiting</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Avg per Commission</p>
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calculator text-blue-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">${{ number_format($stats['avg_commission'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">This month: ${{ number_format($stats['this_month'], 2) }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Monthly Paid vs Pending Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Monthly Commissions — Paid vs Pending</h3>
                <span class="text-xs text-gray-400">Last {{ $period }} months</span>
            </div>
            @if($monthlyChart->count() > 0)
                @php $maxM = max($monthlyChart->max('paid'), $monthlyChart->max('pending')) ?: 1; @endphp
                <div class="bar-wrap">
                    @foreach($monthlyChart as $row)
                        @php
                            $paidPct    = round(($row->paid / $maxM) * 100);
                            $pendingPct = round(($row->pending / $maxM) * 100);
                            $label      = \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M');
                        @endphp
                        <div class="bar-col">
                            <span style="font-size:9px;font-weight:600;color:#6b7280;">
                                ${{ number_format($row->paid + $row->pending, 0) }}
                            </span>
                            <div class="w-full flex gap-0.5 relative" style="height: 100px;">
                                <div class="flex-1 bg-gray-100 rounded-t-md relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-emerald-500 rounded-t-md"
                                         style="height: {{ $paidPct }}%"></div>
                                </div>
                                <div class="flex-1 bg-gray-100 rounded-t-md relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-amber-400 rounded-t-md"
                                         style="height: {{ $pendingPct }}%"></div>
                                </div>
                            </div>
                            <span style="font-size:9px;color:#9ca3af;">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-3">
                    <span class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-3 h-3 rounded-sm bg-emerald-500 inline-block"></span> Paid
                    </span>
                    <span class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-3 h-3 rounded-sm bg-amber-400 inline-block"></span> Pending
                    </span>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-32 text-gray-300">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p class="text-sm">No data for this period</p>
                </div>
            @endif
        </div>

        {{-- Rate Distribution --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Commission Rate Distribution</h3>
            @if($rateDistribution->count() > 0)
                @php $maxRate = $rateDistribution->max('total') ?: 1; @endphp
                <div class="space-y-3">
                    @foreach($rateDistribution as $row)
                        @php $pct = round(($row->total / $maxRate) * 100); @endphp
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-xs font-bold text-gray-700">{{ $row->commission_rate }}% rate</span>
                                <span class="text-xs text-gray-500">{{ $row->count }} orders</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">
                                ${{ number_format($row->total, 2) }} total
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-32 text-gray-300">
                    <i class="fas fa-percentage text-4xl mb-2"></i>
                    <p class="text-sm">No rate data</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Per-Vendor Breakdown --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-800">Commissions by Vendor</h3>
            <a href="{{ route('agent.earnings.index') }}"
               class="text-xs text-blue-600 hover:underline font-medium">
                View Earnings <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Paid</th>
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
                            <td class="px-4 py-3">
                                <span class="font-bold text-gray-900">${{ number_format($row->total, 2) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-emerald-700 font-semibold">${{ number_format($row->paid, 2) }}</span>
                            </td>
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
                            <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm">
                                No commission data yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Commissions --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">Recent Commissions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recent as $c)
                        @php
                            $map = [
                                'paid'     => 'bg-green-100 text-green-700',
                                'pending'  => 'bg-amber-100 text-amber-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                            $cls = $map[$c->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono font-semibold text-gray-700">
                                    {{ $c->reference ?? 'COM-' . str_pad($c->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $c->vendor?->businessProfile?->business_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-emerald-700">${{ number_format($c->amount, 2) }}</span>
                                @if($c->commission_rate)
                                    <span class="text-[10px] text-gray-400 ml-1">@ {{ $c->commission_rate }}%</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                    <i class="fas fa-circle text-[6px]"></i> {{ ucfirst($c->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $c->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No recent commissions.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
