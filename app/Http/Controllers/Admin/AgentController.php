<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Agent::with(['user', 'country', 'businessProfile']);

            // Filter by status
            $filter = $request->get('filter', '');
            if ($filter === 'active') {
                $query->where('account_status', 'active');
            } elseif ($filter === 'pending') {
                $query->where('account_status', 'pending');
            } elseif ($filter === 'suspended') {
                $query->where('account_status', 'suspended');
            }

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
                });
            }

            // Country filter
            if ($request->filled('country')) {
                $query->where('country_id', $request->country);
            }

            // Email verification filter
            if ($request->filled('email_verified')) {
                $query->where('email_verified', $request->email_verified);
            }

            // Commission filter
            if ($request->filled('commission_range')) {
                $range = $request->commission_range;
                if ($range === 'high') {
                    $query->where('commission_earned', '>', 1000);
                } elseif ($range === 'medium') {
                    $query->whereBetween('commission_earned', [500, 1000]);
                } elseif ($range === 'low') {
                    $query->where('commission_earned', '<', 500);
                }
            }

            // Date range filter
            if ($request->filled('date_range')) {
                $dateRange = $request->date_range;
                if ($dateRange === 'today') {
                    $query->whereDate('created_at', today());
                } elseif ($dateRange === 'week') {
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($dateRange === 'month') {
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                }
            }

            // Handle sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'name':
                    $query->join('users', 'agents.user_id', '=', 'users.id')
                          ->orderBy('users.name', $sortOrder)
                          ->select('agents.*');
                    break;
                case 'commission_earned':
                    $query->orderBy('commission_earned', $sortOrder);
                    break;
                case 'total_sales':
                    $query->orderBy('total_sales', $sortOrder);
                    break;
                case 'account_status':
                    $query->orderBy('account_status', $sortOrder);
                    break;
                default:
                    $query->orderBy('created_at', $sortOrder);
            }

            $agents = $query->paginate(15)->withQueryString();

            // Get all countries for filter dropdown
            $countries = Country::orderBy('name')->get();

            // Calculate statistics
            $total = Agent::count();
            $active = Agent::where('account_status', 'active')->count();
            $pending = Agent::where('account_status', 'pending')->count();
            $suspended = Agent::where('account_status', 'suspended')->count();
            $emailVerified = Agent::where('email_verified', true)->count();
            $emailPending = Agent::where('email_verified', false)->count();

            // Performance stats
            $totalCommission = Agent::sum('commission_earned');
            $totalSales = Agent::sum('total_sales');
            $avgCommissionRate = Agent::avg('commission_rate');

            // Time-based stats
            $today = Agent::whereDate('created_at', today())->count();
            $thisWeek = Agent::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count();
            $thisMonth = Agent::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $stats = [
                'total' => $total,
                'active' => $active,
                'pending' => $pending,
                'suspended' => $suspended,
                'email_verified' => $emailVerified,
                'email_pending' => $emailPending,
                'total_commission' => $totalCommission,
                'total_sales' => $totalSales,
                'avg_commission_rate' => round($avgCommissionRate, 2),
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
                'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 1) : 0,
                'suspended_percentage' => $total > 0 ? round(($suspended / $total) * 100, 1) : 0,
                'verified_percentage' => $total > 0 ? round(($emailVerified / $total) * 100, 1) : 0,
                'today' => $today,
                'this_week' => $thisWeek,
                'this_month' => $thisMonth,
            ];

            return view('admin.agent.index', compact(
                'agents',
                'countries',
                'stats'
            ));
        } catch (\Exception $e) {
            Log::error('Admin Agent Index Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('admin.dashboard.home')
                ->with('error', 'An error occurred while loading agents.');
        }
    }

    public function show(Agent $agent)
    {
        $agent->load(['user', 'country', 'businessProfile.user']);

        return view('admin.agent.show', compact('agent'));
    }

    public function activate(Agent $agent)
    {
        try {
            $agent->activate();

            return redirect()->back()->with('success', 'Agent account activated successfully.');
        } catch (\Exception $e) {
            Log::error('Agent Activation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to activate agent account.');
        }
    }

    public function suspend(Agent $agent)
    {
        try {
            $agent->suspend();

            return redirect()->back()->with('success', 'Agent account suspended successfully.');
        } catch (\Exception $e) {
            Log::error('Agent Suspension Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to suspend agent account.');
        }
    }

    public function verifyEmail(Agent $agent)
    {
        try {
            $agent->verifyEmail();

            return redirect()->back()->with('success', 'Agent email verified successfully.');
        } catch (\Exception $e) {
            Log::error('Agent Email Verification Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to verify agent email.');
        }
    }

    public function destroy(Agent $agent)
    {
        try {
            $agent->delete();

            return redirect()->route('admin.agents.index')->with('success', 'Agent deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Agent Deletion Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete agent.');
        }
    }
}
