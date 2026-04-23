<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Vendor\Vendor;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $agentId  = auth()->id();
        $period   = (int) $request->get('period', 6);
        $type     = $request->get('type', 'earnings');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        // ── Vendor IDs for this agent ──────────────────────────────────
        $vendorIds = Vendor::where('agent_id', $agentId)->pluck('id');

        // ── Summary Stats ──────────────────────────────────────────────
        $summary = [
            'total_earned'    => Commission::where('agent_id', $agentId)->where('status', 'paid')->sum('amount'),
            'total_pending'   => Commission::where('agent_id', $agentId)->where('status', 'pending')->sum('amount'),
            'total_vendors'   => Vendor::where('agent_id', $agentId)->count(),
            'active_vendors'  => Vendor::where('agent_id', $agentId)->where('account_status', 'active')->count(),
            'this_month'      => Commission::where('agent_id', $agentId)
                                    ->where('status', 'paid')
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->sum('amount'),
            'last_month'      => Commission::where('agent_id', $agentId)
                                    ->where('status', 'paid')
                                    ->whereMonth('created_at', now()->subMonth()->month)
                                    ->whereYear('created_at', now()->subMonth()->year)
                                    ->sum('amount'),
            'total_commissions' => Commission::where('agent_id', $agentId)->count(),
        ];

        // ── Earnings Monthly Chart ─────────────────────────────────────
        $earningsChart = Commission::where('agent_id', $agentId)
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths($period)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Commission Status Chart ────────────────────────────────────
        $commissionStatusChart = Commission::where('agent_id', $agentId)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // ── Vendor Growth Chart ────────────────────────────────────────
        $vendorGrowthChart = Vendor::where('agent_id', $agentId)
            ->where('created_at', '>=', now()->subMonths($period)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Per-vendor commission breakdown ────────────────────────────
        $vendorBreakdown = Commission::where('agent_id', $agentId)
            ->select(
                'vendor_id',
                DB::raw('SUM(amount) as total'),
                DB::raw('SUM(CASE WHEN status="paid" THEN amount ELSE 0 END) as paid'),
                DB::raw('SUM(CASE WHEN status="pending" THEN amount ELSE 0 END) as pending'),
                DB::raw('COUNT(*) as count')
            )
            ->with('vendor.businessProfile')
            ->groupBy('vendor_id')
            ->orderByDesc('total')
            ->get();

        // ── Main paginated data table based on report type ─────────────
        $records = collect();

        if ($type === 'earnings') {
            $query = Commission::where('agent_id', $agentId)
                ->with(['vendor.businessProfile', 'order'])
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->when($request->status, fn($q) => $q->where('status', $request->status))
                ->when($request->vendor_id, fn($q) => $q->where('vendor_id', $request->vendor_id));

            $records = $query->latest()->paginate(20)->withQueryString();

        } elseif ($type === 'vendors') {
            $query = Vendor::where('agent_id', $agentId)
                ->with(['user', 'businessProfile.country'])
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->when($request->status, fn($q) => $q->where('account_status', $request->status));

            $records = $query->latest()->paginate(20)->withQueryString();

        } elseif ($type === 'transactions') {
            $query = Transaction::whereIn('vendor_id', $vendorIds)
                ->with(['order'])
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->when($request->status, fn($q) => $q->where('status', $request->status));

            $records = $query->latest()->paginate(20)->withQueryString();
        }

        $myVendors = Vendor::where('agent_id', $agentId)->with('businessProfile')->get();

        return view('agent.reports.index', compact(
            'summary', 'earningsChart', 'commissionStatusChart',
            'vendorGrowthChart', 'vendorBreakdown', 'records',
            'myVendors', 'period', 'type'
        ));
    }

    // ─── PRINT ────────────────────────────────────────────────────────
    public function print(Request $request)
    {
        $agentId  = auth()->id();
        $type     = $request->get('type', 'earnings');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $vendorIds = Vendor::where('agent_id', $agentId)->pluck('id');

        $summary = [
            'total_earned'  => Commission::where('agent_id', $agentId)->where('status', 'paid')->sum('amount'),
            'total_pending' => Commission::where('agent_id', $agentId)->where('status', 'pending')->sum('amount'),
            'total_vendors' => Vendor::where('agent_id', $agentId)->count(),
            'this_month'    => Commission::where('agent_id', $agentId)
                                ->where('status', 'paid')
                                ->whereMonth('created_at', now()->month)
                                ->sum('amount'),
        ];

        $records = collect();

        if ($type === 'earnings') {
            $records = Commission::where('agent_id', $agentId)
                ->with(['vendor.businessProfile', 'order'])
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->when($request->status, fn($q) => $q->where('status', $request->status))
                ->latest()->get();

        } elseif ($type === 'vendors') {
            $records = Vendor::where('agent_id', $agentId)
                ->with(['user', 'businessProfile.country'])
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->latest()->get();

        } elseif ($type === 'transactions') {
            $records = Transaction::whereIn('vendor_id', $vendorIds)
                ->with(['order'])
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->latest()->get();
        }

        return view('agent.reports.print', compact('summary', 'records', 'type', 'dateFrom', 'dateTo'));
    }

    // ─── EXPORT CSV ───────────────────────────────────────────────────
    public function export(Request $request)
    {
        $agentId  = auth()->id();
        $type     = $request->get('type', 'earnings');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $vendorIds = Vendor::where('agent_id', $agentId)->pluck('id');

        $filename = "agent-report-{$type}-" . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($agentId, $type, $dateFrom, $dateTo, $vendorIds) {
            $out = fopen('php://output', 'w');

            if ($type === 'earnings') {
                fputcsv($out, ['Reference', 'Vendor', 'Order #', 'Amount', 'Rate', 'Status', 'Date']);
                $records = Commission::where('agent_id', $agentId)
                    ->with(['vendor.businessProfile', 'order'])
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()->get();

                foreach ($records as $r) {
                    fputcsv($out, [
                        $r->reference ?? 'COM-' . str_pad($r->id, 5, '0', STR_PAD_LEFT),
                        $r->vendor?->businessProfile?->business_name ?? 'N/A',
                        $r->order?->order_number ?? 'N/A',
                        number_format($r->amount, 2),
                        $r->rate ? $r->rate . '%' : 'N/A',
                        ucfirst($r->status),
                        $r->created_at->format('Y-m-d'),
                    ]);
                }

            } elseif ($type === 'vendors') {
                fputcsv($out, ['Business Name', 'Contact Person', 'Email', 'Phone', 'Country', 'City', 'Status', 'Joined']);
                $records = Vendor::where('agent_id', $agentId)
                    ->with(['user', 'businessProfile.country'])
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()->get();

                foreach ($records as $r) {
                    fputcsv($out, [
                        $r->businessProfile?->business_name ?? 'N/A',
                        $r->user?->name ?? 'N/A',
                        $r->user?->email ?? 'N/A',
                        $r->businessProfile?->phone ?? 'N/A',
                        $r->businessProfile?->country?->name ?? 'N/A',
                        $r->businessProfile?->city ?? 'N/A',
                        ucfirst($r->account_status),
                        $r->created_at->format('Y-m-d'),
                    ]);
                }

            } elseif ($type === 'transactions') {
                fputcsv($out, ['Transaction #', 'Order #', 'Amount', 'Currency', 'Payment Method', 'Status', 'Date']);
                $records = Transaction::whereIn('vendor_id', $vendorIds)
                    ->with(['order'])
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()->get();

                foreach ($records as $r) {
                    fputcsv($out, [
                        $r->transaction_number ?? 'TXN-' . str_pad($r->id, 5, '0', STR_PAD_LEFT),
                        $r->order?->order_number ?? 'N/A',
                        number_format($r->amount, 2),
                        $r->currency ?? 'USD',
                        $r->payment_method ?? 'N/A',
                        ucfirst($r->status),
                        $r->created_at->format('Y-m-d'),
                    ]);
                }
            }

            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
