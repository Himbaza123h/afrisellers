<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class SecurityController extends Controller
{
    /**
     * Display security dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Security Overview Stats
        $stats = [
            'active_sessions' => $this->getActiveSessionsCount($user->id),
            'recent_logins' => $this->getRecentLoginsCount($user->id),
            'failed_attempts' => $this->getFailedAttemptsCount($user->id),
            'two_factor_enabled' => $user->two_factor_enabled ?? false,
        ];

        // Recent Login Activity (Last 30 days)
        $recentActivity = $this->getRecentLoginActivity($user->id, 30);

        // Active Sessions
        $activeSessions = $this->getActiveSessions($user->id);

        // Failed Login Attempts (Last 7 days)
        $failedAttempts = $this->getFailedLoginAttempts($user->id, 7);

        // Security Events (Last 30 days)
        $securityEvents = $this->getSecurityEvents($user->id, 30);

        // Device & Browser Stats
        $deviceStats = $this->getDeviceStats($user->id);

        // Geographic Login Stats
        $geoStats = $this->getGeographicStats($user->id);

        return view('admin.security.index', compact(
            'stats',
            'recentActivity',
            'activeSessions',
            'failedAttempts',
            'securityEvents',
            'deviceStats',
            'geoStats'
        ));
    }

    /**
     * Enable Two-Factor Authentication
     */
    public function enableTwoFactor(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'enable' => 'required|boolean',
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid password']);
        }

        // Toggle 2FA
        $user->update([
            'two_factor_enabled' => $request->enable,
            'two_factor_secret' => $request->enable ? $this->generate2FASecret() : null,
        ]);

        // Log security event
        $this->logSecurityEvent($user->id, $request->enable ? '2FA Enabled' : '2FA Disabled', $request->ip());

        return back()->with('success', $request->enable ? 'Two-Factor Authentication enabled successfully!' : 'Two-Factor Authentication disabled successfully!');
    }

    /**
     * Get active sessions
     */
    public function sessions()
    {
        $user = Auth::user();
        $sessions = $this->getActiveSessions($user->id);

        return view('admin.security.sessions', compact('sessions'));
    }

    /**
     * Revoke a session
     */
    public function revokeSession(Request $request, $sessionId)
    {
        $user = Auth::user();

        // Delete session from database
        DB::table('sessions')->where('id', $sessionId)->where('user_id', $user->id)->delete();

        // Log security event
        $this->logSecurityEvent($user->id, 'Session Revoked', $request->ip());

        return back()->with('success', 'Session revoked successfully!');
    }

    /**
     * Helper Methods
     */

    private function getActiveSessionsCount($userId)
    {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>', now()->subMinutes(30)->timestamp)
            ->count();
    }

    private function getRecentLoginsCount($userId, $days = 7)
    {
        return DB::table('login_history')
            ->where('user_id', $userId)
            ->where('login_at', '>', now()->subDays($days))
            ->count();
    }

    private function getFailedAttemptsCount($userId, $days = 7)
    {
        return DB::table('failed_login_attempts')
            ->where('user_id', $userId)
            ->where('attempted_at', '>', now()->subDays($days))
            ->count();
    }

    private function getRecentLoginActivity($userId, $days = 30)
    {
        return DB::table('login_history')
            ->where('user_id', $userId)
            ->where('login_at', '>', now()->subDays($days))
            ->orderBy('login_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($log) {
                $log->browser = $this->parseBrowser($log->user_agent ?? '');
                $log->device = $this->parseDevice($log->user_agent ?? '');
                return $log;
            });
    }

    private function getActiveSessions($userId)
    {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                $session->is_current = $session->id === Session::getId();
                $session->last_activity_time = Carbon::createFromTimestamp($session->last_activity);
                $session->browser = $this->parseBrowser($session->user_agent ?? '');
                $session->device = $this->parseDevice($session->user_agent ?? '');
                return $session;
            });
    }

    private function getFailedLoginAttempts($userId, $days = 7)
    {
        return DB::table('failed_login_attempts')
            ->where('user_id', $userId)
            ->where('attempted_at', '>', now()->subDays($days))
            ->orderBy('attempted_at', 'desc')
            ->limit(20)
            ->get();
    }

    private function getSecurityEvents($userId, $days = 30)
    {
        return DB::table('security_events')
            ->where('user_id', $userId)
            ->where('created_at', '>', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    private function getDeviceStats($userId)
    {
        $stats = DB::table('login_history')
            ->where('user_id', $userId)
            ->where('login_at', '>', now()->subDays(30))
            ->get()
            ->map(function ($log) {
                return $this->parseDevice($log->user_agent ?? '');
            })
            ->countBy()
            ->toArray();

        return $stats;
    }

    private function getGeographicStats($userId)
    {
        return DB::table('login_history')
            ->where('user_id', $userId)
            ->where('login_at', '>', now()->subDays(30))
            ->select('country', DB::raw('COUNT(*) as count'))
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->pluck('count', 'country')
            ->toArray();
    }

    private function parseBrowser($userAgent)
    {
        if (str_contains($userAgent, 'Chrome')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox')) return 'Firefox';
        if (str_contains($userAgent, 'Safari')) return 'Safari';
        if (str_contains($userAgent, 'Edge')) return 'Edge';
        if (str_contains($userAgent, 'Opera')) return 'Opera';
        return 'Unknown';
    }

    private function parseDevice($userAgent)
    {
        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone')) {
            return 'Mobile';
        }
        if (str_contains($userAgent, 'Tablet') || str_contains($userAgent, 'iPad')) {
            return 'Tablet';
        }
        return 'Desktop';
    }

    private function generate2FASecret()
    {
        return bin2hex(random_bytes(16));
    }

    private function logSecurityEvent($userId, $event, $ipAddress)
    {
        DB::table('security_events')->insert([
            'user_id' => $userId,
            'event' => $event,
            'ip_address' => $ipAddress,
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
 * Update password
 */
public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    $user = Auth::user();

    // Verify current password
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect']);
    }

    // Update password
    $user->update([
        'password' => Hash::make($request->new_password)
    ]);

    // Revoke all other sessions for security
    $currentSessionId = Session::getId();
    DB::table('sessions')
        ->where('user_id', $user->id)
        ->where('id', '!=', $currentSessionId)
        ->delete();

    // Log security event
    $this->logSecurityEvent($user->id, 'Password Changed', $request->ip());

    return redirect()->route('admin.security.index')->with('success', 'Password updated successfully! All other sessions have been logged out.');
}
}
