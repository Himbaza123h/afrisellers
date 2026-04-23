@extends('layouts.home')

@push('styles')
<style>
    .bar-wrap { display: flex; align-items: flex-end; gap: 6px; height: 120px; }
    .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .bar-label { font-size: 9px; color: #9ca3af; }
    .bar-val { font-size: 9px; font-weight: 600; color: #6b7280; }
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
                <h1 class="text-xl font-bold text-gray-900">Referral Analytics</h1>
                <p class="mt-1 text-xs text-gray-500">Track and analyse your referral performance</p>
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
        @foreach([
            ['Total Referrals',  $stats['total'],     'bg-blue-100',   'text-blue-600',  'fa-users'],
            ['Converted',        $stats['converted'], 'bg-green-100',  'text-green-600', 'fa-check-circle'],
            ['Pending',          $stats['pending'],   'bg-amber-100',  'text-amber-600', 'fa-clock'],
            ['Rejected',         $stats['rejected'],  'bg-red-100',    'text-red-600',   'fa-times-circle'],
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

    {{-- Conversion Rate Hero --}}
    <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center gap-6">
            <div class="flex-1">
                <p class="text-xs font-semibold text-blue-100 uppercase tracking-wider mb-1">Conversion Rate</p>
                <p class="text-5xl font-bold">{{ $stats['conversion_rate'] }}%</p>
                <p class="text-blue-100 text-sm mt-2">
                    {{ $stats['converted'] }} out of {{ $stats['total'] }} referrals converted
                </p>
            </div>
            <div class="flex-1">
                <p class="text-xs font-semibold text-blue-100 uppercase tracking-wider mb-3">Status Breakdown</p>
                @php
                    $total = $stats['total'] ?: 1;
                    $breakdown = [
                        'Converted' => [$stats['converted'], 'bg-green-400'],
                        'Pending'   => [$stats['pending'],   'bg-amber-400'],
                        'Rejected'  => [$stats['rejected'],  'bg-red-400'],
                    ];
                @endphp
                <div class="space-y-2">
                    @foreach($breakdown as $label => [$count, $color])
                        @php $pct = round(($count / $total) * 100); @endphp
                        <div>
                            <div class="flex justify-between text-xs text-blue-100 mb-1">
                                <span>{{ $label }}</span>
                                <span>{{ $count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full bg-white/20 rounded-full h-1.5">
                                <div class="{{ $color }} h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="text-center flex-shrink-0">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto">
                    <i class="fas fa-user-plus text-3xl"></i>
                </div>
                <p class="text-xs text-blue-100 mt-2">This Month: <strong>{{ $stats['this_month'] }}</strong></p>
            </div>
        </div>
    </div>

    {{-- Monthly Chart --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Monthly Referrals vs Conversions</h3>
        @if($monthlyChart->count() > 0)
            @php $maxR = $monthlyChart->max('total') ?: 1; @endphp
            <div class="flex items-end gap-3" style="height: 130px;">
                @foreach($monthlyChart as $row)
                    @php
                        $totalPct    = round(($row->total / $maxR) * 100);
                        $convertPct  = $row->total > 0 ? round(($row->converted / $row->total) * 100) : 0;
                        $label       = \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M');
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span style="font-size:9px;color:#6b7280;font-weight:600;">{{ $row->total }}</span>
                        <div class="w-full relative flex gap-0.5" style="height: 100px;">
                            <div class="flex-1 bg-gray-100 rounded-t-md relative">
                                <div class="absolute bottom-0 left-0 right-0 bg-blue-400 rounded-t-md"
                                     style="height: {{ $totalPct }}%"></div>
                            </div>
                            <div class="flex-1 bg-gray-100 rounded-t-md relative">
                                <div class="absolute bottom-0 left-0 right-0 bg-green-400 rounded-t-md"
                                     style="height: {{ $convertPct }}%"></div>
                            </div>
                        </div>
                        <span style="font-size:9px;color:#9ca3af;">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-3">
                <span class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="w-3 h-3 rounded-sm bg-blue-400 inline-block"></span> Total Referrals
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="w-3 h-3 rounded-sm bg-green-400 inline-block"></span> Converted
                </span>
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-32 text-gray-300">
                <i class="fas fa-chart-bar text-4xl mb-2"></i>
                <p class="text-sm">No referral data for this period</p>
            </div>
        @endif
    </div>

    {{-- Recent Referrals Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-800">Recent Referrals</h3>
            <a href="{{ route('agent.referrals.index') }}"
               class="text-xs text-blue-600 hover:underline font-medium">
                View All <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentReferrals as $referral)
                        @php
                            $map = [
                                'converted' => 'bg-green-100 text-green-700',
                                'pending'   => 'bg-amber-100 text-amber-700',
                                'rejected'  => 'bg-red-100 text-red-700',
                            ];
                            $cls = $map[$referral->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($referral->name ?? 'R', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900 text-sm">{{ $referral->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $referral->email ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                    <i class="fas fa-circle text-[6px]"></i>
                                    {{ ucfirst($referral->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $referral->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-400 text-sm">
                                No referrals yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
