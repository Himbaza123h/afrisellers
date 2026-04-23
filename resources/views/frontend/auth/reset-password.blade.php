<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - AfriSellers</title>
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
                <h2 class="text-xl font-bold text-gray-900">Set new password</h2>
                <p class="text-sm text-gray-600">Must be at least 8 characters.</p>
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

            <!-- Form -->
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Email Address
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        value="{{ $email ?? old('email') }}"
                        class="block w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm @error('email') border-red-500 @enderror"
                        placeholder="Enter your email">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-1.5">
                        New Password
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="block w-full px-3.5 py-2.5 pr-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm @error('password') border-red-500 @enderror"
                            placeholder="At least 8 characters">
                        <button type="button" onclick="togglePassword('password', 'icon1')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <i id="icon1" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            class="block w-full px-3.5 py-2.5 pr-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm"
                            placeholder="Repeat your new password">
                        <button type="button" onclick="togglePassword('password_confirmation', 'icon2')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <i id="icon2" class="fas fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        id="submitBtn"
                        class="w-full py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-[#ff0808] hover:bg-[#dd0606] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff0808] transition-all shadow-sm disabled:opacity-70 disabled:cursor-not-allowed">
                        <span id="btnText">Reset Password</span>
                        <span id="btnLoader" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Resetting...
                        </span>
                    </button>
                </div>
            </form>

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
        function togglePassword(fieldId, iconId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        }

        document.querySelector('form').addEventListener('submit', function () {
            document.getElementById('btnText').classList.add('hidden');
            document.getElementById('btnLoader').classList.remove('hidden');
            document.getElementById('submitBtn').disabled = true;
        });
    </script>
</body>
</html>