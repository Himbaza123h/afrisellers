<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $vendor = auth()->user();

        // Get current subscription
        $currentSubscription = Subscription::where('seller_id', $vendor->id)
            ->where('status', 'active')
            ->with('plan.features')
            ->first();

        // Get subscription history
        $subscriptionHistory = Subscription::where('seller_id', $vendor->id)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get available plans
        $availablePlans = MembershipPlan::where('is_active', true)
            ->with('features')
            ->orderBy('display_order')
            ->get();

        $stats = [
            'total_spent' => Subscription::where('seller_id', $vendor->id)
                ->where('status', '!=', 'trial')
                ->join('membership_plans', 'subscriptions.plan_id', '=', 'membership_plans.id')
                ->sum('membership_plans.price'),
            'total_subscriptions' => Subscription::where('seller_id', $vendor->id)->count(),
            'active_days' => $currentSubscription ? $currentSubscription->daysRemaining() : 0,
        ];

        return view('vendor.subscriptions.index', compact(
            'currentSubscription',
            'subscriptionHistory',
            'availablePlans',
            'stats'
        ));
    }

    public function subscribe(Request $request, MembershipPlan $plan)
    {
        $vendor = auth()->user();

        // Check if already has active subscription
        $activeSubscription = Subscription::where('seller_id', $vendor->id)
            ->where('status', 'active')
            ->first();

        if ($activeSubscription) {
            return back()->with('error', 'You already have an active subscription. Please cancel it first or upgrade.');
        }

        // Create new subscription
        $subscription = Subscription::create([
            'seller_id' => $vendor->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration_days),
            'status' => 'active',
            'is_trial' => false,
            'auto_renew' => true,
        ]);

        // TODO: Process payment here

        return redirect()->route('vendor.subscriptions.index')
            ->with('success', 'Successfully subscribed to ' . $plan->name . '!');
    }

    public function upgrade(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:membership_plans,id',
        ]);

        $vendor = auth()->user();
        $newPlan = MembershipPlan::findOrFail($validated['plan_id']);

        // Get current subscription
        $currentSubscription = Subscription::where('seller_id', $vendor->id)
            ->where('status', 'active')
            ->first();

        if (!$currentSubscription) {
            return back()->with('error', 'No active subscription found.');
        }

        // Calculate prorated amount (if upgrading mid-cycle)
        $daysRemaining = $currentSubscription->daysRemaining();
        $currentPlan = $currentSubscription->plan;

        // Cancel current subscription
        $currentSubscription->update(['status' => 'cancelled']);

        // Create new subscription
        $subscription = Subscription::create([
            'seller_id' => $vendor->id,
            'plan_id' => $newPlan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($newPlan->duration_days),
            'status' => 'active',
            'is_trial' => false,
            'auto_renew' => true,
        ]);

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

        // Extend current subscription
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

        // Cancel subscription (will expire at end date)
        $currentSubscription->update([
            'auto_renew' => false,
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Subscription cancelled. Access will continue until ' . $currentSubscription->ends_at->format('M d, Y'));
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

    public function invoice(Subscription $subscription)
    {
        // Check if vendor owns this subscription
        if ($subscription->seller_id !== auth()->user()->id) {
            abort(403);
        }

        return view('vendor.subscriptions.invoice', compact('subscription'));
    }
}
