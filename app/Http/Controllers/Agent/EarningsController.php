<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentCredit;
use App\Models\Commission;
use App\Models\Credit;
use App\Models\CreditValue;
use App\Models\Target;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EarningsController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $agentId = auth()->id();

        // ── Credits ───────────────────────────────────────────────────
        $agentCredit    = AgentCredit::where('agent_id', $agentId)->first();
        $totalCredits   = (float) ($agentCredit?->total_credits ?? 0);
        $creditValue    = CreditValue::latest()->first();
        $multiplier     = (float) ($creditValue?->value ?? 100);
        $monetaryValue  = $totalCredits * $multiplier;
        $creditsCatalog = Credit::all();

        // ── Commission earning history ─────────────────────────────────
        $query = Commission::where('agent_id', $agentId)
            ->with(['vendor.businessProfile', 'order'])
            ->when($request->vendor_id, fn($q) => $q->where('vendor_id', $request->vendor_id))
            ->when($request->status,    fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->search,    fn($q) =>
                $q->where('reference', 'like', "%{$request->search}%")
            );

        $earnings = $query->latest()->paginate(20)->withQueryString();

        // ── Stats ──────────────────────────────────────────────────────
        $base = Commission::where('agent_id', $agentId);

        $thisMonthEarned = (clone $base)
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $lastMonthEarned = (clone $base)
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $stats = [
            'total_credits'  => $totalCredits,
            'monetary_value' => $monetaryValue,
            'multiplier'     => $multiplier,
            'total_earned'   => (clone $base)->where('status', 'paid')->sum('amount'),
            'pending'        => (clone $base)->where('status', 'pending')->sum('amount'),
            'this_month'     => $thisMonthEarned,
            'last_month'     => $lastMonthEarned,
            'total_count'    => (clone $base)->count(),
            'paid_count'     => (clone $base)->where('status', 'paid')->count(),
            'pending_count'  => (clone $base)->where('status', 'pending')->count(),
        ];

        // ── Monthly chart (last 6 months) ──────────────────────────────
        $last6 = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        $paidRaw = Commission::where('agent_id', $agentId)
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')->pluck('total', 'month');

        $chartData = $last6->mapWithKeys(fn($m) => [$m => (float) ($paidRaw[$m] ?? 0)])->toArray();

        // ── Vendor breakdown ───────────────────────────────────────────
        $vendorBreakdown = Commission::where('agent_id', $agentId)
            ->select('vendor_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->with('vendor.businessProfile')
            ->groupBy('vendor_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $myVendors = Vendor::where('agent_id', $agentId)->with('businessProfile')->get();

        // ── Targets ────────────────────────────────────────────────────
        $yearlyEarned = (clone $base)
            ->where('status', 'paid')
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $weeklyEarned = (clone $base)
            ->where('status', 'paid')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('amount');

        $targets = Target::where(function ($q) {
            $q->whereDate('end_at', '>=', now())->orWhereNull('end_at');
        })->latest()->get()->map(function ($t) use ($thisMonthEarned, $yearlyEarned, $weeklyEarned) {
            $compare = match ($t->target_type) {
                'monthly' => $thisMonthEarned,
                'yearly'  => $yearlyEarned,
                'weekly'  => $weeklyEarned,
                default   => $thisMonthEarned,
            };
            $t->progress   = $compare;
            $t->percentage = $t->progressFor($compare);
            $t->reached    = $compare >= $t->target_amount;
            return $t;
        });

        return view('agent.earnings.index', compact(
            'earnings', 'stats', 'chartData', 'vendorBreakdown',
            'myVendors', 'agentCredit', 'creditsCatalog', 'targets'
        ));
    }

    // ─── PRINT ────────────────────────────────────────────────────────
    public function print(Request $request)
    {
        $agentId = auth()->id();

        $credits  = AgentCredit::where('agent_id', $agentId)->first();
        $cvValue  = CreditValue::latest()->value('value') ?? 100;

        $earnings = Commission::where('agent_id', $agentId)
            ->with(['vendor.businessProfile', 'order'])
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()->get();

        $total   = $earnings->where('status', 'paid')->sum('amount');
        $pending = $earnings->where('status', 'pending')->sum('amount');
        $balance = $credits?->total_credits ?? 0;

        return view('agent.earnings.print', compact('earnings', 'total', 'pending', 'balance', 'cvValue'));
    }

    // ─── EXPORT CSV ───────────────────────────────────────────────────
    public function export(Request $request)
    {
        $agentId = auth()->id();

        $earnings = Commission::where('agent_id', $agentId)
            ->with(['vendor.businessProfile', 'order'])
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->status,    fn($q) => $q->where('status', $request->status))
            ->latest()->get();

        $filename = 'earnings-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($earnings) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Reference', 'Vendor', 'Order #', 'Amount', 'Currency', 'Status', 'Date']);
            foreach ($earnings as $e) {
                fputcsv($out, [
                    $e->reference ?? "COM-{$e->id}",
                    $e->vendor?->businessProfile?->business_name ?? 'N/A',
                    $e->order?->order_number ?? 'N/A',
                    number_format($e->amount, 2),
                    $e->currency ?? 'USD',
                    ucfirst($e->status),
                    $e->created_at->format('Y-m-d'),
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
