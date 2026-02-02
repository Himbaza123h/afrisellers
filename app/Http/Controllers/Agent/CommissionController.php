<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Commission;
use App\Models\Referral;
use Carbon\Carbon;

class CommissionController extends Controller
{
    /**
     * Display a listing of commissions
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get filter parameters
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $dateFilter = $request->get('date_filter', 'all');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Build query
        $query = Commission::where('commissions.agent_id', $user->id)
            ->with(['referral.user', 'agent']);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('referral', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('referral_code', 'like', "%{$search}%");
                });
            });
        }

        // Apply status filter
        if ($status && $status !== 'all') {
            $query->where('commissions.status', $status);
        }

        // Apply date filter
        if ($dateFilter && $dateFilter !== 'all') {
            switch($dateFilter) {
                case 'today':
                    $query->whereDate('commissions.created_at', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('commissions.created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('commissions.created_at', Carbon::now()->month)
                          ->whereYear('commissions.created_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $query->whereYear('commissions.created_at', Carbon::now()->year);
                    break;
            }
        }

        // Apply sorting
        $query->orderBy('commissions.' . $sortBy, $sortOrder);

        // Get commissions with pagination
        $commissions = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Commission::where('commissions.agent_id', $user->id)->count(),
            'paid' => Commission::where('commissions.agent_id', $user->id)
                ->where('commissions.status', 'paid')
                ->count(),
            'pending' => Commission::where('commissions.agent_id', $user->id)
                ->where('commissions.status', 'pending')
                ->count(),
            'processing' => Commission::where('commissions.agent_id', $user->id)
                ->where('commissions.status', 'processing')
                ->count(),
            'total_amount' => Commission::where('commissions.agent_id', $user->id)
                ->sum('commissions.amount'),
            'paid_amount' => Commission::where('commissions.agent_id', $user->id)
                ->where('commissions.status', 'paid')
                ->sum('commissions.amount'),
            'pending_amount' => Commission::where('commissions.agent_id', $user->id)
                ->where('commissions.status', 'pending')
                ->sum('commissions.amount'),
        ];

        // Get period statistics for comparison
        $periodStats = $this->getPeriodStats($user->id, $dateFilter);

        return view('agent.commissions.index', compact(
            'commissions',
            'stats',
            'periodStats',
            'search',
            'status',
            'dateFilter',
            'sortBy',
            'sortOrder'
        ));
    }

    /**
     * Display the specified commission
     */
    public function show($id)
    {
        $commission = Commission::where('commissions.agent_id', Auth::id())
            ->with(['referral.user', 'agent'])
            ->findOrFail($id);

        return view('agent.commissions.show', compact('commission'));
    }

    /**
     * Print commissions report
     */
    public function print(Request $request)
    {
        $user = Auth::user();

        // Get filter parameters
        $status = $request->get('status', 'all');
        $dateFilter = $request->get('date_filter', 'all');

        // Build query
        $query = Commission::where('commissions.agent_id', $user->id)
            ->with(['referral.user', 'agent']);

        // Apply status filter
        if ($status && $status !== 'all') {
            $query->where('commissions.status', $status);
        }

        // Apply date filter
        if ($dateFilter && $dateFilter !== 'all') {
            switch($dateFilter) {
                case 'today':
                    $query->whereDate('commissions.created_at', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('commissions.created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('commissions.created_at', Carbon::now()->month)
                          ->whereYear('commissions.created_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $query->whereYear('commissions.created_at', Carbon::now()->year);
                    break;
            }
        }

        // Get all commissions
        $commissions = $query->orderBy('commissions.created_at', 'desc')->get();

        // Calculate statistics
        $stats = [
            'total' => $commissions->count(),
            'paid' => $commissions->where('status', 'paid')->count(),
            'pending' => $commissions->where('status', 'pending')->count(),
            'processing' => $commissions->where('status', 'processing')->count(),
            'total_amount' => $commissions->sum('amount'),
            'paid_amount' => $commissions->where('status', 'paid')->sum('amount'),
            'pending_amount' => $commissions->where('status', 'pending')->sum('amount'),
            'processing_amount' => $commissions->where('status', 'processing')->sum('amount'),
        ];

        // Get filter labels for report
        $filterLabels = [
            'status' => $status !== 'all' ? ucfirst($status) : 'All Status',
            'date' => $this->getDateFilterLabel($dateFilter),
        ];

        return view('agent.commissions.print', compact('commissions', 'stats', 'filterLabels'));
    }

    /**
     * Get period statistics
     */
    private function getPeriodStats($agentId, $dateFilter)
    {
        $query = Commission::where('commissions.agent_id', $agentId);

        // Apply date filter
        if ($dateFilter && $dateFilter !== 'all') {
            switch($dateFilter) {
                case 'today':
                    $query->whereDate('commissions.created_at', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('commissions.created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('commissions.created_at', Carbon::now()->month)
                          ->whereYear('commissions.created_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $query->whereYear('commissions.created_at', Carbon::now()->year);
                    break;
            }
        }

        return [
            'count' => $query->count(),
            'amount' => $query->sum('commissions.amount'),
            'paid' => $query->where('commissions.status', 'paid')->sum('commissions.amount'),
            'pending' => $query->where('commissions.status', 'pending')->sum('commissions.amount'),
        ];
    }

    /**
     * Get date filter label
     */
    private function getDateFilterLabel($filter)
    {
        return match($filter) {
            'today' => 'Today',
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'this_year' => 'This Year',
            default => 'All Time',
        };
    }
}
