<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['order', 'buyer', 'vendor']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vendor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('filter')) {
            $query->where('status', $request->filter);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Currency filter
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        // Amount range filter
        if ($request->filled('amount_range')) {
            switch ($request->amount_range) {
                case 'high':
                    $query->where('amount', '>=', 10000);
                    break;
                case 'medium':
                    $query->whereBetween('amount', [1000, 9999]);
                    break;
                case 'low':
                    $query->where('amount', '<', 1000);
                    break;
            }
        }

        // Vendor filter
        if ($request->filled('vendor')) {
            $query->where('vendor_id', $request->vendor);
        }

        // Buyer filter
        if ($request->filled('buyer')) {
            $query->where('buyer_id', $request->buyer);
        }

        // Date range filter
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'quarter':
                    $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
                    break;
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $transactions = $query->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Transaction::count(),
            'pending' => Transaction::where('status', 'pending')->count(),
            'processing' => Transaction::where('status', 'processing')->count(),
            'completed' => Transaction::where('status', 'completed')->count(),
            'failed' => Transaction::where('status', 'failed')->count(),
            'refunded' => Transaction::where('status', 'refunded')->count(),
            'total_amount' => Transaction::where('status', 'completed')->sum('amount'),
            'pending_amount' => Transaction::where('status', 'pending')->sum('amount'),
            'avg_transaction' => Transaction::where('status', 'completed')->avg('amount'),
            'this_month' => Transaction::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->count(),
            'this_month_amount' => Transaction::whereMonth('created_at', now()->month)
                                              ->whereYear('created_at', now()->year)
                                              ->where('status', 'completed')
                                              ->sum('amount'),
        ];

        $stats['completed_percentage'] = $stats['total'] > 0
            ? round(($stats['completed'] / $stats['total']) * 100, 1)
            : 0;
        $stats['failed_percentage'] = $stats['total'] > 0
            ? round(($stats['failed'] / $stats['total']) * 100, 1)
            : 0;

        // Add status badges
        $transactions->getCollection()->transform(function ($transaction) {
            $transaction->status_badge = $this->getStatusBadge($transaction->status);
            $transaction->type_badge = $this->getTypeBadge($transaction->type);
            return $transaction;
        });

    // Get vendors - users who have the 'vendor' role
    $vendors = User::whereHas('businessProfile')
                ->whereHas('roles', function($query) {
                    $query->where('slug', 'vendor');
                })
                ->orderBy('name')
                ->get();

    // Get buyers - users who have the 'buyer' role
    $buyers = User::whereHas('roles', function($query) {
                    $query->where('slug', 'buyer');
                })
                ->orderBy('name')
                ->get();

        return view('admin.transactions.index', compact('transactions', 'stats', 'vendors', 'buyers'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['order', 'buyer', 'vendor']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function refund(Transaction $transaction, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'amount' => 'nullable|numeric|min:0|max:' . $transaction->amount,
        ]);

        $refundAmount = $request->amount ?? $transaction->amount;

        // Create refund transaction
        $refundTransaction = Transaction::create([
            'transaction_number' => Transaction::generateTransactionNumber(),
            'order_id' => $transaction->order_id,
            'buyer_id' => $transaction->buyer_id,
            'vendor_id' => $transaction->vendor_id,
            'type' => 'refund',
            'status' => 'completed',
            'amount' => -$refundAmount,
            'currency' => $transaction->currency,
            'payment_method' => $transaction->payment_method,
            'payment_reference' => 'REFUND-' . $transaction->transaction_number,
            'notes' => $request->reason,
            'completed_at' => now(),
        ]);

        // Update original transaction
        $transaction->update([
            'status' => 'refunded',
            'notes' => ($transaction->notes ?? '') . "\n\nRefunded: " . $request->reason,
        ]);

        return redirect()->back()->with('success', 'Transaction refunded successfully');
    }

    public function export(Request $request)
    {
        // TODO: Implement CSV/Excel export
        return redirect()->back()->with('info', 'Export feature coming soon');
    }

    public function updateStatus(Transaction $transaction, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,refunded',
            'notes' => 'nullable|string|max:500',
        ]);

        $transaction->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $transaction->notes,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Transaction status updated successfully');
    }

    private function getStatusBadge($status)
    {
        return match($status) {
            'pending' => ['text' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-800'],
            'processing' => ['text' => 'Processing', 'class' => 'bg-blue-100 text-blue-800'],
            'completed' => ['text' => 'Completed', 'class' => 'bg-green-100 text-green-800'],
            'failed' => ['text' => 'Failed', 'class' => 'bg-red-100 text-red-800'],
            'refunded' => ['text' => 'Refunded', 'class' => 'bg-purple-100 text-purple-800'],
            'cancelled' => ['text' => 'Cancelled', 'class' => 'bg-gray-100 text-gray-800'],
            default => ['text' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }

    private function getTypeBadge($type)
    {
        return match($type) {
            'payment' => ['text' => 'Payment', 'class' => 'bg-green-100 text-green-800'],
            'refund' => ['text' => 'Refund', 'class' => 'bg-orange-100 text-orange-800'],
            'commission' => ['text' => 'Commission', 'class' => 'bg-purple-100 text-purple-800'],
            'payout' => ['text' => 'Payout', 'class' => 'bg-blue-100 text-blue-800'],
            'subscription' => ['text' => 'Subscription', 'class' => 'bg-indigo-100 text-indigo-800'],
            default => ['text' => ucfirst($type), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }
}
