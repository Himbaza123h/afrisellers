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
            <h1 class="text-xl font-bold text-gray-900">Commission Settings</h1>
            <p class="text-xs text-gray-500 mt-0.5">Payout threshold and frequency preferences</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-2"></i>
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

    <form action="{{ route('agent.settings.update-commission') }}" method="POST" class="space-y-5">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">

            {{-- Payout Threshold --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Minimum Payout Threshold <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold mt-2">$</span>
                    <input type="number" name="commission_payout_threshold" min="1" step="1"
                        value="{{ old('commission_payout_threshold', $settings->commission_payout_threshold) }}"
                        class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="mt-1 text-xs text-gray-400">
                    You'll only receive a payout when your balance reaches this amount.
                </p>
                @error('commission_payout_threshold')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Payout Frequency --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                    Payout Frequency <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach([
                        ['weekly',    'fa-calendar-week',  'Weekly',    'Every Monday'],
                        ['biweekly',  'fa-calendar-alt',   'Bi-Weekly', 'Every 2 weeks'],
                        ['monthly',   'fa-calendar',       'Monthly',   '1st of month'],
                    ] as [$val, $icon, $label, $sub])
                    <label class="relative cursor-pointer">
                        <input type="radio" name="commission_payout_frequency" value="{{ $val }}"
                               {{ old('commission_payout_frequency', $settings->commission_payout_frequency) === $val ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="flex flex-col items-center gap-1.5 p-4 rounded-xl border-2 border-gray-200
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50
                                    hover:border-gray-300 transition-all text-center cursor-pointer">
                            <i class="fas {{ $icon }} text-lg text-gray-400 peer-checked:text-blue-600"></i>
                            <span class="text-xs font-bold text-gray-700">{{ $label }}</span>
                            <span class="text-[10px] text-gray-400">{{ $sub }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('commission_payout_frequency')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
            <i class="fas fa-info-circle text-amber-500 mt-0.5 flex-shrink-0"></i>
            <p class="text-sm text-amber-800">
                These are your <strong>preferences</strong>. Final payout scheduling is subject to admin approval and your payment method being configured in
                <a href="{{ route('agent.settings.payment') }}" class="underline font-semibold">Payment Settings</a>.
            </p>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agent.settings.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                <i class="fas fa-save"></i> Save Commission Settings
            </button>
        </div>
    </form>
</div>
@endsection
