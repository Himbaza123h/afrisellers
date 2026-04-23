@extends('layouts.guest')

@section('title', __('messages.verify_email_title'))

@section('content')
<div class="min-h-screen bg-white flex items-center justify-center">
    <div class="max-w-lg mx-auto px-6 py-12">

        <!-- Success Icon -->
        <div class="flex justify-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-lg font-bold text-gray-900 mb-3">
                {{ __('messages.check_your_email') }}
            </h1>
            <p class="text-lg text-gray-600">
                {{ __('messages.verification_email_sent') }}
            </p>
        </div>

        <!-- Email Display -->
        @if($email)
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-8">
            <p class="text-sm text-gray-600 mb-1">{{ __('messages.email_sent_to') }}</p>
            <p class="text-base font-medium text-gray-900">{{ $email }}</p>
        </div>
        @endif

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Manual OTP Entry Form -->
        <div class="bg-white border-2 border-gray-200 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('messages.enter_verification_code') }}
            </h3>
            <p class="text-sm text-gray-600 mb-6">
                {{ __('messages.enter_6_digit_code') }}
            </p>

            <form action="{{ route('vendor.verify.otp') }}" method="POST">
                @csrf

                <!-- OTP Input -->
                <div class="mb-6">
                    <label for="otp" class="block text-sm font-medium text-gray-900 mb-2">
                        {{ __('messages.verification_code') }}
                    </label>
                    <input
                        type="text"
                        name="otp"
                        id="otp"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        value="{{ old('otp') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900 text-center text-2xl tracking-widest font-mono"
                        placeholder="000000"
                        required
                        autocomplete="off"
                    >
                    <p class="mt-2 text-xs text-gray-500">{{ __('messages.code_format_hint') }}</p>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full px-6 py-3 bg-[#ff0808] text-white rounded-lg font-medium hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors"
                >
                    {{ __('messages.verify_code') }}
                </button>
            </form>
        </div>

        <!-- Divider -->
        <div class="flex items-center gap-4 my-8">
            <div class="flex-1 border-t border-gray-200"></div>
            <span class="text-sm text-gray-500">{{ __('messages.or') }}</span>
            <div class="flex-1 border-t border-gray-200"></div>
        </div>

        <!-- Instructions -->
        <div class="space-y-4 mb-8">
            <h3 class="text-sm font-semibold text-gray-900">
                {{ __('messages.email_verification_steps') }}
            </h3>

            <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-gray-900 text-white rounded-full flex items-center justify-center flex-shrink-0 text-sm font-medium mt-0.5">
                    1
                </div>
                <p class="text-gray-700">
                    {{ __('messages.verification_step_1') }}
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-gray-900 text-white rounded-full flex items-center justify-center flex-shrink-0 text-sm font-medium mt-0.5">
                    2
                </div>
                <p class="text-gray-700">
                    {{ __('messages.verification_step_2') }}
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-6 h-6 bg-gray-900 text-white rounded-full flex items-center justify-center flex-shrink-0 text-sm font-medium mt-0.5">
                    3
                </div>
                <p class="text-gray-700">
                    {{ __('messages.verification_step_3') }}
                </p>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-8"></div>

        <!-- Help Section -->
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-gray-900">
                {{ __('messages.didnt_receive_email') }}
            </h3>

            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    {{ __('messages.check_spam_folder') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    {{ __('messages.check_email_correct') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    {{ __('messages.wait_few_minutes') }}
                </li>
            </ul>

            <!-- Resend Button -->
            <div class="pt-4">
                <form action="{{ route('vendor.resend.verification') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="w-full px-6 py-3 bg-gray-100 text-gray-900 rounded-lg font-medium hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors"
                    >
                        {{ __('messages.resend_verification_email') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="mt-8 pt-8 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-600">
                {{ __('messages.need_help') }}
                <a href="mailto:support@afrisellers.com" class="font-medium text-gray-900 underline hover:no-underline">
                    {{ __('messages.contact_support') }}
                </a>
            </p>
        </div>

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('messages.back_to_home') }}
            </a>
        </div>

    </div>
</div>

<style>
    input:focus, select:focus {
        --tw-ring-color: #111827;
    }

    /* Remove spinner from number input */
    input[type="text"]#otp::-webkit-outer-spin-button,
    input[type="text"]#otp::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<script>
    // Auto-format OTP input (only allow numbers)
    document.getElementById('otp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endsection
