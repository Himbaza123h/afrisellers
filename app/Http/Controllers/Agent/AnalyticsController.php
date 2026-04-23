<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentSubscription;
use App\Models\Commission;
use App\Models\Referral;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // ─── Shared: agent's vendor IDs ───────────────────────────────────
    private function myVendorIds(): array
    {
        return Vendor::where('agent_id', auth()->id())
            ->pluck('id')
            ->toArray();
    }

    // ─── INDEX (Overview Dashboard) ───────────────────────────────────
    public function index(Request $request)
    {
        $agentId   = auth()->id();
        $vendorIds = $this->myVendorIds();

        $period    = $request->get('period', '6'); // months
        $startDate = now()->subMonths((int) $period)->startOfMonth();

        // ── Summary Cards ─────────────────────────────────────────────
        $summary = [
            'total_vendors'      => Vendor::where('agent_id', $agentId)->count(),
            'active_vendors'     => Vendor::where('agent_id', $agentId)->where('account_status', 'active')->count(),
            'total_earned'       => Commission::where('agent_id', $agentId)->where('status', 'paid')->sum('amount'),
            'earned_this_month'  => Commission::where('agent_id', $agentId)
                                        ->where('status', 'paid')
                                        ->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->sum('amount'),
            'total_referrals'    => Referral::where('agent_id', $agentId)->count(),
            'converted_referrals'=> Referral::where('agent_id', $agentId)->where('status', 'converted')->count(),
            'total_commissions'  => Commission::where('agent_id', $agentId)->count(),
            'pending_amount'     => Commission::where('agent_id', $agentId)->where('status', 'pending')->sum('amount'),
        ];

        $summary['conversion_rate'] = $summary['total_referrals'] > 0
            ? round(($summary['converted_referrals'] / $summary['total_referrals']) * 100, 1)
            : 0;

        // ── Monthly Earnings Chart ────────────────────────────────────
        $earningsChart = Commission::where('agent_id', $agentId)
            ->where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(commission_amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Monthly Vendor Growth ─────────────────────────────────────
        $vendorGrowthChart = Vendor::where('agent_id', $agentId)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Monthly Referral Chart ────────────────────────────────────
        $referralChart = Referral::where('agent_id', $agentId)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Top 5 Vendors by Commission ───────────────────────────────
        $topVendors = Commission::where('agent_id', $agentId)
            ->select('vendor_id', DB::raw('SUM(commission_amount) as total'), DB::raw('COUNT(*) as count'))
            ->with('vendor.businessProfile')
            ->groupBy('vendor_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── Commission Status Breakdown ───────────────────────────────
        $commissionBreakdown = Commission::where('agent_id', $agentId)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(commission_amount) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // ── Subscription Info ─────────────────────────────────────────
        $subscription = AgentSubscription::where('agent_id', $agentId)
            ->active()
            ->with('package')
            ->first();

        return view('agent.analytics.index', compact(
            'summary',
            'earningsChart',
            'vendorGrowthChart',
            'referralChart',
            'topVendors',
            'commissionBreakdown',
            'subscription',
            'period'
        ));
    }

    // ─── REFERRALS Analytics ──────────────────────────────────────────
    public function referrals(Request $request)
    {
        $agentId   = auth()->id();
        $period    = $request->get('period', '6');
        $startDate = now()->subMonths((int) $period)->startOfMonth();

        // ── Stats ─────────────────────────────────────────────────────
        $stats = [
            'total'     => Referral::where('agent_id', $agentId)->count(),
            'converted' => Referral::where('agent_id', $agentId)->where('status', 'converted')->count(),
            'pending'   => Referral::where('agent_id', $agentId)->where('status', 'pending')->count(),
            'rejected'  => Referral::where('agent_id', $agentId)->where('status', 'rejected')->count(),
            'this_month'=> Referral::where('agent_id', $agentId)
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count(),
        ];

        $stats['conversion_rate'] = $stats['total'] > 0
            ? round(($stats['converted'] / $stats['total']) * 100, 1)
            : 0;

        // ── Monthly Chart ─────────────────────────────────────────────
        $monthlyChart = Referral::where('agent_id', $agentId)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Status Breakdown ──────────────────────────────────────────
        $statusBreakdown = Referral::where('agent_id', $agentId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Recent Referrals ──────────────────────────────────────────
        $recentReferrals = Referral::where('agent_id', $agentId)
            ->latest()
            ->limit(10)
            ->get();

        return view('agent.analytics.referrals', compact(
            'stats',
            'monthlyChart',
            'statusBreakdown',
            'recentReferrals',
            'period'
        ));
    }

    // ─── VENDORS Analytics ────────────────────────────────────────────
    public function vendors(Request $request)
    {
        $agentId   = auth()->id();
        $period    = $request->get('period', '6');
        $startDate = now()->subMonths((int) $period)->startOfMonth();

        // ── Stats ─────────────────────────────────────────────────────
        $stats = [
            'total'     => Vendor::where('agent_id', $agentId)->count(),
            'active'    => Vendor::where('agent_id', $agentId)->where('account_status', 'active')->count(),
            'pending'   => Vendor::where('agent_id', $agentId)->where('account_status', 'pending')->count(),
            'suspended' => Vendor::where('agent_id', $agentId)->where('account_status', 'suspended')->count(),
            'this_month'=> Vendor::where('agent_id', $agentId)
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count(),
        ];

        $sub = AgentSubscription::where('agent_id', $agentId)->active()->with('package')->first();
        $stats['limit'] = (int) ($sub?->package?->max_vendors ?? 1);

        // ── Monthly Growth Chart ──────────────────────────────────────
        $growthChart = Vendor::where('agent_id', $agentId)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Status Breakdown ──────────────────────────────────────────
        $statusBreakdown = Vendor::where('agent_id', $agentId)
            ->select('account_status', DB::raw('COUNT(*) as count'))
            ->groupBy('account_status')
            ->pluck('count', 'account_status')
            ->toArray();

        // ── Top Vendors by Commissions ────────────────────────────────
        $topVendors = Commission::where('agent_id', $agentId)
            ->select('vendor_id', DB::raw('SUM(commission_amount) as total'), DB::raw('COUNT(*) as orders'))
            ->with('vendor.businessProfile')
            ->groupBy('vendor_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // ── Vendors by Country ────────────────────────────────────────
        $byCountry = Vendor::where('agent_id', $agentId)
            ->with('businessProfile.country')
            ->get()
            ->groupBy(fn($v) => $v->businessProfile?->country?->name ?? 'Unknown')
            ->map->count()
            ->sortDesc()
            ->take(8);

        // ── All Vendors Table ─────────────────────────────────────────
        $vendors = Vendor::where('agent_id', $agentId)
            ->with(['user', 'businessProfile.country'])
            ->latest()
            ->paginate(10);

        return view('agent.analytics.vendors', compact(
            'stats',
            'growthChart',
            'statusBreakdown',
            'topVendors',
            'byCountry',
            'vendors',
            'period'
        ));
    }

    // ─── COMMISSIONS Analytics ────────────────────────────────────────
    public function commissions(Request $request)
    {
        $agentId   = auth()->id();
        $period    = $request->get('period', '6');
        $startDate = now()->subMonths((int) $period)->startOfMonth();

        // ── Stats ─────────────────────────────────────────────────────
        $all = Commission::where('agent_id', $agentId);

        $stats = [
            'total_paid'       => (clone $all)->where('status', 'paid')->sum('amount'),
            'total_pending'    => (clone $all)->where('status', 'pending')->sum('amount'),
            'total_rejected'   => (clone $all)->where('status', 'rejected')->sum('amount'),
            'count_paid'       => (clone $all)->where('status', 'paid')->count(),
            'count_pending'    => (clone $all)->where('status', 'pending')->count(),
            'avg_commission'   => (clone $all)->where('status', 'paid')->avg('amount') ?? 0,
            'this_month'       => (clone $all)->where('status', 'paid')
                                        ->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->sum('amount'),
            'last_month'       => (clone $all)->where('status', 'paid')
                                        ->whereMonth('created_at', now()->subMonth()->month)
                                        ->whereYear('created_at', now()->subMonth()->year)
                                        ->sum('amount'),
        ];

        // ── Monthly Chart ─────────────────────────────────────────────
        $monthlyChart = Commission::where('agent_id', $agentId)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid"),
                DB::raw("SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Per-Vendor Breakdown ──────────────────────────────────────
        $vendorBreakdown = Commission::where('agent_id', $agentId)
            ->select('vendor_id', DB::raw('SUM(commission_amount) as total'), DB::raw('COUNT(*) as count'),
                     DB::raw("SUM(CASE WHEN status='paid' THEN amount ELSE 0 END) as paid"))
            ->with('vendor.businessProfile')
            ->groupBy('vendor_id')
            ->orderByDesc('total')
            ->get();

        // ── Rate Distribution ─────────────────────────────────────────
        $rateDistribution = Commission::where('agent_id', $agentId)
            ->whereNotNull('commission_rate')
            ->select('commission_rate', DB::raw('COUNT(*) as count'), DB::raw('SUM(commission_amount) as total'))
            ->groupBy('commission_rate')
            ->orderBy('commission_rate')
            ->get();

        // ── Recent Commissions ────────────────────────────────────────
        $recent = Commission::where('agent_id', $agentId)
            ->with(['vendor.businessProfile', 'order'])
            ->latest()
            ->limit(8)
            ->get();

        return view('agent.analytics.commissions', compact(
            'stats',
            'monthlyChart',
            'vendorBreakdown',
            'rateDistribution',
            'recent',
            'period'
        ));
    }
}
