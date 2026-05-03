<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-900">
            {{ $isEdit ? 'Update Your Partner Request' : 'Submit a Partner Request' }}
        </h2>
        @if(!$isEdit)
            <p class="text-xs text-gray-500 mt-0.5">
                Use a <strong>different email</strong> from your vendor account — this will be your partner login credentials.
            </p>
        @endif
    </div>

    <form
        action="{{ $isEdit ? route('vendor.partner-request.update') : route('vendor.partner-request.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="p-5 space-y-5"
    >
        @csrf
        @if($isEdit) @method('PUT') @endif

        @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        @endif

        {{-- Company Details --}}
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Company Details</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name"
                        value="{{ old('company_name', $partnerRequest?->company_name) }}" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Contact Person <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="contact_name"
                        value="{{ old('contact_name', $partnerRequest?->contact_name) }}" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Partner Type</label>
                    <select name="partner_type"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Select…</option>
                        @foreach(['Global Partner','Strategic Partner','Banking Partner','Logistics Partner','Technology Partner','Other'] as $type)
                            <option value="{{ $type }}"
                                {{ old('partner_type', $partnerRequest?->partner_type) == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Industry</label>
                    <input type="text" name="industry"
                        value="{{ old('industry', $partnerRequest?->industry) }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Website</label>
                    <input type="url" name="website_url"
                        value="{{ old('website_url', $partnerRequest?->website_url) }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Country</label>
                    <input type="text" name="country"
                        value="{{ old('country', $partnerRequest?->country) }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone"
                        value="{{ old('phone', $partnerRequest?->phone) }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>

                {{-- Logo upload with preview --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Company Logo</label>
                    @if($partnerRequest?->logo)
                        <div class="mb-2">
                            <img id="logo-preview"
                                src="{{ Storage::url($partnerRequest->logo) }}"
                                class="h-14 w-14 object-contain rounded-lg border border-gray-200 bg-gray-50">
                        </div>
                    @else
                        <div class="mb-2 hidden" id="logo-preview-wrapper">
                            <img id="logo-preview" src="" class="h-14 w-14 object-contain rounded-lg border border-gray-200 bg-gray-50">
                        </div>
                    @endif
                    <input type="file" name="logo" id="logo-input" accept="image/*"
                        onchange="previewLogo(this)"
                        class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2
                               file:mr-2 file:py-1 file:px-2 file:rounded file:border-0
                               file:text-xs file:font-semibold file:bg-green-50 file:text-green-700">
                </div>
            </div>
        </div>

        {{-- Account Credentials (only on new request) --}}
        @if(!$isEdit)
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                Partner Login Credentials
                <span class="ml-1 text-gray-300 font-normal normal-case">(different from your vendor account)</span>
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Display Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        placeholder="Name shown on partner profile">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Partner Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                        placeholder="Must differ from your vendor account email">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password-field" required
                            oninput="checkStrength(this.value)"
                            class="w-full px-3 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                            placeholder="Min. 8 characters">
                        <button type="button" onclick="togglePassword('password-field', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 mt-3">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    {{-- Strength bar --}}
                    <div class="mt-2 space-y-1">
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div id="strength-bar" class="h-1.5 rounded-full transition-all duration-300 w-0 bg-red-500"></div>
                        </div>
                        <p id="strength-label" class="text-xs text-gray-400"></p>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="confirm-field" required
                            oninput="checkMatch()"
                            class="w-full px-3 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                            placeholder="Repeat your password">
                        <button type="button" onclick="togglePassword('confirm-field', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 mt-3">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    <p id="match-label" class="text-xs mt-1 hidden"></p>
                </div>
            </div>
        </div>
        @else
        {{-- On edit: name is still editable, email/password not (already created) --}}
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Partner Account</p>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Display Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name"
                    value="{{ old('name', $partnerRequest?->name) }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-400 mt-1">Email and password cannot be changed here. Contact support if needed.</p>
            </div>
        </div>
        @endif

        {{-- Message --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
                Why do you want to partner? <span class="text-red-500">*</span>
            </label>
            <textarea name="message" rows="4" required
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 resize-none"
                placeholder="Minimum 20 characters…">{{ old('message', $partnerRequest?->message) }}</textarea>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-sm shadow-sm transition-all">
                <i class="fas fa-paper-plane"></i>
                {{ $isEdit ? 'Update Request' : 'Submit Request' }}
            </button>
        </div>
    </form>
</div>

<script>
function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function checkStrength(val) {
    const bar   = document.getElementById('strength-bar');
    const label = document.getElementById('strength-label');
    if (!bar) return;

    let score = 0;
    if (val.length >= 8)                          score++;
    if (val.length >= 12)                         score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val))  score++;
    if (/[0-9]/.test(val))                        score++;
    if (/[^A-Za-z0-9]/.test(val))                score++;

    const levels = [
        { pct: '20%',  color: 'bg-red-500',    text: 'Very weak',  labelColor: 'text-red-500'    },
        { pct: '40%',  color: 'bg-orange-500',  text: 'Weak',       labelColor: 'text-orange-500'  },
        { pct: '60%',  color: 'bg-yellow-500',  text: 'Fair',       labelColor: 'text-yellow-600'  },
        { pct: '80%',  color: 'bg-blue-500',    text: 'Good',       labelColor: 'text-blue-600'    },
        { pct: '100%', color: 'bg-green-500',   text: 'Strong',     labelColor: 'text-green-600'   },
    ];

    const level = levels[Math.max(0, score - 1)] || levels[0];
    bar.style.width = val.length ? level.pct : '0';
    bar.className = `h-1.5 rounded-full transition-all duration-300 ${level.color}`;
    label.textContent = val.length ? level.text : '';
    label.className = `text-xs ${level.labelColor}`;
}

function checkMatch() {
    const pw      = document.getElementById('password-field')?.value;
    const confirm = document.getElementById('confirm-field')?.value;
    const label   = document.getElementById('match-label');
    if (!label || !confirm) return;

    label.classList.remove('hidden');
    if (pw === confirm) {
        label.textContent = '✓ Passwords match';
        label.className = 'text-xs mt-1 text-green-600';
    } else {
        label.textContent = '✗ Passwords do not match';
        label.className = 'text-xs mt-1 text-red-500';
    }
}

function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        let img = document.getElementById('logo-preview');
        const wrapper = document.getElementById('logo-preview-wrapper');
        if (wrapper) wrapper.classList.remove('hidden');
        if (img) {
            img.src = e.target.result;
        }
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
