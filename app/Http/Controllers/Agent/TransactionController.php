<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentCredit;
use App\Models\CreditTransaction;
use App\Models\CreditValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $agentId = auth()->id();

        $query = CreditTransaction::where('agent_id', $agentId)
            ->when($request->search, fn($q) =>
                $q->where('transaction_type', 'like', "%{$request->search}%")
            )
            ->when($request->type, fn($q) =>
                $q->where('transaction_type', $request->type)
            )
            ->when($request->date_from, fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from)
            )
            ->when($request->date_to, fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to)
            );

        $transactions = $query->latest()->paginate(20)->withQueryString();

        // ── Stats ──────────────────────────────────────────────────────
        $base        = CreditTransaction::where('agent_id', $agentId);
        $agentCredit = AgentCredit::where('agent_id', $agentId)->first();
        $creditValue = CreditValue::latest()->first();
        $multiplier  = (float) ($creditValue?->value ?? 100);

        $thisMonth = (clone $base)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('credits');

        $lastMonth = (clone $base)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('credits');

        $stats = [
            'total_credits'    => (float) ($agentCredit?->total_credits ?? 0),
            'monetary_value'   => (float) ($agentCredit?->total_credits ?? 0) * $multiplier,
            'multiplier'       => $multiplier,
            'total_transacted' => (clone $base)->sum('credits'),
            'total_count'      => (clone $base)->count(),
            'this_month'       => $thisMonth,
            'last_month'       => $lastMonth,
            'types'            => (clone $base)->distinct()->pluck('transaction_type'),
        ];

        // ── Chart — last 6 months ──────────────────────────────────────
        $last6 = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        $rawChart = CreditTransaction::where('agent_id', $agentId)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(credits) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $chartMonths = $last6->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))
                             ->values()->toArray();
        $chartData   = $last6->map(fn($m) => (float) ($rawChart[$m] ?? 0))->values()->toArray();

        // ── Type breakdown ─────────────────────────────────────────────
        $typeBreakdown = CreditTransaction::where('agent_id', $agentId)
            ->select('transaction_type', DB::raw('SUM(credits) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('transaction_type')
            ->orderByDesc('total')
            ->get();

        return view('agent.transactions.index', compact(
            'transactions', 'stats', 'chartMonths', 'chartData',
            'typeBreakdown', 'agentCredit'
        ));
    }

    // ─── SHOW ─────────────────────────────────────────────────────────
    public function show($id)
    {
        $transaction = CreditTransaction::where('agent_id', auth()->id())
            ->findOrFail($id);

        return view('agent.transactions.show', compact('transaction'));
    }

    // ─── PRINT ────────────────────────────────────────────────────────
    public function print(Request $request)
    {
        $agentId     = auth()->id();
        $agentCredit = AgentCredit::where('agent_id', $agentId)->first();
        $multiplier  = CreditValue::latest()->value('value') ?? 100;

        $transactions = CreditTransaction::where('agent_id', $agentId)
            ->when($request->date_from, fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from)
            )
            ->when($request->date_to, fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to)
            )
            ->when($request->type, fn($q) =>
                $q->where('transaction_type', $request->type)
            )
            ->latest()
            ->get();

        $totalCredits  = $transactions->sum('credits');
        $balance       = $agentCredit?->total_credits ?? 0;
        $monetaryValue = $balance * $multiplier;

        return view('agent.transactions.print', compact(
            'transactions', 'totalCredits', 'balance', 'monetaryValue', 'multiplier'
        ));
    }

    // ─── EXPORT CSV ───────────────────────────────────────────────────
    public function export(Request $request)
    {
        $agentId = auth()->id();

        $transactions = CreditTransaction::where('agent_id', $agentId)
            ->when($request->date_from, fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from)
            )
            ->when($request->date_to, fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to)
            )
            ->when($request->type, fn($q) =>
                $q->where('transaction_type', $request->type)
            )
            ->latest()
            ->get();

        $filename = 'credit-transactions-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($transactions) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'Transaction Type', 'Credits', 'Date']);
            foreach ($transactions as $t) {
                fputcsv($out, [
                    str_pad($t->id, 5, '0', STR_PAD_LEFT),
                    ucfirst(str_replace('_', ' ', $t->transaction_type)),
                    number_format($t->credits, 2),
                    $t->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
