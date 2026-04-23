@extends('layouts.home')

@section('page-content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.profile.show') }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Business Profile</h1>
            <p class="text-xs text-gray-500 mt-0.5">Your business information visible to vendors</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('agent.profile.update-business') }}" method="POST"
          enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        {{-- Business Logo --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2 mb-4">
                <i class="fas fa-image text-purple-500"></i> Business Logo
            </h2>
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-xl bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center flex-shrink-0 overflow-hidden">
                    @if($businessProfile?->logo)
                        <img src="{{ Storage::url($businessProfile->logo) }}"
                             alt="Logo" class="w-full h-full object-cover" id="logo-preview">
                    @else
                        <i class="fas fa-building text-3xl text-gray-300" id="logo-placeholder"></i>
                        <img src="" alt="" class="hidden w-full h-full object-cover" id="logo-preview">
                    @endif
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Upload Logo
                        <span class="text-gray-400 font-normal">(JPG, PNG, WebP — max 2MB)</span>
                    </label>
                    <input type="file" name="logo" accept="image/*" id="logoInput"
                        onchange="previewLogo(this)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600
                               file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                               file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700
                               hover:file:bg-blue-100">
                    @error('logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i> Basic Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Business Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="business_name" required
                        value="{{ old('business_name', $businessProfile?->business_name) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @error('business_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Business Email
                    </label>
                    <input type="email" name="business_email"
                        value="{{ old('business_email', $businessProfile?->business_email) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @error('business_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Website
                    </label>
                    <input type="url" name="website" placeholder="https://"
                        value="{{ old('website', $businessProfile?->website) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @error('website')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Business Type
                    </label>
                    <input type="text" name="business_type" placeholder="e.g. Sole Proprietor, LLC"
                        value="{{ old('business_type', $businessProfile?->business_type) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Year Established
                    </label>
                    <input type="number" name="year_established" min="1900" max="{{ date('Y') }}"
                        placeholder="{{ date('Y') }}"
                        value="{{ old('year_established', $businessProfile?->year_established) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Company Size
                    </label>
                    <select name="company_size"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Select size</option>
                        @foreach(['1-10','11-50','51-200','201-500','500+'] as $size)
                            <option value="{{ $size }}"
                                {{ old('company_size', $businessProfile?->company_size) === $size ? 'selected' : '' }}>
                                {{ $size }} employees
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        About / Description
                    </label>
                    <textarea name="description" rows="4"
                        placeholder="Brief description of your business…"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 resize-none leading-relaxed">{{ old('description', $businessProfile?->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Contact & Location --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-red-500"></i> Contact & Location
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Phone Code
                    </label>
                    <input type="text" name="phone_code" placeholder="+1"
                        value="{{ old('phone_code', $businessProfile?->phone_code) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Phone Number
                    </label>
                    <input type="text" name="phone"
                        value="{{ old('phone', $businessProfile?->phone) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        WhatsApp Number
                    </label>
                    <input type="text" name="whatsapp_number"
                        value="{{ old('whatsapp_number', $businessProfile?->whatsapp_number) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Country
                    </label>
                    <select name="country_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Select country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ old('country_id', $businessProfile?->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        City
                    </label>
                    <input type="text" name="city"
                        value="{{ old('city', $businessProfile?->city) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Postal Code
                    </label>
                    <input type="text" name="postal_code"
                        value="{{ old('postal_code', $businessProfile?->postal_code) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Address
                    </label>
                    <input type="text" name="address"
                        value="{{ old('address', $businessProfile?->address) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Contact Person Name
                    </label>
                    <input type="text" name="contact_person_name"
                        value="{{ old('contact_person_name', $businessProfile?->contact_person_name) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Contact Person Position
                    </label>
                    <input type="text" name="contact_person_position" placeholder="e.g. Sales Manager"
                        value="{{ old('contact_person_position', $businessProfile?->contact_person_position) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Social Media --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-share-alt text-green-500"></i> Social Media
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach([
                    ['facebook_link',  'fa-facebook',  'Facebook',  'https://facebook.com/'],
                    ['twitter_link',   'fa-twitter',   'Twitter',   'https://twitter.com/'],
                    ['linkedin_link',  'fa-linkedin',  'LinkedIn',  'https://linkedin.com/in/'],
                    ['instagram_link', 'fa-instagram', 'Instagram', 'https://instagram.com/'],
                ] as [$name, $icon, $label, $placeholder])
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        <i class="fab {{ $icon }} mr-1"></i> {{ $label }}
                    </label>
                    <input type="url" name="{{ $name }}" placeholder="{{ $placeholder }}"
                        value="{{ old($name, $businessProfile?->$name) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agent.profile.show') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                <i class="fas fa-save"></i> Save Business Profile
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('logo-preview');
            const placeholder = document.getElementById('logo-placeholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
