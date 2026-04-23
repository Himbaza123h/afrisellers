@extends('layouts.home')

@section('page-content')

{{-- Quill Rich Text Editor --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<style>
    .ql-container {
        min-height: 120px;
        font-size: 14px;
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }
    .ql-toolbar {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }
    .ql-error .ql-container,
    .ql-error .ql-toolbar {
        border-color: #ef4444;
    }
    #btn-submit .spinner {
        display: none;
    }
    #btn-submit.loading .spinner {
        display: inline-block;
    }
    #btn-submit.loading .btn-text {
        display: none;
    }
</style>

<div class="space-y-6">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <i class="fas fa-circle-check text-green-500"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <i class="fas fa-circle-xmark text-red-500"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">General Settings</h1>
            </div>
            <p class="text-sm text-gray-500">Configure basic site information and preferences</p>
        </div>
    </div>

    <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="section" value="general">

        <div class="space-y-6">

            <!-- Site Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Site Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Name <span class="text-red-500">*</span></label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('site_name') border-red-500 @enderror">
                        @error('site_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Tagline</label>
                        <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Rich Text Description --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Site Description <span class="text-red-500">*</span>
                        </label>

                        <input type="hidden" name="site_description" id="site_description_input"
                            value="{{ old('site_description', $settings['site_description']) }}">

                        <div id="quill-wrapper" class="{{ $errors->has('site_description') ? 'ql-error' : '' }}">
                            <div id="quill-editor"></div>
                        </div>

                        @error('site_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p id="quill-error-msg" class="mt-1 text-sm text-red-600 hidden">Site description is required.</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Email <span class="text-red-500">*</span></label>
                        <input type="email" name="site_email" value="{{ old('site_email', $settings['site_email']) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('site_email') border-red-500 @enderror">
                        @error('site_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Phone <span class="text-red-500">*</span></label>
                        <input type="text" name="site_phone" value="{{ old('site_phone', $settings['site_phone']) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('site_phone') border-red-500 @enderror">
                        @error('site_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- WhatsApp Number --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-whatsapp text-green-500 mr-1"></i>
                            WhatsApp Number <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm">+</span>
                            <input type="text" name="whatsapp_number"
                                value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}"
                                required placeholder="14698379001"
                                class="w-full pl-7 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('whatsapp_number') border-red-500 @enderror">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Country code + number, no spaces or dashes (e.g. 14698379001)</p>
                        @error('whatsapp_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Address</label>
                        <textarea name="site_address" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('site_address', $settings['site_address']) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Branding -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Branding</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Logo</label>
                        @if($settings['site_logo'])
                            <img src="{{ Storage::url($settings['site_logo']) }}" alt="Logo" class="h-16 mb-3 object-contain">
                        @endif
                        <input type="file" name="site_logo" accept="image/jpeg,image/png,image/jpg,image/svg+xml"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Max 2MB. Supported: JPG, PNG, SVG</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                        @if($settings['site_favicon'])
                            <img src="{{ Storage::url($settings['site_favicon']) }}" alt="Favicon" class="h-8 mb-3">
                        @endif
                        <input type="file" name="site_favicon" accept="image/x-icon,image/png"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Max 1MB. Supported: ICO, PNG (32x32)</p>
                    </div>
                </div>
            </div>

            <!-- Localization -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Localization</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Timezone <span class="text-red-500">*</span></label>
                        <select name="timezone" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @foreach($timezones as $timezone)
                                <option value="{{ $timezone }}" {{ $settings['timezone'] == $timezone ? 'selected' : '' }}>
                                    {{ $timezone }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                        <select name="date_format" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="Y-m-d" {{ $settings['date_format'] == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="d/m/Y" {{ $settings['date_format'] == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                            <option value="m/d/Y" {{ $settings['date_format'] == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time Format</label>
                        <select name="time_format" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="H:i:s" {{ $settings['time_format'] == 'H:i:s' ? 'selected' : '' }}>24 Hour (HH:MM:SS)</option>
                            <option value="h:i:s A" {{ $settings['time_format'] == 'h:i:s A' ? 'selected' : '' }}>12 Hour (hh:mm:ss AM/PM)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Items Per Page</label>
                        <input type="number" name="items_per_page" value="{{ old('items_per_page', $settings['items_per_page']) }}"
                            min="5" max="100" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Currency Settings -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Currency Settings</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currency <span class="text-red-500">*</span></label>
                        <input type="text" id="currency_input" name="currency"
                            value="{{ old('currency', $settings['currency']) }}"
                            required maxlength="10" placeholder="USD"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currency Symbol <span class="text-red-500">*</span></label>
                        <input type="text" id="currency_symbol_input" name="currency_symbol"
                            value="{{ old('currency_symbol', $settings['currency_symbol']) }}"
                            required maxlength="5" placeholder="$"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Symbol Position <span class="text-red-500">*</span></label>
                        <select id="currency_position_select" name="currency_position" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="left"  id="pos_left"  {{ $settings['currency_position'] == 'left'  ? 'selected' : '' }}></option>
                            <option value="right" id="pos_right" {{ $settings['currency_position'] == 'right' ? 'selected' : '' }}></option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Preview updates as you type the symbol / currency above</p>
                    </div>
                </div>
            </div>

            <!-- System Options -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">System Options</h2>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode"
                            {{ $settings['maintenance_mode'] ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="maintenance_mode" class="ml-2 text-sm text-gray-700">
                            Enable Maintenance Mode
                            <span class="block text-xs text-gray-500">Site will be inaccessible to regular users</span>
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="allow_registration" id="allow_registration"
                            {{ $settings['allow_registration'] ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="allow_registration" class="ml-2 text-sm text-gray-700">
                            Allow New Registrations
                            <span class="block text-xs text-gray-500">Users can create new accounts</span>
                        </label>
                    </div>
                    <!-- Maintenance Secret Key -->
<div class="pt-2 border-t border-gray-100">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Maintenance Secret Key <span class="text-red-500">*</span>
    </label>
    <div class="relative max-w-sm">
        <input type="password" name="maintenance_key"
            id="maintenance_key_input"
            value="{{ old('maintenance_key', $settings['maintenance_key']) }}"
            required minlength="8" maxlength="50"
            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('maintenance_key') border-red-500 @enderror">
        <button type="button" onclick="toggleKeyVisibility()"
            class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">
            <i id="key_eye_icon" class="fas fa-eye text-sm"></i>
        </button>
    </div>
    <p class="mt-1 text-xs text-gray-500">
        Type this key on the maintenance page to access the login. Never share it publicly.
    </p>
    @error('maintenance_key')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.settings.index') }}"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </a>
                <button id="btn-submit" type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-opacity disabled:opacity-70">
                    <svg class="spinner animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span class="btn-text">Save Changes</span>
                </button>
            </div>

        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Quill ───────────────────────────────────────────────────────────────
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Write a site description…',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean'],
            ]
        }
    });

    const existingValue = document.getElementById('site_description_input').value;
    if (existingValue) {
        quill.clipboard.dangerouslyPasteHTML(existingValue);
    }

    // ── Currency position live preview ─────────────────────────────────────
    const symbolInput   = document.getElementById('currency_symbol_input');
    const currencyInput = document.getElementById('currency_input');
    const optLeft       = document.getElementById('pos_left');
    const optRight      = document.getElementById('pos_right');

    function updatePositionLabels() {
        const sym = symbolInput.value.trim()   || '?';
        const cur = currencyInput.value.trim() || '?';
        optLeft.textContent  = `Left  (${sym} 100)`;
        optRight.textContent = `Right (100 ${sym})`;
    }

    updatePositionLabels(); // run on load with saved values
    symbolInput.addEventListener('input', updatePositionLabels);
    currencyInput.addEventListener('input', updatePositionLabels);

    // ── Form submit ─────────────────────────────────────────────────────────
    const form        = document.getElementById('settings-form');
    const btn         = document.getElementById('btn-submit');
    const hiddenInput = document.getElementById('site_description_input');
    const errorMsg    = document.getElementById('quill-error-msg');
    const wrapper     = document.getElementById('quill-wrapper');

    form.addEventListener('submit', function (e) {
        const html = quill.root.innerHTML;
        const text = quill.getText().trim();

        hiddenInput.value = html;

        if (!text) {
            e.preventDefault();
            wrapper.classList.add('ql-error');
            if (errorMsg) errorMsg.classList.remove('hidden');
            quill.focus();
            return;
        }

        wrapper.classList.remove('ql-error');
        if (errorMsg) errorMsg.classList.add('hidden');

        btn.classList.add('loading');
        btn.disabled = true;
    });

    quill.on('text-change', function () {
        if (quill.getText().trim()) {
            wrapper.classList.remove('ql-error');
            if (errorMsg) errorMsg.classList.add('hidden');
        }
    });

});
function toggleKeyVisibility() {
    const input = document.getElementById('maintenance_key_input');
    const icon  = document.getElementById('key_eye_icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

@endsection
