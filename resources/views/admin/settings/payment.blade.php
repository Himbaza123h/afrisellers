@extends('layouts.home')

@section('page-content')
<div class="space-y-6">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <i class="fas fa-circle-check text-green-500"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <i class="fas fa-circle-xmark text-red-500"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

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

    <form id="payment-form" action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="payment">

        <div class="space-y-6">

            <!-- Payment Gateway -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Default Payment Gateway</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Primary Gateway <span class="text-red-500">*</span></label>
                    <select name="payment_gateway" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="stripe"        {{ $settings['payment_gateway'] == 'stripe'        ? 'selected' : '' }}>Stripe</option>
                        <option value="paypal"        {{ $settings['payment_gateway'] == 'paypal'        ? 'selected' : '' }}>PayPal</option>
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
                        <input type="text" name="stripe_public_key"
                            value="{{ old('stripe_public_key', $settings['stripe_public_key']) }}"
                            placeholder="pk_test_..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                        <div class="relative max-w-full">
                            <input type="password" name="stripe_secret_key" id="stripe_secret_input"
                                value="{{ old('stripe_secret_key', $settings['stripe_secret_key']) }}"
                                placeholder="sk_test_..."
                                class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                            <button type="button" onclick="toggleVisibility('stripe_secret_input','stripe_secret_icon')"
                                class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">
                                <i id="stripe_secret_icon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
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
                        <input type="text" name="paypal_client_id"
                            value="{{ old('paypal_client_id', $settings['paypal_client_id']) }}"
                            placeholder="AXxxx..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Secret</label>
                        <div class="relative">
                            <input type="password" name="paypal_secret" id="paypal_secret_input"
                                value="{{ old('paypal_secret', $settings['paypal_secret']) }}"
                                placeholder="EXxxx..."
                                class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                            <button type="button" onclick="toggleVisibility('paypal_secret_input','paypal_secret_icon')"
                                class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">
                                <i id="paypal_secret_icon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mode</label>
                        <select name="paypal_mode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="sandbox" {{ $settings['paypal_mode'] == 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                            <option value="live"    {{ $settings['paypal_mode'] == 'live'    ? 'selected' : '' }}>Live (Production)</option>
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
                    <textarea name="bank_details" rows="6"
                        placeholder="Bank Name:&#10;Account Name:&#10;Account Number:&#10;Routing Number:&#10;Swift Code:"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('bank_details', $settings['bank_details']) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">This information will be displayed to buyers when they select bank transfer</p>
                </div>
            </div>

            <!-- Commission Settings -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Commission Settings</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform Commission (%) <span class="text-red-500">*</span></label>
                        <input type="number" name="platform_commission_rate"
                            value="{{ old('platform_commission_rate', $settings['platform_commission_rate']) }}"
                            required min="0" max="100" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('platform_commission_rate') border-red-500 @enderror">
                        @error('platform_commission_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Percentage charged on each transaction</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Agent Commission (%) <span class="text-red-500">*</span></label>
                        <input type="number" name="agent_commission_rate"
                            value="{{ old('agent_commission_rate', $settings['agent_commission_rate']) }}"
                            required min="0" max="100" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('agent_commission_rate') border-red-500 @enderror">
                        @error('agent_commission_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Commission for referral agents</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Commission Amount <span class="text-red-500">*</span></label>
                        <input type="number" name="min_commission_amount"
                            value="{{ old('min_commission_amount', $settings['min_commission_amount']) }}"
                            required min="0" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('min_commission_amount') border-red-500 @enderror">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Auto-Release Period (Days) <span class="text-red-500">*</span></label>
                    <input type="number" name="escrow_release_days"
                        value="{{ old('escrow_release_days', $settings['escrow_release_days']) }}"
                        required min="1" max="90"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('escrow_release_days') border-red-500 @enderror">
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
                        <input type="text" name="tax_name"
                            value="{{ old('tax_name', $settings['tax_name']) }}"
                            placeholder="VAT, GST, Sales Tax"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                        <input type="number" name="tax_rate"
                            value="{{ old('tax_rate', $settings['tax_rate']) }}"
                            min="0" max="100" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            {{-- ── Exchange Rates ──────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Exchange Rates</h2>
            <p class="text-xs text-gray-400 mt-0.5">Set how currencies are converted across the platform</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="enable_exchange_rates" id="enable_exchange_rates"
                   {{ $settings['enable_exchange_rates'] ? 'checked' : '' }} class="sr-only peer"
                   onchange="toggleExchangeSection()">
            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300
                        rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white
                        after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white
                        after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5
                        after:transition-all peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-700">Enable Exchange Rates</span>
        </label>
    </div>

    <div id="exchange-section" class="{{ $settings['enable_exchange_rates'] ? '' : 'hidden' }} space-y-6">

        {{-- Base Currency + Provider --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Base Currency</label>
                <select name="base_currency"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    @foreach([
                        'USD'=>'USD — US Dollar','EUR'=>'EUR — Euro','GBP'=>'GBP — British Pound',
                        'RWF'=>'RWF — Rwandan Franc','KES'=>'KES — Kenyan Shilling',
                        'NGN'=>'NGN — Nigerian Naira','GHS'=>'GHS — Ghanaian Cedi',
                        'ZAR'=>'ZAR — South African Rand','UGX'=>'UGX — Ugandan Shilling',
                        'TZS'=>'TZS — Tanzanian Shilling','ETB'=>'ETB — Ethiopian Birr',
                    ] as $code => $label)
                        <option value="{{ $code }}"
                                {{ $settings['base_currency'] === $code ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400">All rates are relative to this currency</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rate Provider</label>
                <select name="exchange_rate_provider" id="rate_provider"
                        onchange="toggleApiKey()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="manual"               {{ $settings['exchange_rate_provider']==='manual'               ?'selected':'' }}>Manual (enter rates below)</option>
                    <option value="openexchangerates"    {{ $settings['exchange_rate_provider']==='openexchangerates'    ?'selected':'' }}>Open Exchange Rates</option>
                    <option value="fixer"                {{ $settings['exchange_rate_provider']==='fixer'                ?'selected':'' }}>Fixer.io</option>
                    <option value="exchangerate_api"     {{ $settings['exchange_rate_provider']==='exchangerate_api'     ?'selected':'' }}>ExchangeRate-API</option>
                </select>
            </div>
        </div>

        {{-- API Key + Update Frequency (hidden when manual) --}}
        <div id="api-key-section"
             class="{{ $settings['exchange_rate_provider'] === 'manual' ? 'hidden' : '' }} grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                <div class="relative">
                    <input type="password" name="exchange_rate_api_key" id="fx_api_key_input"
                           value="{{ old('exchange_rate_api_key', $settings['exchange_rate_api_key']) }}"
                           placeholder="Your provider API key"
                           class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                    <button type="button"
                            onclick="toggleVisibility('fx_api_key_input','fx_api_key_icon')"
                            class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">
                        <i id="fx_api_key_icon" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-400">
                    Get your key from
                    <a href="https://openexchangerates.org" target="_blank" class="text-blue-600 hover:underline">Open Exchange Rates</a>,
                    <a href="https://fixer.io" target="_blank" class="text-blue-600 hover:underline">Fixer.io</a>, or
                    <a href="https://www.exchangerate-api.com" target="_blank" class="text-blue-600 hover:underline">ExchangeRate-API</a>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Auto-Update Frequency</label>
                <select name="exchange_rate_update_freq"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="hourly" {{ $settings['exchange_rate_update_freq']==='hourly' ?'selected':'' }}>Every Hour</option>
                    <option value="daily"  {{ $settings['exchange_rate_update_freq']==='daily'  ?'selected':'' }}>Daily</option>
                    <option value="weekly" {{ $settings['exchange_rate_update_freq']==='weekly' ?'selected':'' }}>Weekly</option>
                </select>
                <p class="mt-1 text-xs text-gray-400">Requires a Laravel scheduler running (<code class="bg-gray-100 px-1 rounded">schedule:run</code>)</p>
            </div>
        </div>

        {{-- Supported currencies --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Supported Currencies</label>
            <input type="text" name="supported_currencies"
                   value="{{ old('supported_currencies', $settings['supported_currencies']) }}"
                   placeholder="USD,EUR,GBP,RWF,KES,NGN"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-mono">
            <p class="mt-1 text-xs text-gray-400">Comma-separated ISO 4217 codes. These are the currencies buyers can switch to on the site.</p>
        </div>

        {{-- Manual rates (only shown when provider = manual) --}}
        <div id="manual-rates-section"
             class="{{ $settings['exchange_rate_provider'] !== 'manual' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700 mb-2">Manual Exchange Rates</label>
            <textarea name="manual_rates" rows="7"
                      placeholder="One rate per line, format: CURRENCY=RATE&#10;Example (1 USD = X):&#10;EUR=0.92&#10;GBP=0.79&#10;RWF=1310&#10;KES=129&#10;NGN=1550&#10;GHS=15.5&#10;ZAR=18.4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('manual_rates', $settings['manual_rates']) }}</textarea>
            <p class="mt-1 text-xs text-gray-400">
                Format: <code class="bg-gray-100 px-1 rounded">CURRENCY_CODE=RATE</code> — one per line.
                Rates are relative to your base currency above.
            </p>
        </div>

        {{-- Info banner --}}
        <div class="flex items-start gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <i class="fas fa-info-circle text-blue-500 mt-0.5 text-sm flex-shrink-0"></i>
            <p class="text-xs text-blue-800">
                Exchange rates are applied at checkout and on product listings when a buyer changes their display currency.
                Rates fetched from a provider are cached and refreshed on your selected schedule.
            </p>
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
                <a href="{{ route('admin.settings.index') }}"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </a>
                <button id="btn-submit" type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-opacity disabled:opacity-70">
                    <svg class="spinner animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display:none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span class="btn-text">Save Payment Settings</span>
                </button>
            </div>

</div>
    </form>


        {{-- ── Target Settings ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Performance Targets</h2>
            <p class="text-xs text-gray-400 mt-0.5">Set credit targets for agents. When an agent reaches the target in the period, they are automatically awarded the prize credits.</p>
        </div>

        {{-- Add New Target --}}
        <form action="{{ route('admin.settings.targets.store') }}" method="POST"
              class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Period Type <span class="text-red-500">*</span></label>
                <select name="target_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="monthly">Monthly</option>
                    <option value="weekly">Weekly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Target Credits <span class="text-red-500">*</span></label>
                <input type="number" name="target_amount" step="0.01" min="1" placeholder="e.g. 500"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <p class="mt-0.5 text-[10px] text-gray-400">Agent must earn this many credits in the period</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Prize Credits <span class="text-red-500">*</span></label>
                <input type="number" name="prize" step="0.01" min="0.01" placeholder="e.g. 50"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <p class="mt-0.5 text-[10px] text-gray-400">Bonus credits awarded when target is reached</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_at"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <p class="mt-0.5 text-[10px] text-gray-400">Leave blank = no expiry</p>
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i> Add Target
                </button>
            </div>
        </form>

        {{-- Existing Targets --}}
        @if(isset($targets) && $targets->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Period</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Target Credits</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Prize Credits</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">End Date</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($targets as $target)
                    <tr class="hover:bg-gray-50" id="target-row-{{ $target->id }}">
                        {{-- View mode --}}
                        <td class="px-4 py-3 view-target-{{ $target->id }}">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full capitalize">
                                <i class="fas fa-bullseye text-[9px]"></i>
                                {{ ucfirst($target->target_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-gray-900 view-target-{{ $target->id }}">
                            {{ number_format($target->target_amount, 2) }} credits
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-emerald-700 view-target-{{ $target->id }}">
                            +{{ number_format($target->prize, 2) }} credits
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 view-target-{{ $target->id }}">
                            {{ $target->end_at ? $target->end_at->format('M d, Y') : 'No expiry' }}
                        </td>
                        <td class="px-4 py-3 text-right view-target-{{ $target->id }}">
                            <button type="button" onclick="toggleEditTarget({{ $target->id }})"
                                    class="px-3 py-1 text-xs bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 font-medium">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <form action="{{ route('admin.settings.targets.destroy', $target->id) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Delete this target? Existing rewards will not be affected.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 font-medium ml-1">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </form>
                        </td>

                        {{-- Edit mode --}}
                        <td colspan="5" class="px-4 py-3 edit-target-{{ $target->id }} hidden">
                            <form action="{{ route('admin.settings.targets.update', $target->id) }}"
                                  method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                @csrf @method('PUT')
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Period</label>
                                    <select name="target_type" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="monthly" {{ $target->target_type === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="weekly"  {{ $target->target_type === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                                        <option value="yearly"  {{ $target->target_type === 'yearly'  ? 'selected' : '' }}>Yearly</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Target Credits</label>
                                    <input type="number" name="target_amount" step="0.01" value="{{ $target->target_amount }}"
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Prize Credits</label>
                                    <input type="number" name="prize" step="0.01" value="{{ $target->prize }}"
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">End Date</label>
                                    <input type="date" name="end_at" value="{{ $target->end_at?->format('Y-m-d') }}"
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="md:col-span-4 flex gap-2">
                                    <button type="submit"
                                            class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700">
                                        <i class="fas fa-save mr-1"></i> Save
                                    </button>
                                    <button type="button" onclick="toggleEditTarget({{ $target->id }})"
                                            class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-sm text-gray-400 text-center py-4">No targets yet. Add your first one above.</p>
        @endif
    </div>


    {{-- ── Credit Settings ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Credit Settings</h2>
            <p class="text-xs text-gray-400 mt-0.5">Manage credit types and the monetary value per credit</p>
        </div>

        {{-- Credit Value (multiplier) --}}
        <form action="{{ route('admin.settings.credit-value.update') }}" method="POST"
              class="flex items-end gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Monetary Value per Credit <span class="text-red-500">*</span>
                </label>
                <input type="number" name="value" step="0.01" min="0.01"
                       value="{{ $creditValue?->value ?? 100 }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                <p class="mt-1 text-xs text-gray-400">Example: if value = 15, then 1 credit = $15</p>
            </div>
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium whitespace-nowrap">
                <i class="fas fa-save mr-1"></i> Update Value
            </button>
        </form>

        {{-- Add New Credit Type --}}
        <form action="{{ route('admin.settings.credit-types.store') }}" method="POST"
              class="flex items-end gap-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type Name <span class="text-red-500">*</span></label>
                <input type="text" name="type" placeholder="e.g. registration_credit"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
            </div>
            <div class="w-36">
                <label class="block text-sm font-medium text-gray-700 mb-1">Credits Value <span class="text-red-500">*</span></label>
                <input type="number" name="value" step="0.01" min="0" placeholder="15"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
            </div>
            <button type="submit"
                    class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium whitespace-nowrap">
                <i class="fas fa-plus mr-1"></i> Add Type
            </button>
        </form>

        {{-- Existing Credit Types --}}
        @if($credits->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Credits Value</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Monetary Eq.</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($credits as $credit)
                    <tr class="hover:bg-gray-50" id="credit-row-{{ $credit->id }}">
                        <td class="px-4 py-3 text-xs text-gray-400 view-mode-{{ $credit->id }}">{{ str_pad($credit->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 view-mode-{{ $credit->id }}">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full capitalize">
                                <i class="fas fa-tag text-[9px]"></i>
                                {{ str_replace('_', ' ', $credit->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-gray-900 view-mode-{{ $credit->id }}">
                            {{ number_format($credit->value, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-emerald-700 view-mode-{{ $credit->id }}">
                            ${{ number_format($credit->value * ($creditValue?->value ?? 100), 2) }}
                        </td>
                        <td class="px-4 py-3 text-right view-mode-{{ $credit->id }}">
                            <button type="button" onclick="toggleEditCredit({{ $credit->id }})"
                                    class="px-3 py-1 text-xs bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 font-medium">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <form action="{{ route('admin.settings.credit-types.destroy', $credit->id) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Delete this credit type?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 font-medium ml-1">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </form>
                        </td>
                        <td colspan="5" class="px-4 py-3 edit-mode-{{ $credit->id }} hidden">
                            <form action="{{ route('admin.settings.credit-types.update', $credit->id) }}"
                                  method="POST" class="flex items-end gap-3">
                                @csrf @method('PUT')
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                                    <input type="text" name="type" value="{{ $credit->type }}"
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="w-32">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Value</label>
                                    <input type="number" name="value" step="0.01" value="{{ $credit->value }}"
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                </div>
                                <button type="submit"
                                        class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700">
                                    <i class="fas fa-save mr-1"></i> Save
                                </button>
                                <button type="button" onclick="toggleEditCredit({{ $credit->id }})"
                                        class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200">
                                    Cancel
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-sm text-gray-400 text-center py-4">No credit types yet. Add your first one above.</p>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('payment-form');
    const btn  = document.getElementById('btn-submit');

    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.querySelector('.spinner').style.display  = 'inline-block';
        btn.querySelector('.btn-text').style.display = 'none';
    });
});

function toggleVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function toggleExchangeSection() {
    const enabled = document.getElementById('enable_exchange_rates').checked;
    document.getElementById('exchange-section').classList.toggle('hidden', !enabled);
}

function toggleApiKey() {
    const provider = document.getElementById('rate_provider').value;
    const isManual = provider === 'manual';
    document.getElementById('api-key-section').classList.toggle('hidden', isManual);
    document.getElementById('manual-rates-section').classList.toggle('hidden', !isManual);
}
function toggleEditCredit(id) {
    document.querySelectorAll('.view-mode-' + id).forEach(el => el.classList.toggle('hidden'));
    document.querySelectorAll('.edit-mode-' + id).forEach(el => el.classList.toggle('hidden'));
}
function toggleEditTarget(id) {
    document.querySelectorAll('.view-target-' + id).forEach(el => el.classList.toggle('hidden'));
    document.querySelectorAll('.edit-target-' + id).forEach(el => el.classList.toggle('hidden'));
}
</script>

@endsection
