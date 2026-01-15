<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Vendor\Vendor;
use App\Models\Product;

class ProductPlanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $vendor = Vendor::with('plan')->where('user_id', $user->id)->first();

        if (!$vendor->plan_id || !$vendor->plan) {
            return redirect()->back()->with('error', 'You need to have a plan assigned to access this feature. Please contact support.');
        }

        $plan = $vendor->plan;
        $currentProductCount = Product::where('user_id', $user->id)->count();

        if ($plan->product_limit !== null) {
            if ($currentProductCount >= $plan->product_limit) {
                return redirect()
                    ->back()
                    ->with('error', "You have reached your plan's product limit of {$plan->product_limit} products. Please upgrade your plan to add more products.");
            }
        }

        return $next($request);
    }
}
