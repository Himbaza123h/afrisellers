<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Escrow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EscrowController extends Controller
{
    public function index(Request $request)
    {
        $query = Escrow::with(['transaction', 'order', 'buyer', 'vendor']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('escrow_number', 'like', "%{$search}%")
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
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type filter
        if ($request->filled('escrow_type')) {
            $query->where('escrow_type', $request->escrow_type);
        }

        // Disputed filter
        if ($request->filled('disputed')) {
            $query->where('disputed', $request->disputed === 'yes');
        }

        // Release condition filter
        if ($request->filled('release_condition')) {
            $query->where('release_condition', $request->release_condition);
        }

        // Buyer filter
        if ($request->filled('buyer')) {
            $query->where('buyer_id', $request->buyer);
        }

        // Vendor filter
        if ($request->filled('vendor')) {
            $query->where('vendor_id', $request->vendor);
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
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $escrows = $query->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Escrow::count(),
            'pending' => Escrow::where('status', 'pending')->count(),
            'active' => Escrow::where('status', 'active')->count(),
            'released' => Escrow::where('status', 'released')->count(),
            'refunded' => Escrow::where('status', 'refunded')->count(),
            'disputed' => Escrow::where('disputed', true)->count(),
            'total_held' => Escrow::whereIn('status', ['pending', 'active'])->sum('amount'),
            'total_released' => Escrow::where('status', 'released')->sum('amount'),
            'awaiting_release' => Escrow::awaitingRelease()->count(),
            'auto_release_ready' => Escrow::autoReleaseReady()->count(),
        ];

        // Get users for filters
        $buyers = User::whereHas('escrowsAsBuyer')->orderBy('name')->get();
        $vendors = User::whereHas('escrowsAsVendor')->orderBy('name')->get();

        // Add badges
        $escrows->getCollection()->transform(function ($escrow) {
            $escrow->status_badge = $this->getStatusBadge($escrow->status);
            $escrow->type_badge = $this->getTypeBadge($escrow->escrow_type);
            return $escrow;
        });

        return view('admin.escrow.index', compact('escrows', 'stats', 'buyers', 'vendors'));
    }

    public function show(Escrow $escrow)
    {
        $escrow->load(['transaction', 'order', 'buyer', 'vendor', 'adminApprover']);
        return view('admin.escrow.show', compact('escrow'));
    }

    public function release(Escrow $escrow, Request $request)
    {
        $request->validate([
            'release_method' => 'required|string|max:50',
            'release_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $escrow->release(
                $request->release_method,
                $request->release_reference,
                $request->notes
            );

            return redirect()->back()->with('success', 'Escrow funds released successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function refund(Escrow $escrow, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $escrow->refund($request->reason);

            return redirect()->back()->with('success', 'Escrow refunded successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function activate(Escrow $escrow)
    {
        if (!$escrow->isPending()) {
            return redirect()->back()->with('error', 'Only pending escrows can be activated');
        }

        $escrow->activate();

        return redirect()->back()->with('success', 'Escrow activated successfully');
    }

    public function openDispute(Escrow $escrow, Request $request)
    {
        $request->validate([
            'dispute_reason' => 'required|string|max:1000',
        ]);

        if ($escrow->isDisputed()) {
            return redirect()->back()->with('error', 'This escrow is already in dispute');
        }

        $escrow->openDispute($request->dispute_reason);

        return redirect()->back()->with('success', 'Dispute opened successfully');
    }

    public function resolveDispute(Escrow $escrow, Request $request)
    {
        $request->validate([
            'resolution' => 'required|string|max:1000',
            'release_to_vendor' => 'required|boolean',
        ]);

        if (!$escrow->isDisputed()) {
            return redirect()->back()->with('error', 'This escrow is not in dispute');
        }

        try {
            $escrow->resolveDispute($request->resolution, $request->release_to_vendor);

            return redirect()->back()->with('success', 'Dispute resolved successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function adminApprove(Escrow $escrow)
    {
        if (!$escrow->isActive()) {
            return redirect()->back()->with('error', 'Only active escrows can be approved');
        }

        $escrow->adminApprove(auth()->id());

        return redirect()->back()->with('success', 'Escrow approved successfully');
    }

    public function cancel(Escrow $escrow, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (in_array($escrow->status, ['released', 'refunded'])) {
            return redirect()->back()->with('error', 'Cannot cancel a completed escrow');
        }

        $escrow->update([
            'status' => 'cancelled',
            'notes' => ($escrow->notes ?? '') . "\n\nCancelled: " . $request->reason,
        ]);

        return redirect()->back()->with('success', 'Escrow cancelled successfully');
    }

    public function export(Request $request)
    {
        $query = Escrow::with(['transaction', 'order', 'buyer', 'vendor']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $escrows = $query->get();

        $filename = 'escrows_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($escrows) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Escrow Number',
                'Buyer',
                'Vendor',
                'Amount',
                'Currency',
                'Status',
                'Type',
                'Disputed',
                'Created At',
                'Held At',
                'Released At',
            ]);

            // Data
            foreach ($escrows as $escrow) {
                fputcsv($file, [
                    $escrow->escrow_number,
                    $escrow->buyer->name ?? 'N/A',
                    $escrow->vendor->name ?? 'N/A',
                    $escrow->amount,
                    $escrow->currency,
                    $escrow->status,
                    $escrow->escrow_type,
                    $escrow->disputed ? 'Yes' : 'No',
                    $escrow->created_at->format('Y-m-d H:i:s'),
                    $escrow->held_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $escrow->released_at?->format('Y-m-d H:i:s') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStatusBadge($status)
    {
        return match($status) {
            'pending' => ['text' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-800'],
            'active' => ['text' => 'Active', 'class' => 'bg-blue-100 text-blue-800'],
            'released' => ['text' => 'Released', 'class' => 'bg-green-100 text-green-800'],
            'refunded' => ['text' => 'Refunded', 'class' => 'bg-purple-100 text-purple-800'],
            'disputed' => ['text' => 'Disputed', 'class' => 'bg-red-100 text-red-800'],
            'cancelled' => ['text' => 'Cancelled', 'class' => 'bg-gray-100 text-gray-800'],
            default => ['text' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }

    private function getTypeBadge($type)
    {
        return match($type) {
            'order' => ['text' => 'Order', 'class' => 'bg-blue-100 text-blue-800'],
            'service' => ['text' => 'Service', 'class' => 'bg-purple-100 text-purple-800'],
            'milestone' => ['text' => 'Milestone', 'class' => 'bg-indigo-100 text-indigo-800'],
            'custom' => ['text' => 'Custom', 'class' => 'bg-gray-100 text-gray-800'],
            default => ['text' => ucfirst($type), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }
}
