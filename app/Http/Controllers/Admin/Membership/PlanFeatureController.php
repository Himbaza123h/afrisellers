<?php

namespace App\Http\Controllers\Admin\Membership;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\PlanFeature;
use Illuminate\Http\Request;

class PlanFeatureController extends Controller
{
    public function index(MembershipPlan $membershipPlan)
    {
        $features = $membershipPlan->features()->paginate(15);
        return view('admin.membership.features.index', compact('membershipPlan', 'features'));
    }

    public function store(Request $request, MembershipPlan $membershipPlan)
    {
        $validated = $request->validate([
            'feature_key' => 'required|string|max:255',
            'feature_value' => 'required|string|max:255',
        ]);

        $membershipPlan->features()->create($validated);

        return back()->with('success', 'Feature added successfully!');
    }

    public function update(Request $request, PlanFeature $feature)
    {
        $validated = $request->validate([
            'feature_key' => 'required|string|max:255',
            'feature_value' => 'required|string|max:255',
        ]);

        $feature->update($validated);

        return back()->with('success', 'Feature updated successfully!');
    }

    public function destroy(PlanFeature $feature)
    {
        $feature->delete();
        return back()->with('success', 'Feature deleted successfully!');
    }
}
