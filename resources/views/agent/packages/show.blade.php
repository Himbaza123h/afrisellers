@extends('layouts.home')

@push('styles')
<style>
    .feature-row { transition: background 0.2s; }
    .feature-row:hover { background: #f8fafc; }
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
    .progress-bar { transition: width 1s cubic-bezier(0.4,0,0.2,1); }
</style>
@endpush

@section('page-content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Back + breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('agent.packages.index') }}" class="hover:text-gray-700 flex items-center gap-1">
            <i class="fas fa-arrow-left text-xs"></i> All Packages
        </a>

        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $package->name }}</span>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Current subscription notice --}}
    @if($currentSubscription && $currentSubscription->package_id === $package->id)
        <div class="p-4 bg-blue-50 rounded-xl border border-blue-200 flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-blue-900">This is your current active package</p>
                <p class="text-sm text-blue-700">Expires on {{ $currentSubscription->expires_at->format('M d, Y') }} — {{ $currentSubscription->daysRemaining() }} days remaining</p>
            </div>
            <a href="{{ route('agent.subscriptions.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 whitespace-nowrap">
                Manage →
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: Package details (2 cols) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Header card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50 flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $package->name }}</h1>
                            @if($package->is_featured)
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full border border-purple-200">
                                    MOST POPULAR
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600">{{ $package->description }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($package->price, 2) }}</p>
                        <p class="text-sm text-gray-500">/ {{ $package->billing_cycle }}</p>
                        @if($package->billing_cycle !== 'monthly')
                            <p class="text-xs text-gray-400 mt-1">~${{ number_format($package->getMonthlyPrice(), 2) }}/mo</p>
                        @endif
                    </div>
                </div>

                {{-- Stats row --}}
                <div class="grid grid-cols-3 divide-x divide-gray-100">
                    <div class="px-6 py-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $package->max_referrals }}</p>
                        <p class="text-xs text-gray-500 mt-1">Max Referrals</p>
                    </div>
                    <div class="px-6 py-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $package->commission_rate }}%</p>
                        <p class="text-xs text-gray-500 mt-1">Commission Rate</p>
                    </div>
                    <div class="px-6 py-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $package->max_payouts_per_month }}</p>
                        <p class="text-xs text-gray-500 mt-1">Payouts/Month</p>
                    </div>
                </div>
            </div>

            {{-- Features breakdown --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700">What's Included</h2>
                </div>
                <div class="divide-y divide-gray-50">

                    @php
                        $features = [
                            ['icon' => 'fa-users',          'label' => 'Max Referrals',        'value' => $package->max_referrals . ' referrals',         'active' => true],
                            ['icon' => 'fa-percentage',     'label' => 'Commission Rate',       'value' => $package->commission_rate . '% per referral',   'active' => true],
                            ['icon' => 'fa-money-bill-wave','label' => 'Monthly Payouts',       'value' => $package->max_payouts_per_month . ' payout' . ($package->max_payouts_per_month > 1 ? 's' : '') . '/month', 'active' => true],
                            ['icon' => 'fa-file-invoice',   'label' => 'RFQ Access',            'value' => 'Request for Quotes',                           'active' => $package->allow_rfqs],
                            ['icon' => 'fa-headset',        'label' => 'Priority Support',      'value' => '24/7 priority assistance',                     'active' => $package->priority_support],
                            ['icon' => 'fa-chart-bar',      'label' => 'Advanced Analytics',    'value' => 'Detailed reports & insights',                  'active' => $package->advanced_analytics],
                            ['icon' => 'fa-star',           'label' => 'Featured Profile',      'value' => 'Stand out in agent listings',                  'active' => $package->featured_profile],
                            ['icon' => 'fa-calendar-alt',   'label' => 'Duration',              'value' => $package->duration_days . ' days (' . $package->billing_cycle . ')', 'active' => true],
                        ];
                    @endphp

                    @foreach($features as $feature)
                    <div class="feature-row flex items-center justify-between px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                {{ $feature['active'] ? 'bg-green-50' : 'bg-gray-50' }}">
                                <i class="fas {{ $feature['icon'] }} text-xs
                                    {{ $feature['active'] ? 'text-green-600' : 'text-gray-300' }}"></i>
                            </div>
                            <span class="text-sm font-medium {{ $feature['active'] ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $feature['label'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($feature['active'])
                                <span class="text-sm text-gray-600">{{ $feature['value'] }}</span>
                                <i class="fas fa-check-circle text-green-500 text-xs"></i>
                            @else
                                <span class="text-sm text-gray-300">Not included</span>
                                <i class="fas fa-times-circle text-gray-300 text-xs"></i>
                            @endif
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>

            {{-- Compare note --}}
            <div class="text-center text-sm text-gray-500">
                Want to compare all packages?
                <a href="{{ route('agent.packages.index') }}" class="text-[#ff0808] hover:underline font-medium">View all packages →</a>
            </div>

        </div>

        {{-- RIGHT: Sticky CTA sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">

                {{-- Price summary --}}
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">Package Summary</p>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">{{ $package->name }}</span>
                        <span class="text-sm font-bold text-gray-900">${{ number_format($package->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Duration</span>
                        <span class="text-xs text-gray-500">{{ $package->duration_days }} days</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">Total</span>
                        <span class="text-lg font-bold text-gray-900">${{ number_format($package->price, 2) }}</span>
                    </div>
                </div>

                {{-- CTA Buttons --}}
                <div class="p-5 space-y-3">
                  @if($currentSubscription && $currentSubscription->package_id === $package->id)
                    <button disabled
                            class="w-full px-6 py-3 bg-gray-100 text-gray-400 rounded-xl font-semibold text-sm cursor-not-allowed">
                        <i class="fas fa-check mr-2"></i> Current Package
                    </button>
                    <a href="{{ route('agent.subscriptions.index') }}"
                    class="block w-full px-6 py-3 border-2 border-gray-200 text-gray-700 text-center rounded-xl font-semibold text-sm hover:bg-gray-50 transition-all">
                        Manage Subscription
                    </a>
                    <form action="{{ route('agent.packages.cancel') }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to cancel your subscription?')">
                        @csrf
                        <button type="submit"
                                class="w-full px-6 py-3 border-2 border-red-200 text-red-600 rounded-xl font-semibold text-sm hover:bg-red-50 transition-all">
                            <i class="fas fa-times mr-2"></i> Cancel Subscription
                        </button>
                    </form>
                    @elseif($currentSubscription)
                        <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-xs text-amber-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            You have an active subscription. Cancel it first to switch.
                        </div>
                        <a href="{{ route('agent.subscriptions.index') }}"
                           class="block w-full px-6 py-3 bg-[#ff0808] text-white text-center rounded-xl font-semibold text-sm hover:bg-red-700 transition-all shadow-sm">
                            Manage Current Plan
                        </a>
                    @else
                        <a href="{{ route('agent.packages.checkout', $package->id) }}"
                           class="block w-full px-6 py-3 bg-[#ff0808] text-white text-center rounded-xl font-bold text-sm hover:bg-red-700 transition-all shadow-sm">
                            <i class="fas fa-bolt mr-2"></i> Get Started
                        </a>
                        <a href="{{ route('agent.packages.index') }}"
                           class="block w-full px-6 py-3 border-2 border-gray-200 text-gray-700 text-center rounded-xl font-semibold text-sm hover:bg-gray-50 transition-all">
                            Compare Plans
                        </a>
                    @endif
                </div>

                {{-- Trust badges --}}
                <div class="px-5 pb-5 space-y-2">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-lock text-gray-400"></i> Secure payment processing
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-undo text-gray-400"></i> Cancel anytime
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-headset text-gray-400"></i> Support included
                    </div>
                </div>

            </div>

            {{-- Need help? --}}
            <div class="bg-blue-50 rounded-xl border border-blue-100 p-4 text-center">
                <p class="text-xs font-semibold text-blue-800 mb-1">Need help choosing?</p>
                <p class="text-xs text-blue-600 mb-3">Our team is ready to assist you</p>
                <a href="{{ route('agent.packages.index') }}"
                   class="text-xs font-semibold text-blue-700 hover:text-blue-900 underline">
                    Compare all packages
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
