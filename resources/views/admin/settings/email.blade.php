@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
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

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="section" value="email">

        <div class="space-y-6">
            <!-- Email Configuration -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Mail Configuration</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Driver *</label>
                        <select name="mail_driver" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="smtp" {{ $settings['mail_driver'] == 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ $settings['mail_driver'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="mailgun" {{ $settings['mail_driver'] == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="ses" {{ $settings['mail_driver'] == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                            <option value="postmark" {{ $settings['mail_driver'] == 'postmark' ? 'selected' : '' }}>Postmark</option>
                        </select>
                        @error('mail_driver')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Host *</label>
                        <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" required placeholder="smtp.mailtrap.io" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('mail_host')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Port *</label>
                        <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}" required placeholder="2525" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('mail_port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                        <select name="mail_encryption" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
                        <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}" placeholder="your-username" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Leave empty if not required</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="mail_password" value="{{ old('mail_password', $settings['mail_password']) }}" placeholder="••••••••" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Leave empty to keep current password</p>
                    </div>
                </div>
            </div>

            <!-- Sender Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Sender Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Email *</label>
                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" required placeholder="noreply@example.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('mail_from_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">All outgoing emails will be sent from this address</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Name *</label>
                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}" required placeholder="Platform Name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
                    <div class="bg-white p-3 rounded-lg">
                        <p class="font-semibold text-gray-900 mb-1">Gmail</p>
                        <p class="text-gray-600">Host: smtp.gmail.com</p>
                        <p class="text-gray-600">Port: 587 (TLS) or 465 (SSL)</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg">
                        <p class="font-semibold text-gray-900 mb-1">SendGrid</p>
                        <p class="text-gray-600">Host: smtp.sendgrid.net</p>
                        <p class="text-gray-600">Port: 587 (TLS)</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg">
                        <p class="font-semibold text-gray-900 mb-1">Mailgun</p>
                        <p class="text-gray-600">Host: smtp.mailgun.org</p>
                        <p class="text-gray-600">Port: 587 (TLS)</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg">
                        <p class="font-semibold text-gray-900 mb-1">Amazon SES</p>
                        <p class="text-gray-600">Host: email-smtp.region.amazonaws.com</p>
                        <p class="text-gray-600">Port: 587 (TLS)</p>
                    </div>
                </div>
            </div>

            <!-- Test Email -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-semibold text-yellow-900 mb-1">Test Your Configuration</h3>
                        <p class="text-sm text-yellow-800 mb-3">After saving, send a test email to verify your settings are working correctly.</p>
                        <button type="button" onclick="sendTestEmail()" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm font-medium">
                            <i class="fas fa-paper-plane"></i>Send Test Email
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.settings.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Save Email Settings
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function sendTestEmail() {
    // You can implement a test email endpoint later
    alert('Test email functionality will be implemented. Save settings first.');
}
</script>
@endpush
@endsection
