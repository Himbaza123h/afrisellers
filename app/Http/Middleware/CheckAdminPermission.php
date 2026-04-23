<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        if (!auth()->check()) {
            return redirect()->route('auth.signin');
        }

        $user = auth()->user();

        // Super admin (id=1) always passes
        if ($user->id === 1) {
            return $next($request);
        }

        // Must have admin role
        if (!$user->roles()->where('roles.slug', 'admin')->exists()) {
            abort(403, 'Access denied.');
        }

        $perms = $user->manageablePermission;

        // No permission record = no access
        if (!$perms) {
            abort(403, 'You do not have permission to access this section.');
        }

        // Check the specific permission column
        if (!$perms->{$permission}) {
            abort(403, 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
