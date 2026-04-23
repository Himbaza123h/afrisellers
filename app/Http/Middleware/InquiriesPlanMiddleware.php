<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Vendor\Vendor;
use App\Models\Product;

class InquiriesPlanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
      
        return $next($request);
    }
}
