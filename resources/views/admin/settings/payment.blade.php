@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Payment Settings</h1>
            </div>
            <p class="text-sm text-gray-500">Configure payment gateways, commissions and transaction settings</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="payment">

        <div class="space-y-6">
            <!-- Payment Gateway -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Default Payment Gateway</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Primary Gateway *</label>
                    <select name="payment_gateway" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="stripe" {{ $settings['payment_gateway'] == 'stripe' ? 'selected' : '' }}>Stripe</option>
                        <option value="paypal" {{ $settings['payment_gateway'] == 'paypal' ? 'selected' : '' }}>PayPal</option>
                        <option value="bank_transfer" {{ $settings['payment_gateway'] == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>
            </div>

            <!-- Stripe Settings -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Stripe Configuration</h2>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_stripe" {{ $settings['enable_stripe'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Enable Stripe</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Publishable Key</label>
                        <input type="text" name="stripe_public_key" value="{{ old('stripe_public_key', $settings['stripe_public_key']) }}" placeholder="pk_test_..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                        <input type="password" name="stripe_secret_key" value="{{ old('stripe_secret_key', $settings['stripe_secret_key']) }}" placeholder="sk_test_..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                        <p class="mt-1 text-xs text-gray-500">Get your keys from <a href="https://dashboard.stripe.com/apikeys" target="_blank" class="text-blue-600 hover:underline">Stripe Dashboard</a></p>
                    </div>
                </div>
            </div>

            <!-- PayPal Settings -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">PayPal Configuration</h2>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_paypal" {{ $settings['enable_paypal'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Enable PayPal</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                        <input type="text" name="paypal_client_id" value="{{ old('paypal_client_id', $settings['paypal_client_id']) }}" placeholder="AXxxx..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Secret</label>
                        <input type="password" name="paypal_secret" value="{{ old('paypal_secret', $settings['paypal_secret']) }}" placeholder="EXxxx..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mode</label>
                        <select name="paypal_mode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="sandbox" {{ $settings['paypal_mode'] == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                            <option value="live" {{ $settings['paypal_mode'] == 'live' ? 'selected' : '' }}>Live (Production)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bank Transfer Settings -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Bank Transfer</h2>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_bank_transfer" {{ $settings['enable_bank_transfer'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Enable Bank Transfer</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Account Details</label>
                    <textarea name="bank_details" rows="6" placeholder="Bank Name:&#10;Account Name:&#10;Account Number:&#10;Routing Number:&#10;Swift Code:" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('bank_details', $settings['bank_details']) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">This information will be displayed to buyers when they select bank transfer</p>
                </div>
            </div>

            <!-- Commission Settings -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Commission Settings</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform Commission (%) *</label>
                        <input type="number" name="platform_commission_rate" value="{{ old('platform_commission_rate', $settings['platform_commission_rate']) }}" required min="0" max="100" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('platform_commission_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Percentage charged on each transaction</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Agent Commission (%) *</label>
                        <input type="number" name="agent_commission_rate" value="{{ old('agent_commission_rate', $settings['agent_commission_rate']) }}" required min="0" max="100" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('agent_commission_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Commission for referral agents</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Commission Amount *</label>
                        <input type="number" name="min_commission_amount" value="{{ old('min_commission_amount', $settings['min_commission_amount']) }}" required min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('min_commission_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Minimum commission to earn</p>
                    </div>
                </div>
            </div>

            <!-- Escrow Settings -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Escrow Settings</h2>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_escrow" {{ $settings['enable_escrow'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Enable Escrow</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Auto-Release Period (Days) *</label>
                    <input type="number" name="escrow_release_days" value="{{ old('escrow_release_days', $settings['escrow_release_days']) }}" required min="1" max="90" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('escrow_release_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Days before funds are automatically released to vendor</p>
                </div>
            </div>

            <!-- Tax Settings -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Tax Settings</h2>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_tax" {{ $settings['enable_tax'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Enable Tax</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax Name</label>
                        <input type="text" name="tax_name" value="{{ old('tax_name', $settings['tax_name']) }}" placeholder="VAT, GST, Sales Tax" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                        <input type="number" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate']) }}" min="0" max="100" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Important Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-semibold text-yellow-900 mb-1">Important</h3>
                        <ul class="text-sm text-yellow-800 list-disc list-inside space-y-1">
                            <li>Test all payment gateways thoroughly before going live</li>
                            <li>Commission changes only affect new transactions</li>
                            <li>Ensure your API keys are kept secure and never shared</li>
                            <li>Use sandbox/test mode for development environments</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.settings.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Save Payment Settings
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
