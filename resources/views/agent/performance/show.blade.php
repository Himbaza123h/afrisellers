@extends('layouts.home')

@push('styles')
<style>
    .bar-wrap { display: flex; align-items: flex-end; gap: 5px; height: 110px; }
    .bar-col  { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px; }
</style>
@endpush

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('agent.performance.index') }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    Product Performance &bull; {{ $product->productCategory?->name ?? 'Uncategorised' }}
                </p>
            </div>
        </div>
        <form method="GET" class="flex gap-2 no-print">
            <select name="period" onchange="this.form.submit()"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="3"  {{ $period == 3  ? 'selected' : '' }}>Last 3 Months</option>
                <option value="6"  {{ $period == 6  ? 'selected' : '' }}>Last 6 Months</option>
                <option value="12" {{ $period == 12 ? 'selected' : '' }}>Last 12 Months</option>
            </select>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

        {{-- CTR Hero --}}
        <div class="bg-indigo-500 to-blue-600 rounded-xl p-5 text-white shadow-md">
            <p class="text-xs font-semibold text-indigo-100 uppercase tracking-wider">CTR</p>
            <p class="text-4xl font-bold mt-1">{{ number_format($totals->ctr ?? 0, 2) }}%</p>
            <p class="text-xs text-indigo-100 mt-2">Click-through rate</p>
            <div class="mt-3 w-full bg-white/20 rounded-full h-2">
                @php $ctrWidth = min(100, ($totals->ctr ?? 0) * 10); @endphp
                <div class="bg-white h-2 rounded-full" style="width: {{ $ctrWidth }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Clicks</p>
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-pointer text-blue-600 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totals->total_clicks ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">All time clicks on this product</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Impressions</p>
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-eye text-purple-600 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totals->total_impressions ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total times product was shown</p>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Monthly Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Monthly Clicks vs Impressions</h3>
                <span class="text-xs text-gray-400">Last {{ $period }} months</span>
            </div>
            @if($monthlyChart->count() > 0)
                @php
                    $maxC = max($monthlyChart->max('total_clicks'), $monthlyChart->max('total_impressions')) ?: 1;
                @endphp
                <div class="bar-wrap">
                    @foreach($monthlyChart as $row)
                        @php
                            $cp    = round(($row->total_clicks / $maxC) * 100);
                            $ip    = round(($row->total_impressions / $maxC) * 100);
                            $label = \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M');
                        @endphp
                        <div class="bar-col">
                            <span style="font-size:9px;font-weight:600;color:#6b7280;">{{ number_format($row->total_clicks) }}</span>
                            <div class="w-full flex gap-0.5 relative" style="height:100px;">
                                <div class="flex-1 bg-gray-100 rounded-t-md relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-blue-500 rounded-t-md" style="height:{{ $cp }}%"></div>
                                </div>
                                <div class="flex-1 bg-gray-100 rounded-t-md relative">
                                    <div class="absolute bottom-0 left-0 right-0 bg-purple-300 rounded-t-md" style="height:{{ $ip }}%"></div>
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
            @php $maxCC = $byCountry->max('total_clicks') ?: 1; @endphp
            <div class="space-y-3">
                @forelse($byCountry as $row)
                    @php $pct = round(($row->total_clicks / $maxCC) * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-700 truncate max-w-[130px]">
                                {{ $row->country?->name ?? 'Unknown' }}
                            </span>
                            <div class="text-right">
                                <span class="text-xs font-bold text-blue-600">{{ number_format($row->total_clicks) }}</span>
                                <span class="text-[10px] text-gray-400 ml-1">/ {{ number_format($row->total_impressions) }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center mt-6">No country breakdown yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Product Details Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Product Details</h3>
        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Product Name</dt>
                <dd class="text-sm font-semibold text-gray-800">{{ $product->name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Category</dt>
                <dd class="text-sm font-medium text-gray-700">{{ $product->productCategory?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Country</dt>
                <dd class="text-sm font-medium text-gray-700">{{ $product->country?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Status</dt>
                <dd>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                        {{ $product->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        <i class="fas fa-circle text-[6px]"></i>
                        {{ ucfirst($product->status ?? 'N/A') }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Min. Order Qty</dt>
                <dd class="text-sm font-medium text-gray-700">{{ $product->min_order_quantity ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Negotiable</dt>
                <dd class="text-sm font-medium text-gray-700">{{ $product->is_negotiable ? 'Yes' : 'No' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Admin Verified</dt>
                <dd class="text-sm font-medium text-gray-700">{{ $product->is_admin_verified ? 'Yes' : 'No' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Total Views</dt>
                <dd class="text-sm font-semibold text-indigo-600">{{ number_format($product->views ?? 0) }}</dd>
            </div>
        </dl>

        @if($product->short_description)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <dt class="text-xs text-gray-400 mb-1">Short Description</dt>
                <dd class="text-sm text-gray-700 leading-relaxed">{{ $product->short_description }}</dd>
            </div>
        @endif
    </div>

    {{-- CTR Gauge + Monthly Table --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- CTR Indicator --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col items-center justify-center">
            <h3 class="text-sm font-bold text-gray-800 mb-5">CTR Benchmark</h3>
            @php
                $ctrVal = $totals->ctr ?? 0;
                $ctrColor = $ctrVal >= 5 ? 'text-green-600' : ($ctrVal >= 2 ? 'text-amber-600' : 'text-red-500');
                $ctrBg    = $ctrVal >= 5 ? 'bg-green-100' : ($ctrVal >= 2 ? 'bg-amber-100' : 'bg-red-100');
                $ctrLabel = $ctrVal >= 5 ? 'Excellent' : ($ctrVal >= 2 ? 'Average' : 'Low');
            @endphp
            <div class="w-28 h-28 rounded-full {{ $ctrBg }} flex flex-col items-center justify-center mb-4">
                <p class="text-3xl font-bold {{ $ctrColor }}">{{ number_format($ctrVal, 1) }}%</p>
                <p class="text-xs font-semibold {{ $ctrColor }} mt-1">{{ $ctrLabel }}</p>
            </div>
            <div class="space-y-2 w-full text-xs">
                @foreach([['≥ 5%', 'Excellent', 'text-green-600', 'bg-green-100'], ['2–5%', 'Average', 'text-amber-600', 'bg-amber-100'], ['< 2%', 'Low', 'text-red-500', 'bg-red-100']] as [$range, $label, $tc, $bgc])
                    <div class="flex items-center justify-between px-3 py-1.5 {{ $bgc }} rounded-lg">
                        <span class="font-semibold {{ $tc }}">{{ $range }}</span>
                        <span class="font-medium {{ $tc }}">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Monthly Table --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800">Monthly Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Month</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Clicks</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Impressions</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">CTR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($monthlyChart as $row)
                            @php
                                $rowCtr   = $row->total_impressions > 0
                                    ? round(($row->total_clicks / $row->total_impressions) * 100, 2)
                                    : 0;
                                $rowColor = $rowCtr >= 5 ? 'text-green-600 bg-green-50' : ($rowCtr >= 2 ? 'text-amber-600 bg-amber-50' : 'text-gray-600 bg-gray-50');
                                $month    = \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('F Y');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-800 text-sm">{{ $month }}</td>
                                <td class="px-4 py-3 font-bold text-blue-600">{{ number_format($row->total_clicks) }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ number_format($row->total_impressions) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold {{ $rowColor }}">
                                        {{ $rowCtr }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-gray-400 text-sm">No monthly data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($monthlyChart->count() > 0)
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Total</td>
                                <td class="px-4 py-3 font-bold text-blue-700">{{ number_format($totals->total_clicks ?? 0) }}</td>
                                <td class="px-4 py-3 font-bold text-gray-700">{{ number_format($totals->total_impressions ?? 0) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold
                                        {{ ($totals->ctr ?? 0) >= 5 ? 'text-green-600 bg-green-50' : (($totals->ctr ?? 0) >= 2 ? 'text-amber-600 bg-amber-50' : 'text-gray-600 bg-gray-50') }}">
                                        {{ number_format($totals->ctr ?? 0, 2) }}%
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
