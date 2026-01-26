<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions
     */
    public function index(Request $request)
    {
        $vendorId = Auth::id();

        $query = Transaction::with(['order', 'buyer'])
            ->where('vendor_id', $vendorId)
            ->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhereHas('order', function($q) use ($search) {
                      $q->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('buyer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $transactions = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Transaction::where('vendor_id', $vendorId)->count(),
            'completed' => Transaction::where('vendor_id', $vendorId)->where('status', 'completed')->count(),
            'pending' => Transaction::where('vendor_id', $vendorId)->where('status', 'pending')->count(),
            'failed' => Transaction::where('vendor_id', $vendorId)->where('status', 'failed')->count(),
            'total_amount' => Transaction::where('vendor_id', $vendorId)->where('status', 'completed')->sum('amount'),
            'pending_amount' => Transaction::where('vendor_id', $vendorId)->where('status', 'pending')->sum('amount'),
        ];

        $stats['completed_percentage'] = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0;
        $stats['pending_percentage'] = $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100, 1) : 0;

        return view('vendor.transactions.index', compact('transactions', 'stats'));
    }

    /**
 * Print transactions report.
 */
public function print(Request $request)
{
    try {
        $vendorId = Auth::id();

        $query = Transaction::with(['order', 'buyer'])
            ->where('vendor_id', $vendorId);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhereHas('order', function($q) use ($search) {
                      $q->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('buyer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            }
        }

        // Get transactions without pagination for print
        $transactions = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $allTransactions = Transaction::where('vendor_id', $vendorId);
        $stats = [
            'total' => $allTransactions->count(),
            'completed' => (clone $allTransactions)->where('status', 'completed')->count(),
            'pending' => (clone $allTransactions)->where('status', 'pending')->count(),
            'failed' => (clone $allTransactions)->where('status', 'failed')->count(),
            'total_amount' => (clone $allTransactions)->where('status', 'completed')->sum('amount'),
            'pending_amount' => (clone $allTransactions)->where('status', 'pending')->sum('amount'),
        ];

        // Calculate percentages
        $stats['completed_percentage'] = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0;
        $stats['pending_percentage'] = $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100, 1) : 0;

        // Get status distribution
        $statusDistribution = Transaction::where('vendor_id', $vendorId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();

        // Get payment method distribution
        $paymentMethodDistribution = Transaction::where('vendor_id', $vendorId)
            ->select('payment_method', DB::raw('count(*) as count'))
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->orderBy('count', 'desc')
            ->get();

        return view('vendor.transactions.print', compact('transactions', 'stats', 'statusDistribution', 'paymentMethodDistribution'));
    } catch (\Exception $e) {
        Log::error('Transaction Print Error: ' . $e->getMessage());
        abort(500, 'An error occurred while generating the print report.');
    }
}

    /**
     * Display the specified transaction
     */
    public function show(Transaction $transaction)
    {
        // Ensure vendor can only view their own transactions
        if ($transaction->vendor_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $transaction->load(['order.items', 'buyer', 'vendor']);

        return view('vendor.transactions.show', compact('transaction'));
    }
}
