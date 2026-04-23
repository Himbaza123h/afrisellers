<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Vendor - AfriSellers</title>

    <!-- Favicon -->
    <link rel="icon" href="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png" type="image/png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
        }
        input:focus, select:focus {
            --tw-ring-color: #111827;
        }
    </style>
</head>
<body class="bg-white">
    <div class="min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

            <!-- Logo & Back Link -->
            <div class="mb-8 sm:mb-12">
                <a href="/" class="inline-flex items-center text-xs text-gray-600 hover:text-gray-900 mb-6 sm:mb-8">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Go back
                </a>
                <a href="/"><img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png" alt="AfriSellers" class="h-8 sm:h-10"></a>
            </div>

            <!-- Header -->
            <div class="mb-8 sm:mb-10">
                <h1 class="text-2xl sm:text-2xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">Start Selling on AfriSellers</h1>
                <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Join thousands of vendors reaching customers across Africa</p>

                <!-- Benefits -->
                <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:gap-x-8 sm:gap-y-2 text-xs text-gray-600">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-900 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Quick approval process
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-900 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        24/7 support
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-900 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Access to millions of buyers
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            <div id="errorContainer" class="hidden mb-6 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-xs font-medium text-red-900 mb-2">Please fix the following errors:</p>
                <ul id="errorList" class="text-xs text-red-700 space-y-1"></ul>
            </div>

            <!-- Success Message -->
            <div id="successContainer" class="hidden mb-6 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p id="successMessage" class="text-xs text-green-700"></p>
            </div>

            <!-- Form -->
            <form id="vendorRegistrationForm" class="space-y-4">
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Full Name
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900"
                        placeholder="E.g. HIMBAZA Alain Honore"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Email Address
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900"
                        placeholder="E.g. himbazaalain022@gmail.com"
                    >
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Phone Number
                    </label>
                    <div class="flex gap-2">
                        <select
                            name="phone_code"
                            class="px-2.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-white text-gray-900 w-20 sm:w-24">
                            <option value="+250">+250</option>
                            <option value="+254">+254</option>
                            <option value="+255">+255</option>
                            <option value="+256">+256</option>
                            <option value="+234">+234</option>
                            <option value="+233">+233</option>
                            <option value="+27">+27</option>
                        </select>
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            required
                            class="flex-1 px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900"
                            placeholder="E.g. 788123456"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900"
                        placeholder="Minimum 8 characters"
                    >
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Confirm Password
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900"
                        placeholder="Re-enter password"
                    >
                </div>

                <!-- Submit Button -->
                <div class="pt-3 flex justify-start">
                    <button
                        type="submit"
                        id="submitBtn"
                        class="w-full sm:w-3/4 md:w-3/5 px-6 py-2.5 text-sm bg-[#ff0808] text-white rounded-lg font-semibold hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <span id="btnText">Next: Business Information</span>
                        <span id="btnLoader" class="hidden">
                            <svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Already have account -->
            <div class="mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-gray-200">
                <p class="text-xs text-gray-600">
                    Already have an account?
                    <a href="#" class="font-semibold text-gray-900 hover:text-[#ff0808] transition-colors">Sign In</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Form validation and submission
        document.getElementById('vendorRegistrationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const errorContainer = document.getElementById('errorContainer');
            const errorList = document.getElementById('errorList');
            const successContainer = document.getElementById('successContainer');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');

            // Clear previous errors
            errorList.innerHTML = '';
            errorContainer.classList.add('hidden');
            successContainer.classList.add('hidden');

            // Get form values
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;

            // Validation
            const errors = [];

            if (name.length < 3) {
                errors.push('Full name must be at least 3 characters long');
            }

            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                errors.push('Please enter a valid email address');
            }

            if (phone.length < 9) {
                errors.push('Please enter a valid phone number');
            }

            if (password.length < 8) {
                errors.push('Password must be at least 8 characters long');
            }

            if (password !== passwordConfirmation) {
                errors.push('Passwords do not match');
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
            submitBtn.disabled = true;

            // Simulate form submission
            setTimeout(() => {
                btnText.classList.remove('hidden');
                btnLoader.classList.add('hidden');
                submitBtn.disabled = false;

                // Show success message
                document.getElementById('successMessage').textContent = 'Account created successfully! Redirecting to business information...';
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
