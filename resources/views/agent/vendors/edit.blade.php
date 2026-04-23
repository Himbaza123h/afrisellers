@extends('layouts.home')

@section('page-content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.vendors.show', $vendor->id) }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Vendor</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ $vendor->businessProfile?->business_name }}</p>
        </div>
    </div>

    {{-- Success --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-xl border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-500 mt-0.5 flex-shrink-0"></i>
            <p class="text-sm font-semibold text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-400 hover:text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Session Error --}}
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-xl border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 flex-shrink-0"></i>
            <p class="text-sm font-semibold text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-xl border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 flex-shrink-0"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-900 mb-2">Please fix the following errors:</p>
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="flex items-center gap-1.5"><i class="fas fa-circle text-[5px] text-red-400"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('agent.vendors.update', $vendor->id) }}" method="POST" class="space-y-6" id="vendor-edit-form">
        @csrf @method('PUT')

        {{-- ══════════════════════════════════════════════════════════
             SECTION 1 — Contact Person
        ══════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
                <i class="fas fa-user text-blue-600"></i> Contact Person
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $vendor->user?->name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 bg-red-50 @enderror"
                        placeholder="e.g. John Doe">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" value="{{ $vendor->user?->email }}" disabled
                        class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-400">Email cannot be changed here.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $vendor->businessProfile?->phone) }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('phone') border-red-400 bg-red-50 @enderror"
                        placeholder="+250 78x xxx xxx">
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Contact Person Position</label>
                    <input type="text" name="contact_person_position" value="{{ old('contact_person_position', $vendor->businessProfile?->contact_person_position) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. CEO, Manager">
                    @error('contact_person_position')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $vendor->businessProfile?->whatsapp_number) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="+250 78x xxx xxx">
                    @error('whatsapp_number')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SECTION 2 — Business Information
        ══════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
                <i class="fas fa-store text-purple-600"></i> Business Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Business Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="business_name" value="{{ old('business_name', $vendor->businessProfile?->business_name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('business_name') border-red-400 bg-red-50 @enderror"
                        placeholder="e.g. Acme Trading Ltd">
                    @error('business_name')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Business Type</label>
                    <select name="business_type"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Select type</option>
                        @foreach(['Manufacturer','Wholesaler','Retailer','Distributor','Exporter','Importer','Trader','Other'] as $type)
                            <option value="{{ $type }}" {{ old('business_type', $vendor->businessProfile?->business_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Business Reg. Number</label>
                    <input type="text" name="business_registration_number" value="{{ old('business_registration_number', $vendor->businessProfile?->business_registration_number) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. RDB/2024/001234">
                    @error('business_registration_number')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tax ID / TIN</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $vendor->businessProfile?->tax_id) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. 102345678">
                    @error('tax_id')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Year Established</label>
                    <input type="number" name="year_established" value="{{ old('year_established', $vendor->businessProfile?->year_established) }}"
                        min="1900" max="{{ date('Y') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="{{ date('Y') }}">
                    @error('year_established')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Company Size</label>
                    <select name="company_size"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Select size</option>
                        @foreach(['1-10','11-50','51-200','201-500','501-1000','1000+'] as $size)
                            <option value="{{ $size }}" {{ old('company_size', $vendor->businessProfile?->company_size) == $size ? 'selected' : '' }}>{{ $size }} employees</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Annual Revenue (USD)</label>
                    <select name="annual_revenue"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Select range</option>
                        @foreach(['< $50K','$50K – $200K','$200K – $1M','$1M – $5M','$5M – $20M','> $20M'] as $rev)
                            <option value="{{ $rev }}" {{ old('annual_revenue', $vendor->businessProfile?->annual_revenue) == $rev ? 'selected' : '' }}>{{ $rev }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Country with search --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Country <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="country-search-edit" placeholder="Search country..."
                            autocomplete="off"
                            value="{{ $vendor->businessProfile?->country?->name }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 @error('country_id') border-red-400 bg-red-50 @enderror"
                            oninput="filterCountriesEdit(this.value)"
                            onfocus="document.getElementById('country-dropdown-edit').classList.remove('hidden')">
                        <div id="country-dropdown-edit"
                            class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            @foreach($countries as $country)
                                <div class="country-option-edit px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 cursor-pointer"
                                    data-value="{{ $country->id }}"
                                    data-label="{{ $country->name }}"
                                    data-search="{{ strtolower($country->name) }}"
                                    onclick="selectCountryEdit(this)">
                                    {{ $country->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" name="country_id" id="country-id-edit"
                        value="{{ old('country_id', $vendor->businessProfile?->country_id) }}" required>
                    @error('country_id')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">City</label>
                    <input type="text" name="city" value="{{ old('city', $vendor->businessProfile?->city) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. Kigali">
                    @error('city')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Postal Code</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $vendor->businessProfile?->postal_code) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. 00100">
                    @error('postal_code')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Address</label>
                    <input type="text" name="address" value="{{ old('address', $vendor->businessProfile?->address) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="Full business address">
                    @error('address')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Website</label>
                    <input type="url" name="website" value="{{ old('website', $vendor->businessProfile?->website) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="https://example.com">
                    @error('website')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Business Email</label>
                    <input type="email" name="business_email" value="{{ old('business_email', $vendor->businessProfile?->business_email) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="info@business.com">
                    <p class="mt-1 text-xs text-gray-400">Public-facing business email (can differ from login email).</p>
                    @error('business_email')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Operating Hours</label>
                    <input type="text" name="operating_hours" value="{{ old('operating_hours', $vendor->businessProfile?->operating_hours) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. Mon–Fri, 8am–5pm">
                    @error('operating_hours')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Languages Spoken</label>
                    <input type="text" name="languages_spoken" value="{{ old('languages_spoken', $vendor->businessProfile?->languages_spoken) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. English, French, Kinyarwanda">
                    @error('languages_spoken')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="Brief description of the business…">{{ old('description', $vendor->businessProfile?->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SECTION 3 — Operations & Trade
        ══════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
                <i class="fas fa-industry text-green-600"></i> Operations &amp; Trade
                <span class="ml-auto text-xs font-normal text-gray-400 normal-case">Optional</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Main Products / Services</label>
                    <input type="text" name="main_products" value="{{ old('main_products', $vendor->businessProfile?->main_products) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. Coffee, Tea, Spices">
                    @error('main_products')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Export Markets</label>
                    <input type="text" name="export_markets" value="{{ old('export_markets', $vendor->businessProfile?->export_markets) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. Europe, USA, Middle East">
                    @error('export_markets')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Production Capacity</label>
                    <input type="text" name="production_capacity" value="{{ old('production_capacity', $vendor->businessProfile?->production_capacity) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. 500 MT/month">
                    @error('production_capacity')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Minimum Order Value (USD)</label>
                    <input type="number" name="minimum_order_value" value="{{ old('minimum_order_value', $vendor->businessProfile?->minimum_order_value) }}" min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. 500">
                    @error('minimum_order_value')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Payment Terms</label>
                    <input type="text" name="payment_terms" value="{{ old('payment_terms', $vendor->businessProfile?->payment_terms) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. 30% upfront, 70% on delivery">
                    @error('payment_terms')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Delivery Time</label>
                    <input type="text" name="delivery_time" value="{{ old('delivery_time', $vendor->businessProfile?->delivery_time) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. 7–14 business days">
                    @error('delivery_time')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Quality Control Process</label>
                    <textarea name="quality_control" rows="2"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="Describe quality control measures…">{{ old('quality_control', $vendor->businessProfile?->quality_control) }}</textarea>
                    @error('quality_control')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Certifications</label>
                    <input type="text" name="certifications" value="{{ old('certifications', $vendor->businessProfile?->certifications) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. ISO 9001, Organic, Fair Trade">
                    @error('certifications')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             SECTION 4 — Social Media & Online Presence
        ══════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
                <i class="fas fa-share-alt text-pink-500"></i> Social Media &amp; Online Presence
                <span class="ml-auto text-xs font-normal text-gray-400 normal-case">Optional</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        <i class="fab fa-facebook text-blue-600 mr-1"></i>Facebook
                    </label>
                    <input type="url" name="facebook_link" value="{{ old('facebook_link', $vendor->businessProfile?->facebook_link) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="https://facebook.com/yourpage">
                    @error('facebook_link')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        <i class="fab fa-twitter text-sky-500 mr-1"></i>Twitter / X
                    </label>
                    <input type="url" name="twitter_link" value="{{ old('twitter_link', $vendor->businessProfile?->twitter_link) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="https://twitter.com/yourhandle">
                    @error('twitter_link')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        <i class="fab fa-linkedin text-blue-700 mr-1"></i>LinkedIn
                    </label>
                    <input type="url" name="linkedin_link" value="{{ old('linkedin_link', $vendor->businessProfile?->linkedin_link) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="https://linkedin.com/company/yourco">
                    @error('linkedin_link')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        <i class="fab fa-instagram text-pink-500 mr-1"></i>Instagram
                    </label>
                    <input type="url" name="instagram_link" value="{{ old('instagram_link', $vendor->businessProfile?->instagram_link) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="https://instagram.com/yourprofile">
                    @error('instagram_link')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        <i class="fab fa-youtube text-red-600 mr-1"></i>YouTube
                    </label>
                    <input type="url" name="youtube_link" value="{{ old('youtube_link', $vendor->businessProfile?->youtube_link) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="https://youtube.com/@yourchannel">
                    @error('youtube_link')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('agent.vendors.show', $vendor->id) }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" id="submit-btn"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md disabled:opacity-70 disabled:cursor-not-allowed transition-all">
                {{-- Default state --}}
                <span id="btn-default" class="inline-flex items-center gap-2">
                    <i class="fas fa-save"></i> Save Changes
                </span>
                {{-- Loading state --}}
                <span id="btn-loading" class="hidden inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Saving Changes…
                </span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// ── Country search ──────────────────────────────────────────────
function filterCountriesEdit(query) {
    const q = query.toLowerCase();
    document.getElementById('country-dropdown-edit').classList.remove('hidden');
    document.querySelectorAll('.country-option-edit').forEach(opt => {
        opt.style.display = opt.dataset.search.includes(q) ? '' : 'none';
    });
}

function selectCountryEdit(el) {
    document.getElementById('country-search-edit').value = el.dataset.label;
    document.getElementById('country-id-edit').value = el.dataset.value;
    document.getElementById('country-dropdown-edit').classList.add('hidden');
}

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('country-dropdown-edit');
    const input = document.getElementById('country-search-edit');
    if (dropdown && !dropdown.contains(e.target) && e.target !== input) {
        dropdown.classList.add('hidden');
    }
});

// ── Submit button loader ────────────────────────────────────────
document.getElementById('vendor-edit-form').addEventListener('submit', function () {
    const btn        = document.getElementById('submit-btn');
    const btnDefault = document.getElementById('btn-default');
    const btnLoading = document.getElementById('btn-loading');

    btn.disabled = true;
    btnDefault.classList.add('hidden');
    btnLoading.classList.remove('hidden');

    // Safety: re-enable after 15 s in case of network error / slow server
    setTimeout(function () {
        btn.disabled = false;
        btnDefault.classList.remove('hidden');
        btnLoading.classList.add('hidden');
    }, 15000);
});
</script>
@endpush

@endsection
