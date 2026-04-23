<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - AfriSellers</title>
    <link rel="icon" href="{{ asset('logofavicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6">

            <!-- Header -->
            <div class="text-center space-y-3 pt-20 mt-10">
                <a href="{{ route('home') }}" class="inline-block mb-2">
                    <img src="{{ asset('mainlogo.png') }}" alt="AfriSellers" class="h-10 w-auto mx-auto">
                </a>
                <h2 class="text-xl font-bold text-gray-900">Forgot your password?</h2>
                <p class="text-sm text-gray-600">
                    No worries! Enter your email and we'll send you a reset link.
                </p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <ul class="text-xs text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            @if (!session('success'))
                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-900 mb-1.5">
                            Email Address
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            autofocus
                            value="{{ old('email') }}"
                            class="block w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm @error('email') border-red-500 @enderror"
                            placeholder="Enter your registered email">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            id="submitBtn"
                            class="w-full py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-[#ff0808] hover:bg-[#dd0606] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff0808] transition-all shadow-sm disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="btnText">Send Reset Link</span>
                            <span id="btnLoader" class="hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Sending...
                            </span>
                        </button>
                    </div>
                </form>
            @endif

            <!-- Back to Login -->
            <div class="text-center pt-2">
                <a href="{{ route('auth.signin') }}" class="inline-flex items-center text-xs text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Sign In
                </a>
            </div>

        </div>
    </div>

    <script>
        document.querySelector('form')?.addEventListener('submit', function () {
            document.getElementById('btnText').classList.add('hidden');
            document.getElementById('btnLoader').classList.remove('hidden');
            document.getElementById('submitBtn').disabled = true;
        });
    </script>
</body>
</html>