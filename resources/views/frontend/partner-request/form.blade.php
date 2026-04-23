@extends('layouts.app')

@section('title', 'Home - Africa\'s Leading B2B Marketplace')

@section('content')

    {{-- Minimal Header --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('mainlogo.png') }}"
                        alt="AfriSellers" class="h-6 sm:h-8">
                </a>
            <a href="{{ route('home') }}"
               class="text-xs text-gray-500 hover:text-gray-900 flex items-center gap-1.5">
                <i class="fas fa-arrow-left text-xs"></i> Back to Home
            </a>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 py-10 px-4">
        <div class="max-w-2xl mx-auto">

            {{-- Title --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-red-50 rounded-2xl mb-4">
                    <i class="fas fa-handshake text-[#ff0808] text-2xl"></i>
                </div>
                <h1 class="text-2xl font-black text-gray-900 mb-2">Request to be a Partner</h1>
                <p class="text-sm text-gray-500 max-w-md mx-auto">
                    Join Afrisellers' trusted partner network. Fill in your details and our team will review your application.
                </p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
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

            <form action="{{ route('partner.request.store') }}" method="POST"
                  enctype="multipart/form-data"
                  class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 space-y-6">
                @csrf

                {{-- Company Info --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">
                        Company Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Company Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" required
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   placeholder="Your company name">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Industry
                            </label>
                            <input type="text" name="industry" value="{{ old('industry') }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   placeholder="e.g. Logistics, Banking, Tech">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Website URL
                            </label>
                            <input type="url" name="website_url" value="{{ old('website_url') }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   placeholder="https://yourcompany.com">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Country
                            </label>
                            <input type="text" name="country" value="{{ old('country') }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   placeholder="e.g. Rwanda, Kenya, Nigeria">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Partnership Type
                            </label>
                            <select name="partner_type"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                                <option value="">Select a partnership type...</option>
                                <option value="Global Partner"       {{ old('partner_type') === 'Global Partner'       ? 'selected' : '' }}>Global Partner</option>
                                <option value="Strategic Partner"    {{ old('partner_type') === 'Strategic Partner'    ? 'selected' : '' }}>Strategic Partner</option>
                                <option value="Banking Partner"      {{ old('partner_type') === 'Banking Partner'      ? 'selected' : '' }}>Banking Partner</option>
                                <option value="Logistics Partner"    {{ old('partner_type') === 'Logistics Partner'    ? 'selected' : '' }}>Logistics Partner</option>
                                <option value="Technology Partner"   {{ old('partner_type') === 'Technology Partner'   ? 'selected' : '' }}>Technology Partner</option>
                                <option value="Quality Partner"      {{ old('partner_type') === 'Quality Partner'      ? 'selected' : '' }}>Quality Partner</option>
                                <option value="Development Partner"  {{ old('partner_type') === 'Development Partner'  ? 'selected' : '' }}>Development Partner</option>
                                <option value="Other"                {{ old('partner_type') === 'Other'                ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Extended Company Info --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">
                        About Your Company
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Year Established
                            </label>
                            <input type="number" name="established" value="{{ old('established') }}"
                                   min="1800" max="{{ date('Y') }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   placeholder="e.g. 2010">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                No. of Countries Present In
                            </label>
                            <input type="number" name="presence_countries" value="{{ old('presence_countries') }}"
                                   min="1" max="54"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   placeholder="e.g. 5">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">About Us</label>
                            <textarea name="about_us" rows="4"
                                      class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent resize-none"
                                      placeholder="Brief description of your company, mission, and values...">{{ old('about_us') }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Services Offered</label>
                            {{-- Tag input --}}
                            <div id="services-wrapper"
                                 class="w-full min-h-[42px] flex flex-wrap gap-1.5 px-3 py-2 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-[#ff0808] cursor-text"
                                 onclick="document.getElementById('services-input').focus()">
                                {{-- Tags injected here by JS --}}
                                <input id="services-input" type="text"
                                       class="flex-1 min-w-[120px] text-sm outline-none bg-transparent"
                                       placeholder="Type a service and press Enter…">
                            </div>
                            <input type="hidden" name="services_raw" id="services-raw" value="{{ old('services_raw') }}">
                            <p class="text-xs text-gray-400 mt-1">Press <kbd class="bg-gray-100 px-1 rounded">Enter</kbd> or <kbd class="bg-gray-100 px-1 rounded">,</kbd> to add each service.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Company Intro (Image or Video)
                            </label>
                            <input type="file" name="intro" accept="image/*,video/mp4,video/webm,video/quicktime"
                                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2
                                          file:mr-3 file:py-1 file:px-3 file:rounded file:border-0
                                          file:text-xs file:font-semibold file:bg-red-50 file:text-[#ff0808]">
                            <p class="text-xs text-gray-400 mt-1">Accepted: JPG, PNG, GIF, MP4, MOV, WEBM — max 50 MB.</p>
                        </div>
                    </div>
                </div>

                {{-- Contact Info --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">
                        Contact Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Contact Person <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="contact_name" value="{{ old('contact_name') }}" required
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   placeholder="Full name">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   placeholder="contact@company.com">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                   placeholder="+250 XXX XXX XXX">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Company Logo / GIF</label>
                            <input type="file" name="logo" accept="image/*,.gif"
                                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2
                                          file:mr-3 file:py-1 file:px-3 file:rounded file:border-0
                                          file:text-xs file:font-semibold file:bg-red-50 file:text-[#ff0808]">
                        </div>
                    </div>
                </div>

                {{-- ─── Authentication Credentials ─────────────────────────────── --}}
                <div>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">
                        Account Credentials
                    </h2>
                    <p class="text-xs text-gray-500 mb-4">
                        These will be used to log in to your partner dashboard once approved.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Display Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                placeholder="Name shown on your partner profile">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                placeholder="Min. 8 characters">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                                placeholder="Repeat your password">
                        </div>
                    </div>
                </div>
                {{-- ─────────────────────────────────────────────────────────────── --}}

                {{-- Message --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Why do you want to partner with Afrisellers? <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" rows="5" required
                              class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent resize-none"
                              placeholder="Tell us about your company, what you offer, and how this partnership would be mutually beneficial...">{{ old('message') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Minimum 20 characters.</p>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3 bg-[#ff0808] text-white text-sm font-bold rounded-xl hover:bg-red-700 transition-all shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Submit Partnership Request
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-3">
                        Our team reviews all requests within 2-3 business days.
                    </p>
                </div>

            </form>
        </div>
    </main>

    @push('scripts')
<script>
(function () {
    const wrapper  = document.getElementById('services-wrapper');
    const textInput = document.getElementById('services-input');
    const hidden   = document.getElementById('services-raw');
    let tags = hidden.value ? hidden.value.split(',').map(s => s.trim()).filter(Boolean) : [];

    function render() {
        wrapper.querySelectorAll('.tag-pill').forEach(el => el.remove());
        tags.forEach((tag, i) => {
            const pill = document.createElement('span');
            pill.className = 'tag-pill inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 text-[#ff0808] text-xs font-semibold rounded-full';
            pill.innerHTML = `${tag} <button type="button" class="hover:text-red-700" data-i="${i}">&times;</button>`;
            pill.querySelector('button').addEventListener('click', () => { tags.splice(i, 1); render(); });
            wrapper.insertBefore(pill, textInput);
        });
        hidden.value = tags.join(',');
    }

    textInput.addEventListener('keydown', function (e) {
        if ((e.key === 'Enter' || e.key === ',') && this.value.trim()) {
            e.preventDefault();
            tags.push(this.value.trim());
            this.value = '';
            render();
        }
        if (e.key === 'Backspace' && !this.value && tags.length) {
            tags.pop();
            render();
        }
    });

    render();
})();
</script>
@endpush

@endsection
