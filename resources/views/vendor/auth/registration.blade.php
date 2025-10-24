@extends('layouts.guest')

@section('title', 'Vendor Registration - Join Afrisellers')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">

        <!-- Header -->
        <div class="bg-white rounded-t-xl shadow-sm px-6 py-8 text-center border-b-4 border-blue-600">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Vendor Registration</h1>
            <p class="text-gray-600">Join Africa's leading B2B marketplace in 3 simple steps</p>
        </div>

        <!-- Progress Steps -->
        <div class="bg-white px-6 py-8 border-b border-gray-200">
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <!-- Progress Line -->
                    <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200" aria-hidden="true">
                        <div id="progressBar" class="h-full bg-green-500 transition-all duration-500 ease-in-out" style="width: 0%"></div>
                    </div>

                    <!-- Steps -->
                    <div class="relative flex justify-between">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center step-item" data-step="1">
                            <div class="step-circle w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-lg ring-4 ring-white transition-all duration-300">
                                <span class="step-number">1</span>
                                <span class="step-check hidden">✓</span>
                            </div>
                            <div class="mt-3 text-center">
                                <div class="text-sm font-semibold text-blue-600">Account</div>
                                <div class="text-xs text-gray-500 mt-0.5">Login details</div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex flex-col items-center step-item" data-step="2">
                            <div class="step-circle w-12 h-12 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg shadow-lg ring-4 ring-white transition-all duration-300">
                                <span class="step-number">2</span>
                                <span class="step-check hidden">✓</span>
                            </div>
                            <div class="mt-3 text-center">
                                <div class="text-sm font-semibold text-gray-500">Business</div>
                                <div class="text-xs text-gray-400 mt-0.5">Company info</div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex flex-col items-center step-item" data-step="3">
                            <div class="step-circle w-12 h-12 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg shadow-lg ring-4 ring-white transition-all duration-300">
                                <span class="step-number">3</span>
                                <span class="step-check hidden">✓</span>
                            </div>
                            <div class="mt-3 text-center">
                                <div class="text-sm font-semibold text-gray-500">Documents</div>
                                <div class="text-xs text-gray-400 mt-0.5">Verification</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-red-800">Please correct the following errors:</p>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Container -->
        <div class="bg-white rounded-b-xl shadow-sm px-6 py-8">
            <form action="{{ route('vendor.register.submit') }}" method="POST" enctype="multipart/form-data" id="registrationForm">
                @csrf

                <!-- Step 1: Account Information -->
                <div class="form-step" data-step="1">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Create Your Account</h2>
                        <p class="text-gray-600">Set up your login credentials and contact information</p>
                    </div>

                    <div class="space-y-6">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="John Doe"
                                required
                            >
                        </div>

                        <!-- Email and Phone -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="{{ old('email') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="john@example.com"
                                    required
                                >
                                <p class="mt-1.5 text-xs text-gray-500">This will be your login email</p>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="tel"
                                    name="phone"
                                    id="phone"
                                    value="{{ old('phone') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="+250 700 000 000"
                                    required
                                >
                                <p class="mt-1.5 text-xs text-gray-500">Include country code</p>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="••••••••"
                                    required
                                >
                                <p class="mt-1.5 text-xs text-gray-500">Minimum 8 characters</p>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="••••••••"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-600 order-2 sm:order-1">
                            Already have an account? <a href="" class="text-blue-600 font-semibold hover:text-blue-700">Login here</a>
                        </div>
                        <button
                            type="button"
                            onclick="nextStep()"
                            class="order-1 sm:order-2 w-full sm:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-105"
                        >
                            Continue →
                        </button>
                    </div>
                </div>

                <!-- Step 2: Business Information -->
                <div class="form-step hidden" data-step="2">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Business Information</h2>
                        <p class="text-gray-600">Tell us about your company</p>
                    </div>

                    <div class="space-y-6">
                        <!-- Business Name -->
                        <div>
                            <label for="business_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Business Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="business_name"
                                id="business_name"
                                value="{{ old('business_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Your Company Ltd"
                                required
                            >
                            <p class="mt-1.5 text-xs text-gray-500">Enter your registered company name</p>
                        </div>

                        <!-- Registration Number -->
                        <div>
                            <label for="business_registration_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                Business Registration Number <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="business_registration_number"
                                id="business_registration_number"
                                value="{{ old('business_registration_number') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="123456789"
                                required
                            >
                            <p class="mt-1.5 text-xs text-gray-500">Your official business/trade license number</p>
                        </div>

                        <!-- Country and City -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <select
                                    name="country"
                                    id="country"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white"
                                    required
                                >
                                    <option value="">Select your country</option>
                                    <option value="Rwanda" {{ old('country') == 'Rwanda' ? 'selected' : '' }}>Rwanda</option>
                                    <option value="Kenya" {{ old('country') == 'Kenya' ? 'selected' : '' }}>Kenya</option>
                                    <option value="Uganda" {{ old('country') == 'Uganda' ? 'selected' : '' }}>Uganda</option>
                                    <option value="Tanzania" {{ old('country') == 'Tanzania' ? 'selected' : '' }}>Tanzania</option>
                                    <option value="Burundi" {{ old('country') == 'Burundi' ? 'selected' : '' }}>Burundi</option>
                                    <option value="Nigeria" {{ old('country') == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                                    <option value="Ghana" {{ old('country') == 'Ghana' ? 'selected' : '' }}>Ghana</option>
                                    <option value="South Africa" {{ old('country') == 'South Africa' ? 'selected' : '' }}>South Africa</option>
                                    <option value="Egypt" {{ old('country') == 'Egypt' ? 'selected' : '' }}>Egypt</option>
                                    <option value="Morocco" {{ old('country') == 'Morocco' ? 'selected' : '' }}>Morocco</option>
                                    <option value="Ethiopia" {{ old('country') == 'Ethiopia' ? 'selected' : '' }}>Ethiopia</option>
                                </select>
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="city"
                                    id="city"
                                    value="{{ old('city') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Kigali"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <button
                            type="button"
                            onclick="prevStep()"
                            class="order-2 sm:order-1 w-full sm:w-auto px-8 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors"
                        >
                            ← Back
                        </button>
                        <button
                            type="button"
                            onclick="nextStep()"
                            class="order-1 sm:order-2 w-full sm:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-105"
                        >
                            Continue →
                        </button>
                    </div>
                </div>

                <!-- Step 3: Verification Documents -->
                <div class="form-step hidden" data-step="3">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Verification Documents</h2>
                        <p class="text-gray-600">Upload required documents to verify your business</p>
                    </div>

                    <div class="space-y-6">
                        <!-- Owner Name -->
                        <div>
                            <label for="owner_full_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Owner Full Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="owner_full_name"
                                id="owner_full_name"
                                value="{{ old('owner_full_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Legal name as on ID"
                                required
                            >
                            <p class="mt-1.5 text-xs text-gray-500">Must match the name on your ID document</p>
                        </div>

                        <!-- Business Registration Document -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Business Registration Document <span class="text-red-500">*</span>
                            </label>
                            <div
                                id="businessDocArea"
                                onclick="document.getElementById('businessDoc').click()"
                                class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all"
                            >
                                <input
                                    type="file"
                                    name="business_registration_doc"
                                    id="businessDoc"
                                    class="hidden"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >
                                <div class="text-5xl mb-3">📄</div>
                                <div class="text-sm font-semibold text-gray-700 mb-1">Click to upload business certificate</div>
                                <div class="text-xs text-gray-500">PDF, JPG, PNG • Max 5MB</div>
                            </div>
                            <div id="businessDocPreview" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-start justify-between mb-3">
                                    <span id="businessDocName" class="text-sm font-semibold text-gray-700 break-all"></span>
                                    <button
                                        type="button"
                                        onclick="removeFile('businessDoc')"
                                        class="ml-3 text-red-600 hover:text-red-800 font-semibold text-sm"
                                    >
                                        Remove
                                    </button>
                                </div>
                                <img id="businessDocImage" class="hidden max-w-full h-auto rounded-lg border border-gray-200 mb-2">
                                <div id="businessDocInfo" class="text-xs text-gray-600"></div>
                            </div>
                        </div>

                        <!-- Owner ID Document -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Owner ID Document <span class="text-red-500">*</span>
                            </label>
                            <div
                                id="ownerIdArea"
                                onclick="document.getElementById('ownerId').click()"
                                class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all"
                            >
                                <input
                                    type="file"
                                    name="owner_id_document"
                                    id="ownerId"
                                    class="hidden"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >
                                <div class="text-5xl mb-3">🪪</div>
                                <div class="text-sm font-semibold text-gray-700 mb-1">Click to upload ID or Passport</div>
                                <div class="text-xs text-gray-500">PDF, JPG, PNG • Max 5MB</div>
                            </div>
                            <div id="ownerIdPreview" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-start justify-between mb-3">
                                    <span id="ownerIdName" class="text-sm font-semibold text-gray-700 break-all"></span>
                                    <button
                                        type="button"
                                        onclick="removeFile('ownerId')"
                                        class="ml-3 text-red-600 hover:text-red-800 font-semibold text-sm"
                                    >
                                        Remove
                                    </button>
                                </div>
                                <img id="ownerIdImage" class="hidden max-w-full h-auto rounded-lg border border-gray-200 mb-2">
                                <div id="ownerIdInfo" class="text-xs text-gray-600"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <button
                            type="button"
                            onclick="prevStep()"
                            class="order-2 sm:order-1 w-full sm:w-auto px-8 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors"
                        >
                            ← Back
                        </button>
                        <button
                            type="submit"
                            class="order-1 sm:order-2 w-full sm:w-auto px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all transform hover:scale-105"
                        >
                            ✓ Submit Registration
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 3;

function updateProgress() {
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressBar').style.width = progress + '%';

    document.querySelectorAll('.step-item').forEach((item, index) => {
        const step = index + 1;
        const circle = item.querySelector('.step-circle');
        const number = item.querySelector('.step-number');
        const check = item.querySelector('.step-check');
        const title = item.querySelectorAll('div')[1].querySelector('div:first-child');
        const desc = item.querySelectorAll('div')[1].querySelector('div:last-child');

        circle.classList.remove('bg-blue-600', 'bg-green-500', 'bg-gray-300', 'text-white', 'text-gray-600', 'scale-110');
        title.classList.remove('text-blue-600', 'text-green-600', 'text-gray-500');
        desc.classList.remove('text-gray-500', 'text-gray-400');

        if (step < currentStep) {
            // Completed
            circle.classList.add('bg-green-500', 'text-white');
            title.classList.add('text-green-600');
            desc.classList.add('text-gray-500');
            number.classList.add('hidden');
            check.classList.remove('hidden');
        } else if (step === currentStep) {
            // Active
            circle.classList.add('bg-blue-600', 'text-white', 'scale-110');
            title.classList.add('text-blue-600');
            desc.classList.add('text-gray-500');
            number.classList.remove('hidden');
            check.classList.add('hidden');
        } else {
            // Inactive
            circle.classList.add('bg-gray-300', 'text-gray-600');
            title.classList.add('text-gray-500');
            desc.classList.add('text-gray-400');
            number.classList.remove('hidden');
            check.classList.add('hidden');
        }
    });
}

function showStep(step) {
    document.querySelectorAll('.form-step').forEach(s => s.classList.add('hidden'));
    document.querySelector(`.form-step[data-step="${step}"]`).classList.remove('hidden');
    updateProgress();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
    const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
    const inputs = currentStepEl.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        input.classList.remove('border-red-500', 'ring-red-500');
        if (!input.value.trim()) {
            input.classList.add('border-red-500');
            isValid = false;
        }
    });

    if (step === 1) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (password.length < 8) {
            document.getElementById('password').classList.add('border-red-500');
            alert('Password must be at least 8 characters long');
            return false;
        }

        if (password !== confirmPassword) {
            document.getElementById('password_confirmation').classList.add('border-red-500');
            alert('Passwords do not match!');
            return false;
        }
    }

    if (!isValid) {
        alert('Please fill in all required fields correctly');
    }

    return isValid;
}

