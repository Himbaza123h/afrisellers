<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use App\Models\RegionalAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Resolve the authenticated regional admin and their region.
     */
    private function resolveRegion(): \App\Models\Region
    {
        $user = Auth::user();

        $regionalAdmin = RegionalAdmin::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('region.countries')
            ->first();

        if (!$regionalAdmin || !$regionalAdmin->region) {
            abort(403, 'You are not assigned to any active region.');
        }

        return $regionalAdmin->region;
    }

    /**
     * Get the base query for agents across all countries in the region.
     */
    private function baseQuery(\App\Models\Region $region)
    {
        $countryIds = $region->countries->pluck('id');

        return User::with(['agentSettings', 'roles', 'country'])
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'agent');
            })
            ->whereIn('country_id', $countryIds);
    }

    /**
     * Apply shared filters to a query.
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('created_at', '>=', $request->date_from)
                  ->whereDate('created_at', '<=', $request->date_to);
        }

        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowed   = ['created_at', 'name', 'email', 'status'];
        if (in_array($sortBy, $allowed)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }

    /**
     * Build statistics for all agents across the region's countries.
     */
    private function buildStats(\App\Models\Region $region): array
    {
        $countryIds = $region->countries->pluck('id');

        $base = fn() => User::whereHas('roles', function ($q) {
            $q->where('slug', 'agent');
        })->whereIn('country_id', $countryIds);

        $total     = $base()->count();
        $active    = $base()->where('status', 'active')->count();
        $suspended = $base()->where('status', 'suspended')->count();
        $inactive  = $total - $active - $suspended;

        // Per-country breakdown
        $perCountry = [];
        foreach ($region->countries as $country) {
            $perCountry[] = [
                'country' => $country,
                'total'   => (clone $base())->where('country_id', $country->id)->count(),
                'active'  => (clone $base())->where('country_id', $country->id)->where('status', 'active')->count(),
            ];
        }

        return [
            'total'              => $total,
            'active'             => $active,
            'suspended'          => $suspended,
            'inactive'           => $inactive,
            'active_percentage'  => $total > 0 ? round(($active / $total) * 100) : 0,
            'per_country'        => $perCountry,
        ];
    }

    /**
     * Display a listing of agents across all countries in the regional admin's region.
     */
    public function index(Request $request)
    {
        $region = $this->resolveRegion();

        $query  = $this->applyFilters($this->baseQuery($region), $request);
        $agents = $query->paginate(15)->withQueryString();

        $stats     = $this->buildStats($region);
        $countries = $region->countries;

        return view('regional.agents.index', compact('agents', 'stats', 'region', 'countries'));
    }

    /**
     * Display the specified agent (must belong to the region).
     */
    public function show($id)
    {
        $region     = $this->resolveRegion();
        $countryIds = $region->countries->pluck('id');

        $agent = User::with(['agentSettings', 'roles', 'country', 'products'])
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'agent');
            })
            ->whereIn('country_id', $countryIds)
            ->findOrFail($id);

        $stats = [
            'total_products'  => $agent->products()->count(),
            'active_products' => $agent->products()->where('status', 'active')->count(),
            'total_views'     => $agent->products()->sum('views'),
        ];

        return view('regional.agents.show', compact('agent', 'stats', 'region'));
    }

    /**
     * Print agents report for the region.
     */
    public function print(Request $request)
    {
        $region = $this->resolveRegion();

        $query  = $this->applyFilters($this->baseQuery($region), $request);
        $agents = $query->get();

        $stats     = $this->buildStats($region);
        $countries = $region->countries;

        return view('regional.agents.print', compact('agents', 'stats', 'region', 'countries'));
    }

    /**
     * Suspend an agent (must belong to the region).
     */
    public function suspend($id)
    {
        $region     = $this->resolveRegion();
        $countryIds = $region->countries->pluck('id');

        $agent = User::whereHas('roles', function ($q) {
            $q->where('slug', 'agent');
        })->whereIn('country_id', $countryIds)->findOrFail($id);

        $agent->update(['status' => 'suspended']);

        return redirect()->back()->with('success', 'Agent suspended successfully.');
    }

    /**
     * Activate an agent (must belong to the region).
     */
    public function activate($id)
    {
        $region     = $this->resolveRegion();
        $countryIds = $region->countries->pluck('id');

        $agent = User::whereHas('roles', function ($q) {
            $q->where('slug', 'agent');
        })->whereIn('country_id', $countryIds)->findOrFail($id);

        $agent->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Agent activated successfully.');
    }
}
