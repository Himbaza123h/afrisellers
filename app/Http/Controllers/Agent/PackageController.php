<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AgentPackage;
use App\Models\AgentSubscription;
use Carbon\Carbon;

class PackageController extends Controller
{
    /**
     * Display available packages
     */
    public function index()
    {
        $packages = AgentPackage::active()
            ->ordered()
            ->get();

        // Get agent's current subscription
        $currentSubscription = AgentSubscription::where('agent_id', Auth::id())
            ->with('package')
            ->active()
            ->first();

        return view('agent.packages.index', compact('packages', 'currentSubscription'));
    }

    /**
     * Show package details
     */
    public function show($id)
    {
        $package = AgentPackage::active()->findOrFail($id);

        // Get agent's current subscription
        $currentSubscription = AgentSubscription::where('agent_id', Auth::id())
            ->with('package')
            ->active()
            ->first();

        return view('agent.packages.show', compact('package', 'currentSubscription'));
    }

    /**
     * Show subscription checkout page
     */
    public function checkout($packageId)
    {
        $package = AgentPackage::active()->findOrFail($packageId);

        // Check if agent already has an active subscription
        $activeSubscription = AgentSubscription::where('agent_id', Auth::id())
            ->active()
            ->first();

        if ($activeSubscription) {
            return redirect()
                ->route('agent.packages.current')
                ->with('error', 'You already have an active subscription. Please cancel it first or wait until it expires.');
        }

        return view('agent.packages.checkout', compact('package'));
    }

    /**
     * Process subscription purchase
     */
    public function subscribe(Request $request, $packageId)
    {
        $package = AgentPackage::active()->findOrFail($packageId);

        // Validate payment details
        $validated = $request->validate([
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer',
            'auto_renew' => 'boolean',
        ]);

        // Check if agent already has an active subscription
        $activeSubscription = AgentSubscription::where('agent_id', Auth::id())
            ->active()
            ->first();

        if ($activeSubscription) {
            return redirect()
                ->route('agent.packages.current')
                ->with('error', 'You already have an active subscription.');
        }

        // Create subscription
        $subscription = AgentSubscription::create([
            'agent_id' => Auth::id(),
            'package_id' => $package->id,
            'amount_paid' => $package->price,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($package->duration_days),
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'completed',
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
            'auto_renew' => $request->has('auto_renew'),
        ]);

        return redirect()
            ->route('agent.packages.current')
            ->with('success', 'Successfully subscribed to ' . $package->name . ' package!');
    }

    /**
     * Show current subscription
     */
    public function current()
    {
        $subscription = AgentSubscription::where('agent_id', Auth::id())
            ->with(['package'])
            ->latest()
            ->first();

        if (!$subscription) {
            return redirect()
                ->route('agent.packages.index')
                ->with('info', 'You don\'t have any subscription yet. Choose a package to get started!');
        }

        // Get usage statistics
        $stats = [
            'referrals_remaining' => $subscription->package->max_referrals - $subscription->referrals_used,
            'payouts_remaining' => $subscription->package->max_payouts_per_month - $subscription->payouts_used,
            'days_remaining' => $subscription->daysRemaining(),
            'progress_percentage' => $subscription->getProgressPercentage(),
        ];

        return view('agent.packages.current', compact('subscription', 'stats'));
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $subscription = AgentSubscription::where('agent_id', Auth::id())
            ->active()
            ->first();

        if (!$subscription) {
            return redirect()
                ->route('agent.packages.index')
                ->with('error', 'No active subscription found.');
        }

        $subscription->cancel();

        return redirect()
            ->route('agent.packages.current')
            ->with('success', 'Subscription cancelled successfully.');
    }

    /**
     * Renew subscription
     */
    public function renew(Request $request)
    {
        $subscription = AgentSubscription::where('agent_id', Auth::id())
            ->where('status', 'expired')
            ->latest()
            ->first();

        if (!$subscription) {
            return redirect()
                ->route('agent.packages.index')
                ->with('error', 'No subscription found to renew.');
        }

        // Create new subscription with same package
        $newSubscription = AgentSubscription::create([
            'agent_id' => Auth::id(),
            'package_id' => $subscription->package_id,
            'amount_paid' => $subscription->package->price,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($subscription->package->duration_days),
            'payment_method' => $subscription->payment_method,
            'payment_status' => 'completed',
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
            'auto_renew' => $subscription->auto_renew,
        ]);

        return redirect()
            ->route('agent.packages.current')
            ->with('success', 'Subscription renewed successfully!');
    }

    /**
     * Subscription history
     */
    public function history()
    {
        $subscriptions = AgentSubscription::where('agent_id', Auth::id())
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('agent.packages.history', compact('subscriptions'));
    }

    /**
     * Print subscription report
     */
    public function print()
    {
        $currentSubscription = AgentSubscription::where('agent_id', Auth::id())
            ->with('package')
            ->active()
            ->first();

        $subscriptionHistory = AgentSubscription::where('agent_id', Auth::id())
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_spent' => $subscriptionHistory->sum('amount_paid'),
            'total_subscriptions' => $subscriptionHistory->count(),
            'active_since' => $subscriptionHistory->where('status', 'active')->first()?->starts_at,
        ];

        return view('agent.packages.print', compact('currentSubscription', 'subscriptionHistory', 'stats'));
    }
}
