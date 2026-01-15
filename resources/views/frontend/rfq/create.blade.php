@extends('layouts.app')

@section('title', 'Send RFQ - Request for Quotation')

@section('content')
    <div class="py-12 min-h-screen bg-gray-50">
        <div class="container px-4 mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="mb-4 text-lg font-bold text-gray-900">Send Request for Quotation (RFQ)</h1>
                <p class="text-md text-gray-600">Fill out the form below to request a quotation from our suppliers</p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="p-4 mb-6 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center">
                        <svg class="mr-2 w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm font-medium text-green-900">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-4 mb-6 bg-red-50 rounded-lg border border-red-200">
                    <p class="mb-2 text-sm font-medium text-red-900">Please fix the following errors:</p>
                    <ul class="space-y-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card -->
            <div>
                <form action="{{ route('rfqs.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Selection Section - At least one required -->
                    <div class="">
                        <p class="mb-4 text-sm font-semibold text-yellow-900">
                            <i class="mr-2 fas fa-info-circle"></i>Please select at least one option below:
                        </p>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <!-- Product Selection -->
                            <div>
                                <label for="product_id" class="block mb-2 text-sm font-medium text-gray-900">
                                    Product
                                </label>
                                <select name="product_id" id="product_id"
                                    class="select2-dropdown w-full px-4 py-3 text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="">Select a product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Business Profile Selection -->
                            <div>
                                <label for="business_id" class="block mb-2 text-sm font-medium text-gray-900">
                                    Business/Supplier
                                </label>
                                <select name="business_id" id="business_id"
                                    class="select2-dropdown w-full px-4 py-3 text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="">Select a business</option>
                                    @foreach ($businessProfiles as $business)
                                        <option value="{{ $business->id }}"
                                            {{ old('business_id') == $business->id ? 'selected' : '' }}>
                                            {{ $business->business_name }} @if ($business->country)
                                                - {{ $business->country->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('business_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category Selection -->
                            <div>
                                <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900">
                                    Product Category
                                </label>
                                <select name="category_id" id="category_id"
                                    class="select2-dropdown w-full px-4 py-3 text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-yellow-800">
                            <i class="mr-1 fas fa-exclamation-triangle"></i>You must select at least one: Product, Business,
                            or Category to submit your RFQ.
                        </p>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block mb-2 text-sm font-medium text-gray-900">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="6"
                            class="w-full px-4 py-3 text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                            placeholder="Describe your requirements, quantity needed, delivery timeline, and any other relevant details..."
                            required>{{ old('message') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Minimum 10 characters. Please provide detailed information
                            about your requirements.</p>
                        @error('message')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Information Section -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Contact Information</h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $userData['name'] ?? '') }}"
                                    class="w-full px-4 py-3 text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                    placeholder="Your full name" required>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email"
                                    value="{{ old('email', $userData['email'] ?? '') }}"
                                    class="w-full px-4 py-3 text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                    placeholder="your.email@example.com" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="mt-6">
                            <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <select name="phone_code"
                                    class="px-3 py-3 text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                    required>
                                    <option value="+250" {{ old('phone_code') == '+250' ? 'selected' : '' }}>+250
                                    </option>
                                    <option value="+254" {{ old('phone_code') == '+254' ? 'selected' : '' }}>+254
                                    </option>
                                    <option value="+255" {{ old('phone_code') == '+255' ? 'selected' : '' }}>+255
                                    </option>
                                    <option value="+256" {{ old('phone_code') == '+256' ? 'selected' : '' }}>+256
                                    </option>
                                    <option value="+234" {{ old('phone_code') == '+234' ? 'selected' : '' }}>+234
                                    </option>
                                    <option value="+233" {{ old('phone_code') == '+233' ? 'selected' : '' }}>+233
                                    </option>
                                    <option value="+27" {{ old('phone_code') == '+27' ? 'selected' : '' }}>+27</option>
                                </select>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                    class="flex-1 px-4 py-3 text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                    placeholder="Phone number" required>
                            </div>
                        </div>
                    </div>

                    <!-- Location Information Section -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Location Information</h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Country -->
                            <div>
                                <label for="country_id" class="block mb-2 text-sm font-medium text-gray-900">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <select name="country_id" id="country_id"
                                    class="select2-dropdown w-full px-4 py-3 text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                    required>
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- City -->
                            <div>
                                <label for="city" class="block mb-2 text-sm font-medium text-gray-900">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}"
                                    class="w-full px-4 py-3 text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                    placeholder="Your city" required>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mt-6">
                            <label for="address" class="block mb-2 text-sm font-medium text-gray-900">
                                Address (Optional)
                            </label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}"
                                class="w-full px-4 py-3 text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                placeholder="Street address, building, etc.">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-gray-200">
                        <button type="submit"
                            class="w-full md:w-auto px-8 py-3 text-white bg-[#ff0808] rounded-lg font-semibold hover:bg-[#e00606] transition-colors focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:ring-offset-2">
                            <i class="mr-2 fas fa-paper-plane"></i>Submit RFQ
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="p-6 mt-8 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start">
                    <svg class="flex-shrink-0 mt-0.5 mr-3 w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="mb-1 text-sm font-semibold text-blue-900">What happens next?</h4>
                        <p class="text-sm text-blue-800">
                            After submitting your RFQ, our suppliers will review your request and contact you directly via
                            the contact information you provided. You'll receive quotes and can choose the best option for
                            your needs.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
            rel="stylesheet" />
        <style>
            .select2-container--bootstrap-5 .select2-selection {
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
                min-height: 48px;
            }

            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
                padding: 0.75rem 1rem;
                line-height: 1.5;
            }

            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
                height: 46px;
            }

            .select2-container--bootstrap-5 .select2-dropdown {
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
            }

            .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
                padding: 0.5rem;
            }
        </style>
    @endpush

    @push('scripts')
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                // Initialize Select2 on all dropdowns
                $('#product_id, #business_id, #category_id, #country_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: function() {
                        return $(this).data('placeholder') || 'Select an option';
                    },
                    allowClear: true,
                    width: '100%'
                });

                // Set placeholders
                $('#product_id').attr('data-placeholder', 'Select a product');
                $('#business_id').attr('data-placeholder', 'Select a business');
                $('#category_id').attr('data-placeholder', 'Select a category');
                $('#country_id').attr('data-placeholder', 'Select Country');

                // Validate message length on form submission
                $('form').on('submit', function(e) {
                    var messageText = $('#message').val().trim();
                    if (messageText.length < 10) {
                        e.preventDefault();
                        alert('Please provide at least 10 characters in your message.');
                        return false;
                    }
                });
            });
        </script>
    @endpush
@endsection
