@extends('layouts.home')

@section('page-content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.company.show') }}" class="hover:text-gray-600">Company Info</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Edit</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Edit Company Information</h1>
        <p class="text-xs text-gray-500 mt-0.5">Update your basic company details</p>
    </div>
    <a href="{{ route('partner.company.show') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('partner.company.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-5 pb-2 border-b border-gray-100">
                Basic Details
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name" value="{{ old('company_name', $partner?->company_name) }}" required
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                           placeholder="Your company name">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Trading Name</label>
                    <input type="text" name="trading_name" value="{{ old('trading_name', $partner?->trading_name) }}"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                           placeholder="Trading or brand name">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Business Registration Number</label>
                    <input type="text" name="registration_number" value="{{ old('registration_number', $partner?->registration_number) }}"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                           placeholder="e.g. RW-2024-001234">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Partnership Type</label>
                    <select name="partner_type"
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                        <option value="">Select type...</option>
                        @foreach(['Global Partner','Strategic Partner','Banking Partner','Logistics Partner','Technology Partner','Quality Partner','Development Partner','Other'] as $type)
                            <option value="{{ $type }}" {{ old('partner_type', $partner?->partner_type) === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Location & Web --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-5 pb-2 border-b border-gray-100">
                Location & Web
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Year Established</label>
                    <input type="number" name="established" value="{{ old('established', $partner?->established) }}"
                           min="1800" max="{{ date('Y') }}"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                           placeholder="e.g. 2010">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Country of Registration</label>
                    <input type="text" name="country" value="{{ old('country', $partner?->country) }}"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                           placeholder="e.g. Rwanda">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Physical Address</label>
                    <textarea name="physical_address" rows="2"
                              class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent resize-none"
                              placeholder="Street, City, Country">{{ old('physical_address', $partner?->physical_address) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Website URL</label>
                    <input type="url" name="website_url" value="{{ old('website_url', $partner?->website_url) }}"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                           placeholder="https://yourcompany.com">
                </div>
            </div>
        </div>

    </div>

    {{-- Submit --}}
    <div class="mt-4 flex items-center justify-end gap-3">
        <a href="{{ route('partner.company.show') }}"
           class="px-5 py-2.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">
            Cancel
        </a>
        <button type="submit"
                class="px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>

</form>

@endsection
