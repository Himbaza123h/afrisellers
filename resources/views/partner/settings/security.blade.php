@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.settings.index') }}" class="hover:text-gray-600">Settings</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Security</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Security Settings</h1>
        <p class="text-xs text-gray-500 mt-0.5">Manage your password and account security</p>
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

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('partner.settings.update-password') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider pb-2 border-b border-gray-100">Change Password</h2>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Current Password <span class="text-red-500">*</span></label>
            <input type="password" name="current_password" required
                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                   placeholder="Your current password">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">New Password <span class="text-red-500">*</span></label>
            <input type="password" name="password" required
                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                   placeholder="Minimum 8 characters">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Confirm New Password <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                   placeholder="Repeat new password">
        </div>

        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
            <p class="text-xs font-semibold text-gray-600 mb-2">Password requirements:</p>
            <ul class="text-xs text-gray-500 space-y-1">
                <li><i class="fas fa-circle text-[6px] mr-2 text-gray-300"></i>At least 8 characters</li>
                <li><i class="fas fa-circle text-[6px] mr-2 text-gray-300"></i>Mix of uppercase and lowercase letters</li>
                <li><i class="fas fa-circle text-[6px] mr-2 text-gray-300"></i>At least one number</li>
            </ul>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-end gap-3">
        <a href="{{ route('partner.settings.index') }}"
           class="px-5 py-2.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</a>
        <button type="submit"
                class="px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-lock"></i> Update Password
        </button>
    </div>
</form>
@endsection
