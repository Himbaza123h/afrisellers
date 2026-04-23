@if(auth()->user()->isVendor())
@php
    $_vendor   = auth()->user();
    $_sub      = \App\Models\Subscription::where('seller_id', $_vendor->id)
                    ->where('status', 'active')
                    ->with('plan.features')
                    ->first();

    $_numItems  = [];
    $_boolItems = [];

    if ($_sub && $_sub->plan) {
        $_feats = $_sub->plan->features->keyBy('feature_key');

$_trackable = [
    'max_products' => [
        'label' => 'Products',
        'icon'  => 'box',
        'used'  => \App\Models\Product::where('user_id', $_vendor->id)
                        ->whereNull('deleted_at')->count(),
    ],
    'max_messages' => [
        'label' => 'Messages',
        'icon'  => 'envelope',
        'used'  => \Illuminate\Support\Facades\DB::table('messages')
                        ->where('sender_id', $_vendor->id)
                        ->whereNull('deleted_at')->count(),
    ],
    'max_campaigns' => [
        'label' => 'Campaigns',
        'icon'  => 'bullhorn',
        'used'  => \App\Models\Campaign::where('user_id', $_vendor->id)->count(),
    ],
    'email_campaigns_per_month' => [
        'label' => 'Email Campaigns (month)',
        'icon'  => 'paper-plane',
        'used'  => \App\Models\Campaign::where('user_id', $_vendor->id)
                        ->byType('email')
                        ->whereNotNull('sent_at')
                        ->thisMonth()
                        ->count(),
    ],
    'allowed_ads' => [
        'label' => 'Advertisements',
        'icon'  => 'ad',
        'used'  => \App\Models\Advertisement::where('user_id', $_vendor->id)
                        ->whereNotIn('status', ['rejected', 'expired'])
                        ->count(),
    ],
];

        foreach ($_trackable as $_k => $_d) {
            if (!$_feats->has($_k)) continue;
            $_limit   = $_feats[$_k]->feature_value;
            $_isUnlim = strtolower($_limit) === 'unlimited';
            $_pct     = $_isUnlim ? 0 : ($_limit > 0 ? min(100, (int) round($_d['used'] / $_limit * 100)) : 0);
            $_numItems[] = [
                'label'     => $_d['label'],
                'icon'      => $_d['icon'],
                'used'      => $_d['used'],
                'limit'     => $_limit,
                'unlimited' => $_isUnlim,
                'pct'       => $_pct,
            ];
        }

        $_boolMap = [
            'has_analytics'          => 'Analytics',
            'has_crm'                => 'CRM',
            'has_api_access'         => 'API Access',
            'has_email_marketing'    => 'Email Marketing',
            'has_ads'                => 'Ads',
            'has_live_selling'       => 'Live Selling',
            'has_ecommerce_store'    => 'E-Commerce',
            'has_ai_chatbot'         => 'AI Chatbot',
            'has_advanced_analytics' => 'Adv. Analytics',
            'has_affiliate_marketing'=> 'Affiliate Mktg',
        ];
        foreach ($_boolMap as $_bk => $_bl) {
            if (!$_feats->has($_bk)) continue;
            $_boolItems[] = [
                'label'   => $_bl,
                'enabled' => strtolower($_feats[$_bk]->feature_value) === 'true',
            ];
        }
    }

    $_countable = array_filter($_numItems, fn($i) => !$i['unlimited']);
    $_avgPct    = count($_countable) > 0
        ? (int) round(array_sum(array_column(array_values($_countable), 'pct')) / count($_countable))
        : 0;

    $_r        = 10;
    $_circ     = round(2 * M_PI * $_r, 2);
    $_usedDash = round($_avgPct / 100 * $_circ, 2);
    $_freeDash = round($_circ - $_usedDash, 2);
@endphp

