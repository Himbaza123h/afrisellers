@extends('layouts.home')

@section('page-content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="text-center max-w-xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900">Choose Your Agent Plan</h1>
        <p class="mt-2 text-sm text-gray-500">
            Unlock more vendor slots, higher commissions, and priority support by upgrading your plan.
        </p>
    </div>

    {{-- Current Plan Banner --}}
    @if($current)
        <div class="max-w-3xl mx-auto bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
            <i class="fas fa-crown text-amber-500 flex-shrink-0"></i>
            <p class="text-sm text-amber-800">
                You're currently on the <strong>{{ $current->package?->name }}</strong> plan.
                It expires on <strong>{{ $current->expires_at?->format('M d, Y') }}</strong>.
                Selecting a new plan will replace your current subscription immediately.
            </p>
        </div>
    @endif

    {{-- Alerts --}}
    @if(session('success'))
        <div class="max-w-3xl mx-auto p-4 bg-green-50 rounded-xl border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{!! session('success') !!}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-3xl mx-auto p-4 bg-red-50 rounded-xl border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm text-red-900 font-medium flex-1">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Plans Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 max-w-5xl mx-auto">
        @forelse($packages as $pkg)
            @php
                $isCurrentPlan = $current?->package_id === $pkg->id && $current?->isActive();
                $colorMap = [
                    'purple' => ['ring-purple-500','bg-purple-600','text-purple-600','bg-purple-50'],
                    'yellow' => ['ring-yellow-400','bg-yellow-500','text-yellow-600','bg-yellow-50'],
                    'blue'   => ['ring-blue-500','bg-blue-600','text-blue-600','bg-blue-50'],
                    'green'  => ['ring-green-500','bg-green-600','text-green-600','bg-green-50'],
                    'gray'   => ['ring-gray-400','bg-gray-500','text-gray-600','bg-gray-50'],
                ];
                $colors = $colorMap[$pkg->badge_color] ?? $colorMap['blue'];
            @endphp
            <div class="bg-white rounded-2xl border-2 {{ $isCurrentPlan ? $colors[0] . ' ring-2' : 'border-gray-200' }} shadow-sm flex flex-col relative overflow-hidden transition-all hover:shadow-md">

                @if($pkg->is_featured)
                    <div class="absolute top-3 right-3">
                        <span class="px-2 py-0.5 bg-amber-400 text-white text-[10px] font-bold rounded-full uppercase tracking-wider">
                            Popular
                        </span>
                    </div>
                @endif

                @if($isCurrentPlan)
                    <div class="absolute top-3 left-3">
                        <span class="px-2 py-0.5 bg-green-500 text-white text-[10px] font-bold rounded-full uppercase tracking-wider">
                            Current
                        </span>
                    </div>
                @endif

                <div class="p-6">
                    <div class="w-12 h-12 {{ $colors[3] }} rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-crown {{ $colors[2] }} text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $pkg->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1 min-h-[40px]">{{ $pkg->description }}</p>

                    <div class="mt-4">
                        <span class="text-3xl font-bold text-gray-900">${{ number_format($pkg->price, 2) }}</span>
                        <span class="text-sm text-gray-400"> / {{ $pkg->billing_cycle }}</span>
                    </div>

                    <ul class="mt-5 space-y-2.5">
                        @if($pkg->max_referrals)
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fas fa-check {{ $colors[2] }} w-4 text-center"></i>
                                Up to {{ $pkg->max_referrals }} referrals
                            </li>
                        @endif
                        @if(isset($pkg->max_vendors))
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fas fa-check {{ $colors[2] }} w-4 text-center"></i>
                                Up to {{ $pkg->max_vendors }} vendor slots
                            </li>
                        @endif
                        @if($pkg->commission_boost)
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fas fa-check {{ $colors[2] }} w-4 text-center"></i>
                                {{ $pkg->commission_rate }}% commission rate
                            </li>
                        @endif
                        @if($pkg->priority_support)
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fas fa-check {{ $colors[2] }} w-4 text-center"></i>
                                Priority support
                            </li>
                        @endif
                        @if($pkg->advanced_analytics)
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fas fa-check {{ $colors[2] }} w-4 text-center"></i>
                                Advanced analytics
                            </li>
                        @endif
                        @if($pkg->featured_profile)
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fas fa-check {{ $colors[2] }} w-4 text-center"></i>
                                Featured agent profile
                            </li>
                        @endif
                        @if($pkg->max_payouts_per_month)
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fas fa-check {{ $colors[2] }} w-4 text-center"></i>
                                {{ $pkg->max_payouts_per_month }} payouts/month
                            </li>
                        @endif
                        @if($pkg->allow_rfqs)
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <i class="fas fa-check {{ $colors[2] }} w-4 text-center"></i>
                                RFQ access
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="p-5 mt-auto border-t border-gray-100">
                    @if($isCurrentPlan)
                        <button disabled
                            class="w-full py-2.5 bg-gray-100 text-gray-400 rounded-xl text-sm font-semibold cursor-not-allowed">
                            Current Plan
                        </button>
                    @else
                        <button onclick="document.getElementById('modal-{{ $pkg->id }}').classList.remove('hidden')"
                            class="w-full py-2.5 {{ $colors[1] }} text-white rounded-xl text-sm font-bold hover:opacity-90 shadow transition-all">
                            {{ $current ? 'Switch to This Plan' : 'Subscribe Now' }}
                        </button>
                    @endif
                </div>
            </div>

            {{-- Subscribe Modal for this package --}}
            <div id="modal-{{ $pkg->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
                <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
                    <h3 class="text-base font-bold text-gray-900 mb-1">Subscribe to {{ $pkg->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        ${{ number_format($pkg->price, 2) }} / {{ $pkg->billing_cycle }}
                        — {{ $pkg->duration_days }} days access
                    </p>
                    <form action="{{ route('agent.subscriptions.subscribe', $pkg->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_method" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">Select method</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="card">Credit / Debit Card</option>
                                <option value="manual">Manual / Cash</option>
                            </select>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="auto_renew" value="1" class="rounded">
                            Enable auto-renew
                        </label>
                        <div class="flex gap-2 pt-1">
                            <button type="button"
                                onclick="document.getElementById('modal-{{ $pkg->id }}').classList.add('hidden')"
                                class="flex-1 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 py-2 {{ $colors[1] }} text-white rounded-lg text-sm font-bold hover:opacity-90">
                                Confirm & Pay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-16 text-gray-400">
                <i class="fas fa-box-open text-4xl mb-3 block"></i>
                <p class="text-sm font-medium">No plans available at the moment.</p>
            </div>
        @endforelse
    </div>

    <div class="text-center">
        <a href="{{ route('agent.subscriptions.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i> Back to My Subscription
        </a>
    </div>
</div>
@endsection
