<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
