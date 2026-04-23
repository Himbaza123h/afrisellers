@extends('layouts.home')

@section('page-content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.settings.index') }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Payment Settings</h1>
            <p class="text-xs text-gray-500 mt-0.5">Where to send your payout</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('agent.settings.update-payment') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Method Select --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-gray-800 mb-4">Payout Method</h2>
            <div class="grid grid-cols-3 gap-3">
                @foreach([
                    ['bank',         'fa-university',   'Bank Transfer'],
                    ['mobile_money', 'fa-mobile-alt',   'Mobile Money'],
                    ['paypal',       'fa-paypal',       'PayPal'],
                ] as [$val, $icon, $label])
                <label class="relative cursor-pointer">
                    <input type="radio" name="payout_method" value="{{ $val }}"
                           {{ $settings->payout_method === $val ? 'checked' : '' }}
                           class="sr-only peer" onchange="showSection('{{ $val }}')">
                    <div class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200
                                peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300
                                transition-all text-center">
                        <i class="fas {{ $icon }} text-xl text-gray-400 peer-checked:text-blue-600"></i>
                        <span class="text-xs font-semibold text-gray-600">{{ $label }}</span>
                    </div>
                </label>
                @endforeach
            </div>
            @error('payout_method')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Bank Transfer --}}
        <div id="section-bank"
             class="{{ $settings->payout_method === 'bank' ? '' : 'hidden' }} bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-university text-blue-500"></i> Bank Details
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Bank Name</label>
                    <input type="text" name="bank_name"
                        value="{{ old('bank_name', $settings->bank_name) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Account Number</label>
                    <input type="text" name="bank_account_number"
                        value="{{ old('bank_account_number', $settings->bank_account_number) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Account Name</label>
                    <input type="text" name="bank_account_name"
                        value="{{ old('bank_account_name', $settings->bank_account_name) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Branch / SWIFT / IBAN</label>
                    <input type="text" name="bank_branch"
                        value="{{ old('bank_branch', $settings->bank_branch) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Mobile Money --}}
        <div id="section-mobile_money"
             class="{{ $settings->payout_method === 'mobile_money' ? '' : 'hidden' }} bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-mobile-alt text-green-500"></i> Mobile Money Details
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Provider</label>
                    <select name="mobile_money_provider"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Select provider</option>
                        @foreach(['MTN Mobile Money','Airtel Money','M-Pesa','Orange Money','Tigo Pesa','Vodacom M-Pesa'] as $provider)
                            <option value="{{ $provider }}"
                                {{ old('mobile_money_provider', $settings->mobile_money_provider) === $provider ? 'selected' : '' }}>
                                {{ $provider }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Phone Number</label>
                    <input type="text" name="mobile_money_number" placeholder="+250 7XX XXX XXX"
                        value="{{ old('mobile_money_number', $settings->mobile_money_number) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- PayPal --}}
        <div id="section-paypal"
             class="{{ $settings->payout_method === 'paypal' ? '' : 'hidden' }} bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="fab fa-paypal text-blue-600"></i> PayPal Details
            </h2>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">PayPal Email</label>
                <input type="email" name="paypal_email" placeholder="you@example.com"
                    value="{{ old('paypal_email', $settings->paypal_email) }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agent.settings.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                <i class="fas fa-save"></i> Save Payment Info
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function showSection(method) {
    ['bank','mobile_money','paypal'].forEach(m => {
        document.getElementById('section-' + m)?.classList.add('hidden');
    });
    document.getElementById('section-' + method)?.classList.remove('hidden');
}

// Init on page load
document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name="payout_method"]:checked');
    if (checked) showSection(checked.value);
});
</script>
@endpush
