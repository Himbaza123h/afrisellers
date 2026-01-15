<?php

namespace App\Http\Controllers\Admin\Membership;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['seller.user', 'plan']);

        // Search
        if ($request->filled('search')) {
            $query->whereHas('seller.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        // Filter by trial
        if ($request->filled('is_trial')) {
            $query->where('is_trial', $request->is_trial === 'yes');
        }

        // Date range
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) === 2) {
                $query->whereBetween('starts_at', $dates);
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $subscriptions = $query->paginate(15);

        $plans = MembershipPlan::where('is_active', true)->get();

        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'trial' => Subscription::where('is_trial', true)->where('status', 'active')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
        ];

        return view('admin.membership.subscriptions.index', compact('subscriptions', 'plans', 'stats'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['seller.user', 'plan.features']);
        return view('admin.membership.subscriptions.show', compact('subscription'));
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);
        return back()->with('success', 'Subscription cancelled successfully!');
    }

    public function renew(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'duration_days' => 'required|integer|min:1',
        ]);

        $subscription->update([
            'ends_at' => now()->addDays($validated['duration_days']),
            'status' => 'active',
        ]);

        return back()->with('success', 'Subscription renewed successfully!');
    }

    public function changePlan(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:membership_plans,id',
        ]);

        $newPlan = MembershipPlan::findOrFail($validated['plan_id']);

        $subscription->update([
            'plan_id' => $newPlan->id,
        ]);

        return back()->with('success', 'Plan changed successfully!');
    }
}
