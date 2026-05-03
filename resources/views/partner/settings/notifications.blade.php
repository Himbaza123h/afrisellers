@extends('layouts.home')
@section('page-content')

@php
    $prefs = session('partner_notification_prefs_' . auth()->id(), []);
    $checked = fn($key) => ($prefs[$key] ?? true) ? 'checked' : '';
@endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.settings.index') }}" class="hover:text-gray-600">Settings</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Notifications</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Notification Preferences</h1>
        <p class="text-xs text-gray-500 mt-0.5">Control how you receive notifications</p>
    </div>
    <a href="{{ route('partner.settings.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-700 font-semibold">{{ session('success') }}</p>
    </div>
@endif

<form action="{{ route('partner.settings.update-notifications') }}" method="POST">
    @csrf

    {{-- Email notifications --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-4">
        <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-envelope text-blue-600 text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900">Email Notifications</p>
                <p class="text-xs text-gray-400">Sent to {{ auth()->user()->email }}</p>
            </div>
        </div>
        <div class="space-y-4">
            @foreach([
                ['key' => 'email_messages', 'label' => 'New Messages',       'desc' => 'When you receive a new message from our team'],
                ['key' => 'email_updates',  'label' => 'Platform Updates',   'desc' => 'Important updates about the partner program'],
                ['key' => 'email_support',  'label' => 'Support Replies',    'desc' => 'When your support ticket gets a response'],
            ] as $item)
            <label class="flex items-center justify-between cursor-pointer group">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $item['label'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                </div>
                <div class="relative flex-shrink-0 ml-4">
                    <input type="checkbox" name="{{ $item['key'] }}" value="1" {{ $checked($item['key']) }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-[#ff0808] peer-focus:ring-2 peer-focus:ring-red-300 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-5"></div>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Browser notifications --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-4">
        <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-bell text-amber-600 text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900">In-App Notifications</p>
                <p class="text-xs text-gray-400">Shown inside the dashboard</p>
            </div>
        </div>
        <div class="space-y-4">
            @foreach([
                ['key' => 'browser_messages', 'label' => 'New Messages',     'desc' => 'Badge on the messages menu item'],
                ['key' => 'browser_updates',  'label' => 'System Updates',   'desc' => 'Announcements and news in your notification feed'],
            ] as $item)
            <label class="flex items-center justify-between cursor-pointer group">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $item['label'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                </div>
                <div class="relative flex-shrink-0 ml-4">
                    <input type="checkbox" name="{{ $item['key'] }}" value="1" {{ $checked($item['key']) }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-[#ff0808] peer-focus:ring-2 peer-focus:ring-red-300 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-5"></div>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    <div class="flex items-center justify-end">
        <button type="submit"
                class="px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-save"></i> Save Preferences
        </button>
    </div>
</form>
@endsection
