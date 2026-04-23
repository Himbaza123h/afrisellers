@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Subscription</h1>
            <p class="mt-1 text-xs text-gray-500">Manage your agent subscription plan</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('agent.subscriptions.print') }}" target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i> Print History
            </a>
            <a href="{{ route('agent.subscriptions.plans') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm">
                <i class="fas fa-crown"></i> {{ $current ? 'Change Plan' : 'Get a Plan' }}
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 flex-1 font-medium">{!! session('success') !!}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm text-red-900 flex-1 font-medium">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-crown text-amber-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Subscriptions</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['total_subs'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Active Plans</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['active'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-wallet text-blue-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Spent</p>
                <p class="text-xl font-bold text-gray-900">${{ number_format($stats['total_spent'], 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Active Subscription Card --}}
    @if($current)
        @php
            $pkg  = $current->package;
            $pct  = $current->percentUsed();
            $days = $current->daysRemaining();
        @endphp
        <div class="bg-orange-500 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-crown text-yellow-200"></i>
                        <span class="text-xs font-semibold text-orange-100 uppercase tracking-wider">Active Plan</span>
                    </div>
                    <h2 class="text-2xl font-bold">{{ $pkg->name }}</h2>
                    <p class="text-orange-100 text-sm mt-1">{{ $pkg->description }}</p>

                    <div class="flex flex-wrap gap-3 mt-4">
                        @if($pkg->max_referrals)
                            <span class="inline-flex items-center gap-1.5 bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                                <i class="fas fa-users text-[10px]"></i> {{ $pkg->max_referrals }} referrals
                            </span>
                        @endif
                        @if(isset($pkg->max_vendors))
                            <span class="inline-flex items-center gap-1.5 bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                                <i class="fas fa-store text-[10px]"></i> {{ $pkg->max_vendors }} vendors
                            </span>
                        @endif
                        @if($pkg->priority_support)
                            <span class="inline-flex items-center gap-1.5 bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                                <i class="fas fa-headset text-[10px]"></i> Priority Support
                            </span>
                        @endif
                        @if($pkg->commission_boost)
                            <span class="inline-flex items-center gap-1.5 bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                                <i class="fas fa-percentage text-[10px]"></i> {{ $pkg->commission_rate }}% Commission
                            </span>
                        @endif
                    </div>
                </div>

                <div class="text-right flex-shrink-0">
                    <p class="text-3xl font-bold">${{ number_format($pkg->price, 2) }}</p>
                    <p class="text-orange-100 text-xs">/ {{ $pkg->billing_cycle }}</p>
                    <div class="mt-3 flex flex-col gap-2 items-end">
                        <form action="{{ route('agent.subscriptions.toggle-auto-renew') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-lg text-xs font-semibold transition-colors">
                                <i class="fas fa-sync text-[10px]"></i>
                                Auto-renew: {{ $current->auto_renew ? 'ON' : 'OFF' }}
                            </button>
                        </form>
                        @if($current->status === 'cancelled' && $current->expires_at->isFuture())
                            <form action="{{ route('agent.subscriptions.resume') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-white text-orange-600 rounded-lg text-xs font-bold hover:bg-orange-50 transition-colors">
                                    <i class="fas fa-play text-[10px]"></i> Resume
                                </button>
                            </form>
                        @else
                            <button onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-lg text-xs font-semibold transition-colors">
                                <i class="fas fa-times text-[10px]"></i> Cancel Plan
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="mt-5">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-orange-100">
                        Expires {{ $current->expires_at->format('M d, Y') }}
                    </span>
                    <span class="text-xs font-bold {{ $days <= 7 ? 'text-red-200' : 'text-orange-100' }}">
                        {{ $days }} {{ Str::plural('day', $days) }} remaining
                    </span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2">
                    <div class="h-2 rounded-full bg-white transition-all {{ $pct >= 90 ? 'opacity-50' : '' }}"
                         style="width: {{ 100 - $pct }}%"></div>
                </div>
            </div>

            {{-- Renew / Invoice actions --}}
            <div class="mt-4 flex flex-wrap gap-2">
                <form action="{{ route('agent.subscriptions.renew') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-orange-600 rounded-lg text-sm font-bold hover:bg-orange-50 shadow transition-colors">
                        <i class="fas fa-redo"></i> Renew Now
                    </button>
                </form>
                <a href="{{ route('agent.subscriptions.invoice', $current->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 text-white rounded-lg text-sm font-semibold hover:bg-white/30 transition-colors">
                    <i class="fas fa-file-invoice"></i> View Invoice
                </a>
                <a href="{{ route('agent.subscriptions.plans') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 text-white rounded-lg text-sm font-semibold hover:bg-white/30 transition-colors">
                    <i class="fas fa-arrow-up"></i> Upgrade Plan
                </a>
            </div>
        </div>
    @else
        {{-- No subscription --}}
        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-10 text-center">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-crown text-amber-400 text-2xl"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-900 mb-2">No Active Plan</h2>
            <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">
                Subscribe to an agent plan to unlock vendor slots, higher commissions, and more features.
            </p>
            <a href="{{ route('agent.subscriptions.plans') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-md">
                <i class="fas fa-crown"></i> Browse Plans
            </a>
        </div>
    @endif

    {{-- History Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-800">Subscription History</h2>
            <a href="{{ route('agent.subscriptions.invoices') }}"
               class="text-xs text-blue-600 hover:underline font-medium">
                View All Invoices <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Plan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($history as $sub)
                        @php
                            $statusMap = [
                                'active'    => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                'expired'   => 'bg-gray-100 text-gray-600',
                                'pending'   => 'bg-yellow-100 text-yellow-700',
                            ];
                            $cls = $statusMap[$sub->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-crown text-amber-500 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $sub->package?->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-400">{{ ucfirst($sub->package?->billing_cycle ?? '') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-gray-900">${{ number_format($sub->amount_paid, 2) }}</span>
                                <p class="text-xs text-gray-400">via {{ $sub->payment_method ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ $sub->starts_at?->format('M d, Y') ?? '—' }}
                                <span class="text-gray-400"> → </span>
                                {{ $sub->expires_at?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                    <i class="fas fa-circle text-[6px]"></i>
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('agent.subscriptions.invoice', $sub->id) }}"
                                   class="inline-flex items-center gap-1 px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded-lg font-medium">
                                    <i class="fas fa-file-invoice text-xs"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm">
                                No subscription history yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($history->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $history->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Cancel Modal --}}
@if($current)
<div id="cancelModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900">Cancel Subscription?</h3>
        </div>
        <p class="text-sm text-gray-600 mb-1">
            Your plan remains active until <strong>{{ $current->expires_at->format('M d, Y') }}</strong>.
            After that, vendor slots and premium features will be restricted.
        </p>
        <form action="{{ route('agent.subscriptions.cancel') }}" method="POST" class="mt-4 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                    Reason (optional)
                </label>
                <textarea name="reason" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400"
                    placeholder="Why are you cancelling?"></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                    Keep Plan
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 shadow">
                    Yes, Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
