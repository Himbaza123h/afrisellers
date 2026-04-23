<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Information - AfriSellers</title>

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
                <a href="{{ route('vendor.register.step1') }}" class="inline-flex items-center text-xs text-gray-600 hover:text-gray-900 mb-6 sm:mb-8">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to account details
                </a>
                <a href="/"><img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png" alt="AfriSellers" class="h-8 sm:h-10"></a>
            </div>

            <!-- Progress Indicator -->
            <div class="mb-8 sm:mb-10">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-7 h-7 rounded-full bg-gray-900 text-white text-xs font-medium">
                            ✓
                        </div>
                        <span class="ml-2 text-xs font-medium text-gray-900 hidden sm:inline">Account</span>
                    </div>
                    <div class="flex-1 h-px bg-gray-900 mx-2 sm:mx-4"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-7 h-7 rounded-full bg-gray-900 text-white text-xs font-medium">
                            2
                        </div>
                        <span class="ml-2 text-xs font-medium text-gray-900 hidden sm:inline">Business</span>
                    </div>
                    <div class="flex-1 h-px bg-gray-300 mx-2 sm:mx-4"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-7 h-7 rounded-full bg-gray-300 text-gray-600 text-xs font-medium">
                            3
                        </div>
                        <span class="ml-2 text-xs font-medium text-gray-500 hidden sm:inline">Documents</span>
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="mb-8 sm:mb-10">
                <h1 class="text-2xl sm:text-2xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">Business Information</h1>
                <p class="text-sm sm:text-base text-gray-600">Tell us about your business</p>
            </div>

            <!-- Account Summary -->
            <div class="mb-6 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-xs text-gray-600 mb-1">Registering as</p>
                <p class="text-sm font-medium text-gray-900">John Doe</p>
                <p class="text-xs text-gray-600">john.doe@example.com</p>
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
            <form id="businessInfoForm" class="space-y-4">
                <!-- Business Name -->
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Business Name
                    </label>
                    <input
                        type="text"
                        name="business_name"
                        id="business_name"
                        required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900"
                        placeholder="E.g. Tech Solutions Rwanda Ltd"
                    >
                    <p class="mt-1 text-xs text-gray-500">This will be your store name on AfriSellers</p>
                </div>

                <!-- Registration Number -->
                <div>
                    <label for="business_registration_number" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Business Registration Number
                    </label>
                    <input
                        type="text"
                        name="business_registration_number"
                        id="business_registration_number"
                        required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900"
                        placeholder="123456789"
                    >
                    <p class="mt-1 text-xs text-gray-500">Your official business registration number</p>
                </div>

                <!-- Country and City -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-900 mb-1.5">
                            Country
                        </label>
                        <select
                            name="country"
                            id="country"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-white text-gray-900"
                        >
                            <option value="">Select Country</option>
                            <option value="Rwanda">Rwanda</option>
                            <option value="Kenya">Kenya</option>
                            <option value="Uganda">Uganda</option>
                            <option value="Tanzania">Tanzania</option>
                            <option value="Burundi">Burundi</option>
                            <option value="Nigeria">Nigeria</option>
                            <option value="Ghana">Ghana</option>
                            <option value="South Africa">South Africa</option>
                            <option value="Egypt">Egypt</option>
                            <option value="Morocco">Morocco</option>
                            <option value="Ethiopia">Ethiopia</option>
                        </select>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-900 mb-1.5">
                            City
                        </label>
                        <input
                            type="text"
                            name="city"
                            id="city"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900"
                            placeholder="E.g. Kigali"
                        >
                    </div>
                </div>

                <!-- Info Note -->
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-xs text-gray-700">
                        <span class="font-medium">Note:</span> Make sure your business registration details match your official documents
                    </p>
                </div>

                <!-- Navigation Buttons -->
                <div class="pt-3 flex flex-col-reverse sm:flex-row gap-3">
                    <a
                        href=""
                        class="w-full sm:w-auto px-6 py-2.5 text-sm bg-white border border-gray-300 text-gray-900 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors text-center"
                    >
                        Back
                    </a>
                    <button
                        type="submit"
                        id="submitBtn"
                        class="flex-1 sm:flex-initial px-6 py-2.5 text-sm bg-[#ff0808] text-white font-semibold rounded-lg hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <span id="btnText">Next: Upload Documents</span>
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
        </div>
    </div>

    <script>
        // Form validation and submission
        document.getElementById('businessInfoForm').addEventListener('submit', function(e) {
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
            const businessName = document.getElementById('business_name').value.trim();
            const regNumber = document.getElementById('business_registration_number').value.trim();
            const country = document.getElementById('country').value;
            const city = document.getElementById('city').value.trim();

            // Validation
            const errors = [];

            if (businessName.length < 3) {
                errors.push('Business name must be at least 3 characters long');
            }

            if (regNumber.length < 5) {
                errors.push('Please enter a valid business registration number');
            }

            if (!country) {
                errors.push('Please select a country');
            }

            if (city.length < 2) {
                errors.push('Please enter a valid city name');
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

            // Simulate form submission and redirect
            setTimeout(() => {
                // Store data in sessionStorage (in real app, this would be handled by backend)
                sessionStorage.setItem('businessInfo', JSON.stringify({
                    businessName,
                    regNumber,
                    country,
                    city
                }));

                // Redirect to step 3
                window.location.href = "{{ route('vendor.register.step2') }}"
            }, 1500);
        });
    </script>
</body>
</html>
