<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Referral;
use App\Models\User;

class ReferralController extends Controller
{
    /**
     * Display a listing of referrals
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get filter parameters
        $search = $request->get('search');
        $status = $request->get('status');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Build query
        $query = Referral::where('agent_id', $user->id)
            ->with(['user', 'commissions']);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get referrals with pagination
        $referrals = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Referral::where('agent_id', $user->id)->count(),
            'active' => Referral::where('agent_id', $user->id)->where('status', 'active')->count(),
            'pending' => Referral::where('agent_id', $user->id)->where('status', 'pending')->count(),
            'inactive' => Referral::where('agent_id', $user->id)->where('status', 'inactive')->count(),
            'rejected' => Referral::where('agent_id', $user->id)->where('status', 'rejected')->count(),
            'total_commissions' => Referral::where('referrals.agent_id', $user->id)
                ->join('commissions', 'referrals.id', '=', 'commissions.referral_id')
                ->sum('commissions.amount'),
        ];

        return view('agent.referrals.index', compact('referrals', 'stats', 'search', 'status', 'sortBy', 'sortOrder'));
    }

    /**
     * Show the form for creating a new referral
     */
    public function create()
    {
        return view('agent.referrals.form', [
            'referral' => null,
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created referral
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:referrals,email',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:pending,active,inactive,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['agent_id'] = Auth::id();

        // Generate unique referral code (handled by model boot method)
        $referral = Referral::create($validated);

        return redirect()
            ->route('agent.referrals.show', $referral->id)
            ->with('success', 'Referral created successfully!');
    }

    /**
     * Display the specified referral
     */
    public function show($id)
    {
        $referral = Referral::where('agent_id', Auth::id())
            ->with(['user', 'commissions', 'agent'])
            ->findOrFail($id);

        // Get commission statistics for this referral
        $commissionStats = [
            'total' => $referral->commissions()->sum('amount'),
            'paid' => $referral->commissions()->where('status', 'paid')->sum('amount'),
            'pending' => $referral->commissions()->where('status', 'pending')->sum('amount'),
            'count' => $referral->commissions()->count(),
        ];

        return view('agent.referrals.show', compact('referral', 'commissionStats'));
    }

    /**
     * Show the form for editing the specified referral
     */
    public function edit($id)
    {
        $referral = Referral::where('agent_id', Auth::id())->findOrFail($id);

        return view('agent.referrals.form', [
            'referral' => $referral,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified referral
     */
    public function update(Request $request, $id)
    {
        $referral = Referral::where('agent_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:referrals,email,' . $referral->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:pending,active,inactive,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $referral->update($validated);

        return redirect()
            ->route('agent.referrals.show', $referral->id)
            ->with('success', 'Referral updated successfully!');
    }

    /**
     * Remove the specified referral
     */
    public function destroy($id)
    {
        $referral = Referral::where('agent_id', Auth::id())->findOrFail($id);

        // Soft delete
        $referral->delete();

        return redirect()
            ->route('agent.referrals.index')
            ->with('success', 'Referral deleted successfully!');
    }

    /**
     * Update referral status
     */
    public function updateStatus(Request $request, $id)
    {
        $referral = Referral::where('agent_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,active,inactive,rejected',
        ]);

        $referral->update(['status' => $validated['status']]);

        return back()->with('success', 'Status updated successfully!');
    }

    /**
     * Print referrals report
     */
    public function print(Request $request)
    {
        $user = Auth::user();

        // Get all referrals
        $referrals = Referral::where('agent_id', $user->id)
            ->with(['user', 'commissions'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $referrals->count(),
            'active' => $referrals->where('status', 'active')->count(),
            'pending' => $referrals->where('status', 'pending')->count(),
            'inactive' => $referrals->where('status', 'inactive')->count(),
            'rejected' => $referrals->where('status', 'rejected')->count(),
            'total_commissions' => $referrals->sum(function($ref) {
                return $ref->commissions->sum('amount');
            }),
        ];

        return view('agent.referrals.print', compact('referrals', 'stats'));
    }
}
