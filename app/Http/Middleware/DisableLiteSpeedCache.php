<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableLiteSpeedCache
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        
        return $response;
    }
}