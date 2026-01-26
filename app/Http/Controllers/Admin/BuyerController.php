<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buyer\Buyer;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BuyerController extends Controller
{

    public function index(Request $request)
{
    try {
        $query = Buyer::with(['user', 'country']);

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
                ->orWhere('city', 'like', "%{$search}%");
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
                $query->join('users', 'buyers.user_id', '=', 'users.id')
                      ->orderBy('users.name', $sortOrder)
                      ->select('buyers.*');
                break;
            case 'account_status':
                $query->orderBy('account_status', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $buyers = $query->paginate(15)->withQueryString();

        // Get all countries for filter dropdown
        $countries = \App\Models\Country::orderBy('name')->get();

        // Calculate statistics
        $total = Buyer::count();
        $active = Buyer::where('account_status', 'active')->count();
        $pending = Buyer::where('account_status', 'pending')->count();
        $suspended = Buyer::where('account_status', 'suspended')->count();
        $emailVerified = Buyer::where('email_verified', true)->count();
        $emailPending = Buyer::where('email_verified', false)->count();

        // Time-based stats
        $today = Buyer::whereDate('created_at', today())->count();
        $thisWeek = Buyer::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        $thisMonth = Buyer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $stats = [
            'total' => $total,
            'active' => $active,
            'pending' => $pending,
            'suspended' => $suspended,
            'email_verified' => $emailVerified,
            'email_pending' => $emailPending,
            'active_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
            'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 1) : 0,
            'suspended_percentage' => $total > 0 ? round(($suspended / $total) * 100, 1) : 0,
            'verified_percentage' => $total > 0 ? round(($emailVerified / $total) * 100, 1) : 0,
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
        ];

        return view('admin.buyer.index', compact(
            'buyers',
            'countries',
            'stats'
        ));
    } catch (\Exception $e) {
        Log::error('Admin Buyer Index Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.dashboard.home')
            ->with('error', 'An error occurred while loading buyers.');
    }
}
    /**
     * Show the specified buyer.
     */
    public function show(Buyer $buyer)
    {
        try {
            $buyer->load(['user', 'country']);

            return view('admin.buyer.show', compact('buyer'));
        } catch (\Exception $e) {
            Log::error('Admin Buyer Show Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'buyer_id' => $buyer->id ?? null,
            ]);

            return redirect()->route('admin.buyer.index')
                ->with('error', 'An error occurred while loading the buyer details.');
        }
    }

    /**
     * Update buyer account status.
     */
    public function updateStatus(Request $request, Buyer $buyer)
    {
        $validated = $request->validate([
            'account_status' => 'required|in:active,pending,suspended',
        ]);

        try {
            $buyer->update(['account_status' => $validated['account_status']]);

            Log::info('Buyer account status updated', [
                'buyer_id' => $buyer->id,
                'new_status' => $validated['account_status'],
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.buyer.index')
                ->with('success', 'Buyer account status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update buyer account status', [
                'buyer_id' => $buyer->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to update buyer account status. Please try again.']);
        }
    }

    public function print()
{
    $buyers = Buyer::with(['user', 'country'])->get();

    // Calculate statistics
    $total = Buyer::count();
    $active = Buyer::where('account_status', 'active')->count();
    $pending = Buyer::where('account_status', 'pending')->count();
    $suspended = Buyer::where('account_status', 'suspended')->count();
    $emailVerified = Buyer::where('email_verified', true)->count();
    $emailPending = Buyer::where('email_verified', false)->count();

    $today = Buyer::whereDate('created_at', today())->count();
    $thisWeek = Buyer::whereBetween('created_at', [
        now()->startOfWeek(),
        now()->endOfWeek()
    ])->count();
    $thisMonth = Buyer::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $stats = [
        'total' => $total,
        'active' => $active,
        'pending' => $pending,
        'suspended' => $suspended,
        'email_verified' => $emailVerified,
        'email_pending' => $emailPending,
        'active_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
        'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 1) : 0,
        'suspended_percentage' => $total > 0 ? round(($suspended / $total) * 100, 1) : 0,
        'verified_percentage' => $total > 0 ? round(($emailVerified / $total) * 100, 1) : 0,
        'today' => $today,
        'this_week' => $thisWeek,
        'this_month' => $thisMonth,
    ];

    return view('admin.buyer.print', compact('buyers', 'stats'));
}

public function switchToBuyer(Buyer $buyer)
{
    try {
        $user = $buyer->user;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user account found for this buyer.'
            ], 404);
        }

        $buyerRole = Role::where('slug', 'buyer')->first();

        if (!$buyerRole) {
            return response()->json([
                'success' => false,
                'message' => 'Buyer role not found in system.'
            ], 404);
        }

        // Attach role if not exists
        if (!$user->roles()->where('role_id', $buyerRole->id)->exists()) {
            $user->roles()->attach($buyerRole->id);
        }

        // Generate login token
        $token = \Illuminate\Support\Str::random(60);
        \Illuminate\Support\Facades\Cache::put(
            'buyer_login_token_' . $token,
            $user->id,
            now()->addMinutes(5)
        );

        return response()->json([
            'success' => true,
            'message' => 'Ready to switch to Buyer Dashboard',
            'login_url' => route('auth.buyer.token-login', ['token' => $token])
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to switch: ' . $e->getMessage()
        ], 500);
    }
}
}
