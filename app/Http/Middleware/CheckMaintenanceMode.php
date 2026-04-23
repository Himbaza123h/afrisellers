<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        $isMaintenanceMode = SystemSetting::get('maintenance_mode', false);


        // ── If maintenance is OFF ────────────────────────────────────────────
        // Block direct access to /maintenance — redirect to home
        if (!$isMaintenanceMode) {
            if ($request->routeIs('maintenance')) {
                return redirect()->route('home');
            }

            return $next($request);
        }

        // ── Maintenance is ON ────────────────────────────────────────────────

        // Always allow: the maintenance page itself
        if ($request->routeIs('maintenance')) {
            return $next($request);
        }

        // Always allow: login / logout routes
        if ($request->routeIs('auth.signin') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Always allow: all admin/* routes
        if (str_starts_with($request->path(), 'admin')) {
            return $next($request);
        }

        return redirect()->route('maintenance');
    }
}
