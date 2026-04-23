<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckVendorFeature
{
    public function handle(Request $request, Closure $next, string $feature): mixed
    {
        if (!auth()->check()) {
            return redirect()->route('auth.signin');
        }

        $userId = auth()->id();

        // Check active subscription
        $activeSub = \App\Models\Subscription::where('seller_id', $userId)
            ->where('status', 'active')
            ->with('plan.features')
            ->first();

        // Check active trial — trial unlocks everything
        $activeTrial = \App\Models\VendorTrial::where('user_id', $userId)
            ->where('is_active', true)
            ->where('ends_at', '>=', now())
            ->first();

        if ($activeTrial) {
            return $next($request);
        }

        if ($activeSub && $activeSub->plan) {
            $featureRow = $activeSub->plan->features
                ->where('feature_key', $feature)
                ->first();

            if ($featureRow && in_array(strtolower($featureRow->feature_value), ['true', '1', 'yes'])) {
                return $next($request);
            }
        }

        return redirect()->route('vendor.subscriptions.index')
            ->with('error', 'Your current plan does not include access to this feature. Please upgrade.');
    }
}
