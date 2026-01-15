@extends('layouts.home')

@push('styles')
<style>
    .security-card { transition: all 0.2s; }
    .security-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Security Center</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your account security and monitor activity</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.security.sessions') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                <i class="fas fa-desktop"></i>Manage Sessions
            </a>
        </div>
    </div>

    <!-- Security Overview Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="security-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-desktop text-blue-600 text-xl"></i>
                </div>
                @if($stats['active_sessions'] > 1)
                    <span class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">{{ $stats['active_sessions'] }}</span>
                @endif
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Active Sessions</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['active_sessions'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Currently logged in devices</p>
        </div>

        <div class="security-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sign-in-alt text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Recent Logins</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['recent_logins'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Last 7 days</p>
        </div>

        <div class="security-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 {{ $stats['failed_attempts'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle {{ $stats['failed_attempts'] > 0 ? 'text-red-600' : 'text-gray-400' }} text-xl"></i>
                </div>
                @if($stats['failed_attempts'] > 0)
                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">{{ $stats['failed_attempts'] }}</span>
                @endif
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Failed Attempts</p>
            <p class="text-2xl font-bold {{ $stats['failed_attempts'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $stats['failed_attempts'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Last 7 days</p>
        </div>

        <div class="security-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 {{ $stats['two_factor_enabled'] ? 'bg-green-100' : 'bg-yellow-100' }} rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt {{ $stats['two_factor_enabled'] ? 'text-green-600' : 'text-yellow-600' }} text-xl"></i>
                </div>
                <span class="px-2 py-1 text-xs font-semibold {{ $stats['two_factor_enabled'] ? 'text-green-700 bg-green-100' : 'text-yellow-700 bg-yellow-100' }} rounded-full">
                    {{ $stats['two_factor_enabled'] ? 'Enabled' : 'Disabled' }}
                </span>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Two-Factor Auth</p>
            <p class="text-lg font-bold {{ $stats['two_factor_enabled'] ? 'text-green-600' : 'text-yellow-600' }}">
                {{ $stats['two_factor_enabled'] ? 'Protected' : 'Not Protected' }}
            </p>
            <p class="text-xs text-gray-500 mt-2">Additional security layer</p>
        </div>
    </div>

    <!-- Two-Factor Authentication -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-shield-alt text-blue-600"></i>
                    Two-Factor Authentication (2FA)
                </h3>
                <p class="text-sm text-gray-600 mt-1">Add an extra layer of security to your account</p>
            </div>
            <span class="px-3 py-1 text-sm font-semibold {{ $stats['two_factor_enabled'] ? 'text-green-700 bg-green-100' : 'text-gray-700 bg-gray-100' }} rounded-full">
                {{ $stats['two_factor_enabled'] ? 'Enabled' : 'Disabled' }}
            </span>
        </div>

        @if(!$stats['two_factor_enabled'])
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <div class="flex gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                    <div>
                        <p class="text-sm font-semibold text-yellow-900">Your account is not fully protected</p>
                        <p class="text-xs text-yellow-700 mt-1">Enable two-factor authentication to add an extra layer of security</p>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.security.two-factor') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="enable" value="{{ $stats['two_factor_enabled'] ? '0' : '1' }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter your password to continue">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 {{ $stats['two_factor_enabled'] ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-lg font-medium">
                <i class="fas {{ $stats['two_factor_enabled'] ? 'fa-times' : 'fa-check' }}"></i>
                {{ $stats['two_factor_enabled'] ? 'Disable' : 'Enable' }} Two-Factor Authentication
            </button>
        </form>
    </div>

    <!-- Active Sessions & Device Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Active Sessions -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Active Sessions</h3>
                <a href="{{ route('admin.security.sessions') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-3">
                @forelse($activeSessions->take(5) as $session)
                    <div class="flex items-start gap-3 p-3 {{ $session->is_current ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50' }} rounded-lg">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center border">
                            <i class="fas {{ $session->device === 'Mobile' ? 'fa-mobile-alt' : ($session->device === 'Tablet' ? 'fa-tablet-alt' : 'fa-desktop') }} text-gray-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-gray-900">{{ $session->browser }} on {{ $session->device }}</p>
                                @if($session->is_current)
                                    <span class="px-2 py-0.5 text-xs font-semibold text-blue-700 bg-blue-100 rounded">Current</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $session->ip_address ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Last active: {{ $session->last_activity_time->diffForHumans() }}
                            </p>
                        </div>
                        @if(!$session->is_current)
                            <form method="POST" action="{{ route('admin.security.revoke-session', $session->id) }}">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('Revoke this session?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">No active sessions</p>
                @endforelse
            </div>
        </div>

        <!-- Device Stats -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Device Statistics (Last 30 Days)</h3>
            <div class="space-y-3">
                @forelse($deviceStats as $device => $count)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas {{ $device === 'Mobile' ? 'fa-mobile-alt' : ($device === 'Tablet' ? 'fa-tablet-alt' : 'fa-desktop') }} text-blue-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $device }}</span>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">{{ $count }}</p>
                            <p class="text-xs text-gray-500">logins</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">No device data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activity & Failed Attempts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Login Activity -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Login Activity</h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($recentActivity as $activity)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Successful Login</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-globe mr-1"></i>{{ $activity->ip_address ?? 'Unknown IP' }}
                                · {{ $activity->browser }} · {{ $activity->device }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($activity->login_at)->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">No recent activity</p>
                @endforelse
            </div>
        </div>

        <!-- Failed Login Attempts -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Failed Login Attempts</h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($failedAttempts as $attempt)
                    <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-times text-red-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-red-900">Failed Login Attempt</p>
                            <p class="text-xs text-red-700 mt-1">
                                <i class="fas fa-globe mr-1"></i>{{ $attempt->ip_address ?? 'Unknown IP' }}
                            </p>
                            <p class="text-xs text-red-600 mt-1">
                                {{ \Carbon\Carbon::parse($attempt->attempted_at)->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-shield-alt text-green-600 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-600">No failed login attempts</p>
                        <p class="text-xs text-gray-500 mt-1">Your account is secure</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Security Events -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Events (Last 30 Days)</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Event</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($securityEvents as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <i class="fas fa-circle text-xs {{ str_contains(strtolower($event->event), 'failed') ? 'text-red-600' : 'text-green-600' }} mr-2"></i>
                                {{ $event->event }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $event->ip_address ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $event->location ?? 'Unknown' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($event->created_at)->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No security events recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Geographic Login Stats -->
    @if(!empty($geoStats))
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Geographic Login Distribution</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($geoStats as $country => $count)
                <div class="p-4 bg-gray-50 rounded-lg text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $count }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ $country ?? 'Unknown' }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Auto-refresh active sessions every 30 seconds
setInterval(function() {
    // You can implement AJAX refresh here if needed
}, 30000);
</script>
@endpush

@endsection
