<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Buyer - AfriSellers</title>

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

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-6">

            <!-- Header -->
            <div class="text-center space-y-3">
                <!-- Logo -->
                <a href="#" class="inline-block mb-2">
                    <img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png"
                         alt="AfriSellers"
                         class="h-8 sm:h-10 w-auto mx-auto">
                </a>

                <!-- Welcome Text -->
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Create Your Account</h2>
                <p class="text-xs sm:text-sm text-gray-600">
                    Join AfriSellers and start shopping 🛒
                </p>
            </div>

            <!-- Error Messages -->
            <div id="errorContainer" class="hidden bg-red-50 border border-red-200 rounded-lg p-3">
                <ul id="errorList" class="text-xs text-red-600 space-y-1"></ul>
            </div>

            <!-- Success Message -->
            <div id="successContainer" class="hidden bg-green-50 border border-green-200 rounded-lg p-3">
                <p id="successMessage" class="text-xs text-green-700"></p>
            </div>

            <!-- Registration Form -->
            <form id="registrationForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-900 mb-1.5">
                            Full Name
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            required
                            class="block w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm"
                            placeholder="E.g. HIMBAZA Alain Honore">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-900 mb-1.5">
                            Email Address
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            class="block w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm"
                            placeholder="E.g. himbazaalain022@gmail.com">
                    </div>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Phone Number
                    </label>
                    <div class="flex gap-2">
                        <select
                            name="phone_code"
                            class="px-2.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent bg-white w-20 sm:w-24">
                            <option value="+250">+250</option>
                            <option value="+254">+254</option>
                            <option value="+255">+255</option>
                            <option value="+256">+256</option>
                            <option value="+234">+234</option>
                            <option value="+233">+233</option>
                            <option value="+27">+27</option>
                        </select>
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            required
                            class="flex-1 px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm"
                            placeholder="E.g. 788123456">
                    </div>
                </div>

                <!-- Location -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-900 mb-1.5">
                            Country
                        </label>
                        <select
                            id="country"
                            name="country"
                            required
                            class="block w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent bg-white text-sm">
                            <option value="">Select</option>
                            <option value="rwanda">Rwanda</option>
                            <option value="kenya">Kenya</option>
                            <option value="tanzania">Tanzania</option>
                            <option value="uganda">Uganda</option>
                            <option value="nigeria">Nigeria</option>
                            <option value="ghana">Ghana</option>
                            <option value="south-africa">South Africa</option>
                        </select>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-900 mb-1.5">
                            City
                        </label>
                        <input
                            id="city"
                            name="city"
                            type="text"
                            required
                            class="block w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm"
                            placeholder="E.g. Kigali">
                    </div>
                </div>

                <!-- Date of Birth and Sex -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="dob" class="block text-sm font-medium text-gray-900 mb-1.5">
                            Date of Birth
                        </label>
                        <input
                            id="dob"
                            name="dob"
                            type="date"
                            required
                            class="block w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm">
                        <p class="mt-1 text-xs text-gray-500">Must be 18+</p>
                    </div>
                    <div>
                        <label for="sex" class="block text-sm font-medium text-gray-900 mb-1.5">
                            Sex
                        </label>
                        <select
                            id="sex"
                            name="sex"
                            required
                            class="block w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent bg-white text-sm">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-900 mb-1.5">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="block w-full px-3.5 py-2.5 pr-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm"
                                placeholder="Minimum 8 characters">
                            <button
                                type="button"
                                onclick="togglePassword('password', 'password-icon')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <i id="password-icon" class="fas fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-900 mb-1.5">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirm"
                                name="password_confirm"
                                type="password"
                                required
                                class="block w-full px-3.5 py-2.5 pr-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all bg-white text-sm"
                                placeholder="Re-enter password">
                            <button
                                type="button"
                                onclick="togglePassword('password_confirm', 'password-confirm-icon')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <i id="password-confirm-icon" class="fas fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-start">
                    <input
                        id="terms"
                        name="terms"
                        type="checkbox"
                        required
                        class="h-4 w-4 text-[#ff0808] focus:ring-[#ff0808] border-gray-300 rounded mt-0.5">
                    <label for="terms" class="ml-2 block text-xs text-gray-700">
                        I agree to the <a href="#" class="font-medium text-[#ff0808] hover:text-[#dd0606]">Terms & Conditions</a> and <a href="#" class="font-medium text-[#ff0808] hover:text-[#dd0606]">Privacy Policy</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex justify-center">
                    <button
                        type="submit"
                        id="registerBtn"
                        class="w-full sm:w-3/4 md:w-3/5 py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-[#ff0808] hover:bg-[#dd0606] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff0808] transition-all shadow-sm disabled:opacity-70 disabled:cursor-not-allowed">
                        <span id="btnText">Create Account</span>
                        <span id="btnLoader" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Creating account...
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
                    <span class="px-4 bg-gray-50 text-gray-500">Or continue with</span>
                </div>
            </div>

            <!-- Social Login Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                    <span class="hidden sm:inline">Continue with</span> Google
                </button>

                <!-- Facebook Login -->
                <button
                    type="button"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="hidden sm:inline">Continue with</span> Facebook
                </button>
            </div>

            <!-- Login Link -->
            <div class="pt-4 border-t border-gray-200 space-y-2 text-center">
                <p class="text-xs text-gray-600">
                    Already have an account?
                    <a href="{{ route('auth.signin') }}" class="font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors">Sign In</a>
                </p>
                <p class="text-xs text-gray-600">
                    Want to sell products?
                    <a href="{{ route('vendor.register.step1') }}" class="font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors">Register as Vendor</a>
                </p>
            </div>

            <!-- Back to Home -->
            <div class="text-center pt-2">
                <a href="#" class="inline-flex items-center text-xs text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Go back
                </a>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(iconId);

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

        // Set max date for DOB (18 years ago)
        const dobInput = document.getElementById('dob');
        const eighteenYearsAgo = new Date();
        eighteenYearsAgo.setFullYear(eighteenYearsAgo.getFullYear() - 18);
        dobInput.max = eighteenYearsAgo.toISOString().split('T')[0];

        // Form validation and submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const errorContainer = document.getElementById('errorContainer');
            const errorList = document.getElementById('errorList');
            const successContainer = document.getElementById('successContainer');
            const registerBtn = document.getElementById('registerBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');

            // Clear previous errors
            errorList.innerHTML = '';
            errorContainer.classList.add('hidden');
            successContainer.classList.add('hidden');

            // Get form values
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            const dob = new Date(document.getElementById('dob').value);
            const today = new Date();
            const age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));

            // Validation
            const errors = [];

            if (password.length < 8) {
                errors.push('Password must be at least 8 characters long');
            }

            if (password !== passwordConfirm) {
                errors.push('Passwords do not match');
            }

            if (age < 18) {
                errors.push('You must be at least 18 years old to register');
            }

            if (errors.length > 0) {
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = '• ' + error;
                    errorList.appendChild(li);
                });
                errorContainer.classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            // Show loader
            btnText.classList.add('hidden');
            btnLoader.classList.remove('hidden');
            registerBtn.disabled = true;

            // Simulate form submission
            setTimeout(() => {
                btnText.classList.remove('hidden');
                btnLoader.classList.add('hidden');
                registerBtn.disabled = false;

                // Show success message
                document.getElementById('successMessage').textContent = 'Account created successfully! Redirecting to login...';
                successContainer.classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });

                // Reset form
                setTimeout(() => {
                    this.reset();
                    successContainer.classList.add('hidden');
                }, 3000);
            }, 2000);
        });

        // Auto-hide messages after 5 seconds
        setTimeout(() => {
            const messages = document.querySelectorAll('#errorContainer, #successContainer');
            messages.forEach(msg => {
                if (!msg.classList.contains('hidden')) {
                    msg.style.transition = 'opacity 0.5s';
                    msg.style.opacity = '0';
                    setTimeout(() => {
                        msg.classList.add('hidden');
                        msg.style.opacity = '1';
                    }, 500);
                }
            });
        }, 5000);
    </script>
</body>
</html>
