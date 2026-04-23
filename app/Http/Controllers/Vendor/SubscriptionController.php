<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    /**
     * Eager-load plan rows with plan_features joined to the feature catalog (sorted by catalog name).
     *
     * @return array<string, \Closure>
     */
    private static function planWithCatalogFeatures(): array
    {
        return [
            'features' => function ($q) {
                $q->with('feature')
                    ->join('features', 'features.id', '=', 'plan_features.feature_id')
                    ->orderBy('features.name')
                    ->select('plan_features.*');
            },
        ];
    }

    public function index()
    {
        $vendor = auth()->user();

        $planFeatureWithCatalog = self::planWithCatalogFeatures();

        // Get current active subscription (not expired, not cancelled)
        $currentSubscription = Subscription::where('seller_id', $vendor->id)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->with(['plan' => fn ($q) => $q->with($planFeatureWithCatalog)])
            ->first();

        // Get ALL subscription history including cancelled (load plan features for detail modals)
        $subscriptionHistory = Subscription::where('seller_id', $vendor->id)
            ->with(['plan' => fn ($q) => $q->with($planFeatureWithCatalog)])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get available plans — features from plan_features + features catalog
        $availablePlans = MembershipPlan::where('is_active', true)
            ->with($planFeatureWithCatalog)
            ->orderBy('display_order')
            ->get();

        $stats = [
            'total_spent' => Subscription::where('seller_id', $vendor->id)
                ->whereIn('status', ['active', 'expired'])
                ->join('membership_plans', 'subscriptions.plan_id', '=', 'membership_plans.id')
                ->sum('membership_plans.price'),
            'total_subscriptions' => Subscription::where('seller_id', $vendor->id)->count(),
            'active_days'         => $currentSubscription ? $currentSubscription->daysRemaining() : 0,
        ];

        // Trial uses legacy App\Models\Plan (no plan_features relation); subscriptions use MembershipPlan.
        $activeTrial = \App\Models\VendorTrial::where('user_id', $vendor->id)
            ->where('is_active', true)
            ->where('ends_at', '>=', now())
            ->with('plan')
            ->first();

        return view('vendor.subscriptions.index', compact(
            'currentSubscription',
            'subscriptionHistory',
            'availablePlans',
            'stats',
            'activeTrial'
        ));
    }

    public function subscribe(Request $request, MembershipPlan $plan)
    {
        $vendor = auth()->user();

        // Cancel any existing active subscription
        $activeSubscription = Subscription::where('seller_id', $vendor->id)
            ->where('status', 'active')
            ->first();

        if ($activeSubscription) {
            $activeSubscription->update(['status' => 'cancelled']);
        }

        $bonusDays = $plan->bonus_days ?? 0;

        $subscription = Subscription::create([
            'seller_id'  => $vendor->id,
            'plan_id'    => $plan->id,
            'starts_at'  => now(),
            'ends_at'    => now()->addDays($plan->duration_days + $bonusDays),
            'status'     => 'active',
            'is_trial'   => false,
            'auto_renew' => true,
        ]);

        // Reset bonus days after use (one-time)
        if ($bonusDays !== 0) {
            $plan->update(['bonus_days' => 0]);
        }

        // Auto-create service delivery rows for manual features
        $manualKeys = \App\Models\ServiceDelivery::manualFeatureKeys();
        $plan->features->each(function ($feature) use ($subscription, $plan, $vendor, $manualKeys) {
            if (
                isset($manualKeys[$feature->feature_key]) &&
                strtolower($feature->feature_value) === 'true'
            ) {
                \App\Models\ServiceDelivery::create([
                    'user_id'         => $vendor->id,
                    'plan_id'         => $plan->id,
                    'subscription_id' => $subscription->id,
                    'feature_key'     => $feature->feature_key,
                    'service_name'    => $manualKeys[$feature->feature_key],
                    'status'          => 'pending',
                ]);
            }
        });

        // TODO: Process payment here

        return redirect()->route('vendor.subscriptions.index')
            ->with('success', 'Successfully subscribed to ' . $plan->name . '!');
    }

    public function toggleAutoRenew(Request $request)
    {
        $vendor = auth()->user();

        $currentSubscription = Subscription::where('seller_id', $vendor->id)
            ->where('status', 'active')
            ->first();

        if (!$currentSubscription) {
            return back()->with('error', 'No active subscription found.');
        }

        $currentSubscription->update([
            'auto_renew' => !$currentSubscription->auto_renew,
        ]);

        $message = $currentSubscription->auto_renew
            ? 'Auto-renewal enabled'
            : 'Auto-renewal disabled';

        return back()->with('success', $message);
    }

    public function planPdf(Subscription $subscription)
    {
        if ($subscription->seller_id !== auth()->user()->id) {
            abort(403);
        }

        $subscription->load(['plan' => fn ($q) => $q->with(self::planWithCatalogFeatures())]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('vendor.subscriptions.plan-pdf', compact('subscription'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('plan-' . Str::slug($subscription->plan->name) . '.pdf');
    }

    public function upgrade(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:membership_plans,id',
        ]);

        $vendor  = auth()->user();
        $newPlan = MembershipPlan::findOrFail($validated['plan_id']);

        $currentSubscription = Subscription::where('seller_id', $vendor->id)
            ->where('status', 'active')
            ->first();

        if (!$currentSubscription) {
            return back()->with('error', 'No active subscription found.');
        }

        $currentSubscription->update(['status' => 'cancelled']);

        $bonusDays = $newPlan->bonus_days ?? 0;

        $subscription = Subscription::create([
            'seller_id'  => $vendor->id,
            'plan_id'    => $newPlan->id,
            'starts_at'  => now(),
            'ends_at'    => now()->addDays($newPlan->duration_days + $bonusDays),
            'status'     => 'active',
            'is_trial'   => false,
            'auto_renew' => true,
        ]);

        if ($bonusDays !== 0) {
            $newPlan->update(['bonus_days' => 0]);
        }

        return redirect()->route('vendor.subscriptions.index')
            ->with('success', 'Successfully upgraded to ' . $newPlan->name . '!');
    }

    public function renew(Request $request)
    {
        $vendor = auth()->user();

        $currentSubscription = Subscription::where('seller_id', $vendor->id)
            ->where('status', 'active')
            ->first();

        if (!$currentSubscription) {
            return back()->with('error', 'No active subscription found.');
        }

        $plan = $currentSubscription->plan;
        $currentSubscription->update([
            'ends_at' => $currentSubscription->ends_at->addDays($plan->duration_days),
        ]);

        // TODO: Process payment here

        return back()->with('success', 'Subscription renewed successfully!');
    }

    public function cancel(Request $request)
    {
        $vendor = auth()->user();

        $currentSubscription = Subscription::where('seller_id', $vendor->id)
            ->where('status', 'active')
            ->first();

        if (!$currentSubscription) {
            return back()->with('error', 'No active subscription found.');
        }

        $currentSubscription->update([
            'auto_renew' => false,
            'status'     => 'cancelled',
        ]);

        return back()->with('success', 'Subscription cancelled. Access will continue until ' . $currentSubscription->ends_at->format('M d, Y'));
    }

    public function invoice(Subscription $subscription)
    {
        if ($subscription->seller_id !== auth()->user()->id) {
            abort(403);
        }

        return view('vendor.subscriptions.invoice', compact('subscription'));
    }
}
