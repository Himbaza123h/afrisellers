<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Referral;
use App\Models\Commission;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get filter period
        $filter = $request->get('filter', 'weekly');
        $dateRange = $this->getDateRange($filter, $request->get('date_range'));

        // Get agent's referrals
        $totalReferrals = Referral::where('agent_id', $user->id)->count();
        $activeReferrals = Referral::where('agent_id', $user->id)
            ->where('status', 'active')
            ->count();
        $pendingReferrals = Referral::where('agent_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Get commissions
        $totalCommissions = Commission::where('agent_id', $user->id)
            ->sum('amount');
        $paidCommissions = Commission::where('agent_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');
        $pendingCommissions = Commission::where('agent_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        // Period commissions
        $periodCommissions = Commission::where('agent_id', $user->id)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('amount');

        // Previous period for comparison
        $previousPeriodCommissions = Commission::where('agent_id', $user->id)
            ->whereBetween('created_at', [$dateRange['previous_start'], $dateRange['previous_end']])
            ->sum('amount');

        $commissionPercentage = $previousPeriodCommissions > 0
            ? round((($periodCommissions - $previousPeriodCommissions) / $previousPeriodCommissions) * 100, 1)
            : 0;

        // Recent referrals
        $recentReferrals = Referral::where('agent_id', $user->id)
            ->with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($referral) {
                return [
                    'id' => $referral->id,
                    'name' => $referral->user->name ?? 'N/A',
                    'email' => $referral->user->email ?? 'N/A',
                    'status' => ucfirst($referral->status),
                    'date' => $referral->created_at->format('M d, Y'),
                    'color' => $referral->status === 'active' ? 'green' : ($referral->status === 'pending' ? 'yellow' : 'red'),
                ];
            });

        // Commission history
        $commissionHistory = Commission::where('agent_id', $user->id)
            ->with('referral.user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($commission) {
                return [
                    'id' => $commission->id,
                    'amount' => $commission->amount,
                    'referral' => $commission->referral->user->name ?? 'N/A',
                    'status' => ucfirst($commission->status),
                    'date' => $commission->created_at->format('M d, Y'),
                    'color' => $commission->status === 'paid' ? 'green' : 'yellow',
                ];
            });

        // Chart data
        $chartData = $this->getChartData($user->id, $filter);

        // Referral statistics
        $referralStats = [
            [
                'status' => 'Active',
                'count' => $activeReferrals,
                'percentage' => $totalReferrals > 0 ? round(($activeReferrals / $totalReferrals) * 100, 1) : 0,
                'icon' => 'user-check',
                'color' => 'green'
            ],
            [
                'status' => 'Pending',
                'count' => $pendingReferrals,
                'percentage' => $totalReferrals > 0 ? round(($pendingReferrals / $totalReferrals) * 100, 1) : 0,
                'icon' => 'clock',
                'color' => 'yellow'
            ],
            [
                'status' => 'Inactive',
                'count' => $totalReferrals - $activeReferrals - $pendingReferrals,
                'percentage' => $totalReferrals > 0 ? round((($totalReferrals - $activeReferrals - $pendingReferrals) / $totalReferrals) * 100, 1) : 0,
                'icon' => 'user-slash',
                'color' => 'gray'
            ],
        ];

        return view('agent.dashboard.index', compact(
            'totalReferrals',
            'activeReferrals',
            'pendingReferrals',
            'totalCommissions',
            'paidCommissions',
            'pendingCommissions',
            'periodCommissions',
            'commissionPercentage',
            'recentReferrals',
            'commissionHistory',
            'chartData',
            'referralStats'
        ));
    }

    public function print(Request $request)
    {
        $user = Auth::user();

        // Get all data for print
        $totalReferrals = Referral::where('agent_id', $user->id)->count();
        $activeReferrals = Referral::where('agent_id', $user->id)->where('status', 'active')->count();
        $pendingReferrals = Referral::where('agent_id', $user->id)->where('status', 'pending')->count();

        $totalCommissions = Commission::where('agent_id', $user->id)->sum('amount');
        $paidCommissions = Commission::where('agent_id', $user->id)->where('status', 'paid')->sum('amount');
        $pendingCommissions = Commission::where('agent_id', $user->id)->where('status', 'pending')->sum('amount');

        $referrals = Referral::where('agent_id', $user->id)->with('user')->latest()->get();
        $commissions = Commission::where('agent_id', $user->id)->with('referral.user')->latest()->get();

        return view('agent.dashboard.print', compact(
            'totalReferrals',
            'activeReferrals',
            'pendingReferrals',
            'totalCommissions',
            'paidCommissions',
            'pendingCommissions',
            'referrals',
            'commissions'
        ));
    }

    private function getDateRange($filter, $customRange = null)
    {
        $now = Carbon::now();

        switch($filter) {
            case 'weekly':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $previousStart = $start->copy()->subWeek();
                $previousEnd = $end->copy()->subWeek();
                break;
            case 'monthly':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $previousStart = $start->copy()->subMonth();
                $previousEnd = $end->copy()->subMonth();
                break;
            case 'yearly':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $previousStart = $start->copy()->subYear();
                $previousEnd = $end->copy()->subYear();
                break;
            case 'custom':
                if ($customRange) {
                    $dates = explode(' to ', $customRange);
                    $start = Carbon::parse($dates[0]);
                    $end = Carbon::parse($dates[1] ?? $dates[0]);
                    $diff = $start->diffInDays($end);
                    $previousStart = $start->copy()->subDays($diff);
                    $previousEnd = $end->copy()->subDays($diff);
                } else {
                    $start = $now->copy()->startOfWeek();
                    $end = $now->copy()->endOfWeek();
                    $previousStart = $start->copy()->subWeek();
                    $previousEnd = $end->copy()->subWeek();
                }
                break;
            default:
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $previousStart = $start->copy()->subWeek();
                $previousEnd = $end->copy()->subWeek();
        }

        return [
            'start' => $start,
            'end' => $end,
            'previous_start' => $previousStart,
            'previous_end' => $previousEnd,
        ];
    }

    private function getChartData($agentId, $filter)
    {
        $labels = [];
        $referrals = [];
        $commissions = [];

        if ($filter === 'yearly') {
            // Monthly data for the year
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->format('M Y');

                $referralCount = Referral::where('agent_id', $agentId)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $referrals[] = $referralCount;

                $commissionSum = Commission::where('agent_id', $agentId)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');
                $commissions[] = $commissionSum;
            }
        } else {
            // Daily data for week/month
            $days = $filter === 'monthly' ? 30 : 7;
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('M d');

                $referralCount = Referral::where('agent_id', $agentId)
                    ->whereDate('created_at', $date)
                    ->count();
                $referrals[] = $referralCount;

                $commissionSum = Commission::where('agent_id', $agentId)
                    ->whereDate('created_at', $date)
                    ->sum('amount');
                $commissions[] = $commissionSum;
            }
        }

        return [
            'labels' => $labels,
            'referrals' => $referrals,
            'commissions' => $commissions,
        ];
    }
}
