<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display sales reports
     */
public function index(Request $request)
{
    $vendorId = Auth::id();

    // Report type
    $reportType = $request->get('type', 'daily');

    // Date range
    $startDate = $request->get('start_date', now()->startOfMonth());
    $endDate = $request->get('end_date', now());

    if ($request->filled('date_range')) {
        $dates = explode(' to ', $request->date_range);
        if (count($dates) == 2) {
            $startDate = Carbon::parse($dates[0]);
            $endDate = Carbon::parse($dates[1]);
        }
    } else {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
    }

    // Generate report based on type
    $reportData = $this->generateReport($vendorId, $reportType, $startDate, $endDate);

    // Summary statistics
    $summary = [
        'total_sales' => Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->sum('amount'),
        'total_orders' => Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count(),
        'average_order_value' => 0,
        'total_items_sold' => DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.vendor_id', $vendorId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->sum('order_items.quantity'),
    ];

    if ($summary['total_orders'] > 0) {
        $summary['average_order_value'] = $summary['total_sales'] / $summary['total_orders'];
    }

    // Sales by payment method
    $salesByPaymentMethod = Transaction::where('vendor_id', $vendorId)
        ->where('status', 'completed')
        ->whereBetween('completed_at', [$startDate, $endDate])
        ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
        ->groupBy('payment_method')
        ->get();

    // Sales by order status
    $salesByStatus = Order::where('vendor_id', $vendorId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
        ->groupBy('status')
        ->get();

    $stats = $this->calculateStats($vendorId, $startDate, $endDate);

    // ADD THESE NEW VARIABLES
    // Top Products
    $topProducts = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('orders.vendor_id', $vendorId)
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->select(
            'products.id',
            'products.name',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.subtotal) as total_revenue')
        )
        ->groupBy('products.id', 'products.name')
        ->orderBy('total_revenue', 'desc')
        ->limit(10)
        ->get();

    // Top Customers
    $topCustomers = Order::where('vendor_id', $vendorId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select(
            'buyer_id',
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total) as total_spent')
        )
        ->groupBy('buyer_id')
        ->with('buyer')
        ->orderBy('total_spent', 'desc')
        ->limit(10)
        ->get();

    // Revenue Trend (Last 30 Days)
    $revenueTrend = Transaction::where('vendor_id', $vendorId)
        ->where('status', 'completed')
        ->whereBetween('completed_at', [now()->subDays(30), now()])
        ->select(
            DB::raw('DATE(completed_at) as date'),
            DB::raw('SUM(amount) as revenue')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

    // Orders Trend (Last 30 Days)
    $ordersTrend = Order::where('vendor_id', $vendorId)
        ->whereBetween('created_at', [now()->subDays(30), now()])
        ->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

    // Order Status Breakdown
    $orderStatusBreakdown = Order::where('vendor_id', $vendorId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

    return view('vendor.reports.index', compact(
        'reportData',
        'summary',
        'salesByPaymentMethod',
        'salesByStatus',
        'reportType',
        'startDate',
        'stats',
        'endDate',
        'topProducts',
        'topCustomers',
        'revenueTrend',
        'ordersTrend',
        'orderStatusBreakdown'
    ));
}

private function calculateStats($vendorId, $startDate, $endDate)
{
    $totalSales = Transaction::where('vendor_id', $vendorId)
        ->where('status', 'completed')
        ->whereBetween('completed_at', [$startDate, $endDate])
        ->sum('amount');

    $totalOrders = Order::where('vendor_id', $vendorId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

    // Add the missing stats that the view expects
    $totalProducts = DB::table('products')
        ->where('user_id', $vendorId)
        ->count();

    $activeProducts = DB::table('products')
        ->where('user_id', $vendorId)
        ->where('status', 'active') // Adjust field name if different
        ->count();

    $totalCustomers = Order::where('vendor_id', $vendorId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->distinct('buyer_id')
        ->count('buyer_id');

    return [
        'total_sales' => $totalSales,
        'total_orders' => $totalOrders,
        'average_order_value' => $averageOrderValue,
        'total_products' => $totalProducts,
        'active_products' => $activeProducts,
        'total_revenue' => $totalSales, // Same as total_sales
        'total_customers' => $totalCustomers,
    ];
}

    /**
     * Generate report based on type
     */
    private function generateReport($vendorId, $type, $startDate, $endDate)
    {
        switch ($type) {
            case 'daily':
                return $this->getDailyReport($vendorId, $startDate, $endDate);
            case 'weekly':
                return $this->getWeeklyReport($vendorId, $startDate, $endDate);
            case 'monthly':
                return $this->getMonthlyReport($vendorId, $startDate, $endDate);
            case 'product':
                return $this->getProductReport($vendorId, $startDate, $endDate);
            case 'customer':
                return $this->getCustomerReport($vendorId, $startDate, $endDate);
            default:
                return $this->getDailyReport($vendorId, $startDate, $endDate);
        }
    }

    /**
     * Get daily sales report
     */
    private function getDailyReport($vendorId, $startDate, $endDate)
    {
        return Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(completed_at) as period'),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->get()
            ->map(function ($item) {
                $item->period_label = Carbon::parse($item->period)->format('M d, Y');
                $item->orders = Order::where('vendor_id', Auth::id())
                    ->whereDate('created_at', $item->period)
                    ->count();
                return $item;
            });
    }

    /**
     * Get weekly sales report
     */
    private function getWeeklyReport($vendorId, $startDate, $endDate)
    {
        return Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(
                DB::raw('YEARWEEK(completed_at) as period'),
                DB::raw('MIN(DATE(completed_at)) as week_start'),
                DB::raw('MAX(DATE(completed_at)) as week_end'),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->get()
            ->map(function ($item) {
                $item->period_label = 'Week ' . Carbon::parse($item->week_start)->format('W, Y');
                $item->orders = Order::where('vendor_id', Auth::id())
                    ->whereBetween('created_at', [$item->week_start, $item->week_end])
                    ->count();
                return $item;
            });
    }

    /**
     * Get monthly sales report
     */
    private function getMonthlyReport($vendorId, $startDate, $endDate)
    {
        return Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(completed_at, "%Y-%m") as period'),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->get()
            ->map(function ($item) {
                $item->period_label = Carbon::parse($item->period . '-01')->format('F Y');
                $item->orders = Order::where('vendor_id', Auth::id())
                    ->whereYear('created_at', Carbon::parse($item->period)->year)
                    ->whereMonth('created_at', Carbon::parse($item->period)->month)
                    ->count();
                return $item;
            });
    }

    /**
     * Get product sales report
     */
    private function getProductReport($vendorId, $startDate, $endDate)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.vendor_id', $vendorId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name as period_label',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('revenue', 'desc')
            ->get();
    }

    /**
     * Get customer sales report
     */
    private function getCustomerReport($vendorId, $startDate, $endDate)
    {
        return Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'buyer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('AVG(total) as average_order_value')
            )
            ->groupBy('buyer_id')
            ->with('buyer')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(function ($item) {
                $item->period_label = $item->buyer->name ?? 'Unknown Customer';
                return $item;
            });
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        // TODO: Implement CSV/PDF export functionality
        return back()->with('success', 'Export functionality coming soon!');
    }
}
