<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // ─── Resolve all vendor user IDs belonging to this agent ──────────
    private function agentVendorUserIds(): \Illuminate\Support\Collection
    {
        return Vendor::where('agent_id', auth()->id())->pluck('user_id');
    }

    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $vendorUserIds = $this->agentVendorUserIds();

        $query = Transaction::whereIn('vendor_id', $vendorUserIds)
            ->with(['order', 'buyer', 'vendor'])
            ->when($request->search, fn($q) =>
                $q->where('transaction_number', 'like', "%{$request->search}%")
                  ->orWhere('payment_reference', 'like', "%{$request->search}%")
                  ->orWhereHas('order', fn($q2) =>
                      $q2->where('order_number', 'like', "%{$request->search}%")
                  )
            )
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->type, fn($q) =>
                $q->where('type', $request->type)
            )
            ->when($request->vendor_id, function ($q) use ($request) {
                $vendorUserId = Vendor::where('agent_id', auth()->id())
                    ->where('id', $request->vendor_id)
                    ->value('user_id');
                return $q->where('vendor_id', $vendorUserId);
            })
            ->when($request->date_from, fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from)
            )
            ->when($request->date_to, fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to)
            );

        $transactions = $query->latest()->paginate(20)->withQueryString();

        // ── Stats ──────────────────────────────────────────────────────
        $base = Transaction::whereIn('vendor_id', $vendorUserIds);

        $stats = [
            'total_volume'    => (clone $base)->where('status', 'completed')->sum('amount'),
            'total_count'     => (clone $base)->count(),
            'completed_count' => (clone $base)->where('status', 'completed')->count(),
            'pending_count'   => (clone $base)->where('status', 'pending')->count(),
            'failed_count'    => (clone $base)->where('status', 'failed')->count(),
            'this_month'      => (clone $base)
                                     ->where('status', 'completed')
                                     ->whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->sum('amount'),
        ];

        // ── Types for filter dropdown ──────────────────────────────────
        $types = Transaction::whereIn('vendor_id', $vendorUserIds)
            ->distinct()
            ->pluck('type')
            ->filter()
            ->sort()
            ->values();

        // ── My vendors for filter dropdown ─────────────────────────────
        $myVendors = Vendor::where('agent_id', auth()->id())
            ->with('businessProfile')
            ->get();

        return view('agent.transactions.index', compact(
            'transactions', 'stats', 'types', 'myVendors'
        ));
    }

    // ─── SHOW ─────────────────────────────────────────────────────────
    public function show($id)
    {
        $vendorUserIds = $this->agentVendorUserIds();

        $transaction = Transaction::whereIn('vendor_id', $vendorUserIds)
            ->with(['order.items.product', 'buyer', 'vendor'])
            ->findOrFail($id);

        return view('agent.transactions.show', compact('transaction'));
    }

    // ─── PRINT ────────────────────────────────────────────────────────
    public function print(Request $request)
    {
        $vendorUserIds = $this->agentVendorUserIds();

        $transactions = Transaction::whereIn('vendor_id', $vendorUserIds)
            ->with(['order', 'buyer', 'vendor'])
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

        $totals = [
            'volume'  => $transactions->where('status', 'completed')->sum('amount'),
            'count'   => $transactions->count(),
            'pending' => $transactions->where('status', 'pending')->sum('amount'),
        ];

        return view('agent.transactions.print', compact('transactions', 'totals'));
    }

    // ─── EXPORT CSV ───────────────────────────────────────────────────
    public function export(Request $request)
    {
        $vendorUserIds = $this->agentVendorUserIds();

        $transactions = Transaction::whereIn('vendor_id', $vendorUserIds)
            ->with(['order', 'buyer', 'vendor'])
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

        $filename = 'transactions-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($transactions) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Transaction #', 'Order #', 'Vendor', 'Buyer',
                'Type', 'Amount', 'Currency', 'Payment Method',
                'Status', 'Date',
            ]);
            foreach ($transactions as $t) {
                fputcsv($out, [
                    $t->transaction_number,
                    $t->order?->order_number ?? 'N/A',
                    $t->vendor?->name ?? 'N/A',
                    $t->buyer?->name ?? 'N/A',
                    ucfirst($t->type ?? '—'),
                    number_format($t->amount, 2),
                    $t->currency ?? 'USD',
                    ucfirst($t->payment_method ?? '—'),
                    ucfirst($t->status),
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
