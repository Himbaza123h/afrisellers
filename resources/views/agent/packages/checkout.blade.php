@extends('layouts.home')

@section('page-content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('agent.packages.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
            <i class="fas fa-arrow-left"></i>
            <span>Back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Subscribe to {{ $package->name }}</h1>
            <p class="text-sm text-gray-500">Complete your purchase to get started</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('agent.packages.subscribe', $package->id) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Payment Method -->
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

                <!-- Auto-Renewal -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="auto_renew" class="mt-1 w-5 h-5 text-blue-600 rounded">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 mb-1">Enable Auto-Renewal</div>
                            <p class="text-sm text-gray-600">Automatically renew your subscription before it expires. You can cancel anytime.</p>
                        </div>
                    </label>
                </div>

                <!-- Terms -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" required class="mt-1 w-5 h-5 text-blue-600 rounded">
                        <div class="flex-1">
                            <span class="text-sm text-gray-700">I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a></span>
                        </div>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold text-lg hover:shadow-lg transition-all">
                    <i class="fas fa-lock mr-2"></i>
                    Complete Purchase - ${{ number_format($package->price, 2) }}
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden sticky top-6">
                <div class="p-6 bg-gradient-to-br from-{{ $package->badge_color }}-50 to-{{ $package->badge_color }}-100 border-b border-{{ $package->badge_color }}-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Order Summary</h3>
                    <p class="text-sm text-gray-600">{{ $package->name }} Package</p>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Package Details -->
                    <div class="space-y-3 pb-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Package</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $package->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Billing Cycle</span>
                            <span class="text-sm font-semibold text-gray-900">{{ ucfirst($package->billing_cycle) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Duration</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $package->duration_days }} days</span>
                        </div>
                    </div>

                    <!-- Features Summary -->
                    <div class="space-y-2 pb-4 border-b border-gray-200">
                        <p class="text-xs font-semibold text-gray-700 uppercase">Includes:</p>
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
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check text-green-500 text-xs"></i>
                                <span class="text-sm text-gray-700">{{ $package->commission_rate }}% Commission</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-lg font-semibold text-gray-900">Total</span>
                            <span class="text-2xl font-bold text-gray-900">${{ number_format($package->price, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500">Billed {{ $package->billing_cycle }}</p>
                    </div>

                    <!-- Security Badge -->
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-lock text-gray-400"></i>
                            <span class="text-xs font-semibold text-gray-700">Secure Payment</span>
                        </div>
                        <p class="text-xs text-gray-500">Your payment information is encrypted and secure</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
