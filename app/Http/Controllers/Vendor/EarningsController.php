<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EarningsController extends Controller
{
    /**
     * Display earnings with list and statistics views
     */
    public function index(Request $request)
    {
        $vendorId = Auth::id();

        // Get the active tab (default to list)
        $activeTab = $request->get('tab', 'list');

        // Build base query for completed transactions only
        $query = Transaction::with(['order', 'buyer'])
            ->where('vendor_id', $vendorId)
            ->where('status', 'completed') // Only successful transactions
            ->latest();

        // Apply filters
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

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Date range filter
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];
                $query->whereBetween('completed_at', [$startDate, $endDate]);
            }
        } else {
            $query->whereBetween('completed_at', [$startDate, $endDate]);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'completed_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate for list view
        $transactions = $query->paginate(15)->withQueryString();

        // Calculate overall statistics
        $stats = $this->calculateStats($vendorId, $startDate, $endDate);

        // Get chart data for statistics tab
        $chartData = $this->getChartData($vendorId, $startDate, $endDate);

        return view('vendor.earnings.index', compact(
            'transactions',
            'stats',
            'chartData',
            'activeTab',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Calculate earnings statistics
     */
    private function calculateStats($vendorId, $startDate, $endDate)
    {
        $baseQuery = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate]);

        $totalEarnings = $baseQuery->sum('amount');
        $totalTransactions = $baseQuery->count();
        $averageTransaction = $totalTransactions > 0 ? $totalEarnings / $totalTransactions : 0;

        // Get previous period for comparison
        $previousStart = Carbon::parse($startDate)->subDays(
            Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate))
        );
        $previousEnd = Carbon::parse($startDate)->subDay();

        $previousEarnings = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$previousStart, $previousEnd])
            ->sum('amount');

        $earningsChange = $previousEarnings > 0
            ? (($totalEarnings - $previousEarnings) / $previousEarnings) * 100
            : 0;

        // Payment method breakdown
        $paymentMethodBreakdown = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // Top earning days
        $topDays = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_earnings' => $totalEarnings,
            'total_transactions' => $totalTransactions,
            'average_transaction' => $averageTransaction,
            'earnings_change' => $earningsChange,
            'payment_methods' => $paymentMethodBreakdown,
            'top_days' => $topDays,
        ];
    }

    /**
 * Print earnings report.
 */
public function print(Request $request)
{
    try {
        $vendorId = Auth::id();

        // Build base query for completed transactions only
        $query = Transaction::with(['order', 'buyer'])
            ->where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->latest();

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

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Date range filter
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];
                $query->whereBetween('completed_at', [$startDate, $endDate]);
            }
        } else {
            $query->whereBetween('completed_at', [$startDate, $endDate]);
        }

        // Get earnings without pagination for print
        $transactions = $query->orderBy('completed_at', 'desc')->get();

        // Calculate statistics
        $baseQuery = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate]);

        $totalEarnings = $baseQuery->sum('amount');
        $totalTransactions = $baseQuery->count();
        $averageTransaction = $totalTransactions > 0 ? $totalEarnings / $totalTransactions : 0;

        // Get previous period for comparison
        $previousStart = Carbon::parse($startDate)->subDays(
            Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate))
        );
        $previousEnd = Carbon::parse($startDate)->subDay();

        $previousEarnings = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$previousStart, $previousEnd])
            ->sum('amount');

        $earningsChange = $previousEarnings > 0
            ? (($totalEarnings - $previousEarnings) / $previousEarnings) * 100
            : 0;

        // Payment method breakdown
        $paymentMethodDistribution = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->orderBy('total_amount', 'desc')
            ->get();

        // Top earning days
        $topDays = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Daily earnings for chart data
        $dailyEarnings = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $stats = [
            'total_earnings' => $totalEarnings,
            'total_transactions' => $totalTransactions,
            'average_transaction' => $averageTransaction,
            'earnings_change' => $earningsChange,
            'top_days' => $topDays,
        ];

        return view('vendor.earnings.print', compact(
            'transactions',
            'stats',
            'paymentMethodDistribution',
            'dailyEarnings',
            'startDate',
            'endDate'
        ));
    } catch (\Exception $e) {
        Log::error('Earnings Print Error: ' . $e->getMessage());
        abort(500, 'An error occurred while generating the print report.');
    }
}

    /**
     * Get chart data for statistics visualization
     */
    private function getChartData($vendorId, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $diffInDays = $start->diffInDays($end);

        // Determine grouping based on date range
        if ($diffInDays <= 31) {
            // Daily data for up to 31 days
            $groupBy = DB::raw('DATE(completed_at) as period');
            $format = 'Y-m-d';
        } elseif ($diffInDays <= 90) {
            // Weekly data for up to 3 months
            $groupBy = DB::raw('YEARWEEK(completed_at) as period');
            $format = 'W-Y';
        } else {
            // Monthly data for longer periods
            $groupBy = DB::raw('DATE_FORMAT(completed_at, "%Y-%m") as period');
            $format = 'Y-m';
        }

        $data = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(
                $groupBy,
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        // Format for Chart.js
        $labels = [];
        $amounts = [];
        $counts = [];

        foreach ($data as $item) {
            if ($diffInDays <= 31) {
                $labels[] = Carbon::parse($item->period)->format('M d');
            } elseif ($diffInDays <= 90) {
                $labels[] = 'Week ' . date('W', strtotime($item->period));
            } else {
                $labels[] = Carbon::parse($item->period . '-01')->format('M Y');
            }

            $amounts[] = (float) $item->total_amount;
            $counts[] = (int) $item->transaction_count;
        }

        return [
            'labels' => $labels,
            'amounts' => $amounts,
            'counts' => $counts,
        ];
    }
}
