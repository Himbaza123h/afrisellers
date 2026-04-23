<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;

class TrackVisitor
{
    // Only log GET requests, skip these paths
    private array $skip = [
        'admin*', 'api*', '_debugbar*', 'livewire*',
        'favicon.ico', '*.css', '*.js', '*.png', '*.jpg',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only track GET requests that returned 200
        if (
            $request->isMethod('GET') &&
            $response->getStatusCode() === 200 &&
            !$this->shouldSkip($request)
        ) {
            try {
                AuditLog::log('visited', 'Page visited: ' . $request->path());
            } catch (\Exception $e) {
                // Silently fail — never block a request
            }
        }

        return $response;
    }

    private function shouldSkip(Request $request): bool
    {
        foreach ($this->skip as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }
        return false;
    }
}
