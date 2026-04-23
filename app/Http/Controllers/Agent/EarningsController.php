<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Transaction;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EarningsController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $agentId = auth()->id();

        // All vendor IDs belonging to this agent
        $vendorUserIds = Vendor::where('agent_id', $agentId)
            ->pluck('user_id');

        // ── Base commission query ──────────────────────────────────────
        $query = Commission::where('agent_id', $agentId)
            ->with(['vendor.businessProfile', 'order'])
            ->when($request->vendor_id, fn($q) =>
                $q->where('vendor_id', $request->vendor_id)
            )
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->date_from, fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from)
            )
            ->when($request->date_to, fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to)
            )
            ->when($request->search, fn($q) =>
                $q->where('reference', 'like', "%{$request->search}%")
                  ->orWhereHas('order', fn($q2) =>
                        $q2->where('order_number', 'like', "%{$request->search}%")
                  )
            );

        $earnings = $query->latest()->paginate(20)->withQueryString();

        // ── Stats ──────────────────────────────────────────────────────
        $allCommissions = Commission::where('agent_id', $agentId);

        $stats = [
            'total_earned'   => (clone $allCommissions)->where('status', 'paid')->sum('amount'),
            'pending'        => (clone $allCommissions)->where('status', 'pending')->sum('amount'),
            'this_month'     => (clone $allCommissions)
                                    ->where('status', 'paid')
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->sum('amount'),
            'last_month'     => (clone $allCommissions)
                                    ->where('status', 'paid')
                                    ->whereMonth('created_at', now()->subMonth()->month)
                                    ->whereYear('created_at', now()->subMonth()->year)
                                    ->sum('amount'),
            'total_count'    => (clone $allCommissions)->count(),
            'paid_count'     => (clone $allCommissions)->where('status', 'paid')->count(),
            'pending_count'  => (clone $allCommissions)->where('status', 'pending')->count(),
        ];

        // ── Monthly chart data (last 6 months) ─────────────────────────
        $chartData = Commission::where('agent_id', $agentId)
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Per-vendor breakdown ────────────────────────────────────────
        $vendorBreakdown = Commission::where('agent_id', $agentId)
            ->select('vendor_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->with('vendor.businessProfile')
            ->groupBy('vendor_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── Vendors dropdown filter ─────────────────────────────────────
        $myVendors = Vendor::where('agent_id', $agentId)
            ->with('businessProfile')
            ->get();

        return view('agent.earnings.index', compact(
            'earnings', 'stats', 'chartData', 'vendorBreakdown', 'myVendors'
        ));
    }

    // ─── PRINT ────────────────────────────────────────────────────────
    public function print(Request $request)
    {
        $agentId = auth()->id();

        $earnings = Commission::where('agent_id', $agentId)
            ->with(['vendor.businessProfile', 'order'])
            ->when($request->date_from, fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from)
            )
            ->when($request->date_to, fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to)
            )
            ->latest()
            ->get();

        $total = $earnings->where('status', 'paid')->sum('amount');
        $pending = $earnings->where('status', 'pending')->sum('amount');

        return view('agent.earnings.print', compact('earnings', 'total', 'pending'));
    }

    // ─── EXPORT CSV ───────────────────────────────────────────────────
    public function export(Request $request)
    {
        $agentId = auth()->id();

        $earnings = Commission::where('agent_id', $agentId)
            ->with(['vendor.businessProfile', 'order'])
            ->when($request->date_from, fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from)
            )
            ->when($request->date_to, fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to)
            )
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->get();

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
