@extends('layouts.home')

@push('styles')
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
            <h1 class="text-xl font-bold text-gray-900">Performance</h1>
            <p class="mt-1 text-xs text-gray-500">Clicks, impressions and CTR across your vendors' products</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <form method="GET" class="flex gap-2 items-center">
                <input type="hidden" name="search"    value="{{ request('search') }}">
                <input type="hidden" name="vendor_id" value="{{ request('vendor_id') }}">
                <input type="hidden" name="sort"      value="{{ request('sort') }}">
                <select name="period" onchange="this.form.submit()"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="3"  {{ $period == 3  ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="6"  {{ $period == 6  ? 'selected' : '' }}>Last 6 Months</option>
                    <option value="12" {{ $period == 12 ? 'selected' : '' }}>Last 12 Months</option>
                </select>
            </form>
            <a href="{{ route('agent.performance.print') }}" target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i> Print
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">

        {{-- CTR Hero --}}
        <div class="stat-card col-span-2 lg:col-span-1 bg-indigo-500 to-blue-600 rounded-xl p-5 text-white shadow-md flex flex-col justify-between">
            <div>
                <p class="text-xs font-semibold text-indigo-100 uppercase tracking-wider">Overall CTR</p>
                <p class="text-4xl font-bold mt-1">{{ $summary['overall_ctr'] }}%</p>
                <p class="text-xs text-indigo-100 mt-1">Click-through rate</p>
            </div>
            <div class="mt-4 w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-mouse-pointer text-lg"></i>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Total Clicks</p>
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-pointer text-blue-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($summary['total_clicks']) }}</p>
            @php $clickDiff = $summary['clicks_this_month'] - $summary['clicks_last_month']; $pos = $clickDiff >= 0; @endphp
            <p class="text-xs mt-1 {{ $pos ? 'text-green-600' : 'text-red-500' }}">
                <i class="fas fa-arrow-{{ $pos ? 'up' : 'down' }} text-[10px]"></i>
                {{ number_format(abs($clickDiff)) }} vs last month
            </p>
        </div>

        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Impressions</p>
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-eye text-purple-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($summary['total_impressions']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ number_format($summary['impressions_this_month']) }} this month</p>
        </div>

        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Products</p>
                <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-emerald-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($summary['total_products']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Across all vendors</p>
        </div>

        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Active Vendors</p>
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-amber-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ $summary['active_vendors'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Contributing data</p>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Monthly Clicks + Impressions Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Monthly Clicks vs Impressions</h3>
                <span class="text-xs text-gray-400">Last {{ $period }} months</span>
            </div>
            @if($monthlyChart->count() > 0)
                @php
                    $maxClicks      = $monthlyChart->max('total_clicks') ?: 1;
                    $maxImpressions = $monthlyChart->max('total_impressions') ?: 1;
                    $maxVal         = max($maxClicks, $maxImpressions);
                @endphp
                <div class="bar-wrap">
                    @foreach($monthlyChart as $row)
                        @php
                            $clickPct      = round(($row->total_clicks / $maxVal) * 100);
                            $impressionPct = round(($row->total_impressions / $maxVal) * 100);
                            $label         = \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M');
                        @endphp
                        <div class="bar-col">
                            <span style="font-size:9px;font-weight:600;color:#6b7280;">{{ number_format($row->total_clicks) }}</span>
                            <div class="w-full flex gap-0.5 relative" style="height:100px;">
                                <div class="flex-1 bg-gray-100 rounded-t-md relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-blue-500 rounded-t-md"
                                         style="height: {{ $clickPct }}%"></div>
                                </div>
                                <div class="flex-1 bg-gray-100 rounded-t-md relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-purple-300 rounded-t-md"
                                         style="height: {{ $impressionPct }}%"></div>
                                </div>
                            </div>
                            <span style="font-size:9px;color:#9ca3af;">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-3">
                    <span class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-3 h-3 rounded-sm bg-blue-500 inline-block"></span> Clicks
                    </span>
                    <span class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-3 h-3 rounded-sm bg-purple-300 inline-block"></span> Impressions
                    </span>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-32 text-gray-300">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p class="text-sm">No data for this period</p>
                </div>
            @endif
        </div>

        {{-- By Country --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Clicks by Country</h3>
            @php $maxCountry = $byCountry->max('total_clicks') ?: 1; @endphp
            <div class="space-y-3">
                @forelse($byCountry as $row)
                    @php $pct = round(($row->total_clicks / $maxCountry) * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-700 truncate max-w-[130px]">
                                {{ $row->country?->name ?? 'Unknown' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ number_format($row->total_clicks) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center mt-6">No country data yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top Products + Vendor Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Top Products by Clicks --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800">Top Products — Clicks</h3>
                <span class="text-xs text-gray-400">Top 10</span>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topProductsByClicks as $i => $row)
                    @php $maxP = $topProductsByClicks->max('total_clicks') ?: 1; $pct = round(($row->total_clicks / $maxP) * 100); @endphp
                    <a href="{{ route('agent.performance.show', $row->product_id) }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors block">
                        <span class="text-xs font-bold text-gray-400 w-4 flex-shrink-0">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ $row->product?->name ?? 'N/A' }}
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-[10px] text-gray-400 flex-shrink-0">{{ number_format($row->total_impressions) }} imp.</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-blue-600">{{ number_format($row->total_clicks) }}</p>
                            <p class="text-[10px] text-gray-400">clicks</p>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-10 text-center text-gray-400 text-sm">No product data yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Top Products by CTR --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800">Top Products — CTR</h3>
                <span class="px-2 py-0.5 text-xs bg-indigo-100 text-indigo-700 rounded-full font-semibold">Click-through rate</span>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topProductsByCtr as $i => $row)
                    @php $ctrColor = $row->ctr >= 5 ? 'text-green-600' : ($row->ctr >= 2 ? 'text-amber-600' : 'text-gray-600'); @endphp
                    <a href="{{ route('agent.performance.show', $row->product_id) }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors block">
                        <span class="text-xs font-bold text-gray-400 w-4 flex-shrink-0">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ $row->product?->name ?? 'N/A' }}
                            </p>
                            <p class="text-[10px] text-gray-400 mt-0.5">
                                {{ number_format($row->total_clicks) }} clicks / {{ number_format($row->total_impressions) }} imp.
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold {{ $ctrColor }}">{{ $row->ctr }}%</p>
                            <p class="text-[10px] text-gray-400">CTR</p>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-10 text-center text-gray-400 text-sm">No CTR data yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Vendor Breakdown --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-800">Performance by Vendor</h3>
            <span class="px-2 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full">
                {{ $vendorBreakdown->count() }} {{ Str::plural('vendor', $vendorBreakdown->count()) }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Clicks</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Impressions</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">CTR</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Products</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Share</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $maxVClicks = $vendorBreakdown->max('total_clicks') ?: 1; @endphp
                    @forelse($vendorBreakdown as $row)
                        @php
                            $pct = round(($row->total_clicks / $maxVClicks) * 100);
                            $ctrColor = ($row->ctr ?? 0) >= 5 ? 'text-green-600' : (($row->ctr ?? 0) >= 2 ? 'text-amber-600' : 'text-gray-600');
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-store text-purple-600 text-xs"></i>
                                    </div>
                                    <span class="font-semibold text-gray-900 text-sm">
                                        {{ $row->vendor?->business_name ?? 'N/A' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-bold text-blue-600">{{ number_format($row->total_clicks) }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ number_format($row->total_impressions) }}</td>
                            <td class="px-4 py-3 font-semibold {{ $ctrColor }}">{{ $row->ctr ?? 0 }}%</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                                    {{ $row->product_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2 w-20">
                                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">No vendor performance data yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- All Records with Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden no-print">
        <div class="p-4 border-b border-gray-100">
            <form method="GET" action="{{ route('agent.performance.index') }}" class="flex flex-wrap gap-3 items-center">
                <input type="hidden" name="period" value="{{ $period }}">
                <div class="flex-1 min-w-[180px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search product name…"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <select name="vendor_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Vendors</option>
                        @foreach($agentVendors as $v)
                            <option value="{{ $v->business_profile_id }}" {{ request('vendor_id') == $v->business_profile_id ? 'selected' : '' }}>
                                {{ $v->businessProfile?->business_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="sort" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="clicks"      {{ request('sort','clicks') == 'clicks'      ? 'selected' : '' }}>Sort: Clicks</option>
                        <option value="impressions" {{ request('sort') == 'impressions' ? 'selected' : '' }}>Sort: Impressions</option>
                        <option value="ctr"         {{ request('sort') == 'ctr'         ? 'selected' : '' }}>Sort: CTR</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('agent.performance.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </form>
        </div>

        <div class="p-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-800">Product Performance Records</h3>
            <span class="px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full">
                {{ $records->total() }} {{ Str::plural('product', $records->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Clicks</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Impressions</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">CTR</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Last Recorded</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($records as $row)
                        @php
                            $ctr      = $row->ctr ?? 0;
                            $ctrColor = $ctr >= 5 ? 'text-green-600 bg-green-50' : ($ctr >= 2 ? 'text-amber-600 bg-amber-50' : 'text-gray-600 bg-gray-50');
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900 text-sm">{{ $row->product?->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $row->product?->productCategory?->name ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-700">{{ $row->vendor?->business_name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-blue-600 text-sm">{{ number_format($row->total_clicks) }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 text-sm">{{ number_format($row->total_impressions) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold {{ $ctrColor }}">
                                    {{ $ctr }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $row->last_recorded ? \Carbon\Carbon::parse($row->last_recorded)->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('agent.performance.show', $row->product_id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-medium hover:bg-indigo-100 transition-colors">
                                    <i class="fas fa-chart-line text-[10px]"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-chart-line text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No performance data yet</p>
                                    <p class="text-xs text-gray-400 mt-1">Data appears as your vendors' products receive views</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

</div>
@endsection
