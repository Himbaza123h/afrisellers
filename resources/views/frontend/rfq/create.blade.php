@extends('layouts.app')

@section('title', 'Send RFQ - Request for Quotation')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.snow.css" rel="stylesheet">
<style>
    .slide-up {
        animation: slideUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    @keyframes slideUp {
        to { opacity: 1; transform: translateY(0); }
    }

    .tab-content { display: none; }
    .tab-content.active {
        display: block;
        animation: slideUp 0.4s ease-out;
    }

    .tab-button {
        position: relative;
        transition: all 0.3s ease;
        padding: 12px 20px;
        border-bottom: 3px solid transparent;
        font-size: 12px;
        font-weight: 500;
        color: #6b7280;
    }
    .tab-button:hover { background: #f9fafb; color: #374151; }
    .tab-button.active {
        color: #ff0808;
        font-weight: 600;
        border-bottom-color: #ff0808;
        background: #fff5f5;
    }

    .ql-editor { min-height: 150px; font-size: 14px; line-height: 1.6; }
    .ql-toolbar.ql-snow {
        border-radius: 8px 8px 0 0;
        background: #f9fafb;
        border-color: #d1d5db;
    }
    .ql-container.ql-snow {
        border-radius: 0 0 8px 8px;
        border-color: #d1d5db;
    }

    .form-section {
        opacity: 0;
        transform: translateY(20px);
        animation: slideUp 0.5s ease-out forwards;
    }
    .form-section:nth-child(1) { animation-delay: 0.05s; }
    .form-section:nth-child(2) { animation-delay: 0.1s; }
    .form-section:nth-child(3) { animation-delay: 0.15s; }
    .form-section:nth-child(4) { animation-delay: 0.2s; }
    .form-section:nth-child(5) { animation-delay: 0.25s; }
    .form-section:nth-child(6) { animation-delay: 0.3s; }

    /* Translation bar */
    .translate-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        padding: 8px 12px;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        margin-bottom: 8px;
    }
    .translate-bar select {
        padding: 4px 10px;
        font-size: 12px;
        border: 1px solid #bae6fd;
        border-radius: 6px;
        background: #fff;
        color: #0369a1;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }
    .btn-translate {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        background: #0284c7;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-translate:hover:not(:disabled) { background: #0369a1; }
    .btn-translate:disabled { opacity: 0.6; cursor: not-allowed; }
    .btn-translate .spinner {
        display: none;
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255,255,255,0.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    .btn-translate.loading .spinner { display: inline-block; }
    .btn-translate.loading .btn-translate-icon { display: none; }

    @keyframes spin { to { transform: rotate(360deg); } }

    .translate-status { font-size: 11px; color: #6b7280; margin-left: auto; }
    .translate-status.success { color: #16a34a; }
    .translate-status.error   { color: #dc2626; }

    /* ─── Submit Loading Overlay ─────────────────────────────────────── */
    #rfq-loading-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 20px;
    }
    #rfq-loading-overlay.visible {
        display: flex;
        animation: overlayFadeIn 0.25s ease-out;
    }

    @keyframes overlayFadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .rfq-spinner-ring {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: 4px solid rgba(255,255,255,0.15);
        border-top-color: #ff0808;
        border-right-color: #ff6060;
        animation: ringSpinFast 0.75s linear infinite;
    }

    @keyframes ringSpinFast {
        to { transform: rotate(360deg); }
    }

    .rfq-loading-card {
        background: #fff;
        border-radius: 16px;
        padding: 32px 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.35);
        min-width: 260px;
        text-align: center;
    }

    .rfq-loading-title {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        letter-spacing: -0.01em;
    }

    .rfq-loading-sub {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.5;
    }

    .rfq-loading-dots {
        display: flex;
        gap: 6px;
        margin-top: 4px;
    }
    .rfq-loading-dots span {
        width: 7px;
        height: 7px;
        background: #ff0808;
        border-radius: 50%;
        animation: dotBounce 1.2s ease-in-out infinite;
        opacity: 0.3;
    }
    .rfq-loading-dots span:nth-child(1) { animation-delay: 0s; }
    .rfq-loading-dots span:nth-child(2) { animation-delay: 0.2s; }
    .rfq-loading-dots span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes dotBounce {
        0%, 80%, 100% { transform: scale(1); opacity: 0.3; }
        40%           { transform: scale(1.4); opacity: 1; }
    }

    /* Submit button loading state */
    #submitBtn.submitting {
        opacity: 0.7;
        pointer-events: none;
        cursor: not-allowed;
    }
    #submitBtn .submit-spinner {
        display: none;
        width: 13px;
        height: 13px;
        border: 2px solid rgba(255,255,255,0.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    #submitBtn.submitting .submit-spinner { display: inline-block; }
    #submitBtn.submitting .submit-icon   { display: none; }
</style>
@endpush

@section('content')

    {{-- ─── Full-screen loading overlay ──────────────────────────────────── --}}
    <div id="rfq-loading-overlay" role="status" aria-live="polite" aria-label="Submitting your RFQ">
        <div class="rfq-loading-card">
            <div class="rfq-spinner-ring"></div>
            <p class="rfq-loading-title">Submitting your RFQ…</p>
            <p class="rfq-loading-sub">Please wait while we send<br>your request to suppliers.</p>
            <div class="rfq-loading-dots">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <div class="py-6 min-h-screen bg-gray-50">
        <div class="container px-4 mx-auto max-w-8xl">

            <!-- Page Header -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between slide-up mb-6">
                <div class="flex items-center gap-3">
                    <a href="{{ url()->previous() }}"
                       class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-all hover:scale-105">
                        <i class="fas fa-arrow-left text-base"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Send Request for Quotation (RFQ)</h1>
                        <p class="mt-0.5 text-xs text-gray-500">Fill out the form to request quotations from suppliers</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                        <i class="fas fa-file-invoice mr-1"></i> New RFQ
                    </span>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="p-3 mb-4 bg-green-50 rounded-lg border border-green-200 slide-up">
                    <div class="flex items-center">
                        <svg class="mr-2 w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs font-medium text-green-900">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-3 mb-4 bg-red-50 rounded-xl border border-red-200 flex items-start gap-3 slide-up shadow-sm">
                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5 text-base"></i>
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-red-900 mb-2">Please fix the following errors:</p>
                        <ul class="text-xs text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-circle text-[6px] mt-1"></i>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="this.parentElement.remove()"
                            class="text-red-600 hover:text-red-800 transition-colors p-1">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden slide-up" style="animation-delay: 0.2s;">
                <form action="{{ route('rfqs.store') }}" method="POST" id="rfqForm" class="space-y-5">
                    @csrf

                    <!-- Tabs Navigation -->
                    <div class="border-b border-gray-200 bg-gray-50">
                        <div class="flex overflow-x-auto">
                            <button type="button" class="tab-button active" onclick="switchTab(0)">
                                <i class="fas fa-info-circle mr-2"></i>Request Details
                            </button>
                            <button type="button" class="tab-button" onclick="switchTab(1)">
                                <i class="fas fa-user mr-2"></i>Contact Info
                            </button>
                            <button type="button" class="tab-button" onclick="switchTab(2)">
                                <i class="fas fa-map-marker-alt mr-2"></i>Location
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-5">

                        <!-- Tab 0: Request Details -->
                        <div class="tab-content active" id="tab-0">
                            <div class="form-section p-3 mb-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-xs font-semibold text-yellow-900">
                                    <i class="mr-1 fas fa-info-circle"></i>Please select at least one option below:
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-4">
                                <!-- Product -->
                                <div class="form-section">
                                    <label for="product_id" class="block mb-1.5 text-xs font-semibold text-gray-700">
                                        <i class="fas fa-box text-blue-500 mr-1"></i>Product
                                    </label>
                                    <select name="product_id" id="product_id"
                                        class="select2-dropdown w-full px-3 py-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                        <option value="">Select a product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <!-- Business -->
                                <div class="form-section">
                                    <label for="business_id" class="block mb-1.5 text-xs font-semibold text-gray-700">
                                        <i class="fas fa-building text-purple-500 mr-1"></i>Business/Supplier
                                    </label>
                                    <select name="business_id" id="business_id"
                                        class="select2-dropdown w-full px-3 py-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                        <option value="">Select a business</option>
                                        @foreach ($businessProfiles as $business)
                                            <option value="{{ $business->id }}" {{ old('business_id') == $business->id ? 'selected' : '' }}>
                                                {{ $business->business_name }}@if($business->country) - {{ $business->country->name }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('business_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <!-- Category -->
                                <div class="form-section">
                                    <label for="category_id" class="block mb-1.5 text-xs font-semibold text-gray-700">
                                        <i class="fas fa-folder text-yellow-500 mr-1"></i>Product Category
                                    </label>
                                    <select name="category_id" id="category_id"
                                        class="select2-dropdown w-full px-3 py-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                        <option value="">Select a category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <p class="form-section mb-4 text-xs text-yellow-800 bg-yellow-50 p-2 rounded border border-yellow-200">
                                <i class="mr-1 fas fa-exclamation-triangle"></i>You must select at least one: Product, Business, or Category.
                            </p>

                            <!-- Message + Translation Bar -->
                            <div class="form-section">
                                <label class="block mb-1.5 text-xs font-semibold text-gray-700">
                                    <i class="fas fa-align-left text-indigo-500 mr-1"></i>
                                    Message <span class="text-red-500">*</span>
                                </label>

                                <div class="translate-bar">
                                    <span class="text-xs font-semibold text-sky-700">
                                        <i class="fas fa-language mr-1"></i> Translate to:
                                    </span>
                                    <select id="translateTargetLang">
                                        <option value="">— choose language —</option>
                                        <option value="french">🇫🇷 French</option>
                                        <option value="swahili">🇹🇿 Kiswahili</option>
                                        <option value="english">🇬🇧 English</option>
                                        <option value="arabic">🇸🇦 Arabic</option>
                                        <option value="portuguese">🇧🇷 Portuguese</option>
                                        <option value="spanish">🇪🇸 Spanish</option>
                                    </select>
                                    <button type="button" class="btn-translate" id="btnTranslate" onclick="translateMessage()" disabled>
                                        <span class="btn-translate-icon"><i class="fas fa-exchange-alt"></i></span>
                                        <span class="spinner"></span>
                                        Translate
                                    </button>
                                    <span class="translate-status" id="translateStatus"></span>
                                    <span class="text-[10px] text-gray-400 ml-1">Type in any language, translate instantly</span>
                                </div>

                                <div id="message-editor" class="bg-white"></div>
                                <textarea name="message" id="message" class="hidden" required>{{ old('message') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Minimum 10 characters. Right-click underlined words to fix spelling.
                                </p>
                                @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- Tab 1: Contact Information -->
                        <div class="tab-content" id="tab-1">
                            <h3 class="mb-4 text-base font-semibold text-gray-900 flex items-center gap-2 pb-3 border-b">
                                <i class="fas fa-user-circle text-blue-600"></i>Contact Information
                            </h3>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4">
                                <div class="form-section">
                                    <label for="name" class="block mb-1.5 text-xs font-semibold text-gray-700">
                                        <i class="fas fa-user text-blue-500 mr-1"></i>
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $userData['name'] ?? '') }}"
                                        autocomplete="name" spellcheck="true"
                                        class="w-full px-3 py-2 text-sm text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all"
                                        placeholder="Your full name" required>
                                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div class="form-section">
                                    <label for="email" class="block mb-1.5 text-xs font-semibold text-gray-700">
                                        <i class="fas fa-envelope text-purple-500 mr-1"></i>
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $userData['email'] ?? '') }}"
                                        autocomplete="email"
                                        class="w-full px-3 py-2 text-sm text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all"
                                        placeholder="your.email@example.com" required>
                                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="form-section mb-4">
                                <label for="phone" class="block mb-1.5 text-xs font-semibold text-gray-700">
                                    <i class="fas fa-phone text-green-500 mr-1"></i>
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <select name="phone_code"
                                        class="px-2 py-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                        required>
                                        <option value="+250" {{ old('phone_code','+250') == '+250' ? 'selected' : '' }}>+250 RW</option>
                                        <option value="+254" {{ old('phone_code') == '+254' ? 'selected' : '' }}>+254 KE</option>
                                        <option value="+255" {{ old('phone_code') == '+255' ? 'selected' : '' }}>+255 TZ</option>
                                        <option value="+256" {{ old('phone_code') == '+256' ? 'selected' : '' }}>+256 UG</option>
                                        <option value="+234" {{ old('phone_code') == '+234' ? 'selected' : '' }}>+234 NG</option>
                                        <option value="+233" {{ old('phone_code') == '+233' ? 'selected' : '' }}>+233 GH</option>
                                        <option value="+27"  {{ old('phone_code') == '+27'  ? 'selected' : '' }}>+27 ZA</option>
                                        <option value="+243" {{ old('phone_code') == '+243' ? 'selected' : '' }}>+243 CD</option>
                                        <option value="+251" {{ old('phone_code') == '+251' ? 'selected' : '' }}>+251 ET</option>
                                        <option value="+1"   {{ old('phone_code') == '+1'   ? 'selected' : '' }}>+1 US</option>
                                        <option value="+44"  {{ old('phone_code') == '+44'  ? 'selected' : '' }}>+44 UK</option>
                                        <option value="+33"  {{ old('phone_code') == '+33'  ? 'selected' : '' }}>+33 FR</option>
                                    </select>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                        autocomplete="tel"
                                        class="flex-1 px-3 py-2 text-sm text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all"
                                        placeholder="Phone number" required>
                                </div>
                                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- Tab 2: Location -->
                        <div class="tab-content" id="tab-2">
                            <h3 class="mb-4 text-base font-semibold text-gray-900 flex items-center gap-2 pb-3 border-b">
                                <i class="fas fa-map-marked-alt text-green-600"></i>Location Information
                            </h3>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4">
                                <div class="form-section">
                                    <label for="country_id" class="block mb-1.5 text-xs font-semibold text-gray-700">
                                        <i class="fas fa-globe text-blue-500 mr-1"></i>
                                        Country <span class="text-red-500">*</span>
                                    </label>
                                    <select name="country_id" id="country_id"
                                        class="select2-dropdown w-full px-3 py-2 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                        required>
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div class="form-section">
                                    <label for="city" class="block mb-1.5 text-xs font-semibold text-gray-700">
                                        <i class="fas fa-city text-purple-500 mr-1"></i>
                                        City <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                                        spellcheck="true"
                                        class="w-full px-3 py-2 text-sm text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all"
                                        placeholder="Your city" required>
                                    @error('city')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="form-section mb-4">
                                <label for="address" class="block mb-1.5 text-xs font-semibold text-gray-700">
                                    <i class="fas fa-map-pin text-yellow-500 mr-1"></i>
                                    Address (Optional)
                                </label>
                                <input type="text" name="address" id="address" value="{{ old('address') }}"
                                    spellcheck="true" autocomplete="street-address"
                                    class="w-full px-3 py-2 text-sm text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent transition-all"
                                    placeholder="Street address, building, etc.">
                                @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="bg-gray-50 px-5 py-4 border-t border-gray-200 flex gap-3 justify-between items-center">
                        <div>
                            <button type="button" id="prevBtn" onclick="previousTab()"
                                class="hidden inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" id="nextBtn" onclick="nextTab()"
                                class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all shadow-sm">
                                Next <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                            <button type="submit" id="submitBtn"
                                class="hidden inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#e00606] transition-all shadow-md hover:shadow-lg">
                                <span class="submit-icon"><i class="fas fa-paper-plane"></i></span>
                                <span class="submit-spinner"></span>
                                Submit RFQ
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="p-4 mt-5 bg-blue-50 rounded-lg border border-blue-200 slide-up" style="animation-delay: 0.3s;">
                <div class="flex items-start">
                    <svg class="flex-shrink-0 mt-0.5 mr-2 w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="mb-1 text-xs font-semibold text-blue-900">What happens next?</h4>
                        <p class="text-xs text-blue-800">
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
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>
        <style>
            .select2-container--bootstrap-5 .select2-selection { border:1px solid #d1d5db; border-radius:.5rem; min-height:38px; }
            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { padding:.5rem .75rem; line-height:1.5; font-size:.875rem; }
            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { height:36px; }
            .select2-container--bootstrap-5 .select2-dropdown { border:1px solid #d1d5db; border-radius:.5rem; }
            .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field { border:1px solid #d1d5db; border-radius:.5rem; padding:.375rem; font-size:.875rem; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.js"></script>

        <script>
            let currentTab = 0;
            let messageQuill;

            // ── Tab Switching ────────────────────────────────────────────────
            function switchTab(index) {
                currentTab = index;
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                document.querySelectorAll('.tab-button')[index].classList.add('active');
                document.getElementById('tab-' + index).classList.add('active');
                updateNavigationButtons();
                localStorage.setItem('activeRFQTab', index);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function nextTab()     { if (currentTab < 2) switchTab(currentTab + 1); }
            function previousTab() { if (currentTab > 0) switchTab(currentTab - 1); }

            function updateNavigationButtons() {
                document.getElementById('prevBtn').classList.toggle('hidden', currentTab === 0);
                document.getElementById('nextBtn').classList.toggle('hidden', currentTab === 2);
                document.getElementById('submitBtn').classList.toggle('hidden', currentTab !== 2);
            }

            // ── Translation ──────────────────────────────────────────────────
            function translateMessage() {
                const btn      = document.getElementById('btnTranslate');
                const statusEl = document.getElementById('translateStatus');
                const lang     = document.getElementById('translateTargetLang').value;

                if (!lang) {
                    statusEl.textContent = 'Please choose a language first.';
                    statusEl.className   = 'translate-status error';
                    return;
                }

                const plainText = messageQuill.getText().trim();
                if (plainText.length < 3) {
                    statusEl.textContent = 'Please write something first.';
                    statusEl.className   = 'translate-status error';
                    return;
                }

                btn.disabled = true;
                btn.classList.add('loading');
                statusEl.textContent = 'Translating…';
                statusEl.className   = 'translate-status';

                fetch('{{ route("rfqs.translate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ text: messageQuill.root.innerHTML, target_language: lang }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.translated) {
                        messageQuill.root.innerHTML = data.translated;
                        document.getElementById('message').value = messageQuill.root.innerHTML;
                        statusEl.textContent = '✓ Translated to ' + lang.charAt(0).toUpperCase() + lang.slice(1);
                        statusEl.className   = 'translate-status success';
                    } else {
                        statusEl.textContent = data.message || 'Translation failed.';
                        statusEl.className   = 'translate-status error';
                    }
                })
                .catch(() => {
                    statusEl.textContent = 'Network error. Please try again.';
                    statusEl.className   = 'translate-status error';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.classList.remove('loading');
                });
            }

            document.getElementById('translateTargetLang').addEventListener('change', function () {
                document.getElementById('btnTranslate').disabled = !this.value;
                document.getElementById('translateStatus').textContent = '';
            });

            // ── Loading Overlay Helpers ──────────────────────────────────────
            function showLoadingOverlay() {
                document.getElementById('rfq-loading-overlay').classList.add('visible');
                const btn = document.getElementById('submitBtn');
                btn.classList.add('submitting');
                btn.querySelector('.submit-spinner').style.display = 'inline-block';
            }

            function hideLoadingOverlay() {
                document.getElementById('rfq-loading-overlay').classList.remove('visible');
                const btn = document.getElementById('submitBtn');
                btn.classList.remove('submitting');
            }

            // ── DOM Ready ────────────────────────────────────────────────────
            $(document).ready(function () {

                // Init Quill
                messageQuill = new Quill('#message-editor', {
                    theme: 'snow',
                    placeholder: 'Describe your requirements, quantity needed, delivery timeline, and any other relevant details…',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ header: [1, 2, 3, false] }],
                            ['link'],
                            ['clean'],
                        ],
                    },
                });

                messageQuill.root.setAttribute('spellcheck', true);

                const msgVal = document.getElementById('message').value;
                if (msgVal) messageQuill.root.innerHTML = msgVal;

                messageQuill.on('text-change', function () {
                    document.getElementById('message').value = messageQuill.root.innerHTML;
                    const s = document.getElementById('translateStatus');
                    if (s.classList.contains('success')) {
                        s.textContent = '';
                        s.className   = 'translate-status';
                    }
                });

                // Init Select2
                $('#product_id').select2({ theme:'bootstrap-5', placeholder:'Select a product',  allowClear:true, width:'100%' });
                $('#business_id').select2({ theme:'bootstrap-5', placeholder:'Select a business', allowClear:true, width:'100%' });
                $('#category_id').select2({ theme:'bootstrap-5', placeholder:'Select a category', allowClear:true, width:'100%' });
                $('#country_id').select2({  theme:'bootstrap-5', placeholder:'Select Country',    allowClear:true, width:'100%' });

                // Form submit → validate + show loading overlay
                $('#rfqForm').on('submit', function (e) {
                    // Sync Quill textarea
                    document.getElementById('message').value = messageQuill.root.innerHTML;

                    const msgText = messageQuill.getText().trim();
                    if (msgText.length < 10) {
                        e.preventDefault();
                        alert('Please provide at least 10 characters in your message.');
                        switchTab(0);
                        return false;
                    }

                    // Show overlay — form will submit normally to server
                    showLoadingOverlay();

                    // Safety: hide overlay if browser navigates away or back-button is pressed
                    window.addEventListener('pagehide', hideLoadingOverlay);
                });

                // Restore last active tab
                const savedTab = parseInt(localStorage.getItem('activeRFQTab')) || 0;
                switchTab(savedTab);
            });
        </script>
    @endpush
@endsection
