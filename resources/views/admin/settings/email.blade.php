@extends('layouts.home')

@section('page-content')
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
                <h1 class="text-2xl font-bold text-gray-900">Email Settings</h1>
            </div>
            <p class="text-sm text-gray-500">Configure SMTP server and email preferences</p>
        </div>
    </div>

    <form id="email-settings-form" action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="email">

        <div class="space-y-6">

            <!-- Mail Configuration -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Mail Configuration</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Driver <span class="text-red-500">*</span></label>
                        <select name="mail_driver" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('mail_driver') border-red-500 @enderror">
                            <option value="smtp"     {{ $settings['mail_driver'] == 'smtp'     ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ $settings['mail_driver'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="mailgun"  {{ $settings['mail_driver'] == 'mailgun'  ? 'selected' : '' }}>Mailgun</option>
                            <option value="ses"      {{ $settings['mail_driver'] == 'ses'      ? 'selected' : '' }}>Amazon SES</option>
                            <option value="postmark" {{ $settings['mail_driver'] == 'postmark' ? 'selected' : '' }}>Postmark</option>
                        </select>
                        @error('mail_driver')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Host <span class="text-red-500">*</span></label>
                        <input type="text" name="mail_host"
                            value="{{ old('mail_host', $settings['mail_host']) }}"
                            required placeholder="smtp.mailtrap.io"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('mail_host') border-red-500 @enderror">
                        @error('mail_host')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Port <span class="text-red-500">*</span></label>
                        <input type="number" name="mail_port"
                            value="{{ old('mail_port', $settings['mail_port']) }}"
                            required placeholder="587"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('mail_port') border-red-500 @enderror">
                        @error('mail_port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                        <select name="mail_encryption"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">None</option>
                            <option value="tls" {{ $settings['mail_encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ $settings['mail_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Authentication -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Authentication</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="mail_username"
                            value="{{ old('mail_username', $settings['mail_username']) }}"
                            placeholder="your-username"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Leave empty if not required</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="mail_password_input" name="mail_password"
                                value="{{ old('mail_password', $settings['mail_password']) }}"
                                placeholder="••••••••"
                                class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="toggleMailPassword()"
                                class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">
                                <i id="mail_eye_icon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Leave empty to keep current password</p>
                    </div>
                </div>
            </div>

            <!-- Sender Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Sender Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Email <span class="text-red-500">*</span></label>
                        <input type="email" name="mail_from_address"
                            value="{{ old('mail_from_address', $settings['mail_from_address']) }}"
                            required placeholder="noreply@example.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('mail_from_address') border-red-500 @enderror">
                        @error('mail_from_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">All outgoing emails will be sent from this address</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Name <span class="text-red-500">*</span></label>
                        <input type="text" name="mail_from_name"
                            value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                            required placeholder="Platform Name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('mail_from_name') border-red-500 @enderror">
                        @error('mail_from_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Common SMTP Providers -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>Common SMTP Providers
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="bg-white p-3 rounded-lg cursor-pointer hover:shadow-sm transition-shadow"
                        onclick="fillSmtp('smtp.gmail.com', 587, 'tls')">
                        <p class="font-semibold text-gray-900 mb-1">Gmail <span class="text-blue-500 text-xs font-normal">(click to fill)</span></p>
                        <p class="text-gray-600">Host: smtp.gmail.com</p>
                        <p class="text-gray-600">Port: 587 (TLS) or 465 (SSL)</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg cursor-pointer hover:shadow-sm transition-shadow"
                        onclick="fillSmtp('smtp.sendgrid.net', 587, 'tls')">
                        <p class="font-semibold text-gray-900 mb-1">SendGrid <span class="text-blue-500 text-xs font-normal">(click to fill)</span></p>
                        <p class="text-gray-600">Host: smtp.sendgrid.net</p>
                        <p class="text-gray-600">Port: 587 (TLS)</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg cursor-pointer hover:shadow-sm transition-shadow"
                        onclick="fillSmtp('smtp.mailgun.org', 587, 'tls')">
                        <p class="font-semibold text-gray-900 mb-1">Mailgun <span class="text-blue-500 text-xs font-normal">(click to fill)</span></p>
                        <p class="text-gray-600">Host: smtp.mailgun.org</p>
                        <p class="text-gray-600">Port: 587 (TLS)</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg cursor-pointer hover:shadow-sm transition-shadow"
                        onclick="fillSmtp('email-smtp.us-east-1.amazonaws.com', 587, 'tls')">
                        <p class="font-semibold text-gray-900 mb-1">Amazon SES <span class="text-blue-500 text-xs font-normal">(click to fill)</span></p>
                        <p class="text-gray-600">Host: email-smtp.region.amazonaws.com</p>
                        <p class="text-gray-600">Port: 587 (TLS)</p>
                    </div>
                </div>
            </div>

            <!-- Test Email -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                <div class="flex gap-3">
                    <i class="fas fa-flask text-yellow-600 mt-0.5 text-lg"></i>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-yellow-900 mb-1">Test Your Configuration</h3>
                        <p class="text-sm text-yellow-800 mb-3">Send a test email to verify your saved settings are working correctly.</p>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="email" id="test_email_address"
                                placeholder="Enter recipient email..."
                                class="flex-1 px-4 py-2 border border-yellow-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400 bg-white">
                            <button type="button" id="test-email-btn" onclick="sendTestEmail()"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm font-medium transition-opacity disabled:opacity-60">
                                <i id="test-btn-icon" class="fas fa-paper-plane"></i>
                                <span id="test-btn-text">Send Test Email</span>
                            </button>
                        </div>

                        <!-- Test result message -->
                        <div id="test-result" class="hidden mt-3 flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-lg"></div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.settings.index') }}"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </a>
                <button id="save-btn" type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:opacity-70">
                    <svg id="save-spinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span id="save-btn-text">Save Email Settings</span>
                </button>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
// ── Password toggle ─────────────────────────────────────────────────────────
function toggleMailPassword() {
    const input = document.getElementById('mail_password_input');
    const icon  = document.getElementById('mail_eye_icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// ── Fill SMTP provider details on card click ────────────────────────────────
function fillSmtp(host, port, encryption) {
    document.querySelector('[name="mail_host"]').value       = host;
    document.querySelector('[name="mail_port"]').value       = port;
    document.querySelector('[name="mail_encryption"]').value = encryption;
}

// ── Save button loader ──────────────────────────────────────────────────────
document.getElementById('email-settings-form').addEventListener('submit', function () {
    const btn     = document.getElementById('save-btn');
    const spinner = document.getElementById('save-spinner');
    const text    = document.getElementById('save-btn-text');
    btn.disabled      = true;
    spinner.classList.remove('hidden');
    text.textContent  = 'Saving…';
});

// ── Test email ──────────────────────────────────────────────────────────────
function sendTestEmail() {
    const email     = document.getElementById('test_email_address').value.trim();
    const resultBox = document.getElementById('test-result');
    const btn       = document.getElementById('test-email-btn');
    const icon      = document.getElementById('test-btn-icon');
    const btnText   = document.getElementById('test-btn-text');

    if (!email) {
        showTestResult('error', 'Please enter a recipient email address first.');
        return;
    }

    // Loading state
    btn.disabled         = true;
    icon.className       = 'fas fa-spinner fa-spin';
    btnText.textContent  = 'Sending…';
    resultBox.classList.add('hidden');

    fetch('{{ route('admin.settings.test-email') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ email }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showTestResult('success', data.message || 'Test email sent successfully!');
        } else {
            showTestResult('error', data.message || 'Failed to send test email.');
        }
    })
    .catch(() => {
        showTestResult('error', 'Network error. Please try again.');
    })
    .finally(() => {
        btn.disabled        = false;
        icon.className      = 'fas fa-paper-plane';
        btnText.textContent = 'Send Test Email';
    });
}

function showTestResult(type, message) {
    const box = document.getElementById('test-result');
    box.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'border', 'border-green-200',
                                   'bg-red-50',   'text-red-700',   'border-red-200');
    if (type === 'success') {
        box.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
        box.innerHTML = `<i class="fas fa-circle-check"></i> ${message}`;
    } else {
        box.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
        box.innerHTML = `<i class="fas fa-circle-xmark"></i> ${message}`;
    }
}
</script>
@endpush

@endsection
