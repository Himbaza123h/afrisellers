<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimits
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $activePlan = $user->getActivePlan();

        if (!$activePlan) {
            return redirect()->route('vendor.plans.choose')
                ->with('error', 'You need an active plan to access this feature.');
        }

        // Check feature limits
        switch ($feature) {
            case 'product':
                if (!$activePlan->canAddProduct()) {
                    return redirect()->route('vendor.product.index')
                        ->with('error', 'You have reached your product limit. Please upgrade your plan.');
                }
                break;

            case 'inquiry':
                if (!$activePlan->canSendInquiry()) {
                    return back()
                        ->with('error', 'You have reached your inquiry limit. Please upgrade your plan.');
                }
                break;

            case 'rfq':
                if (!$activePlan->canSendRfq()) {
                    return back()
                        ->with('error', 'You have reached your RFQ limit. Please upgrade your plan.');
                }
                break;

            default:
                break;
        }

        return $next($request);
    }
}
