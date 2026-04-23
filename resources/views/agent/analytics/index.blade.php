@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
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
        <div>
            <h1 class="text-xl font-bold text-gray-900">Analytics Overview</h1>
            <p class="mt-1 text-xs text-gray-500">Your performance at a glance</p>
        </div>
        <div class="flex flex-wrap gap-2">
            {{-- Period Filter --}}
            <form method="GET" class="flex gap-2">
                <select name="period" onchange="this.form.submit()"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="3"  {{ $period == 3  ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="6"  {{ $period == 6  ? 'selected' : '' }}>Last 6 Months</option>
                    <option value="12" {{ $period == 12 ? 'selected' : '' }}>Last 12 Months</option>
                </select>
            </form>
            <a href="{{ route('agent.analytics.referrals') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 shadow-sm">
                <i class="fas fa-user-plus text-blue-500"></i> Referrals
            </a>
            <a href="{{ route('agent.analytics.vendors') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 shadow-sm">
                <i class="fas fa-store text-purple-500"></i> Vendors
            </a>
            <a href="{{ route('agent.analytics.commissions') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 shadow-sm">
                <i class="fas fa-percentage text-green-500"></i> Commissions
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        {{-- Total Earned --}}
        <div class="stat-card col-span-2 bg-emerald-500 to-green-600 rounded-xl p-5 text-white shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-100 uppercase tracking-wider">Total Earned</p>
                    <p class="text-3xl font-bold mt-1">${{ number_format($summary['total_earned'], 2) }}</p>
                    <p class="text-xs text-green-100 mt-1">
                        +${{ number_format($summary['earned_this_month'], 2) }} this month
                    </p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Active Vendors</p>
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-purple-600 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['active_vendors'] }}</p>
            <p class="text-xs text-gray-400 mt-1">of {{ $summary['total_vendors'] }} total</p>
        </div>

        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Conversion Rate</p>
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-funnel-dollar text-blue-600 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['conversion_rate'] }}%</p>
            <p class="text-xs text-gray-400 mt-1">
                {{ $summary['converted_referrals'] }} / {{ $summary['total_referrals'] }} referrals
            </p>
        </div>
    </div>

    {{-- Second Row Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach([
            ['Referrals',         $summary['total_referrals'],   'bg-blue-100',   'text-blue-600',   'fa-user-plus'],
            ['Converted',         $summary['converted_referrals'],'bg-green-100',  'text-green-600',  'fa-check-circle'],
            ['Commissions',       $summary['total_commissions'],  'bg-amber-100',  'text-amber-600',  'fa-coins'],
            ['Pending Amount',    '$'.number_format($summary['pending_amount'],2), 'bg-red-100', 'text-red-600', 'fa-clock'],
        ] as [$label, $value, $bg, $text, $icon])
        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
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

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Earnings Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Monthly Earnings</h3>
                <span class="text-xs text-gray-400">Last {{ $period }} months</span>
            </div>
            @if(count($earningsChart) > 0)
                @php $maxEarnings = max($earningsChart) ?: 1; @endphp
                <div class="bar-wrap">
                    @foreach($earningsChart as $month => $total)
                        @php
                            $pct = round(($total / $maxEarnings) * 100);
                            $label = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M');
                        @endphp
                        <div class="bar-col">
                            <span class="bar-val">${{ number_format($total, 0) }}</span>
                            <div class="w-full bg-gray-100 rounded-t-md relative flex-1">
                                <div class="absolute bottom-0 left-0 right-0 bg-emerald-500 rounded-t-md"
                                     style="height: {{ $pct }}%"></div>
                            </div>
                            <span class="bar-label">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-32 text-gray-300">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p class="text-sm">No earnings data yet</p>
                </div>
            @endif
        </div>

        {{-- Commission Status Breakdown --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Commission Status</h3>
            @php
                $statusColors = [
                    'paid'     => ['bg-green-500',  'bg-green-100',  'text-green-700'],
                    'pending'  => ['bg-amber-400',  'bg-amber-100',  'text-amber-700'],
                    'rejected' => ['bg-red-500',    'bg-red-100',    'text-red-700'],
                ];
                $totalCommissions = $commissionBreakdown->sum('count') ?: 1;
            @endphp
            <div class="space-y-3">
                @forelse($commissionBreakdown as $status => $row)
                    @php
                        $colors = $statusColors[$status] ?? ['bg-gray-400', 'bg-gray-100', 'text-gray-600'];
                        $pct = round(($row->count / $totalCommissions) * 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold {{ $colors[2] }} capitalize">{{ $status }}</span>
                            <span class="text-xs text-gray-500">{{ $row->count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full {{ $colors[1] }} rounded-full h-2">
                            <div class="{{ $colors[0] }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">${{ number_format($row->total, 2) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center mt-6">No commission data yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Vendor Growth Chart --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Vendor Growth</h3>
                <span class="text-xs text-gray-400">{{ $period }}mo</span>
            </div>
            @if(count($vendorGrowthChart) > 0)
                @php $maxV = max($vendorGrowthChart) ?: 1; @endphp
                <div class="bar-wrap">
                    @foreach($vendorGrowthChart as $month => $total)
                        @php
                            $pct = round(($total / $maxV) * 100);
                            $label = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M');
                        @endphp
                        <div class="bar-col">
                            <span class="bar-val">{{ $total }}</span>
                            <div class="w-full bg-gray-100 rounded-t-md relative flex-1">
                                <div class="absolute bottom-0 left-0 right-0 bg-purple-500 rounded-t-md"
                                     style="height: {{ $pct }}%"></div>
                            </div>
                            <span class="bar-label">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-32 text-gray-300">
                    <i class="fas fa-store text-4xl mb-2"></i>
                    <p class="text-xs">No vendor data yet</p>
                </div>
            @endif
        </div>

        {{-- Referral Chart --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Referral Activity</h3>
                <span class="text-xs text-gray-400">{{ $period }}mo</span>
            </div>
            @if(count($referralChart) > 0)
                @php $maxR = max($referralChart) ?: 1; @endphp
                <div class="bar-wrap">
                    @foreach($referralChart as $month => $total)
                        @php
                            $pct = round(($total / $maxR) * 100);
                            $label = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M');
                        @endphp
                        <div class="bar-col">
                            <span class="bar-val">{{ $total }}</span>
                            <div class="w-full bg-gray-100 rounded-t-md relative flex-1">
                                <div class="absolute bottom-0 left-0 right-0 bg-blue-500 rounded-t-md"
                                     style="height: {{ $pct }}%"></div>
                            </div>
                            <span class="bar-label">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-32 text-gray-300">
                    <i class="fas fa-user-plus text-4xl mb-2"></i>
                    <p class="text-xs">No referral data yet</p>
                </div>
            @endif
        </div>

        {{-- Top Vendors --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Top Vendors</h3>
                <a href="{{ route('agent.analytics.vendors') }}"
                   class="text-xs text-blue-600 hover:underline">View all</a>
            </div>
            @php $topMax = $topVendors->max('total') ?: 1; @endphp
            <div class="space-y-3">
                @forelse($topVendors as $row)
                    @php $pct = round(($row->total / $topMax) * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-700 truncate max-w-[120px]">
                                {{ $row->vendor?->businessProfile?->business_name ?? 'N/A' }}
                            </span>
                            <span class="text-xs font-bold text-emerald-700">${{ number_format($row->total, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center mt-4">No data yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Subscription Status --}}
    @if($subscription)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Active Subscription</h3>
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-crown text-amber-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $subscription->package?->name }}</p>
                        <p class="text-xs text-gray-400">Expires {{ $subscription->expires_at?->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-500">Plan usage</span>
                        <span class="text-xs font-semibold text-gray-700">{{ $subscription->daysRemaining() }} days left</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full"
                             style="width: {{ 100 - $subscription->percentUsed() }}%"></div>
                    </div>
                </div>
                <a href="{{ route('agent.subscriptions.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 rounded-lg text-xs font-semibold hover:bg-amber-100">
                    Manage <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
