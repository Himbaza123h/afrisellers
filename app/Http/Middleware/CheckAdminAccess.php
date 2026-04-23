<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckAdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Not logged in at all
        if (!auth()->check()) {

            return redirect()->route('auth.signin')
                ->with('error', 'You must be logged in to access this area.');
        }

        $user = auth()->user();

        // Check admin role — adjust role slug to match yours
        $isAdmin = $user->hasRole('admin')
                || $user->hasRole('super-admin')
                || $user->country_admin
                || $user->regional_admin;

        if (!$isAdmin) {

            abort(403, 'You do not have permission to access the admin area.');
        }


        return $next($request);
    }
}
