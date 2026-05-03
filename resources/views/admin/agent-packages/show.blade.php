@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.agent-packages.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $agentPackage->name }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">Package details and subscriber overview</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.agent-packages.edit', $agentPackage) }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('admin.agent-packages.toggle-status', $agentPackage) }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg shadow-sm
                    {{ $agentPackage->is_active ? 'bg-red-50 border border-red-200 text-red-700 hover:bg-red-100' : 'bg-green-50 border border-green-200 text-green-700 hover:bg-green-100' }}">
                    <i class="fas {{ $agentPackage->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                    {{ $agentPackage->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Hero card --}}
    <div class="bg-orange-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-crown text-yellow-200"></i>
                    <span class="text-xs font-semibold text-orange-100 uppercase tracking-wider">
                        {{ $agentPackage->is_active ? 'Active Plan' : 'Inactive Plan' }}
                    </span>
                    @if($agentPackage->is_featured)
                        <span class="px-2 py-0.5 bg-yellow-400 text-yellow-900 text-[10px] font-bold rounded">FEATURED</span>
                    @endif
                </div>
                <h2 class="text-2xl font-bold">{{ $agentPackage->name }}</h2>
                @if($agentPackage->description)
                    <p class="text-orange-100 text-sm mt-1">{{ $agentPackage->description }}</p>
                @endif

                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                        <i class="fas fa-users mr-1 text-[10px]"></i>{{ $agentPackage->max_referrals }} referrals
                    </span>
                    <span class="bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                        <i class="fas fa-money-bill mr-1 text-[10px]"></i>{{ $agentPackage->max_payouts_per_month }} payouts/mo
                    </span>
                    @if($agentPackage->priority_support)
                        <span class="bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                            <i class="fas fa-headset mr-1 text-[10px]"></i>Priority Support
                        </span>
                    @endif
                    @if($agentPackage->commission_boost)
                        <span class="bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                            <i class="fas fa-percentage mr-1 text-[10px]"></i>{{ $agentPackage->commission_rate }}% Commission
                        </span>
                    @endif
                    @if($agentPackage->allow_rfqs)
                        <span class="bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                            <i class="fas fa-file-invoice mr-1 text-[10px]"></i>RFQ Access
                        </span>
                    @endif
                    @if($agentPackage->advanced_analytics)
                        <span class="bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                            <i class="fas fa-chart-bar mr-1 text-[10px]"></i>Advanced Analytics
                        </span>
                    @endif
                    @if($agentPackage->max_vendors)
                        <span class="bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                            <i class="fas fa-store mr-1 text-[10px]"></i>{{ $agentPackage->max_vendors }} vendors
                        </span>
                    @endif
                </div>
            </div>

            <div class="text-right flex-shrink-0">
                <p class="text-3xl font-bold">${{ number_format($agentPackage->price, 2) }}</p>
                <p class="text-orange-100 text-xs">/ {{ $agentPackage->billing_cycle }}</p>
                <p class="text-orange-100 text-xs mt-1">{{ $agentPackage->duration_days }} days</p>
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-green-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Active Subscribers</p>
                <p class="text-xl font-bold text-gray-900">{{ $agentPackage->active_subscriptions_count }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-history text-blue-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Subscriptions</p>
                <p class="text-xl font-bold text-gray-900">{{ $agentPackage->subscriptions_count }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-dollar-sign text-amber-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Revenue</p>
                <p class="text-xl font-bold text-gray-900">${{ number_format($revenue, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Recent subscribers --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-800">Recent Subscribers</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Agent</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount Paid</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentSubscriptions as $sub)
                        @php
                            $cls = match($sub->status) {
                                'active'    => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                'expired'   => 'bg-gray-100 text-gray-600',
                                default     => 'bg-yellow-100 text-yellow-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $sub->agent?->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $sub->agent?->email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 font-bold text-gray-900">${{ number_format($sub->amount_paid, 2) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ $sub->starts_at?->format('M d, Y') }} → {{ $sub->expires_at?->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                    <i class="fas fa-circle text-[6px]"></i> {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-400 text-sm">No subscribers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
