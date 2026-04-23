@extends('layouts.home')

@push('styles')
<style>
    .bar-wrap { display: flex; align-items: flex-end; gap: 6px; height: 120px; }
    .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
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
                <h1 class="text-xl font-bold text-gray-900">Vendor Analytics</h1>
                <p class="mt-1 text-xs text-gray-500">Understand your vendor portfolio performance</p>
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

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
            $slotPct = $stats['limit'] > 0 ? min(100, round(($stats['total'] / $stats['limit']) * 100)) : 0;
        @endphp
        <div class="col-span-2 bg-purple-500 to-violet-600 rounded-xl p-5 text-white shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-purple-100 uppercase tracking-wider">Vendor Slots</p>
                    <p class="text-3xl font-bold mt-1">{{ $stats['total'] }} / {{ $stats['limit'] }}</p>
                    <div class="w-full bg-white/20 rounded-full h-2 mt-3">
                        <div class="bg-white h-2 rounded-full" style="width: {{ $slotPct }}%"></div>
                    </div>
                    <p class="text-xs text-purple-100 mt-1">{{ $slotPct }}% of plan capacity used</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-store text-2xl"></i>
                </div>
            </div>
        </div>

        @foreach([
            ['Active',    $stats['active'],    'bg-green-100',  'text-green-600',  'fa-check-circle'],
            ['Pending',   $stats['pending'],   'bg-amber-100',  'text-amber-600',  'fa-clock'],
            ['Suspended', $stats['suspended'], 'bg-red-100',    'text-red-600',    'fa-ban'],
        ] as [$label, $value, $bg, $text, $icon])
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 {{ $bg }} rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $icon }} {{ $text }} text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $label }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $value }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Growth Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Vendor Onboarding Growth</h3>
                <span class="text-xs text-gray-400">Last {{ $period }} months</span>
            </div>
            @if(count($growthChart) > 0)
                @php $maxG = max($growthChart) ?: 1; @endphp
                <div class="bar-wrap">
                    @foreach($growthChart as $month => $total)
                        @php
                            $pct = round(($total / $maxG) * 100);
                            $label = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M');
                        @endphp
                        <div class="bar-col">
                            <span style="font-size:9px;font-weight:600;color:#6b7280;">{{ $total }}</span>
                            <div class="w-full bg-gray-100 rounded-t-md relative flex-1">
                                <div class="absolute bottom-0 left-0 right-0 bg-purple-500 rounded-t-md"
                                     style="height: {{ $pct }}%"></div>
                            </div>
                            <span style="font-size:9px;color:#9ca3af;">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-32 text-gray-300">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p class="text-sm">No data for this period</p>
                </div>
            @endif
        </div>

        {{-- Status + Country Breakdown --}}
        <div class="space-y-4">
            {{-- Status Breakdown --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-3">By Status</h3>
                @php
                    $totalV = array_sum($statusBreakdown) ?: 1;
                    $statusColors = [
                        'active'    => ['bg-green-500', 'bg-green-100', 'text-green-700'],
                        'pending'   => ['bg-amber-400', 'bg-amber-100', 'text-amber-700'],
                        'suspended' => ['bg-red-500',   'bg-red-100',   'text-red-700'],
                        'rejected'  => ['bg-gray-400',  'bg-gray-100',  'text-gray-600'],
                    ];
                @endphp
                <div class="space-y-2">
                    @foreach($statusBreakdown as $status => $count)
                        @php
                            $colors = $statusColors[$status] ?? ['bg-gray-400', 'bg-gray-100', 'text-gray-600'];
                            $pct = round(($count / $totalV) * 100);
                        @endphp
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-xs font-semibold {{ $colors[2] }} capitalize">{{ $status }}</span>
                                <span class="text-xs text-gray-500">{{ $count }}</span>
                            </div>
                            <div class="w-full {{ $colors[1] }} rounded-full h-1.5">
                                <div class="{{ $colors[0] }} h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- By Country --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-3">By Country</h3>
                @php $maxC = $byCountry->max() ?: 1; @endphp
                <div class="space-y-2">
                    @forelse($byCountry as $country => $count)
                        @php $pct = round(($count / $maxC) * 100); @endphp
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-xs font-medium text-gray-700 truncate max-w-[120px]">{{ $country }}</span>
                                <span class="text-xs text-gray-500">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-purple-400 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-2">No data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Top Vendors by Commission --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-800">Top Vendors by Commission Generated</h3>
            <a href="{{ route('agent.vendors.index') }}"
               class="text-xs text-blue-600 hover:underline font-medium">
                Manage Vendors <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total Earned</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Orders</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Share</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $grandTotal = $topVendors->sum('total') ?: 1; @endphp
                    @forelse($topVendors as $row)
                        @php $pct = round(($row->total / $grandTotal) * 100); @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-store text-purple-600 text-xs"></i>
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
                            <td class="px-4 py-3">
                                <span class="text-base font-bold text-emerald-700">${{ number_format($row->total, 2) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                    {{ $row->orders }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2 w-24">
                                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-400 text-sm">
                                No vendor commission data yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- All Vendors Paginated --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">All Vendors</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Business</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $vendor->businessProfile?->business_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $vendor->user?->email }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ $vendor->businessProfile?->city ? $vendor->businessProfile->city . ', ' : '' }}
                                {{ $vendor->businessProfile?->country?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $map = ['active'=>'bg-green-100 text-green-700','pending'=>'bg-amber-100 text-amber-700','suspended'=>'bg-red-100 text-red-700'];
                                    $cls = $map[$vendor->account_status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                    <i class="fas fa-circle text-[6px]"></i> {{ ucfirst($vendor->account_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $vendor->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">No vendors yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vendors->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $vendors->links() }}</div>
        @endif
    </div>

</div>
@endsection