function nextStep() {
    if (validateStep(currentStep)) {
        currentStep++;
        showStep(currentStep);
    }
}

function prevStep() {
    currentStep--;
    showStep(currentStep);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

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

        area.classList.remove('border-gray-300', 'hover:border-blue-500');
        area.classList.add('border-green-500', 'bg-green-50');
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

function removeFile(type) {
    const input = document.getElementById(type);
    const area = document.getElementById(type + 'Area');
    const preview = document.getElementById(type + 'Preview');
    const image = document.getElementById(type + 'Image');

    input.value = '';
    area.classList.remove('border-green-500', 'bg-green-50');
    area.classList.add('border-gray-300', 'hover:border-blue-500');
    preview.classList.add('hidden');
    image.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    setupFileUpload('businessDoc', 'businessDocArea', 'businessDocPreview', 'businessDocName', 'businessDocInfo', 'businessDocImage');
    setupFileUpload('ownerId', 'ownerIdArea', 'ownerIdPreview', 'ownerIdName', 'ownerIdInfo', 'ownerIdImage');

    // Prevent default click behavior on file areas
    document.querySelectorAll('[id$="Area"]').forEach(area => {
        area.addEventListener('click', function(e) {
            e.preventDefault();
        });
    });

    // Initialize progress
    updateProgress();
});
</script>
@endpush
