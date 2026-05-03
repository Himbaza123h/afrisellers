<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentCredit;
use App\Models\Credit;
use App\Models\CreditTransaction;
use App\Models\CreditValue;
use App\Models\Target;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $search     = $request->get('search');
        $typeFilter = $request->get('type', 'all');
        $dateFilter = $request->get('date_filter', 'all');
        $sortOrder  = $request->get('sort_order', 'desc');

        // ── Credit transactions query ──────────────────────────────────
        $query = CreditTransaction::where('agent_id', $user->id);

        if ($search) {
            $query->where('transaction_type', 'like', "%{$search}%");
        }

        if ($typeFilter && $typeFilter !== 'all') {
            $query->where('transaction_type', $typeFilter);
        }

        $this->applyDateFilter($query, $dateFilter, 'created_at');
        $query->orderBy('created_at', $sortOrder);

        $transactions = $query->paginate(15)->withQueryString();

        // ── Credit info ────────────────────────────────────────────────
        $agentCredit    = AgentCredit::where('agent_id', $user->id)->first();
        $creditValue    = CreditValue::latest()->first();
        $multiplier     = (float) ($creditValue?->value ?? 100);
        $totalCredits   = (float) ($agentCredit?->total_credits ?? 0);
        $monetaryValue  = $totalCredits * $multiplier;
        $creditsCatalog = Credit::all();

        // ── Stats ──────────────────────────────────────────────────────
        $base = CreditTransaction::where('agent_id', $user->id);

        $thisMonthCredits = (clone $base)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('credits');

        $lastMonthCredits = (clone $base)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('credits');

        $thisWeekCredits = (clone $base)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('credits');

        $thisYearCredits = (clone $base)
            ->whereYear('created_at', now()->year)
            ->sum('credits');

        $stats = [
            'total_credits'       => $totalCredits,
            'monetary_value'      => $monetaryValue,
            'multiplier'          => $multiplier,
            'total_transacted'    => (clone $base)->sum('credits'),
            'total_count'         => (clone $base)->count(),
            'this_month_credits'  => $thisMonthCredits,
            'last_month_credits'  => $lastMonthCredits,
            'this_week_credits'   => $thisWeekCredits,
            'this_year_credits'   => $thisYearCredits,
            'this_month_value'    => $thisMonthCredits * $multiplier,
            'last_month_value'    => $lastMonthCredits * $multiplier,
        ];

        $periodStats = $this->getPeriodStats($user->id, $dateFilter);

        // ── Available transaction types for filter ─────────────────────
        $availableTypes = CreditTransaction::where('agent_id', $user->id)
            ->distinct()
            ->pluck('transaction_type');

        // ── Monthly chart — last 6 months ──────────────────────────────
        $last6 = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        $creditsRaw = CreditTransaction::where('agent_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') as month, SUM(credits) as total")
            ->groupBy('month')->pluck('total', 'month');

        $chartMonths  = $last6->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values()->toArray();
        $chartCredits = $last6->map(fn($m) => (float) ($creditsRaw[$m] ?? 0))->values()->toArray();
        $chartValues  = array_map(fn($c) => round($c * $multiplier, 2), $chartCredits);

        // ── Type breakdown ─────────────────────────────────────────────
        $typeBreakdown = CreditTransaction::where('agent_id', $user->id)
            ->select('transaction_type', DB::raw('SUM(credits) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('transaction_type')
            ->orderByDesc('total')
            ->get();

        // ── Targets (based on credit monetary value) ───────────────────
        $targets = Target::where(function ($q) {
            $q->whereDate('end_at', '>=', now())->orWhereNull('end_at');
        })->latest()->get()->map(function ($t) use ($thisMonthCredits, $thisYearCredits, $thisWeekCredits, $multiplier) {
            $creditsCompare = match ($t->target_type) {
                'monthly' => $thisMonthCredits,
                'yearly'  => $thisYearCredits,
                'weekly'  => $thisWeekCredits,
                default   => $thisMonthCredits,
            };
            $compare           = $creditsCompare * $multiplier;
            $t->progress       = $compare;
            $t->percentage     = $t->progressFor($compare);
            $t->reached        = $compare >= $t->target_amount;
            return $t;
        });

        return view('agent.commissions.index', compact(
            'transactions', 'stats', 'periodStats',
            'search', 'typeFilter', 'dateFilter', 'sortOrder',
            'chartMonths', 'chartCredits', 'chartValues',
            'typeBreakdown', 'availableTypes',
            'agentCredit', 'creditsCatalog', 'monetaryValue', 'multiplier',
            'targets'
        ));
    }

    public function show($id)
    {
        $transaction = CreditTransaction::where('agent_id', Auth::id())
            ->findOrFail($id);

        return view('agent.commissions.show', compact('transaction'));
    }

    public function print(Request $request)
    {
        $user       = Auth::user();
        $dateFilter = $request->get('date_filter', 'all');
        $typeFilter = $request->get('type', 'all');

        $query = CreditTransaction::where('agent_id', $user->id);

        if ($typeFilter && $typeFilter !== 'all') {
            $query->where('transaction_type', $typeFilter);
        }

        $this->applyDateFilter($query, $dateFilter, 'created_at');

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $creditValue = CreditValue::latest()->value('value') ?? 100;
        $agentCredit = AgentCredit::where('agent_id', $user->id)->first();

        $stats = [
            'total_count'    => $transactions->count(),
            'total_credits'  => $transactions->sum('credits'),
            'balance'        => $agentCredit?->total_credits ?? 0,
            'monetary_value' => ($agentCredit?->total_credits ?? 0) * $creditValue,
        ];

        $filterLabels = [
            'type' => $typeFilter !== 'all' ? ucwords(str_replace('_', ' ', $typeFilter)) : 'All Types',
            'date' => $this->getDateFilterLabel($dateFilter),
        ];

        return view('agent.commissions.print', compact('transactions', 'stats', 'filterLabels', 'creditValue'));
    }

    // ─── Helpers ──────────────────────────────────────────────────────
    private function applyDateFilter($query, string $filter, string $column): void
    {
        match ($filter) {
            'today'      => $query->whereDate($column, Carbon::today()),
            'this_week'  => $query->whereBetween($column, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            'this_month' => $query->whereMonth($column, Carbon::now()->month)->whereYear($column, Carbon::now()->year),
            'this_year'  => $query->whereYear($column, Carbon::now()->year),
            default      => null,
        };
    }

    private function getPeriodStats(int $agentId, string $dateFilter): array
    {
        $query = CreditTransaction::where('agent_id', $agentId);
        $this->applyDateFilter($query, $dateFilter, 'created_at');

        return [
            'count'   => $query->count(),
            'credits' => $query->sum('credits'),
        ];
    }

    private function getDateFilterLabel(string $filter): string
    {
        return match ($filter) {
            'today'      => 'Today',
            'this_week'  => 'This Week',
            'this_month' => 'This Month',
            'this_year'  => 'This Year',
            default      => 'All Time',
        };
    }
}