<div class="relative" id="usage-circle-wrapper">

    <button id="usage-circle-btn"
            class="relative flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 hover:bg-gray-100 rounded-full transition-colors"
            title="Plan Usage">
        <svg width="36" height="36" viewBox="0 0 36 36" class="-rotate-90">
            <circle cx="18" cy="18" r="{{ $_r }}"
                    fill="none" stroke="#dbeafe" stroke-width="4"/>
            @if($_usedDash > 0)
            <circle cx="18" cy="18" r="{{ $_r }}"
                    fill="none" stroke="#ef4444" stroke-width="4"
                    stroke-dasharray="{{ $_usedDash }} {{ $_freeDash }}"
                    stroke-linecap="round"/>
            @endif
            @if($_freeDash > 0 && $_usedDash > 0)
            <circle cx="18" cy="18" r="{{ $_r }}"
                    fill="none" stroke="#3b82f6" stroke-width="4"
                    stroke-dasharray="{{ $_freeDash }} {{ $_usedDash }}"
                    stroke-dashoffset="-{{ $_usedDash }}"
                    stroke-linecap="round"/>
            @endif
            @if($_usedDash == 0)
            <circle cx="18" cy="18" r="{{ $_r }}"
                    fill="none" stroke="#3b82f6" stroke-width="4"
                    stroke-dasharray="{{ $_circ }} 0"/>
            @endif
        </svg>
        <span class="absolute text-[9px] font-bold leading-none {{ $_avgPct >= 80 ? 'text-red-600' : 'text-blue-600' }}">
            {{ $_avgPct }}%
        </span>
    </button>

    <div id="usage-circle-dropdown"
         class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">

        <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-red-50 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-800">Plan Usage</p>
                <p class="text-[10px] text-gray-500">{{ $_sub ? $_sub->plan->name : 'No active plan' }}</p>
            </div>
            <div class="flex items-center gap-3 text-[10px]">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>Used</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>Free</span>
            </div>
        </div>

        <div class="max-h-80 overflow-y-auto">
            @if(!$_sub)
                <div class="px-4 py-6 text-center text-sm text-gray-400">No active subscription</div>
            @else
                @if(count($_numItems))
                <div class="px-4 pt-3 pb-1">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Usage Limits</p>
                    @foreach($_numItems as $_item)
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-700 flex items-center gap-1.5">
                                <i class="fas fa-{{ $_item['icon'] }} text-gray-400 text-[10px]"></i>
                                {{ $_item['label'] }}
                            </span>
                            <span class="text-[10px] font-semibold {{ $_item['unlimited'] ? 'text-blue-600' : ($_item['pct'] >= 80 ? 'text-red-600' : 'text-gray-600') }}">
                                @if($_item['unlimited'])
                                    {{ $_item['used'] }} / ∞
                                @else
                                    {{ $_item['used'] }} / {{ $_item['limit'] }}
                                    <span class="text-gray-400">({{ $_item['pct'] }}%)</span>
                                @endif
                            </span>
                        </div>
                        @if(!$_item['unlimited'])
                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-1.5 rounded-full transition-all {{ $_item['pct'] >= 80 ? 'bg-red-500' : ($_item['pct'] >= 50 ? 'bg-yellow-400' : 'bg-blue-500') }}"
                                 style="width: {{ $_item['pct'] }}%"></div>
                        </div>
                        @else
                        <div class="w-full bg-blue-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-blue-400 w-full"></div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                @if(count($_boolItems))
                <div class="px-4 pt-2 pb-3 border-t border-gray-100">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Features</p>
                    <div class="grid grid-cols-2 gap-1.5">
                        @foreach($_boolItems as $_b)
                        <div class="flex items-center gap-1.5 text-[11px] {{ $_b['enabled'] ? 'text-gray-700' : 'text-gray-300' }}">
                            <i class="fas {{ $_b['enabled'] ? 'fa-check-circle text-blue-500' : 'fa-times-circle text-gray-300' }} text-xs flex-shrink-0"></i>
                            {{ $_b['label'] }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif
        </div>

        @if($_sub)
        <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <span class="text-[10px] text-gray-400">
                Expires {{ $_sub->ends_at ? $_sub->ends_at->format('M d, Y') : '—' }}
            </span>
            <a href="{{ route('vendor.subscriptions.index') }}"
               class="text-[10px] font-semibold text-blue-600 hover:underline">
                Manage Plan →
            </a>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const usageBtn      = document.getElementById('usage-circle-btn');
    const usageDropdown = document.getElementById('usage-circle-dropdown');

    if (usageBtn && usageDropdown) {
        usageBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            usageDropdown.classList.toggle('hidden');

            // close other dropdowns
            document.getElementById('notifications-dropdown')?.classList.add('hidden');
            document.getElementById('alerts-dropdown')?.classList.add('hidden');
            document.getElementById('profile-dropdown-1')?.classList.add('hidden');
        });

        document.addEventListener('click', function (e) {
            if (!usageBtn.contains(e.target) && !usageDropdown.contains(e.target)) {
                usageDropdown.classList.add('hidden');
            }
        });
    }
});
</script>
@endif
