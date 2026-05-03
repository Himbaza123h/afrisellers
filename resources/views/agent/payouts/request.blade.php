@extends('layouts.home')

@push('styles')
<style>
    .method-card { cursor: pointer; transition: all 0.2s; }
    .method-card:hover { border-color: #0891b2; background: #f0fdfa; }
    .method-card.selected { border-color: #0891b2; background: #ecfeff; box-shadow: 0 0 0 3px rgba(8,145,178,0.15); }
    .method-fields { display: none; }
    .method-fields.active { display: block; }
</style>
@endpush

@section('page-content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.payouts.index') }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Request Payout</h1>
            <p class="text-xs text-gray-500 mt-0.5">Withdraw your available earnings</p>
        </div>
    </div>

    {{-- Available Balance --}}
    <div class="bg-teal-600 rounded-xl p-5 text-white shadow-md">
        <p class="text-xs font-semibold text-cyan-100 uppercase tracking-wider mb-1">Available Balance</p>
        <p class="text-3xl font-bold">${{ number_format($available, 2) }}</p>
        <p class="text-xs text-cyan-100 mt-1">
            {{ number_format($totalCredits, 2) }} credits
            × ${{ number_format($multiplier, 2) }} per credit
        </p>
        <p class="text-xs text-cyan-200 mt-0.5">Maximum amount you can request</p>
    </div>

    @if($available <= 0)
        <div class="p-5 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 flex-shrink-0 text-lg"></i>
            <div>
                <p class="text-sm font-bold text-amber-900 mb-1">No balance available</p>
                <p class="text-sm text-amber-700">
                    You don't have any funds available for withdrawal right now.
                    Earnings from your vendors appear here once commissions are marked as paid.
                </p>
                <a href="{{ route('agent.earnings.index') }}"
                   class="inline-flex items-center gap-1 mt-3 text-sm font-semibold text-amber-700 hover:underline">
                    <i class="fas fa-dollar-sign text-xs"></i> View Earnings
                </a>
            </div>
        </div>
    @else

    {{-- Errors --}}
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5 flex-shrink-0"></i>
            <div>
                <p class="text-sm font-semibold text-red-900 mb-1">Please fix the following errors:</p>
                <ul class="text-sm text-red-700 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('agent.payouts.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Amount --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
                <i class="fas fa-dollar-sign text-cyan-600"></i>
                Withdrawal Amount
            </h2>
            <div class="max-w-sm">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Amount (USD) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold mt-3">$</span>
                    <input type="number" name="amount" id="amountInput"
                        value="{{ old('amount') }}"
                        min="1" max="{{ $available }}" step="0.01" required
                        class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                        placeholder="0.00">
                </div>
                <div class="flex gap-2 mt-2">
                    @foreach([25, 50, 75, 100] as $pct)
                        <button type="button"
                            onclick="setPercent({{ $pct }})"
                            class="px-3 py-1 text-xs font-semibold border border-gray-300 rounded-lg hover:bg-cyan-50 hover:border-cyan-400 hover:text-cyan-700 transition-colors">
                            {{ $pct }}%
                        </button>
                    @endforeach
                </div>
                <p class="mt-1.5 text-xs text-gray-400">
                    Max: <span class="font-semibold text-gray-600">${{ number_format($available, 2) }}</span>
                </p>
                @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Payment Method --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
                <i class="fas fa-credit-card text-cyan-600"></i>
                Payment Method
            </h2>

            <input type="hidden" name="payment_method" id="paymentMethodInput" value="{{ old('payment_method') }}">

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
                @php
                    $methods = [
                        'bank_transfer' => ['fa-university',   'Bank Transfer'],
                        'mobile_money'  => ['fa-mobile-alt',   'Mobile Money'],
                        'paypal'        => ['fa-paypal',       'PayPal'],
                        'wise'          => ['fa-exchange-alt', 'Wise'],
                        'crypto'        => ['fa-coins',        'Crypto'],
                    ];
                @endphp
                @foreach($methods as $key => [$icon, $label])
                    <div class="method-card border-2 border-gray-200 rounded-xl p-3 text-center {{ old('payment_method') == $key ? 'selected' : '' }}"
                         data-method="{{ $key }}"
                         onclick="selectMethod('{{ $key }}')">
                        <i class="fas {{ $icon }} text-2xl text-gray-400 mb-2 block"></i>
                        <p class="text-xs font-semibold text-gray-700">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Bank Transfer Fields --}}
            <div id="fields-bank_transfer" class="method-fields {{ old('payment_method') == 'bank_transfer' ? 'active' : '' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Bank Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500"
                            placeholder="e.g. Bank of Kigali">
                        @error('bank_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Account Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="account_name" value="{{ old('account_name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500"
                            placeholder="Account holder name">
                        @error('account_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Account Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="account_number" value="{{ old('account_number') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500"
                            placeholder="IBAN or account number">
                        @error('account_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">SWIFT / BIC Code</label>
                        <input type="text" name="swift_code" value="{{ old('swift_code') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500"
                            placeholder="e.g. BKRWRWRW">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Routing Number (if applicable)</label>
                        <input type="text" name="routing_number" value="{{ old('routing_number') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500"
                            placeholder="US routing number">
                    </div>
                </div>
            </div>

            {{-- Mobile Money Fields --}}
            <div id="fields-mobile_money" class="method-fields {{ old('payment_method') == 'mobile_money' ? 'active' : '' }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Network <span class="text-red-500">*</span>
                        </label>
                        <select name="mobile_network"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500">
                            <option value="">Select network</option>
                            @foreach(['MTN Mobile Money','Airtel Money','M-Pesa','Orange Money','Tigo Cash','Wave','Other'] as $net)
                                <option value="{{ $net }}" {{ old('mobile_network') == $net ? 'selected' : '' }}>{{ $net }}</option>
                            @endforeach
                        </select>
                        @error('mobile_network')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Account Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="account_name" value="{{ old('account_name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500"
                            placeholder="Registered name">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Mobile Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="mobile_number" value="{{ old('mobile_number') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500"
                            placeholder="+250 78x xxx xxx">
                        @error('mobile_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- PayPal Fields --}}
            <div id="fields-paypal" class="method-fields {{ old('payment_method') == 'paypal' ? 'active' : '' }}">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        PayPal Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="paypal_email" value="{{ old('paypal_email') }}"
                        class="w-full max-w-sm px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500"
                        placeholder="your@paypal.com">
                    @error('paypal_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Wise Fields --}}
            <div id="fields-wise" class="method-fields {{ old('payment_method') == 'wise' ? 'active' : '' }}">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Wise Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="wise_email" value="{{ old('wise_email') }}"
                        class="w-full max-w-sm px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500"
                        placeholder="your@wise.com">
                    @error('wise_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Crypto Fields --}}
            <div id="fields-crypto" class="method-fields {{ old('payment_method') == 'crypto' ? 'active' : '' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Network <span class="text-red-500">*</span>
                        </label>
                        <select name="crypto_network"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500">
                            <option value="">Select network</option>
                            @foreach(['Bitcoin (BTC)','Ethereum (ETH)','USDT (TRC20)','USDT (ERC20)','BNB (BEP20)','USDC'] as $net)
                                <option value="{{ $net }}" {{ old('crypto_network') == $net ? 'selected' : '' }}>{{ $net }}</option>
                            @endforeach
                        </select>
                        @error('crypto_network')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Wallet Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="crypto_address" value="{{ old('crypto_address') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 font-mono text-xs"
                            placeholder="0x… or bc1…">
                        @error('crypto_address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            @error('payment_method')
                <p class="mt-2 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-sticky-note text-cyan-600"></i>
                Notes <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
            </h2>
            <textarea name="notes" rows="3"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500"
                placeholder="Any additional information for the finance team…">{{ old('notes') }}</textarea>
        </div>

        {{-- Notice --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Processing time</p>
                <p>Payout requests are reviewed within 1–2 business days and processed within 3–5 business days after approval. You will be notified of status changes.</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('agent.payouts.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                <i class="fas fa-paper-plane"></i> Submit Request
            </button>
        </div>
    </form>

    @endif
</div>
@endsection

@push('scripts')
<script>
const available = {{ $available }};

function setPercent(pct) {
    document.getElementById('amountInput').value = (available * pct / 100).toFixed(2);
}

function selectMethod(method) {
    // Update hidden input
    document.getElementById('paymentMethodInput').value = method;

    // Update card UI
    document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
    document.querySelector(`[data-method="${method}"]`).classList.add('selected');

    // Show correct fields
    document.querySelectorAll('.method-fields').forEach(f => f.classList.remove('active'));
    const fields = document.getElementById('fields-' + method);
    if (fields) fields.classList.add('active');
}

// Restore selection on validation error
document.addEventListener('DOMContentLoaded', () => {
    const saved = document.getElementById('paymentMethodInput').value;
    if (saved) selectMethod(saved);
});
</script>
@endpush
