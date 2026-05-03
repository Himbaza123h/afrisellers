<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentPackage;
use App\Models\AgentSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgentPackageController extends Controller
{
    public function index()
    {
        $packages = AgentPackage::withCount(['subscriptions', 'activeSubscriptions'])
            ->orderBy('sort_order')
            ->paginate(15);

        $stats = [
            'total'    => AgentPackage::count(),
            'active'   => AgentPackage::where('is_active', true)->count(),
            'featured' => AgentPackage::where('is_featured', true)->count(),
            'revenue'  => AgentSubscription::sum('amount_paid'),
        ];

        return view('admin.agent-packages.index', compact('packages', 'stats'));
    }

    public function create()
    {
        return view('admin.agent-packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:100|unique:agent_packages,name',
            'description'           => 'nullable|string|max:500',
            'price'                 => 'required|numeric|min:0',
            'billing_cycle'         => 'required|in:monthly,quarterly,yearly',
            'duration_days'         => 'required|integer|min:1',
            'max_referrals'         => 'required|integer|min:0',
            'max_vendors'           => 'required|integer|min:0',
            'max_payouts_per_month' => 'required|integer|min:0',
            'commission_rate'       => 'nullable|numeric|min:0|max:100',
            'allow_rfqs'            => 'boolean',
            'priority_support'      => 'boolean',
            'advanced_analytics'    => 'boolean',
            'commission_boost'      => 'boolean',
            'featured_profile'      => 'boolean',
            'is_active'             => 'boolean',
            'is_featured'           => 'boolean',
            'sort_order'            => 'nullable|integer|min:0',
        ]);

        $validated['slug']             = Str::slug($validated['name']);
        $validated['allow_rfqs']       = $request->boolean('allow_rfqs');
        $validated['priority_support'] = $request->boolean('priority_support');
        $validated['advanced_analytics']= $request->boolean('advanced_analytics');
        $validated['commission_boost'] = $request->boolean('commission_boost');
        $validated['featured_profile'] = $request->boolean('featured_profile');
        $validated['is_active']        = $request->boolean('is_active');
        $validated['is_featured']      = $request->boolean('is_featured');
        $validated['sort_order']       = $validated['sort_order'] ?? 0;

        AgentPackage::create($validated);

        return redirect()->route('admin.agent-packages.index')
            ->with('success', 'Package "' . $validated['name'] . '" created successfully.');
    }

    public function show(AgentPackage $agentPackage)
    {
        $agentPackage->loadCount(['subscriptions', 'activeSubscriptions']);

        $recentSubscriptions = AgentSubscription::where('package_id', $agentPackage->id)
            ->with('agent')
            ->latest()
            ->take(10)
            ->get();

        $revenue = AgentSubscription::where('package_id', $agentPackage->id)->sum('amount_paid');

        return view('admin.agent-packages.show', compact('agentPackage', 'recentSubscriptions', 'revenue'));
    }

    public function edit(AgentPackage $agentPackage)
    {
        return view('admin.agent-packages.edit', compact('agentPackage'));
    }

    public function update(Request $request, AgentPackage $agentPackage)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:100|unique:agent_packages,name,' . $agentPackage->id,
            'description'           => 'nullable|string|max:500',
            'price'                 => 'required|numeric|min:0',
            'billing_cycle'         => 'required|in:monthly,quarterly,yearly',
            'duration_days'         => 'required|integer|min:1',
            'max_referrals'         => 'required|integer|min:0',
            'max_vendors'           => 'required|integer|min:0',
            'max_payouts_per_month' => 'required|integer|min:0',
            'commission_rate'       => 'nullable|numeric|min:0|max:100',
            'allow_rfqs'            => 'boolean',
            'priority_support'      => 'boolean',
            'advanced_analytics'    => 'boolean',
            'commission_boost'      => 'boolean',
            'featured_profile'      => 'boolean',
            'is_active'             => 'boolean',
            'is_featured'           => 'boolean',
            'sort_order'            => 'nullable|integer|min:0',
        ]);

        $validated['slug']              = Str::slug($validated['name']);
        $validated['allow_rfqs']        = $request->boolean('allow_rfqs');
        $validated['priority_support']  = $request->boolean('priority_support');
        $validated['advanced_analytics']= $request->boolean('advanced_analytics');
        $validated['commission_boost']  = $request->boolean('commission_boost');
        $validated['featured_profile']  = $request->boolean('featured_profile');
        $validated['is_active']         = $request->boolean('is_active');
        $validated['is_featured']       = $request->boolean('is_featured');

        $agentPackage->update($validated);

        return redirect()->route('admin.agent-packages.index')
            ->with('success', 'Package "' . $agentPackage->name . '" updated successfully.');
    }

    public function destroy(AgentPackage $agentPackage)
    {
        if ($agentPackage->activeSubscriptions()->exists()) {
            return back()->with('error', 'Cannot delete a package with active subscriptions.');
        }

        $name = $agentPackage->name;
        $agentPackage->delete();

        return redirect()->route('admin.agent-packages.index')
            ->with('success', 'Package "' . $name . '" deleted.');
    }

    public function toggleStatus(AgentPackage $agentPackage)
    {
        $agentPackage->update(['is_active' => !$agentPackage->is_active]);

        return back()->with('success', 'Package status updated.');
    }
}
