<?php

namespace App\Http\Controllers\Admin\Membership;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = MembershipPlan::with(['features' => fn ($q) => $q->orderBy(
            Feature::query()->select('feature_key')->whereColumn('features.id', 'plan_features.feature_id')
        )])->withCount(['subscriptions', 'features']);

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'display_order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $plans = $query->paginate(15);

        $stats = [
            'total' => MembershipPlan::count(),
            'active' => MembershipPlan::where('is_active', true)->count(),
            'inactive' => MembershipPlan::where('is_active', false)->count(),
            'total_subscriptions' => \App\Models\Subscription::where('status', 'active')->count(),
        ];

        return view('admin.membership.plans.index', compact('plans', 'stats'));
    }

    public function create()
    {
        return view('admin.membership.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:membership_plans,slug',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
$plan = MembershipPlan::create($validated);

        \App\Models\Notification::create([
            'title'     => 'New Membership Plan Available',
            'content'   => 'A new membership plan "' . $plan->name . '" has been added to AfriSellers.',
            'link_url'  => '/vendor/subscription',
            'user_id'   => auth()->id(),
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        return redirect()->route('admin.memberships.plans.index')
            ->with('success', 'Membership plan created successfully!');
    }

    public function edit(MembershipPlan $membershipPlan)
    {
        $membershipPlan->load('features');
        return view('admin.membership.plans.edit', compact('membershipPlan'));
    }

    public function update(Request $request, MembershipPlan $membershipPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:membership_plans,slug,' . $membershipPlan->id,
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

$membershipPlan->update($validated);

        // Notify all active subscribers of this plan
        $subscriberUserIds = \App\Models\Subscription::where('plan_id', $membershipPlan->id)
            ->where('status', 'active')
            ->pluck('user_id');

        foreach ($subscriberUserIds as $userId) {
            \App\Models\Notification::create([
                'title'     => 'Your Membership Plan Was Updated',
                'content'   => 'Your current plan "' . $membershipPlan->name . '" has been updated by the admin. Please review your subscription details.',
                'link_url'  => '/vendor/subscription',
                'user_id'   => $userId,
                'vendor_id' => null,
                'country_id'=> null,
                'is_read'   => false,
            ]);
        }

        return redirect()->route('admin.memberships.plans.index')
            ->with('success', 'Membership plan updated successfully!');
    }

    public function destroy(MembershipPlan $membershipPlan)
    {
        // Check if plan has active subscriptions
        if ($membershipPlan->subscriptions()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete plan with active subscriptions!');
        }

        $planName = $membershipPlan->name;

        // Notify all subscribers of this plan before deletion
        $subscriberUserIds = \App\Models\Subscription::where('plan_id', $membershipPlan->id)
            ->pluck('user_id');

        foreach ($subscriberUserIds as $userId) {
            \App\Models\Notification::create([
                'title'     => 'Membership Plan Removed',
                'content'   => 'Your membership plan "' . $planName . '" has been removed. Please contact admin or choose a new plan.',
                'link_url'  => '/vendor/subscription',
                'user_id'   => $userId,
                'vendor_id' => null,
                'country_id'=> null,
                'is_read'   => false,
            ]);
        }

        $membershipPlan->delete();

        return redirect()->route('admin.memberships.plans.index')
            ->with('success', 'Membership plan deleted successfully!');
    }

    public function toggleStatus(MembershipPlan $membershipPlan)
    {
$membershipPlan->update(['is_active' => !$membershipPlan->is_active]);

        // Notify active subscribers of status change
        $subscriberUserIds = \App\Models\Subscription::where('plan_id', $membershipPlan->id)
            ->where('status', 'active')
            ->pluck('user_id');

        foreach ($subscriberUserIds as $userId) {
            \App\Models\Notification::create([
                'title'     => 'Membership Plan ' . ($membershipPlan->is_active ? 'Activated' : 'Deactivated'),
                'content'   => 'Your membership plan "' . $membershipPlan->name . '" has been ' .
                    ($membershipPlan->is_active ? 'activated. You can continue enjoying all its features.' : 'deactivated by the admin. Please contact support for assistance.'),
                'link_url'  => '/vendor/subscription',
                'user_id'   => $userId,
                'vendor_id' => null,
                'country_id'=> null,
                'is_read'   => false,
            ]);
        }

        return back()->with('success', 'Plan status updated successfully!');
    }

    public function updateBonusDays(Request $request, MembershipPlan $membershipPlan)
{
    $request->validate([
        'bonus_days' => 'required|integer',
    ]);

$membershipPlan->update(['bonus_days' => $request->bonus_days]);

        // Notify active subscribers about bonus days update
        $subscriberUserIds = \App\Models\Subscription::where('plan_id', $membershipPlan->id)
            ->where('status', 'active')
            ->pluck('user_id');

        foreach ($subscriberUserIds as $userId) {
            \App\Models\Notification::create([
                'title'     => 'Bonus Days Updated 🎁',
                'content'   => 'Your membership plan "' . $membershipPlan->name . '" now includes ' . $request->bonus_days . ' bonus days.',
                'link_url'  => '/vendor/subscription',
                'user_id'   => $userId,
                'vendor_id' => null,
                'country_id'=> null,
                'is_read'   => false,
            ]);
        }

        return back()->with('success', 'Bonus days updated successfully!');
}
}
