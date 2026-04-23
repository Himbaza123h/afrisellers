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
            <h1 class="text-xl font-bold text-gray-900">Notification Settings</h1>
            <p class="text-xs text-gray-500 mt-0.5">Choose what you want to be notified about</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <form action="{{ route('agent.settings.update-notifications') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Email Notifications</p>
            </div>

            @foreach([
                ['notify_email',        'fa-envelope',   'text-blue-500',   'All Email Notifications',   'Master toggle — disabling this will suppress all emails'],
                ['notify_new_vendor',   'fa-user-plus',  'text-purple-500', 'New Vendor Onboarded',      'Get notified when a vendor you referred activates their account'],
                ['notify_commission',   'fa-dollar-sign','text-green-500',  'Commission Earned',          'Get notified when you earn a new commission'],
                ['notify_ticket_reply', 'fa-ticket-alt', 'text-amber-500',  'Support Ticket Reply',       'Get notified when support replies to your ticket'],
                ['notify_payout',       'fa-money-bill', 'text-teal-500',   'Payout Processed',           'Get notified when a payout is sent to you'],
                ['notify_expiry',       'fa-clock',      'text-red-500',    'Document Expiry Alerts',     'Get notified 30 days before a document expires'],
            ] as [$name, $icon, $iconCls, $label, $desc])
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $icon }} {{ $iconCls }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $label }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $desc }}</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 ml-4">
                    <input type="checkbox" name="{{ $name }}" value="1"
                           {{ $settings->$name ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300
                                rounded-full peer peer-checked:after:translate-x-5
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:bg-blue-600"></div>
                </label>
            </div>
            @endforeach
        </div>

        <div class="flex items-center justify-end gap-3 mt-5">
            <a href="{{ route('agent.settings.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                <i class="fas fa-save"></i> Save Preferences
            </button>
        </div>
    </form>
</div>
@endsection
