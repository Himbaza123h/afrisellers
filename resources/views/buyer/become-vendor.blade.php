@extends('layouts.app')

@section('content')

    <div class="min-h-screen bg-white">
        <div class="px-6 py-12 mx-auto max-w-2xl">

            <!-- Header -->
            <div class="mb-10">
                <h1 class="mb-4 text-4xl font-bold text-gray-900">Become a Vendor</h1>
                <p class="mb-8 text-lg text-gray-600">Start selling your products on AfriSellers and reach thousands of
                    buyers across Africa.</p>

                <!-- Benefits -->
                <div class="flex flex-wrap gap-y-2 gap-x-8 text-sm text-gray-600">
                    <div class="flex items-center">
                        <svg class="mr-2 w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Quick Approval Process
                    </div>
                    <div class="flex items-center">
                        <svg class="mr-2 w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        24/7 Support
                    </div>
                    <div class="flex items-center">
                        <svg class="mr-2 w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Access to Thousands of Buyers
                    </div>
                </div>
            </div>

            <!-- Account Summary -->
            <div class="p-4 mb-8 bg-gray-50 rounded-lg border border-gray-200">
                <p class="mb-1 text-xs text-gray-600">Registering as</p>
                <p class="font-medium text-gray-900">{{ auth()->user()->name }}</p>
                <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-4 mb-6 bg-gray-50 rounded-lg border border-gray-300">
                    <p class="mb-2 text-sm font-medium text-red-900">{{ __('messages.fix_errors') }}</p>
                    <ul class="space-y-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success Message -->
            @if (session('success'))
                <div class="p-4 mb-6 bg-green-50 rounded-lg border border-green-300">
                    <p class="text-sm text-green-900">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('buyer.become-vendor.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Business Name -->
                <div>
                    <label for="business_name" class="block mb-2 text-sm font-medium text-gray-900">
                        Business Name
                    </label>
                    <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}"
                        class="px-4 py-3 w-full text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        placeholder="Enter your business name" required>
                    <p class="mt-1.5 text-xs text-gray-500">The official name of your business</p>
                </div>

                <!-- Business Registration Number -->
                <div>
                    <label for="business_registration_number" class="block mb-2 text-sm font-medium text-gray-900">
                        Business Registration Number
                    </label>
                    <input type="text" name="business_registration_number" id="business_registration_number"
                        value="{{ old('business_registration_number') }}"
                        class="px-4 py-3 w-full text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        placeholder="Enter registration number" required>
                    <p class="mt-1.5 text-xs text-gray-500">Your official business registration or license number</p>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">
                        Business Phone Number
                    </label>
                    <div class="flex gap-2">
                        <select name="phone_code"
                            class="px-3 py-3 text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <option value="+250" {{ old('phone_code') == '+250' ? 'selected' : '' }}>+250</option>
                            <option value="+254" {{ old('phone_code') == '+254' ? 'selected' : '' }}>+254</option>
                            <option value="+255" {{ old('phone_code') == '+255' ? 'selected' : '' }}>+255</option>
                            <option value="+256" {{ old('phone_code') == '+256' ? 'selected' : '' }}>+256</option>
                            <option value="+234" {{ old('phone_code') == '+234' ? 'selected' : '' }}>+234</option>
                            <option value="+233" {{ old('phone_code') == '+233' ? 'selected' : '' }}>+233</option>
                            <option value="+27" {{ old('phone_code') == '+27' ? 'selected' : '' }}>+27</option>
                        </select>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                            class="flex-1 px-4 py-3 text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                            placeholder="Business phone number" required>
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block mb-2 text-sm font-medium text-gray-900">
                        Business Location
                    </label>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <select name="country_id" id="country_id"
                            class="px-4 py-3 w-full text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                            required>
                            <option value="">Select Country</option>
                            @foreach ($countries ?? [] as $country)
                                <option value="{{ $country->id }}"
                                    {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="city" id="city" value="{{ old('city') }}"
                            class="px-4 py-3 w-full text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                            placeholder="City" required>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex flex-col-reverse gap-3 pt-4 sm:flex-row">
                    <a href="{{ route('buyer.dashboard.home') }}"
                        class="px-8 py-3 font-medium text-center text-gray-900 bg-white rounded-lg border border-gray-300 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="flex-1 px-8 py-3 bg-[#ff0808] text-white font-medium rounded-lg hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors sm:flex-initial">
                        Next: Upload Documents
                    </button>
                </div>
            </form>

            <!-- Info -->
            <div class="pt-8 mt-8 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Your application will be reviewed by our team. You'll receive an email notification once your vendor
                    account is approved.
                </p>
            </div>
        </div>
    </div>

    <style>
        input:focus,
        select:focus {
            --tw-ring-color: #111827;
        }
    </style>
@endsection
