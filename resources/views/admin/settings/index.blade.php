@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your platform configuration and preferences</p>
        </div>
    </div>

    <!-- Settings Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- General Settings -->
        <a href="{{ route('admin.settings.general') }}" class="group bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md hover:border-blue-300 transition">
            <div class="flex items-start gap-4">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition">
                    <i class="fas fa-cog text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">General Settings</h3>
                    <p class="text-sm text-gray-500">Configure site name, logo, timezone, currency and other basic settings</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Site: {{ $settings['site_name'] }}</span>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition"></i>
                </div>
            </div>
        </a>

        <!-- Email Settings -->
        <a href="{{ route('admin.settings.email') }}" class="group bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md hover:border-purple-300 transition">
            <div class="flex items-start gap-4">
                <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition">
                    <i class="fas fa-envelope text-purple-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Email Settings</h3>
                    <p class="text-sm text-gray-500">Configure SMTP server, email credentials and sending preferences</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">From: {{ $settings['site_email'] }}</span>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600 group-hover:translate-x-1 transition"></i>
                </div>
            </div>
        </a>

        <!-- Payment Settings -->
        <a href="{{ route('admin.settings.payment') }}" class="group bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md hover:border-green-300 transition">
            <div class="flex items-start gap-4">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg group-hover:bg-green-200 transition">
                    <i class="fas fa-credit-card text-green-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Payment Settings</h3>
                    <p class="text-sm text-gray-500">Manage payment gateways, commission rates and escrow settings</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Currency: {{ $settings['currency'] }}</span>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-600 group-hover:translate-x-1 transition"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">System Overview</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Timezone</p>
                <p class="text-lg font-semibold text-gray-900">{{ $settings['timezone'] }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Currency</p>
                <p class="text-lg font-semibold text-gray-900">{{ $settings['currency_symbol'] }} {{ $settings['currency'] }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Site Email</p>
                <p class="text-lg font-semibold text-gray-900 truncate">{{ $settings['site_email'] }}</p>
            </div>
        </div>
    </div>

    <!-- Important Notice -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <div class="flex gap-3">
            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
            <div>
                <h3 class="text-sm font-semibold text-yellow-900 mb-1">Important Notice</h3>
                <p class="text-sm text-yellow-800">Changes to system settings will affect the entire platform. Make sure you understand the implications before making changes.</p>
            </div>
        </div>
    </div>
</div>
@endsection
