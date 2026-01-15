<?php

namespace App\Http\Controllers\Admin\Membership;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = MembershipPlan::withCount(['subscriptions', 'features']);

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

        MembershipPlan::create($validated);

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

        return redirect()->route('admin.memberships.plans.index')
            ->with('success', 'Membership plan updated successfully!');
    }

    public function destroy(MembershipPlan $membershipPlan)
    {
        // Check if plan has active subscriptions
        if ($membershipPlan->subscriptions()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete plan with active subscriptions!');
        }

        $membershipPlan->delete();

        return redirect()->route('admin.memberships.plans.index')
            ->with('success', 'Membership plan deleted successfully!');
    }

    public function toggleStatus(MembershipPlan $membershipPlan)
    {
        $membershipPlan->update(['is_active' => !$membershipPlan->is_active]);

        return back()->with('success', 'Plan status updated successfully!');
    }
}
