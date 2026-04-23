@extends('layouts.home')

@section('page-content')
<div class="space-y-5 max-w-6xl mx-auto">

    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900">Settings</h1>
        <p class="mt-1 text-xs text-gray-500">Manage your account preferences</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach([
            [
                'route'   => 'agent.settings.general',
                'icon'    => 'fa-sliders-h',
                'color'   => 'blue',
                'label'   => 'General',
                'desc'    => 'Timezone, language, currency and date format',
            ],
            [
                'route'   => 'agent.settings.notifications',
                'icon'    => 'fa-bell',
                'color'   => 'amber',
                'label'   => 'Notifications',
                'desc'    => 'Control what alerts and emails you receive',
            ],
            [
                'route'   => 'agent.settings.security',
                'icon'    => 'fa-shield-alt',
                'color'   => 'green',
                'label'   => 'Security',
                'desc'    => 'Password and two-factor authentication',
            ],
            [
                'route'   => 'agent.settings.payment',
                'icon'    => 'fa-university',
                'color'   => 'purple',
                'label'   => 'Payment',
                'desc'    => 'Bank, mobile money or PayPal payout details',
            ],
            [
                'route'   => 'agent.settings.commission',
                'icon'    => 'fa-percent',
                'color'   => 'rose',
                'label'   => 'Commission',
                'desc'    => 'Payout threshold and frequency preferences',
            ],
        ] as $card)
        <a href="{{ route($card['route']) }}"
           class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:shadow-md hover:border-{{ $card['color'] }}-200 transition-all group">
            <div class="w-12 h-12 bg-{{ $card['color'] }}-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-{{ $card['color'] }}-200 transition-colors">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800 group-hover:text-{{ $card['color'] }}-700 transition-colors">
                    {{ $card['label'] }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $card['desc'] }}</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-{{ $card['color'] }}-400 transition-colors flex-shrink-0"></i>
        </a>
        @endforeach
    </div>
</div>
@endsection
