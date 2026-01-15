<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.vendor_login_title') }} - AfriSellers</title>

    <!-- Favicon -->
    <link rel="icon" href="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png" type="image/png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6">

            <!-- Header -->
            <div class="text-center space-y-3 pt-20 mt-10">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="inline-block mb-2">
                    <img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png"
                         alt="AfriSellers"
                         class="h-10 w-auto mx-auto">
                </a>

                <!-- Welcome Text -->
                <p class="text-sm text-gray-600">
                    {{ __('messages.welcome_back') }} 👋
                </p>
            </div>

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

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <p class="text-xs text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('auth.signin.submit') }}" class="space-y-4">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-1.5">
                        {{ __('messages.email') }}
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        autofocus
                        value="{{ old('email') }}"
                        class="block w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm @error('email') border-red-500 @enderror"
                        placeholder="E.g. johndoe@email.com">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-gray-900">
                            {{ __('messages.password') }}
                        </label>
                        <a href="#" class="text-xs font-medium text-gray-600 hover:text-[#ff0808] transition-colors">
                            {{ __('messages.forgot_password') }}?
                        </a>
                    </div>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="block w-full px-3.5 py-2.5 pr-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm @error('password') border-red-500 @enderror"
                            placeholder="Enter your password">
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <i id="password-icon" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 text-[#ff0808] focus:ring-[#ff0808] border-gray-300 rounded"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        {{ __('messages.remember_me') }}
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        id="loginBtn"
                        class="w-full py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-[#ff0808] hover:bg-[#dd0606] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff0808] transition-all shadow-sm disabled:opacity-70 disabled:cursor-not-allowed">
                        <span id="btnText">{{ __('messages.sign_in') }}</span>
                        <span id="btnLoader" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Signing in...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-4 bg-gray-50 text-gray-500">{{ __('messages.or_continue_with') }}</span>
                </div>
            </div>

            <!-- Social Login Buttons -->
            <div class="space-y-3">
                <!-- Google Login -->
                <button
                    type="button"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    {{ __('messages.continue_with_google') }}
                </button>

                <!-- Facebook Login -->
                <button
                    type="button"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    {{ __('messages.continue_with_facebook') }}
                </button>
            </div>

            <!-- Register Links -->
            <div class="pt-4 border-t border-gray-200 space-y-2 text-center">
                <p class="text-xs text-gray-600">
                    {{ __('messages.want_to_become_buyer') }}
                    <a href="{{ route('auth.register') }}" class="font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors">{{ __('messages.register') }}</a>
                </p>
                <p class="text-xs text-gray-600">
                    {{ __('messages.dont_have_account') }}
                    <a href="{{ route('vendor.register.step1') }}" class="font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors">{{ __('messages.register_as_vendor') }}</a>
                </p>
            </div>

            <!-- Back to Home -->
            <div class="text-center pt-2">
                <a href="{{ route('home') }}" class="inline-flex items-center text-xs text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('messages.go_back') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            }
        }

        // Login button loader
        document.querySelector('form').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');

            // Show loader
            btnText.classList.add('hidden');
            btnLoader.classList.remove('hidden');

            // Disable button
            loginBtn.disabled = true;
        });

        // Auto-hide error messages after 5 seconds
        setTimeout(() => {
            const errorMessages = document.querySelectorAll('.text-red-600, .bg-red-50, .bg-green-50');
            errorMessages.forEach(msg => {
                msg.style.transition = 'opacity 0.5s';
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
