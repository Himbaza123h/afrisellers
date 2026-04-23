<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPartnerAccess
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!auth()->check()) {
            return redirect()->route('auth.signin');
        }

        $user = auth()->user();

        // Super admin always passes
        if ($user->id === 1) {
            return $next($request);
        }

        // Admin role passes
        if ($user->roles()->where('roles.slug', 'admin')->exists()) {
            return $next($request);
        }

        // Partner passes
        if ($user->is_partner) {
            return $next($request);
        }

        abort(403, 'Access denied. Partner account required.');
    }
}
