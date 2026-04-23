<?php

namespace App\Http\Controllers;

use App\Models\SystemAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemAlertController extends Controller
{
    /**
     * Get alerts for authenticated user based on role
     */
    public function index()
    {
        $user = Auth::user();
        $alerts = $this->getUserAlerts($user);

        return response()->json([
            'alerts' => $alerts,
            'active_count' => $alerts->where('status', 'active')->count()
        ]);
    }

    /**
     * Get user-specific alerts based on role
     */
    private function getUserAlerts($user)
    {
        $query = SystemAlert::with(['country', 'creator', 'resolver'])
            ->orderBy('created_at', 'desc');

        // Admin - Get all alerts
        if ($user->hasRole('admin')) {
            return $query->take(20)->get();
        }

        // Country Admin - Get alerts for their country + global
        if ($user->country_admin && $user->country_id) {
            return $query->where(function ($q) use ($user) {
                $q->where('country_id', $user->country_id)
                  ->orWhereNull('country_id');
            })->take(20)->get();
        }

        // Other users - Only global alerts
        return $query->whereNull('country_id')->take(20)->get();
    }

    /**
     * Resolve an alert
     */
    public function resolve($id)
    {
        $alert = SystemAlert::findOrFail($id);

        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->country_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $alert->markAsResolved($user->id);

        return response()->json(['success' => true]);
    }

    /**
     * Dismiss an alert
     */
    public function dismiss($id)
    {
        $alert = SystemAlert::findOrFail($id);
        $alert->dismiss();

        return response()->json(['success' => true]);
    }

    /**
     * Clear all resolved alerts
     */
    public function clearAll()
    {
        $user = Auth::user();
        $alerts = $this->getUserAlerts($user);

        foreach ($alerts->where('status', 'resolved') as $alert) {
            $alert->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get active alerts count
     */
    public function activeCount()
    {
        $user = Auth::user();
        $alerts = $this->getUserAlerts($user);
        $count = $alerts->where('status', 'active')->count();

        return response()->json(['count' => $count]);
    }
}
