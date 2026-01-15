@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.commissions.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Commission Settings</h1>
            </div>
            <p class="text-sm text-gray-500">Configure commission rates and rules</p>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-900 mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm text-red-800 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
            </div>
        </div>
    @endif

    <!-- Settings Form -->
    <form action="{{ route('admin.commissions.update-settings') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Commission Rates -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900">Commission Rates</h2>
                <p class="text-sm text-gray-500 mt-1">Set the default commission rates for different types</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Vendor Sale Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-store text-purple-600 mr-2"></i>
                        Vendor Sale Commission
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="vendor_sale_rate"
                            step="0.01"
                            min="0"
                            max="100"
                            value="{{ old('vendor_sale_rate', $settings['vendor_sale_rate']) }}"
                            class="w-full pl-4 pr-12 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Commission rate for vendor sales</p>
                </div>

                <!-- Referral Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users text-indigo-600 mr-2"></i>
                        Referral Commission
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="referral_rate"
                            step="0.01"
                            min="0"
                            max="100"
                            value="{{ old('referral_rate', $settings['referral_rate']) }}"
                            class="w-full pl-4 pr-12 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Commission rate for referrals</p>
                </div>

                <!-- Regional Admin Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-shield text-cyan-600 mr-2"></i>
                        Regional Admin Commission
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="regional_admin_rate"
                            step="0.01"
                            min="0"
                            max="100"
                            value="{{ old('regional_admin_rate', $settings['regional_admin_rate']) }}"
                            class="w-full pl-4 pr-12 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Commission rate for regional admins</p>
                </div>

                <!-- Platform Fee Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-percentage text-teal-600 mr-2"></i>
                        Platform Fee
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="platform_fee_rate"
                            step="0.01"
                            min="0"
                            max="100"
                            value="{{ old('platform_fee_rate', $settings['platform_fee_rate']) }}"
                            class="w-full pl-4 pr-12 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Platform fee percentage</p>
                </div>
            </div>
        </div>

        <!-- Commission Rules -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900">Commission Rules</h2>
                <p class="text-sm text-gray-500 mt-1">Configure additional commission rules and settings</p>
            </div>

            <div class="space-y-6">
                <!-- Auto Approve -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-900 mb-1">Auto-Approve Commissions</label>
                        <p class="text-xs text-gray-500">Automatically approve commissions without manual review</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="auto_approve" class="sr-only peer" {{ old('auto_approve', false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Minimum Payout -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                        Minimum Payout Amount
                    </label>
                    <div class="relative max-w-md">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">$</span>
                        <input
                            type="number"
                            name="minimum_payout"
                            step="0.01"
                            min="0"
                            value="{{ old('minimum_payout', 50.00) }}"
                            class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimum amount required for commission payout</p>
                </div>

                <!-- Payment Schedule -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
                        Payment Schedule
                    </label>
                    <select name="payment_schedule" class="max-w-md pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="weekly" {{ old('payment_schedule', 'monthly') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="biweekly" {{ old('payment_schedule', 'monthly') == 'biweekly' ? 'selected' : '' }}>Bi-weekly</option>
                        <option value="monthly" {{ old('payment_schedule', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="manual" {{ old('payment_schedule', 'monthly') == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">How often commissions are paid out</p>
                </div>
            </div>
        </div>

        <!-- Preview Calculations -->
        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl border border-blue-200 p-6">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-gray-900">Preview Calculations</h2>
                <p class="text-sm text-gray-500 mt-1">Example calculations based on current rates</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-2">On a $1,000 sale:</p>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Vendor Sale:</span>
                            <span class="font-bold text-purple-600">${{ number_format(1000 * ($settings['vendor_sale_rate'] / 100), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Referral:</span>
                            <span class="font-bold text-indigo-600">${{ number_format(1000 * ($settings['referral_rate'] / 100), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Regional Admin:</span>
                            <span class="font-bold text-cyan-600">${{ number_format(1000 * ($settings['regional_admin_rate'] / 100), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Platform Fee:</span>
                            <span class="font-bold text-teal-600">${{ number_format(1000 * ($settings['platform_fee_rate'] / 100), 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-2">On a $5,000 sale:</p>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Vendor Sale:</span>
                            <span class="font-bold text-purple-600">${{ number_format(5000 * ($settings['vendor_sale_rate'] / 100), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Referral:</span>
                            <span class="font-bold text-indigo-600">${{ number_format(5000 * ($settings['referral_rate'] / 100), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Regional Admin:</span>
                            <span class="font-bold text-cyan-600">${{ number_format(5000 * ($settings['regional_admin_rate'] / 100), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Platform Fee:</span>
                            <span class="font-bold text-teal-600">${{ number_format(5000 * ($settings['platform_fee_rate'] / 100), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.commissions.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
