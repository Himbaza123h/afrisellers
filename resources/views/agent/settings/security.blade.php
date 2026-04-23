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
            <h1 class="text-xl font-bold text-gray-900">Security Settings</h1>
            <p class="text-xs text-gray-500 mt-0.5">Manage your password and account security</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm text-red-900 font-medium flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Change Password --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2 mb-5">
            <i class="fas fa-lock text-amber-500"></i> Change Password
        </h2>

        @if($errors->has('current_password') || $errors->has('password'))
            <div class="mb-4 p-3 bg-red-50 rounded-lg border border-red-200">
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('agent.settings.update-password') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="current_password" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" required placeholder="Min. 8 characters"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Confirm New Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </div>
        </form>
    </div>

    {{-- Two-Factor --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                    {{ $settings->two_factor_enabled ? 'bg-green-100' : 'bg-gray-100' }}">
                    <i class="fas fa-shield-alt text-sm
                        {{ $settings->two_factor_enabled ? 'text-green-600' : 'text-gray-400' }}"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Two-Factor Authentication</p>
                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">
                        Add an extra layer of security to your account. When enabled, you'll need a code in addition to your password.
                    </p>
                    <span class="mt-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                        {{ $settings->two_factor_enabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        <i class="fas fa-circle text-[5px]"></i>
                        {{ $settings->two_factor_enabled ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
            </div>
            <form action="{{ route('agent.settings.toggle-two-factor') }}" method="POST" class="flex-shrink-0">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border transition-colors
                        {{ $settings->two_factor_enabled
                            ? 'border-red-200 text-red-600 bg-red-50 hover:bg-red-100'
                            : 'border-green-200 text-green-700 bg-green-50 hover:bg-green-100' }}">
                    <i class="fas {{ $settings->two_factor_enabled ? 'fa-times' : 'fa-check' }}"></i>
                    {{ $settings->two_factor_enabled ? 'Disable' : 'Enable' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Login Activity Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-400 mt-0.5 flex-shrink-0"></i>
        <p class="text-sm text-blue-800">
            If you suspect unauthorized access, change your password immediately and contact
            <a href="{{ route('agent.support.ticket.create') }}" class="underline font-semibold">support</a>.
        </p>
    </div>
</div>
@endsection
