<?php

namespace App\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Get the base query for agents in the country admin's country.
     */
    private function baseQuery(Country $country)
    {
        return User::with(['agentSettings', 'roles', 'country'])
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'agent');
            })
            ->where('country_id', $country->id);
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
     * Display a listing of agents in the country admin's country.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $query  = $this->applyFilters($this->baseQuery($country), $request);
        $agents = $query->paginate(15)->withQueryString();

        $stats = $this->buildStats($country);

        return view('country.agents.index', compact('agents', 'stats', 'country'));
    }

    /**
     * Display the specified agent.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $agent = User::with(['agentSettings', 'roles', 'country', 'products'])
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'agent');
            })
            ->where('country_id', $country->id)
            ->findOrFail($id);

        $stats = [
            'total_products'   => $agent->products()->count(),
            'active_products'  => $agent->products()->where('status', 'active')->count(),
            'total_views'      => $agent->products()->sum('views'),
        ];

        return view('country.agents.show', compact('agent', 'stats', 'country'));
    }

    /**
     * Print agents report.
     */
    public function print(Request $request)
    {
        $user = Auth::user();

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $query  = $this->applyFilters($this->baseQuery($country), $request);
        $agents = $query->get();

        $stats = $this->buildStats($country);

        return view('country.agents.print', compact('agents', 'stats', 'country'));
    }

    /**
     * Suspend an agent account.
     */
    public function suspend($id)
    {
        $user = Auth::user();

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $agent = User::whereHas('roles', function ($q) {
            $q->where('slug', 'agent');
        })->where('country_id', $country->id)->findOrFail($id);

        $agent->update(['status' => 'suspended']);

        return redirect()->back()->with('success', 'Agent suspended successfully.');
    }

    /**
     * Activate an agent account.
     */
    public function activate($id)
    {
        $user = Auth::user();

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $agent = User::whereHas('roles', function ($q) {
            $q->where('slug', 'agent');
        })->where('country_id', $country->id)->findOrFail($id);

        $agent->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Agent activated successfully.');
    }

    /**
     * Build statistics for the country's agents.
     */
    private function buildStats(Country $country): array
    {
        $base = fn() => User::whereHas('roles', function ($q) {
            $q->where('slug', 'agent');
        })->where('country_id', $country->id);

        $total     = $base()->count();
        $active    = $base()->where('status', 'active')->count();
        $suspended = $base()->where('status', 'suspended')->count();
        $inactive  = $total - $active - $suspended;

        return [
            'total'              => $total,
            'active'             => $active,
            'suspended'          => $suspended,
            'inactive'           => $inactive,
            'active_percentage'  => $total > 0 ? round(($active / $total) * 100) : 0,
        ];
    }
}
