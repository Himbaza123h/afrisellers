@extends('layouts.home')
@section('page-content')
<div class="max-w-6xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('agent.partners.index') }}"
            class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Register a Partner</h1>
            <p class="text-xs text-gray-500 mt-0.5">Create a new partner account</p>
        </div>
    </div>

    @if($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('agent.partners.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-5">
            @csrf

            {{-- Company Details --}}
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Company Details</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Contact Person <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="contact_name" value="{{ old('contact_name') }}" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Partner Type</label>
                        <select name="partner_type"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">Select…</option>
                            @foreach(['Global Partner','Strategic Partner','Banking Partner','Logistics Partner','Technology Partner','Other'] as $type)
                                <option value="{{ $type }}" {{ old('partner_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Industry</label>
                        <input type="text" name="industry" value="{{ old('industry') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Website</label>
                        <input type="url" name="website_url" value="{{ old('website_url') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Country</label>
                        <input type="text" name="country" value="{{ old('country') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Company Logo</label>
                        <div class="mb-2 hidden" id="logo-preview-wrapper">
                            <img id="logo-preview" src=""
                                class="h-14 w-14 object-contain rounded-lg border border-gray-200 bg-gray-50">
                        </div>
                        <input type="file" name="logo" accept="image/*" onchange="previewLogo(this)"
                            class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2
                                   file:mr-2 file:py-1 file:px-2 file:rounded file:border-0
                                   file:text-xs file:font-semibold file:bg-green-50 file:text-green-700">
                    </div>
                </div>
            </div>

            {{-- Partner Login Credentials --}}
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Partner Login Credentials</p>
                <p class="text-xs text-gray-400 mb-3">These will be used by the partner to log in to their dashboard.</p>
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
                            placeholder="Partner's login email">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="pw-field" required
                                oninput="checkStrength(this.value)"
                                class="w-full px-3 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                placeholder="Min. 8 characters">
                            <button type="button" onclick="togglePw('pw-field', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 mt-3">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <div class="mt-2 space-y-1">
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div id="strength-bar" class="h-1.5 rounded-full transition-all duration-300 w-0"></div>
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
                                placeholder="Repeat password">
                            <button type="button" onclick="togglePw('confirm-field', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 mt-3">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <p id="match-label" class="text-xs mt-1 hidden"></p>
                    </div>
                </div>
            </div>

            {{-- Message --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Partnership Description <span class="text-red-500">*</span>
                </label>
                <textarea name="message" rows="4" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 resize-none"
                    placeholder="Describe this partner and their role (min 20 characters)…">{{ old('message') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('agent.partners.index') }}"
                    class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-sm shadow-sm">
                    <i class="fas fa-user-plus"></i> Register Partner
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePw(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    field.type === 'password'
        ? (field.type = 'text',     icon.classList.replace('fa-eye','fa-eye-slash'))
        : (field.type = 'password', icon.classList.replace('fa-eye-slash','fa-eye'));
}

function checkStrength(val) {
    const bar = document.getElementById('strength-bar');
    const lbl = document.getElementById('strength-label');
    let score = 0;
    if (val.length >= 8)                          score++;
    if (val.length >= 12)                         score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val))  score++;
    if (/[0-9]/.test(val))                        score++;
    if (/[^A-Za-z0-9]/.test(val))                score++;
    const levels = [
        { w:'20%',  c:'bg-red-500',    t:'Very weak',  l:'text-red-500'    },
        { w:'40%',  c:'bg-orange-500', t:'Weak',       l:'text-orange-500' },
        { w:'60%',  c:'bg-yellow-500', t:'Fair',       l:'text-yellow-600' },
        { w:'80%',  c:'bg-blue-500',   t:'Good',       l:'text-blue-600'   },
        { w:'100%', c:'bg-green-500',  t:'Strong',     l:'text-green-600'  },
    ];
    const lv = levels[Math.max(0, score - 1)];
    bar.style.width = val.length ? lv.w : '0';
    bar.className = `h-1.5 rounded-full transition-all duration-300 ${lv.c}`;
    lbl.textContent = val.length ? lv.t : '';
    lbl.className = `text-xs ${lv.l}`;
}

function checkMatch() {
    const pw  = document.getElementById('pw-field')?.value;
    const cf  = document.getElementById('confirm-field')?.value;
    const lbl = document.getElementById('match-label');
    if (!lbl || !cf) return;
    lbl.classList.remove('hidden');
    if (pw === cf) {
        lbl.textContent = '✓ Passwords match';
        lbl.className = 'text-xs mt-1 text-green-600';
    } else {
        lbl.textContent = '✗ Passwords do not match';
        lbl.className = 'text-xs mt-1 text-red-500';
    }
}

function previewLogo(input) {
    if (!input.files?.[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('logo-preview-wrapper').classList.remove('hidden');
        document.getElementById('logo-preview').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endsection
