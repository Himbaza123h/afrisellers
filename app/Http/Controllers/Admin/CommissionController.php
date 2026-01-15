<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Commission::with(['transaction', 'user', 'vendor', 'buyer']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('transaction', function($q) use ($search) {
                      $q->where('transaction_number', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Commission type filter
        if ($request->filled('commission_type')) {
            $query->where('commission_type', $request->commission_type);
        }

        // User filter
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        // Amount range filter
        if ($request->filled('amount_range')) {
            switch ($request->amount_range) {
                case 'high':
                    $query->where('commission_amount', '>=', 1000);
                    break;
                case 'medium':
                    $query->whereBetween('commission_amount', [100, 999]);
                    break;
                case 'low':
                    $query->where('commission_amount', '<', 100);
                    break;
            }
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

        $commissions = $query->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Commission::count(),
            'pending' => Commission::where('status', 'pending')->count(),
            'approved' => Commission::where('status', 'approved')->count(),
            'paid' => Commission::where('status', 'paid')->count(),
            'cancelled' => Commission::where('status', 'cancelled')->count(),
            'total_amount' => Commission::where('status', 'paid')->sum('commission_amount'),
            'pending_amount' => Commission::whereIn('status', ['pending', 'approved'])->sum('commission_amount'),
            'unpaid_amount' => Commission::where('payment_status', 'unpaid')->sum('commission_amount'),
            'this_month' => Commission::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->count(),
            'this_month_amount' => Commission::whereMonth('created_at', now()->month)
                                              ->whereYear('created_at', now()->year)
                                              ->where('status', 'paid')
                                              ->sum('commission_amount'),
        ];

        // Get users who have earned commissions for filter
        $users = User::whereHas('commissions')
                    ->orderBy('name')
                    ->get();

        // Add badges
        $commissions->getCollection()->transform(function ($commission) {
            $commission->status_badge = $this->getStatusBadge($commission->status);
            $commission->payment_status_badge = $this->getPaymentStatusBadge($commission->payment_status);
            $commission->type_badge = $this->getTypeBadge($commission->commission_type);
            return $commission;
        });

        return view('admin.commissions.index', compact('commissions', 'stats', 'users'));
    }

    public function show(Commission $commission)
    {
        $commission->load(['transaction', 'user', 'vendor', 'buyer']);
        return view('admin.commissions.show', compact('commission'));
    }

    public function approve(Commission $commission)
    {
        if ($commission->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending commissions can be approved');
        }

        $commission->approve();

        return redirect()->back()->with('success', 'Commission approved successfully');
    }

    public function markAsPaid(Commission $commission, Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|max:50',
            'payment_reference' => 'required|string|max:100',
        ]);

        if (!in_array($commission->status, ['pending', 'approved'])) {
            return redirect()->back()->with('error', 'Commission must be pending or approved to be paid');
        }

        $commission->markAsPaid($request->payment_method, $request->payment_reference);

        return redirect()->back()->with('success', 'Commission marked as paid successfully');
    }

    public function cancel(Commission $commission, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($commission->status === 'paid') {
            return redirect()->back()->with('error', 'Paid commissions cannot be cancelled');
        }

        $commission->update([
            'status' => 'cancelled',
            'notes' => ($commission->notes ?? '') . "\n\nCancelled: " . $request->reason,
        ]);

        return redirect()->back()->with('success', 'Commission cancelled successfully');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:commissions,id',
        ]);

        Commission::whereIn('id', $request->commission_ids)
                  ->where('status', 'pending')
                  ->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Selected commissions approved successfully');
    }

    public function bulkPay(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:commissions,id',
            'payment_method' => 'required|string|max:50',
        ]);

        $commissions = Commission::whereIn('id', $request->commission_ids)
                                 ->whereIn('status', ['pending', 'approved'])
                                 ->get();

        foreach ($commissions as $commission) {
            $commission->markAsPaid(
                $request->payment_method,
                'BULK-' . now()->format('YmdHis') . '-' . $commission->id
            );
        }

        return redirect()->back()->with('success', 'Selected commissions marked as paid successfully');
    }

    public function export(Request $request)
    {
        $query = Commission::with(['transaction', 'user', 'vendor', 'buyer']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('commission_type')) {
            $query->where('commission_type', $request->commission_type);
        }

        $commissions = $query->get();

        $filename = 'commissions_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($commissions) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID',
                'Transaction Number',
                'User',
                'Type',
                'Commission Amount',
                'Commission Rate',
                'Transaction Amount',
                'Currency',
                'Status',
                'Payment Status',
                'Payment Method',
                'Payment Reference',
                'Created At',
                'Paid At',
            ]);

            // Data
            foreach ($commissions as $commission) {
                fputcsv($file, [
                    $commission->id,
                    $commission->transaction->transaction_number ?? 'N/A',
                    $commission->user->name ?? 'N/A',
                    $commission->commission_type,
                    $commission->commission_amount,
                    $commission->commission_rate,
                    $commission->transaction_amount,
                    $commission->currency,
                    $commission->status,
                    $commission->payment_status,
                    $commission->payment_method ?? 'N/A',
                    $commission->payment_reference ?? 'N/A',
                    $commission->created_at->format('Y-m-d H:i:s'),
                    $commission->paid_at?->format('Y-m-d H:i:s') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function settings()
    {
        // Load commission settings from database or config
        $settings = [
            'vendor_sale_rate' => 5.00,
            'referral_rate' => 2.50,
            'regional_admin_rate' => 3.00,
            'platform_fee_rate' => 2.00,
        ];

        return view('admin.commissions.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'vendor_sale_rate' => 'required|numeric|min:0|max:100',
            'referral_rate' => 'required|numeric|min:0|max:100',
            'regional_admin_rate' => 'required|numeric|min:0|max:100',
            'platform_fee_rate' => 'required|numeric|min:0|max:100',
        ]);

        // Save settings to database or config
        // Implement your settings storage logic here

        return redirect()->back()->with('success', 'Commission settings updated successfully');
    }

    private function getStatusBadge($status)
    {
        return match($status) {
            'pending' => ['text' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-800'],
            'approved' => ['text' => 'Approved', 'class' => 'bg-blue-100 text-blue-800'],
            'paid' => ['text' => 'Paid', 'class' => 'bg-green-100 text-green-800'],
            'cancelled' => ['text' => 'Cancelled', 'class' => 'bg-red-100 text-red-800'],
            default => ['text' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }

    private function getPaymentStatusBadge($status)
    {
        return match($status) {
            'unpaid' => ['text' => 'Unpaid', 'class' => 'bg-red-100 text-red-800'],
            'processing' => ['text' => 'Processing', 'class' => 'bg-blue-100 text-blue-800'],
            'paid' => ['text' => 'Paid', 'class' => 'bg-green-100 text-green-800'],
            'failed' => ['text' => 'Failed', 'class' => 'bg-orange-100 text-orange-800'],
            default => ['text' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }

    private function getTypeBadge($type)
    {
        return match($type) {
            'vendor_sale' => ['text' => 'Vendor Sale', 'class' => 'bg-purple-100 text-purple-800'],
            'referral' => ['text' => 'Referral', 'class' => 'bg-indigo-100 text-indigo-800'],
            'regional_admin' => ['text' => 'Regional Admin', 'class' => 'bg-cyan-100 text-cyan-800'],
            'platform_fee' => ['text' => 'Platform Fee', 'class' => 'bg-teal-100 text-teal-800'],
            'affiliate' => ['text' => 'Affiliate', 'class' => 'bg-pink-100 text-pink-800'],
            'bonus' => ['text' => 'Bonus', 'class' => 'bg-emerald-100 text-emerald-800'],
            default => ['text' => ucfirst($type), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }
}
