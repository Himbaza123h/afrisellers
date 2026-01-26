@extends('layouts.guest')

@section('title', 'Verify Your Email')

@section('content')
<div class="min-h-screen bg-white flex items-center justify-center px-6 py-12">
    <div class="max-w-2xl w-full">

        <!-- Logo -->
        <div class="mb-8 text-center">
            <img src="{{ asset('mainlogo.png') }}" alt="AfriSellers" class="h-10 mx-auto mb-8">
        </div>

        <!-- Icon -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-lg font-bold text-gray-900 mb-2">Verify your email</h1>
            <p class="text-gray-600">
                We've sent a 6-digit verification code to<br>
                <span class="font-medium text-gray-900">{{ $email }}</span>
            </p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p class="text-sm text-red-800">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Verification Form -->
        <form action="{{ route('buyer.verification.verify') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="token" class="block text-sm font-medium text-gray-900 mb-2">
                    Enter verification code
                </label>
                <input
                    type="text"
                    name="token"
                    id="token"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900 text-center text-2xl font-semibold tracking-widest"
                    placeholder="000000"
                    required
                    autofocus
                >
                <p class="mt-2 text-xs text-gray-500 text-center">Enter the 6-digit code from your email</p>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full px-8 py-3 bg-[#ff0808] rounded-lg text-white font-medium hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors"
            >
                Verify Email
            </button>
        </form>

        <!-- Resend Code -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 mb-2">
                Didn't receive the code?
            </p>
            <form action="{{ route('buyer.verification.resend') }}" method="POST" class="inline">
                @csrf
                <button
                    type="submit"
                    class="text-sm font-medium text-gray-900 underline hover:no-underline"
                >
                    Resend verification code
                </button>
            </form>
        </div>

        <!-- Back to Register -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <a href="{{ route('auth.register') }}" class="text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to registration
            </a>
        </div>
    </div>
</div>

<style>
    input:focus {
        --tw-ring-color: #111827;
    }

    /* Remove number input spinners */
    input[type="text"]::-webkit-inner-spin-button,
    input[type="text"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<script>
    // Auto-focus and auto-submit when 6 digits entered
    document.getElementById('token').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value.substring(0, 6);

        if (value.length === 6) {
            // Auto-submit form when 6 digits entered
            setTimeout(() => {
                e.target.form.submit();
            }, 500);
        }
    });

    // Only allow numbers
    document.getElementById('token').addEventListener('keypress', function(e) {
        if (!/[0-9]/.test(e.key)) {
            e.preventDefault();
        }
    });
</script>
@endsection
