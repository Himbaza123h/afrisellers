@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('agent.packages.show', $package->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
            <i class="fas fa-arrow-left"></i>
            <span>Back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Subscribe to {{ $package->name }}</h1>
            <p class="text-sm text-gray-500">Complete your purchase to get started</p>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: Checkout Form --}}
        <div class="lg:col-span-2">
            <form id="checkoutForm" action="{{ route('agent.packages.subscribe', $package->id) }}" method="POST" class="space-y-6">
                @csrf

                {{-- Payment Method --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h2>

                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="payment_method" value="credit_card" required class="w-5 h-5 text-blue-600">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-credit-card text-gray-600"></i>
                                    <span class="font-semibold text-gray-900">Credit Card</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Pay securely with your credit or debit card</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="payment_method" value="paypal" required class="w-5 h-5 text-blue-600">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <i class="fab fa-paypal text-gray-600"></i>
                                    <span class="font-semibold text-gray-900">PayPal</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Fast and secure PayPal payment</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="payment_method" value="bank_transfer" required class="w-5 h-5 text-blue-600">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-university text-gray-600"></i>
                                    <span class="font-semibold text-gray-900">Bank Transfer</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Direct bank transfer payment</p>
                            </div>
                        </label>
                    </div>

                    @error('payment_method')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Auto-Renewal --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="auto_renew" class="mt-1 w-5 h-5 text-blue-600 rounded">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 mb-1">Enable Auto-Renewal</div>
                            <p class="text-sm text-gray-600">Automatically renew your subscription before it expires. You can cancel anytime.</p>
                        </div>
                    </label>
                </div>

                {{-- Terms --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" required class="mt-1 w-5 h-5 text-blue-600 rounded">
                        <div class="flex-1">
                            <span class="text-sm text-gray-700">
                                I agree to the
                                <a href="#" class="text-blue-600 hover:underline">Terms of Service</a>
                                and
                                <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                            </span>
                        </div>
                    </label>
                </div>

{{-- Submit --}}
<button type="submit" id="submitBtn"
        class="w-full px-4 py-2.5 bg-[#ff0808] hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition-all shadow-sm flex items-center justify-center gap-2">
    <i class="fas fa-lock" id="btnIcon"></i>
    <span id="btnText">Complete Purchase — ${{ number_format($package->price, 2) }}</span>
    <svg id="btnLoader" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
    </svg>
</button>



            </form>
        </div>

        {{-- RIGHT: Order Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden sticky top-6">

                {{-- Summary header — inline style avoids dynamic Tailwind issue --}}
                <div class="p-6 border-b border-gray-200" style="background: #f8fafc;">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Order Summary</h3>
                    <p class="text-sm text-gray-500">{{ $package->name }} Package</p>
                </div>

                <div class="p-6 space-y-4">

                    {{-- Package Details --}}
                    <div class="space-y-3 pb-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Package</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $package->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Billing Cycle</span>
                            <span class="text-sm font-semibold text-gray-900">{{ ucfirst($package->billing_cycle) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Duration</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $package->duration_days }} days</span>
                        </div>
                    </div>

                    {{-- Features --}}
                    <div class="space-y-2 pb-4 border-b border-gray-200">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Includes:</p>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check text-green-500 text-xs"></i>
                                <span class="text-sm text-gray-700">{{ $package->max_referrals }} Referrals</span>
                            </div>
                            @if($package->allow_rfqs)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                    <span class="text-sm text-gray-700">RFQ Access</span>
                                </div>
                            @endif
                            @if($package->priority_support)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                    <span class="text-sm text-gray-700">Priority Support</span>
                                </div>
                            @endif
                            @if($package->advanced_analytics)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                    <span class="text-sm text-gray-700">Advanced Analytics</span>
                                </div>
                            @endif
                            @if($package->featured_profile)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                    <span class="text-sm text-gray-700">Featured Profile</span>
                                </div>
                            @endif
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check text-green-500 text-xs"></i>
                                <span class="text-sm text-gray-700">{{ $package->commission_rate }}% Commission</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check text-green-500 text-xs"></i>
                                <span class="text-sm text-gray-700">{{ $package->max_payouts_per_month }} Payout{{ $package->max_payouts_per_month > 1 ? 's' : '' }}/Month</span>
                            </div>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-semibold text-gray-700">Total</span>
                            <span class="text-2xl font-bold text-gray-900">${{ number_format($package->price, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-400">Billed {{ $package->billing_cycle }}</p>
                    </div>

                    {{-- Security --}}
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-lock text-gray-400 text-xs"></i>
                            <span class="text-xs font-semibold text-gray-700">Secure Payment</span>
                        </div>
                        <p class="text-xs text-gray-400">Your payment information is encrypted and secure</p>
                    </div>

                    {{-- Trust badges --}}
                    <div class="space-y-1.5 pt-1">
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <i class="fas fa-undo"></i> Cancel anytime
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <i class="fas fa-headset"></i> Support included
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <i class="fas fa-shield-alt"></i> Buyer protection
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    form.addEventListener('submit', function () {
        const btn    = document.getElementById('submitBtn');
        const icon   = document.getElementById('btnIcon');
        const text   = document.getElementById('btnText');
        const loader = document.getElementById('btnLoader');

        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        icon.classList.add('hidden');
        text.textContent = 'Processing...';
        loader.classList.remove('hidden');
    });
});
</script>


@endsection

