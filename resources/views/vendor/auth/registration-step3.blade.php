<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Documents - AfriSellers</title>

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
                <a href="{{ route('vendor.register.step2') }}" class="inline-flex items-center text-xs text-gray-600 hover:text-gray-900 mb-6 sm:mb-8">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to business details
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
                            ✓
                        </div>
                        <span class="ml-2 text-xs font-medium text-gray-900 hidden sm:inline">Business</span>
                    </div>
                    <div class="flex-1 h-px bg-gray-900 mx-2 sm:mx-4"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-7 h-7 rounded-full bg-gray-900 text-white text-xs font-medium">
                            3
                        </div>
                        <span class="ml-2 text-xs font-medium text-gray-900 hidden sm:inline">Documents</span>
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="mb-8 sm:mb-10">
                <h1 class="text-2xl sm:text-2xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">Upload Documents</h1>
                <p class="text-sm sm:text-base text-gray-600">Final step - Upload your verification documents</p>
            </div>

            <!-- Registration Summary -->
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Account Summary -->
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Account</p>
                    <p class="text-sm font-medium text-gray-900">John Doe</p>
                    <p class="text-xs text-gray-600">john.doe@example.com</p>
                    <p class="text-xs text-gray-600">+250 788123456</p>
                </div>

                <!-- Business Summary -->
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Business</p>
                    <p class="text-sm font-medium text-gray-900">Tech Solutions Rwanda Ltd</p>
                    <p class="text-xs text-gray-600">Reg: 123456789</p>
                    <p class="text-xs text-gray-600">Kigali, Rwanda</p>
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
            <form id="documentsForm" class="space-y-4">
                <!-- Owner Name -->
                <div>
                    <label for="owner_full_name" class="block text-sm font-medium text-gray-900 mb-1.5">
                        Business Owner Full Name
                    </label>
                    <input
                        type="text"
                        name="owner_full_name"
                        id="owner_full_name"
                        required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900"
                        placeholder="E.g. John Doe"
                    >
                    <p class="mt-1 text-xs text-gray-500">As it appears on your ID/Passport</p>
                </div>

                <!-- Business Registration Document -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1.5">
                        Business Registration Certificate
                    </label>
                    <div
                        id="businessDocArea"
                        onclick="document.getElementById('businessDoc').click()"
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 sm:p-8 text-center cursor-pointer hover:border-gray-900 hover:bg-gray-50 transition-all"
                    >
                        <input
                            type="file"
                            name="business_registration_doc"
                            id="businessDoc"
                            class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                        >
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-2 sm:mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <div class="text-sm font-medium text-gray-900 mb-1">Click to upload business certificate</div>
                        <div class="text-xs text-gray-500">PDF, JPG, PNG (Max 5MB)</div>
                    </div>
                    <div id="businessDocPreview" class="hidden mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p id="businessDocName" class="text-sm font-medium text-gray-900 truncate"></p>
                                <p id="businessDocInfo" class="text-xs text-gray-600"></p>
                            </div>
                            <button
                                type="button"
                                onclick="removeFile('businessDoc')"
                                class="ml-3 text-xs text-red-600 hover:text-red-800 font-medium"
                            >
                                Remove
                            </button>
                        </div>
                        <img id="businessDocImage" class="hidden mt-3 max-w-full h-auto rounded-lg border border-gray-200">
                    </div>
                </div>

                <!-- Owner ID Document -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1.5">
                        Owner ID/Passport
                    </label>
                    <div
                        id="ownerIdArea"
                        onclick="document.getElementById('ownerId').click()"
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 sm:p-8 text-center cursor-pointer hover:border-gray-900 hover:bg-gray-50 transition-all"
                    >
                        <input
                            type="file"
                            name="owner_id_document"
                            id="ownerId"
                            class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                        >
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-2 sm:mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <div class="text-sm font-medium text-gray-900 mb-1">Click to upload ID or Passport</div>
                        <div class="text-xs text-gray-500">PDF, JPG, PNG (Max 5MB)</div>
                    </div>
                    <div id="ownerIdPreview" class="hidden mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p id="ownerIdName" class="text-sm font-medium text-gray-900 truncate"></p>
                                <p id="ownerIdInfo" class="text-xs text-gray-600"></p>
                            </div>
                            <button
                                type="button"
                                onclick="removeFile('ownerId')"
                                class="ml-3 text-xs text-red-600 hover:text-red-800 font-medium"
                            >
                                Remove
                            </button>
                        </div>
                        <img id="ownerIdImage" class="hidden mt-3 max-w-full h-auto rounded-lg border border-gray-200">
                    </div>
                </div>

                <!-- Security Info -->
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-xs text-gray-700">
                        <span class="font-medium">🔒 Secure:</span> Your documents are encrypted and stored securely. They will only be used for verification purposes.
                    </p>
                </div>

                <!-- Navigation Buttons -->
                <div class="pt-3 flex flex-col-reverse sm:flex-row gap-3">
                    <a
                        href="{{ route('vendor.register.step2') }}"
                        class="w-full sm:w-auto px-6 py-2.5 text-sm bg-white border border-gray-300 text-gray-900 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors text-center"
                    >
                        Back
                    </a>
                    <button
                        type="submit"
                        id="submitBtn"
                        class="flex-1 sm:flex-initial px-6 py-2.5 text-sm bg-[#ff0808] text-white font-semibold rounded-lg hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <span id="btnText">Complete Registration</span>
                        <span id="btnLoader" class="hidden">
                            <svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Submitting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // File size formatter
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // File upload handler
        function setupFileUpload(inputId, areaId, previewId, nameId, infoId, imageId) {
            const input = document.getElementById(inputId);
            const area = document.getElementById(areaId);
            const preview = document.getElementById(previewId);
            const nameEl = document.getElementById(nameId);
            const infoEl = document.getElementById(infoId);
            const imageEl = document.getElementById(imageId);

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    input.value = '';
                    return;
                }

                area.classList.remove('border-gray-300', 'hover:border-gray-900');
                area.classList.add('border-gray-900', 'bg-gray-50');
                preview.classList.remove('hidden');

                nameEl.textContent = file.name;
                infoEl.textContent = `${formatFileSize(file.size)} • ${file.type}`;

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imageEl.src = e.target.result;
                        imageEl.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    imageEl.classList.add('hidden');
                }
            });
        }

        // Remove file handler
        function removeFile(type) {
            const input = document.getElementById(type);
            const area = document.getElementById(type + 'Area');
            const preview = document.getElementById(type + 'Preview');
            const imageEl = document.getElementById(type + 'Image');

            input.value = '';
            area.classList.remove('border-gray-900', 'bg-gray-50');
            area.classList.add('border-gray-300', 'hover:border-gray-900');
            preview.classList.add('hidden');
            imageEl.classList.add('hidden');
            imageEl.src = '';
        }

        // Setup file uploads
        setupFileUpload('businessDoc', 'businessDocArea', 'businessDocPreview', 'businessDocName', 'businessDocInfo', 'businessDocImage');
        setupFileUpload('ownerId', 'ownerIdArea', 'ownerIdPreview', 'ownerIdName', 'ownerIdInfo', 'ownerIdImage');

        // Form submission
        document.getElementById('documentsForm').addEventListener('submit', function(e) {
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
            const ownerName = document.getElementById('owner_full_name').value.trim();
            const businessDoc = document.getElementById('businessDoc').files[0];
            const ownerId = document.getElementById('ownerId').files[0];

            // Validation
            const errors = [];

            if (ownerName.length < 3) {
                errors.push('Owner name must be at least 3 characters long');
            }

            if (!businessDoc) {
                errors.push('Please upload business registration certificate');
            }

            if (!ownerId) {
                errors.push('Please upload owner ID/Passport');
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
                document.getElementById('successMessage').textContent = 'Registration completed successfully! Redirecting to dashboard...';
                successContainer.classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });

                // Redirect after success
                setTimeout(() => {
                    alert('Registration Complete! In a real app, you would be redirected to the vendor dashboard.');
                    // window.location.href = '/vendor/dashboard';
                }, 2000);
            }, 2000);
        });
    </script>
</body>
</html>
