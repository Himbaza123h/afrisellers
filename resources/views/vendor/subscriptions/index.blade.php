@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-5">

    {{-- ─── Page Header ─── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Subscriptions</h1>
            <p class="text-xs text-gray-500 mt-0.5">Manage your plan, upgrades, and billing history</p>
        </div>
    </div>

    {{-- ─── Flash Messages ─── --}}
    @if(session('success'))
        <div class="flex items-start gap-3 p-3 bg-green-50 border border-green-200 rounded-lg text-sm">
            <i class="fas fa-check-circle text-green-600 mt-0.5 flex-shrink-0"></i>
            <p class="font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 ml-2"><i class="fas fa-times text-xs"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-start gap-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5 flex-shrink-0"></i>
            <p class="font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 ml-2"><i class="fas fa-times text-xs"></i></button>
        </div>
    @endif

    {{-- ─── Stats Row ─── --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-dollar-sign text-green-600 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wide">Total Spent</p>
                <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_spent'], 2) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar text-blue-600 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wide">Days Remaining</p>
                <p class="text-lg font-bold text-gray-900">{{ max(0, (int) ceil(now()->floatDiffInDays($currentSubscription?->ends_at ?? now(), false))) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-history text-purple-600 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wide">Total Plans</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['total_subscriptions'] }}</p>
            </div>
        </div>
    </div>

    {{-- ─── Active Subscription Card ─── --}}
    @if($currentSubscription)
        @php
            $totalDays  = $currentSubscription->starts_at->diffInDays($currentSubscription->ends_at);
            $daysUsed   = $currentSubscription->starts_at->diffInDays(now());
            $daysLeft   = max(0, (int) ceil(now()->floatDiffInDays($currentSubscription->ends_at, false)));
            $percentage = $totalDays > 0 ? min(($daysUsed / $totalDays) * 100, 100) : 100;
            $isExpiring = $daysLeft < 7;
            $hasUpgrade = $availablePlans->where('price', '>', $currentSubscription->plan->price)->count() > 0;
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 text-white flex items-center justify-between"
                 style="background: linear-gradient(135deg, #1a2942 0%, #ff0808 100%);">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[9px] font-black uppercase tracking-widest bg-white/20 text-white px-2 py-0.5 rounded-full">Active Plan</span>
                        @if($isExpiring)
                            <span class="text-[9px] font-black uppercase tracking-widest bg-yellow-400 text-yellow-900 px-2 py-0.5 rounded-full animate-pulse">Expiring Soon</span>
                        @endif
                    </div>
                    <h2 class="text-lg font-black text-white">{{ $currentSubscription->plan->name }}</h2>
                    <p class="text-white/60 text-xs mt-0.5">Expires {{ $currentSubscription->ends_at->format('M d, Y') }} &middot; {{ $daysLeft }} days left</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-black text-white">${{ number_format($currentSubscription->plan->price, 2) }}</p>
                    <p class="text-white/60 text-xs">/ {{ $currentSubscription->plan->duration_days }} days</p>
                </div>
            </div>

            <div class="p-5">
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                        <span>{{ $currentSubscription->starts_at->format('M d, Y') }}</span>
                        <span class="font-semibold {{ $isExpiring ? 'text-red-600' : 'text-gray-700' }}">{{ number_format($percentage, 0) }}% used</span>
                        <span>{{ $currentSubscription->ends_at->format('M d, Y') }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-500 {{ $isExpiring ? 'bg-red-500' : 'bg-[#ff0808]' }}"
                             style="width:{{ $percentage }}%"></div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    {{-- <form action="{{ route('vendor.subscriptions.renew') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 font-bold text-xs transition-all shadow-sm">
                            <i class="fas fa-redo text-[10px]"></i> Renew Plan
                        </button>
                    </form> --}}

                    @if($hasUpgrade)
                        <button onclick="openUpgradeModal()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1a2942] text-white rounded-lg hover:bg-[#0f1c2e] font-bold text-xs transition-all shadow-sm">
                            <i class="fas fa-arrow-circle-up text-[10px]"></i> Upgrade Plan
                        </button>
                    @endif

                    <form action="{{ route('vendor.subscriptions.toggle-auto-renew') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-bold text-xs transition-all">
                            <i class="fas fa-{{ $currentSubscription->auto_renew ? 'pause' : 'play' }}-circle text-[10px]"></i>
                            {{ $currentSubscription->auto_renew ? 'Disable' : 'Enable' }} Auto-Renew
                        </button>
                    </form>

                    <a href="{{ route('vendor.subscription.plan-pdf', $currentSubscription) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 font-bold text-xs transition-all">
                        <i class="fas fa-file-pdf text-red-500 text-[10px]"></i> Plan Details
                    </a>

                    <a href="{{ route('vendor.subscriptions.invoice', $currentSubscription) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 font-bold text-xs transition-all">
                        <i class="fas fa-file-invoice text-[10px]"></i> Invoice
                    </a>

                    <form action="{{ route('vendor.subscriptions.cancel') }}" method="POST"
                          onsubmit="return confirm('Cancel subscription? Access continues until {{ $currentSubscription->ends_at->format('M d, Y') }}.');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-600 border border-red-100 rounded-lg hover:bg-red-100 font-bold text-xs transition-all">
                            <i class="fas fa-ban text-[10px]"></i> Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>

    @else
        @if(isset($activeTrial) && $activeTrial)
            <div class="bg-indigo-600 rounded-xl overflow-hidden shadow-sm">
                <div class="px-5 py-4 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[9px] font-black uppercase tracking-widest bg-white/20 text-white px-2 py-0.5 rounded-full">Free Trial</span>
                            @if(ceil(now()->floatDiffInDays($activeTrial->ends_at, false)) < 7)
                                <span class="text-[9px] font-black uppercase bg-yellow-400 text-yellow-900 px-2 py-0.5 rounded-full animate-pulse">Expiring Soon</span>
                            @endif
                        </div>
                        <h2 class="text-lg font-black text-white">{{ $activeTrial->plan->name ?? 'Free Trial' }}</h2>
                        <p class="text-white/60 text-xs mt-0.5">
                            Expires {{ $activeTrial->ends_at->format('M d, Y') }} &middot;
                            {{ max(0, (int) ceil(now()->floatDiffInDays($activeTrial->ends_at, false))) }} days left
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-black text-white">FREE</p>
                        <p class="text-white/60 text-xs">trial period</p>
                    </div>
                </div>
                <div class="px-5 pb-4">
                    @php
                        $trialTotal = $activeTrial->starts_at->diffInDays($activeTrial->ends_at);
                        $trialUsed  = $activeTrial->starts_at->diffInDays(now());
                        $trialPct   = $trialTotal > 0 ? min(($trialUsed / $trialTotal) * 100, 100) : 100;
                    @endphp
                    <div class="w-full bg-white/20 rounded-full h-1.5 mb-3">
                        <div class="h-1.5 rounded-full bg-white transition-all" style="width:{{ $trialPct }}%"></div>
                    </div>
                    @php
                        $trialPlan = $activeTrial->plan;
                        $trialLines = collect();
                        if ($trialPlan) {
                            $trialLines = collect([
                                ['label' => 'Products', 'value' => $trialPlan->product_limit === null ? 'Unlimited' : (string) $trialPlan->product_limit],
                                ['label' => 'Buyer inquiries', 'value' => $trialPlan->buyer_inquiries_limit === null ? 'Unlimited' : (string) $trialPlan->buyer_inquiries_limit],
                                ['label' => 'Buyer RFQs', 'value' => $trialPlan->buyer_rfqs_limit === null ? 'Unlimited' : (string) $trialPlan->buyer_rfqs_limit],
                                ['label' => 'Ads', 'value' => $trialPlan->has_ads ? 'Yes' : 'No'],
                                ['label' => 'Negotiable pricing', 'value' => $trialPlan->negotiable ? 'Yes' : 'No'],
                                ['label' => 'Featured products', 'value' => $trialPlan->featured_products ? 'Yes' : 'No'],
                            ]);
                            if (! empty($trialPlan->description)) {
                                $trialLines->prepend(['label' => 'About', 'value' => \Illuminate\Support\Str::limit(strip_tags($trialPlan->description), 120)]);
                            }
                        }
                    @endphp
                    @if($trialLines->isNotEmpty())
                        <ul class="mt-2 space-y-1 text-white/90 text-[11px]">
                            @foreach($trialLines->take(8) as $line)
                                <li class="flex gap-2">
                                    <i class="fas fa-check text-emerald-300 mt-0.5" style="font-size:8px;"></i>
                                    <span><span class="font-semibold">{{ $line['label'] }}:</span> {{ $line['value'] }}</span>
                                </li>
                            @endforeach
                            @if($trialLines->count() > 8)
                                <li class="text-white/60 text-[10px]">+ {{ $trialLines->count() - 8 }} more</li>
                            @endif
                        </ul>
                    @else
                        <p class="text-white/60 text-xs">Subscribe to a membership plan below to keep full access after your trial ends.</p>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 flex-shrink-0"></i>
                <div>
                    <h3 class="text-sm font-bold text-amber-900">No Active Subscription</h3>
                    <p class="text-xs text-amber-700 mt-0.5">Choose a plan below to unlock all features.</p>
                </div>
            </div>
        @endif
    @endif

    {{-- ─── Tabs + Content Panel ─── --}}
    @php
        $planTypes = [
            'free-trial'         => ['name' => 'Free Trial',          'icon' => 'fa-gift'],
            'basic'              => ['name' => 'Basic',                'icon' => 'fa-star'],
            'pro'                => ['name' => 'Pro',                  'icon' => 'fa-crown'],
            'starter'            => ['name' => 'Starter',             'icon' => 'fa-rocket'],
            'growth'             => ['name' => 'Growth',              'icon' => 'fa-chart-line'],
            'pro-export'         => ['name' => 'Pro Export',          'icon' => 'fa-globe'],
            'enterprise'         => ['name' => 'Enterprise',          'icon' => 'fa-building'],
            'starter-agent'      => ['name' => 'Starter Agent',       'icon' => 'fa-user-tie'],
            'professional-agent' => ['name' => 'Professional Agent',  'icon' => 'fa-briefcase'],
            'regional-master'    => ['name' => 'Regional Master',     'icon' => 'fa-medal'],
            'country-franchise'  => ['name' => 'Country Franchise',   'icon' => 'fa-flag'],
        ];
    @endphp

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Tab bar --}}
        <div class="flex overflow-x-auto border-b border-gray-100 scrollbar-hide">
            @foreach($planTypes as $slug => $cfg)
                @php
                    $norm = str_replace(['-','_'], '', $slug);
                    $tabHistory = $subscriptionHistory->filter(fn($s) =>
                        stripos(str_replace(['-','_'],'', $s->plan->slug ?? ''), $norm) !== false);
                    $tabPlans = $availablePlans->filter(fn($p) =>
                        stripos(str_replace(['-','_'],'', $p->slug ?? ''), $norm) !== false);
                    $tabVisiblePlans = $tabPlans->filter(fn($p) =>
                        !$currentSubscription || $p->price >= $currentSubscription->plan->price);
                    $tabNonCancelled = $tabHistory->whereNotIn('status', ['cancelled']);
                @endphp
                @if(!$tabHistory->count() && !$tabVisiblePlans->count())
                    @continue
                @endif
                <button onclick="switchTab('{{ $slug }}')" id="tab-{{ $slug }}"
                        class="sub-tab flex-shrink-0 flex items-center gap-1.5 px-4 py-3 text-xs font-semibold text-gray-500
                               border-b-2 border-transparent hover:text-[#ff0808] hover:bg-red-50 transition-all whitespace-nowrap">
                    <i class="fas {{ $cfg['icon'] }} text-[10px]"></i>
                    {{ $cfg['name'] }}
                    @if($tabNonCancelled->count())
                        <span class="ml-1 px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded-full text-[9px] font-bold">
                            {{ $tabNonCancelled->count() }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Tab panels --}}
        <div class="p-4 space-y-4">
            @foreach($planTypes as $slug => $cfg)
                @php
                    $norm    = str_replace(['-','_'], '', $slug);
                    $history = $subscriptionHistory->filter(fn($s) =>
                        stripos(str_replace(['-','_'],'', $s->plan->slug ?? ''), $norm) !== false);
                    $plans   = $availablePlans->filter(fn($p) =>
                        stripos(str_replace(['-','_'],'', $p->slug ?? ''), $norm) !== false);

                    $activeHistory    = $history->whereNotIn('status', ['cancelled']);
                    $cancelledHistory = $history->where('status', 'cancelled');
                @endphp

                <div id="content-{{ $slug }}" class="sub-content hidden space-y-4">

                    {{-- ── ACTIVE / EXPIRED SUBSCRIPTIONS ── --}}
                    @if($activeHistory->count())
                        <div class="rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wide">
                                    <i class="fas fa-history text-gray-400 mr-1.5"></i>
                                    Your {{ $cfg['name'] }} Subscriptions
                                </h3>
                                <span class="text-[10px] text-gray-400">{{ $activeHistory->count() }} record(s)</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Plan</th>
                                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Period</th>
                                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Price</th>
                                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                                            <th class="px-4 py-2.5 text-center font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($activeHistory as $sub)
                                            <tr class="hover:bg-gray-50 transition-colors {{ ($currentSubscription && $sub->id === $currentSubscription->id) ? 'bg-green-50/40' : '' }}">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span class="font-semibold text-gray-900">{{ $sub->plan->name }}</span>
                                                        @if($sub->is_trial)
                                                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-purple-100 text-purple-700">Trial</span>
                                                        @endif
                                                        @if($currentSubscription && $sub->id === $currentSubscription->id)
                                                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-700">Current</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-gray-400 mt-0.5">{{ $sub->plan->duration_days }} days</p>
                                                </td>
                                                <td class="px-4 py-3 text-gray-500">
                                                    <div>{{ $sub->starts_at->format('M d, Y') }}</div>
                                                    <div class="text-gray-400 text-[10px]">&rarr; {{ $sub->ends_at->format('M d, Y') }}</div>
                                                </td>
                                                <td class="px-4 py-3 font-bold text-gray-900">${{ number_format($sub->plan->price, 2) }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-0.5 rounded-full font-semibold text-[10px]
                                                        {{ $sub->status === 'active'  ? 'bg-green-100 text-green-700'   : '' }}
                                                        {{ $sub->status === 'expired' ? 'bg-orange-100 text-orange-700' : '' }}">
                                                        {{ ucfirst($sub->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-3">
                                                        <button onclick="openPlanModal({{ json_encode($sub->plan) }}, 'details')"
                                                                title="Details" class="text-blue-500 hover:text-blue-700 transition-colors">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <a href="{{ route('vendor.subscriptions.invoice', $sub) }}"
                                                           title="Invoice" class="text-green-500 hover:text-green-700 transition-colors">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- ── CANCELLED SUBSCRIPTIONS (hidden by default) ── --}}
                    @if($cancelledHistory->count())
                        <div>
                            {{-- Toggle button --}}
                            <button
                                onclick="toggleCancelledTab('{{ $slug }}')"
                                id="toggle-cancelled-{{ $slug }}"
                                class="flex items-center gap-2 text-xs text-gray-500 hover:text-red-600 font-semibold transition-colors mb-2 group">
                                <span class="w-5 h-5 flex items-center justify-center bg-red-100 text-red-500 rounded-full group-hover:bg-red-200 transition-colors">
                                    <i id="toggle-icon-{{ $slug }}" class="fas fa-chevron-right text-[9px]"></i>
                                </span>
                                <span id="toggle-label-{{ $slug }}">Show {{ $cancelledHistory->count() }} Cancelled</span>
                            </button>

                            {{-- Cancelled table (hidden by default) --}}
                            <div id="cancelled-{{ $slug }}" class="hidden rounded-xl border border-red-100 overflow-hidden">
                                <div class="px-4 py-3 bg-red-50 border-b border-red-100 flex items-center justify-between">
                                    <h3 class="text-xs font-bold text-red-800 uppercase tracking-wide">
                                        <i class="fas fa-ban text-red-400 mr-1.5"></i>
                                        Cancelled {{ $cfg['name'] }} Subscriptions
                                    </h3>
                                    <span class="text-[10px] text-red-400">{{ $cancelledHistory->count() }} record(s)</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs">
                                        <thead class="bg-red-50/50 border-b border-red-100">
                                            <tr>
                                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Plan</th>
                                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Period</th>
                                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Price</th>
                                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                                                <th class="px-4 py-2.5 text-center font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-red-50">
                                            @foreach($cancelledHistory as $sub)
                                                <tr class="hover:bg-red-50/30 transition-colors opacity-80">
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center gap-1.5 flex-wrap">
                                                            <span class="font-semibold text-gray-700">{{ $sub->plan->name }}</span>
                                                            @if($sub->is_trial)
                                                                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-purple-100 text-purple-700">Trial</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-gray-400 mt-0.5">{{ $sub->plan->duration_days }} days</p>
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-500">
                                                        <div>{{ $sub->starts_at->format('M d, Y') }}</div>
                                                        <div class="text-gray-400 text-[10px]">&rarr; {{ $sub->ends_at->format('M d, Y') }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 font-bold text-gray-700">${{ number_format($sub->plan->price, 2) }}</td>
                                                    <td class="px-4 py-3">
                                                        <span class="px-2 py-0.5 rounded-full font-semibold text-[10px] bg-red-100 text-red-700">
                                                            Cancelled
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center justify-center gap-3">
                                                            <button onclick="openPlanModal({{ json_encode($sub->plan) }}, 'details')"
                                                                    title="Details" class="text-blue-500 hover:text-blue-700 transition-colors">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <a href="{{ route('vendor.subscriptions.invoice', $sub) }}"
                                                               title="Invoice" class="text-green-500 hover:text-green-700 transition-colors">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ── AVAILABLE PLANS ── --}}
                    @php
                        $visiblePlans = $plans->filter(fn($p) =>
                            !$currentSubscription || $p->price >= $currentSubscription->plan->price
                        );
                    @endphp
                    @if($visiblePlans->count())
                        <div class="rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wide">
                                    <i class="fas fa-layer-group text-gray-400 mr-1.5"></i>
                                    Available {{ $cfg['name'] }} Plans
                                </h3>
                                <span class="text-[10px] text-gray-400">{{ $visiblePlans->count() }} plan(s)</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Plan</th>
                                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Top Features</th>
                                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wide">Price</th>
                                            <th class="px-4 py-2.5 text-center font-semibold text-gray-600 uppercase tracking-wide">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($visiblePlans as $plan)
                                            @php
                                                $isCurrent = $currentSubscription && $currentSubscription->plan_id === $plan->id;
                                                $isUpgrade = $currentSubscription && $plan->price > $currentSubscription->plan->price;
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors {{ $isCurrent ? 'bg-red-50/30' : '' }}">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span class="font-bold text-gray-900">{{ $plan->name }}</span>
                                                        @if($isCurrent)
                                                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-[#ff0808] text-white">Current</span>
                                                        @elseif($isUpgrade)
                                                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-[#1a2942] text-white">Upgrade</span>
                                                        @endif
                                                        @if(in_array($slug, ['growth','pro-export']))
                                                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-purple-100 text-purple-700">Popular</span>
                                                        @endif
                                                        @if(in_array($slug, ['enterprise','country-franchise']))
                                                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-orange-100 text-orange-700">Premium</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-gray-400 mt-0.5">{{ $plan->duration_days }} days</p>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($plan->features->take(3) as $feature)
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded text-[10px] text-gray-700">
                                                                <i class="fas fa-check text-green-500" style="font-size:8px;"></i>
                                                                <span class="font-medium">{{ $feature->feature?->name ?? ucwords(str_replace('_', ' ', $feature->feature_key ?? '')) }}:</span>
                                                                <span class="text-gray-500">{{ $feature->feature_value }}</span>
                                                            </span>
                                                        @endforeach
                                                        @if($plan->features->count() > 3)
                                                            <button onclick="openPlanModal({{ json_encode($plan) }}, 'details')"
                                                                    class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-[10px] font-bold hover:bg-blue-100 transition-all">
                                                                +{{ $plan->features->count() - 3 }} more
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="text-base font-black text-gray-900">${{ number_format($plan->price, 2) }}</span>
                                                    <span class="text-gray-400"> / {{ $plan->duration_days }}d</span>
                                                    @if($isUpgrade && $currentSubscription)
                                                        <p class="text-[10px] text-green-600 font-semibold mt-0.5">
                                                            +${{ number_format($plan->price - $currentSubscription->plan->price, 2) }} diff
                                                        </p>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($isCurrent)
                                                        <form action="{{ route('vendor.subscriptions.renew') }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 font-bold transition-all">
                                                                <i class="fas fa-redo text-[9px]"></i> Renew
                                                            </button>
                                                        </form>
                                                    @elseif($isUpgrade)
                                                        <button onclick="openPlanModal({{ json_encode($plan) }}, 'upgrade')"
                                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#1a2942] text-white rounded-lg hover:bg-[#0f1c2e] font-bold transition-all">
                                                            <i class="fas fa-arrow-up text-[9px]"></i> Upgrade
                                                        </button>
                                                    @else
                                                        <button onclick="openPlanModal({{ json_encode($plan) }}, 'subscribe')"
                                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-900 text-white rounded-lg hover:bg-gray-700 font-bold transition-all">
                                                            <i class="fas fa-bolt text-[9px]"></i> Subscribe
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(!$history->count() && !$visiblePlans->count())
                        <div class="py-16 text-center">
                            <i class="fas fa-inbox text-4xl text-gray-200 mb-3 block"></i>
                            <p class="text-sm text-gray-400">No {{ $cfg['name'] }} plans available yet.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─── Full History (ALL statuses) ─── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
            <h3 class="text-sm font-bold text-gray-900">Complete Subscription History</h3>
            <div class="flex items-center gap-2 flex-wrap">
                @php
                    $cancelledCount = $subscriptionHistory->where('status','cancelled')->count();
                    $expiredCount   = $subscriptionHistory->where('status','expired')->count();
                @endphp

                {{-- View filter buttons --}}
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                    <button onclick="setHistoryFilter('active')" id="history-filter-active"
                            class="history-filter-btn px-3 py-1 text-[10px] font-bold rounded-md transition-all bg-white text-gray-700 shadow-sm">
                        Active Only
                    </button>
                    <button onclick="setHistoryFilter('all')" id="history-filter-all"
                            class="history-filter-btn px-3 py-1 text-[10px] font-bold rounded-md transition-all text-gray-500">
                        Show All
                    </button>
                    @if($cancelledCount)
                        <button onclick="setHistoryFilter('cancelled')" id="history-filter-cancelled"
                                class="history-filter-btn px-3 py-1 text-[10px] font-bold rounded-md transition-all text-gray-500">
                            Cancelled Only
                        </button>
                    @endif
                </div>

                @if($cancelledCount)
                    <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-[10px] font-bold">{{ $cancelledCount }} cancelled</span>
                @endif
                @if($expiredCount)
                    <span class="px-2 py-0.5 bg-orange-100 text-orange-600 rounded-full text-[10px] font-bold">{{ $expiredCount }} expired</span>
                @endif
                <span class="text-[10px] text-gray-400">{{ $subscriptionHistory->total() }} total</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide">Plan</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide">Period</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide">Price</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="history-tbody">
                    @forelse($subscriptionHistory as $sub)
                        <tr class="hover:bg-gray-50 transition-colors history-row"
                            data-status="{{ $sub->status }}">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-semibold {{ $sub->status === 'cancelled' ? 'text-gray-500 line-through' : 'text-gray-900' }}">
                                        {{ $sub->plan->name }}
                                    </span>
                                    @if($sub->is_trial)
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-purple-100 text-purple-700">Trial</span>
                                    @endif
                                    @if($currentSubscription && $sub->id === $currentSubscription->id)
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-700">Active</span>
                                    @endif
                                </div>
                                <p class="text-gray-400 mt-0.5">{{ $sub->plan->duration_days }} days</p>
                            </td>
                            <td class="px-5 py-3 text-gray-500">
                                <div>{{ $sub->starts_at->format('M d, Y') }}</div>
                                <div class="text-gray-400 text-[10px]">&rarr; {{ $sub->ends_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-5 py-3 font-bold {{ $sub->status === 'cancelled' ? 'text-gray-400' : 'text-gray-900' }}">
                                ${{ number_format($sub->plan->price, 2) }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full font-semibold text-[10px]
                                    {{ $sub->status === 'active'    ? 'bg-green-100 text-green-700'   : '' }}
                                    {{ $sub->status === 'expired'   ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $sub->status === 'cancelled' ? 'bg-red-100 text-red-700'       : '' }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-center gap-3">
                                    <button onclick="openPlanModal({{ json_encode($sub->plan) }}, 'details')"
                                            title="Details" class="text-blue-500 hover:text-blue-700 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('vendor.subscriptions.invoice', $sub) }}"
                                       title="Invoice" class="text-green-500 hover:text-green-700 transition-colors">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400">No subscription history yet.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Empty state shown when filter has no results --}}
            <div id="history-empty" class="hidden px-5 py-10 text-center">
                <i class="fas fa-filter text-2xl text-gray-200 mb-2 block"></i>
                <p class="text-sm text-gray-400">No records match this filter.</p>
            </div>
        </div>
        @if($subscriptionHistory->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">{{ $subscriptionHistory->links() }}</div>
        @endif
    </div>
</div>

{{-- ══════════════ PLAN MODAL ══════════════ --}}
<div id="planModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-start justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl w-full max-w-2xl mt-12 mb-8 shadow-2xl overflow-hidden modal-animate">
        <div class="px-6 py-5 text-white relative" style="background: linear-gradient(135deg, #1a2942 0%, #ff0808 100%);">
            <button onclick="closePlanModal()" class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
            <div class="flex items-start justify-between pr-8">
                <div>
                    <span id="modal-badge" class="inline-block text-[9px] font-black uppercase tracking-widest bg-white/20 text-white px-2 py-0.5 rounded-full mb-1"></span>
                    <h2 id="modal-name" class="text-xl font-black text-white"></h2>
                    <p id="modal-desc" class="text-white/60 text-xs mt-1"></p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p id="modal-price" class="text-3xl font-black text-white"></p>
                    <p id="modal-duration-label" class="text-white/60 text-xs mt-0.5"></p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <h3 class="text-xs font-bold text-gray-900 mb-3 uppercase tracking-wide flex items-center gap-1.5">
                    <i class="fas fa-star text-yellow-500" style="font-size:10px;"></i> Features Included
                </h3>
                <div id="modal-features" class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-60 overflow-y-auto pr-1"></div>
            </div>

            <div class="flex gap-2 pt-3 border-t border-gray-100">
                <button onclick="closePlanModal()"
                        class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-xs hover:bg-gray-200 transition-all">
                    &larr; Back
                </button>
                <form id="modal-form" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" id="modal-cta"
                            class="w-full py-2.5 rounded-xl font-black text-xs text-white transition-all shadow-md">
                        <i id="modal-cta-icon" class="fas fa-bolt mr-1 text-[10px]"></i>
                        <span id="modal-cta-text">Subscribe Now</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ UPGRADE PICKER MODAL ══════════════ --}}
<div id="upgradeModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-start justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl w-full max-w-3xl mt-12 mb-8 shadow-2xl overflow-hidden modal-animate">
        <div class="px-6 py-5 flex items-center justify-between" style="background: linear-gradient(135deg, #1a2942, #2d4a7a);">
            <div>
                <h2 class="text-lg font-black text-white">Upgrade Your Plan</h2>
                <p class="text-white/50 text-xs mt-0.5">Your remaining days carry over when you upgrade.</p>
            </div>
            <button onclick="closeUpgradeModal()" class="text-white/60 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-5">
            @php
                $upgradePlans = $currentSubscription
                    ? $availablePlans->where('price', '>', $currentSubscription->plan->price)->sortBy('price')
                    : collect();
            @endphp
            @if($upgradePlans->count())
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($upgradePlans as $up)
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-[#ff0808] hover:shadow-md transition-all cursor-pointer"
                             onclick="closeUpgradeModal(); openPlanModal({{ json_encode($up) }}, 'upgrade')">
                            <h4 class="font-bold text-sm text-gray-900 mb-0.5">{{ $up->name }}</h4>
                            <p class="text-[10px] text-gray-400 mb-2">{{ $up->duration_days }} days</p>
                            <p class="text-xl font-black text-[#ff0808]">${{ number_format($up->price, 2) }}</p>
                            @if($currentSubscription)
                                <p class="text-[10px] text-green-600 font-semibold mt-0.5">
                                    +${{ number_format($up->price - $currentSubscription->plan->price, 2) }} from current
                                </p>
                            @endif
                            <div class="mt-3 w-full py-1.5 text-[10px] font-black uppercase text-center bg-[#1a2942] text-white rounded-lg">
                                View more &rarr;
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-sm text-gray-400 py-8">You are already on the highest available plan.</p>
            @endif
        </div>
    </div>
</div>

<style>
.sub-tab.active { color:#ff0808; border-bottom-color:#ff0808; background:#fff5f5; }
.sub-content { animation: fadeUp 0.25s ease-out; }
.sub-content.hidden { display:none; }
.scrollbar-hide::-webkit-scrollbar { display:none; }
.scrollbar-hide { -ms-overflow-style:none; scrollbar-width:none; }
.modal-animate { animation: modalIn 0.25s ease-out; }
.history-filter-btn.active { background:#fff; color:#111827; box-shadow:0 1px 3px rgba(0,0,0,.1); }
@keyframes fadeUp  { from{opacity:0;transform:translateY(10px);}  to{opacity:1;transform:translateY(0);} }
@keyframes modalIn { from{opacity:0;transform:translateY(-16px);} to{opacity:1;transform:translateY(0);} }
</style>

<script>
// ── Tab switching ──────────────────────────────────────────────────
function switchTab(slug) {
    document.querySelectorAll('.sub-content').forEach(c => c.classList.add('hidden'));
    document.querySelectorAll('.sub-tab').forEach(t => t.classList.remove('active'));
    const c = document.getElementById('content-' + slug);
    const t = document.getElementById('tab-' + slug);
    if (c) c.classList.remove('hidden');
    if (t) { t.classList.add('active'); t.scrollIntoView({behavior:'smooth',inline:'center',block:'nearest'}); }
    localStorage.setItem('subTab', slug);
}

// ── Toggle cancelled block inside a tab ───────────────────────────
function toggleCancelledTab(slug) {
    const block  = document.getElementById('cancelled-' + slug);
    const icon   = document.getElementById('toggle-icon-' + slug);
    const label  = document.getElementById('toggle-label-' + slug);
    const isOpen = !block.classList.contains('hidden');

    if (isOpen) {
        block.classList.add('hidden');
        icon.classList.replace('fa-chevron-down', 'fa-chevron-right');
        label.textContent = label.textContent.replace('Hide', 'Show');
    } else {
        block.classList.remove('hidden');
        icon.classList.replace('fa-chevron-right', 'fa-chevron-down');
        label.textContent = label.textContent.replace('Show', 'Hide');
    }
}

// ── Full history filter ────────────────────────────────────────────
let currentHistoryFilter = 'active';

function setHistoryFilter(filter) {
    currentHistoryFilter = filter;

    // Update button states
    document.querySelectorAll('.history-filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-white', 'text-gray-700', 'shadow-sm');
        btn.classList.add('text-gray-500');
    });
    const activeBtn = document.getElementById('history-filter-' + filter);
    if (activeBtn) {
        activeBtn.classList.add('active', 'bg-white', 'text-gray-700', 'shadow-sm');
        activeBtn.classList.remove('text-gray-500');
    }

    // Show/hide rows
    const rows = document.querySelectorAll('.history-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const status = row.dataset.status;
        let show = false;

        if (filter === 'all') {
            show = true;
        } else if (filter === 'active') {
            show = status !== 'cancelled';
        } else if (filter === 'cancelled') {
            show = status === 'cancelled';
        }

        row.classList.toggle('hidden', !show);
        if (show) visibleCount++;
    });

    // Toggle empty state
    const emptyEl = document.getElementById('history-empty');
    if (emptyEl) emptyEl.classList.toggle('hidden', visibleCount > 0);

    localStorage.setItem('historyFilter', filter);
}

// ── Plan modal ─────────────────────────────────────────────────────
function openPlanModal(plan, mode) {
    const labels = { subscribe:'New Plan', upgrade:'Upgrade', renew:'Renew', details:'Plan Details' };
    document.getElementById('modal-badge').textContent          = labels[mode] || 'Plan';
    document.getElementById('modal-name').textContent           = plan.name;
    document.getElementById('modal-desc').textContent           = plan.description || 'Full access to all ' + plan.name + ' features.';
    document.getElementById('modal-price').textContent          = '$' + parseFloat(plan.price).toFixed(2);
    document.getElementById('modal-duration-label').textContent = '/ ' + plan.duration_days + ' days';

    const fc = document.getElementById('modal-features');
    fc.innerHTML = '';
    (plan.features || []).forEach(f => {
        const label = (f.feature && f.feature.name)
            ? f.feature.name
            : ucwords(f.feature_key || '');
        const d = document.createElement('div');
        d.className = 'flex items-start gap-2 p-2.5 bg-gray-50 rounded-lg border border-gray-100 text-xs';
        d.innerHTML = `<i class="fas fa-check text-green-500 mt-0.5 flex-shrink-0" style="font-size:8px;"></i>
            <div><span class="font-semibold text-gray-800">${label}: </span>
            <span class="text-gray-500">${String(f.feature_value ?? '')}</span></div>`;
        fc.appendChild(d);
    });
    if (!(plan.features||[]).length) {
        fc.innerHTML = '<p class="col-span-2 text-center text-gray-400 py-6 text-xs">No detailed features listed.</p>';
    }

    const form   = document.getElementById('modal-form');
    const cta    = document.getElementById('modal-cta');
    const ctaTxt = document.getElementById('modal-cta-text');
    const ctaIco = document.getElementById('modal-cta-icon');

    if (mode === 'renew') {
        ctaTxt.textContent = 'Renew Plan';
        ctaIco.className   = 'fas fa-redo mr-1';
        form.action        = '{{ route("vendor.subscriptions.renew") }}';
        cta.style.background = '#ff0808';
    } else if (mode === 'upgrade') {
        ctaTxt.textContent = 'Upgrade to ' + plan.name;
        ctaIco.className   = 'fas fa-arrow-up mr-1';
        form.action        = '/vendor/subscriptions/subscribe/' + plan.id;
        cta.style.background = '#1a2942';
    } else if (mode === 'details') {
        @if($currentSubscription)
        if (plan.id === {{ $currentSubscription->plan_id }}) {
            ctaTxt.textContent = 'Renew This Plan';
            ctaIco.className   = 'fas fa-redo mr-1';
            form.action        = '{{ route("vendor.subscriptions.renew") }}';
            cta.style.background = '#ff0808';
        } else if (parseFloat(plan.price) > {{ $currentSubscription->plan->price }}) {
            ctaTxt.textContent = 'Upgrade to This Plan';
            ctaIco.className   = 'fas fa-arrow-up mr-1';
            form.action        = '/vendor/subscriptions/subscribe/' + plan.id;
            cta.style.background = '#1a2942';
        } else {
            ctaTxt.textContent = 'Subscribe';
            ctaIco.className   = 'fas fa-bolt mr-1';
            form.action        = '/vendor/subscriptions/subscribe/' + plan.id;
            cta.style.background = '#1f2937';
        }
        @else
        ctaTxt.textContent = 'Subscribe Now';
        ctaIco.className   = 'fas fa-bolt mr-1';
        form.action        = '/vendor/subscriptions/subscribe/' + plan.id;
        cta.style.background = '#ff0808';
        @endif
    } else {
        ctaTxt.textContent = 'Subscribe Now';
        ctaIco.className   = 'fas fa-bolt mr-1';
        form.action        = '/vendor/subscriptions/subscribe/' + plan.id;
        cta.style.background = '#ff0808';
    }

    document.getElementById('planModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePlanModal()   { document.getElementById('planModal').classList.add('hidden');   document.body.style.overflow=''; }
function openUpgradeModal() { document.getElementById('upgradeModal').classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeUpgradeModal(){ document.getElementById('upgradeModal').classList.add('hidden'); document.body.style.overflow=''; }

function ucwords(str) {
    return (str||'').replace(/_/g,' ').toLowerCase().replace(/\b[a-z]/g, l => l.toUpperCase());
}

['planModal','upgradeModal'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e){
        if (e.target===this){ this.classList.add('hidden'); document.body.style.overflow=''; }
    });
});

document.addEventListener('keydown', e => { if(e.key==='Escape'){ closePlanModal(); closeUpgradeModal(); } });

// ── Init ───────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Restore tab
    const savedTab = localStorage.getItem('subTab');
    const firstVisible = document.querySelector('.sub-tab')?.id?.replace('tab-', '');
    const targetTab = (savedTab && document.getElementById('tab-' + savedTab)) ? savedTab : (firstVisible || 'free-trial');
    switchTab(targetTab);

    // Restore history filter (default: active only)
    const savedFilter = localStorage.getItem('historyFilter') || 'active';
    setHistoryFilter(savedFilter);
});
</script>
@endsection
