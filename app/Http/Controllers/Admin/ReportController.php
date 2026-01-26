<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display admin reports
     */
    public function index(Request $request)
    {
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
        $reportData = $this->generateReport($reportType, $startDate, $endDate);

        // Calculate statistics
        $stats = $this->calculateStats($startDate, $endDate);

        // Sales by payment method
        $salesByPaymentMethod = Transaction::where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // Sales by order status
        $salesByStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get();

        // Top Products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
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

        // Top Vendors
        $topVendors = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'vendor_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->groupBy('vendor_id')
            ->with('vendor')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Top Customers
        $topCustomers = Order::whereBetween('created_at', [$startDate, $endDate])
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
        $revenueTrend = Transaction::where('status', 'completed')
            ->whereBetween('completed_at', [now()->subDays(30), now()])
            ->select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('SUM(amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Orders Trend (Last 30 Days)
        $ordersTrend = Order::whereBetween('created_at', [now()->subDays(30), now()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Order Status Breakdown
        $orderStatusBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.reports.index', compact(
            'reportData',
            'stats',
            'salesByPaymentMethod',
            'salesByStatus',
            'reportType',
            'startDate',
            'endDate',
            'topProducts',
            'topVendors',
            'topCustomers',
            'revenueTrend',
            'ordersTrend',
            'orderStatusBreakdown'
        ));
    }

    /**
     * Calculate platform statistics
     */
    private function calculateStats($startDate, $endDate)
    {
        $totalSales = Transaction::where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->sum('amount');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $totalProducts = DB::table('products')->count();
        $activeProducts = DB::table('products')->where('status', 'active')->count();

        $totalCustomers = Order::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('buyer_id')
            ->count('buyer_id');

        $totalVendors = DB::table('vendors')->count();
        $activeVendors = DB::table('vendors')->where('account_status', 'active')->count();

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'total_revenue' => $totalSales,
            'total_customers' => $totalCustomers,
            'total_vendors' => $totalVendors,
            'active_vendors' => $activeVendors,
        ];
    }

    /**
 * Print report
 */
public function print(Request $request)
{
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
    $reportData = $this->generateReport($reportType, $startDate, $endDate);

    // Calculate statistics
    $stats = $this->calculateStats($startDate, $endDate);

    // Sales by payment method
    $salesByPaymentMethod = Transaction::where('status', 'completed')
        ->whereBetween('completed_at', [$startDate, $endDate])
        ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
        ->groupBy('payment_method')
        ->get();

    // Top Products
    $topProducts = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
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

    // Top Vendors
    $topVendors = Order::whereBetween('created_at', [$startDate, $endDate])
        ->select(
            'vendor_id',
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total) as total_revenue')
        )
        ->groupBy('vendor_id')
        ->with('vendor')
        ->orderBy('total_revenue', 'desc')
        ->limit(10)
        ->get();

    // Top Customers
    $topCustomers = Order::whereBetween('created_at', [$startDate, $endDate])
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

    // Order Status Breakdown
    $orderStatusBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

    return view('admin.reports.print', compact(
        'reportData',
        'stats',
        'salesByPaymentMethod',
        'reportType',
        'startDate',
        'endDate',
        'topProducts',
        'topVendors',
        'topCustomers',
        'orderStatusBreakdown'
    ));
}

    /**
     * Generate report based on type
     */
    private function generateReport($type, $startDate, $endDate)
    {
        switch ($type) {
            case 'daily':
                return $this->getDailyReport($startDate, $endDate);
            case 'weekly':
                return $this->getWeeklyReport($startDate, $endDate);
            case 'monthly':
                return $this->getMonthlyReport($startDate, $endDate);
            case 'product':
                return $this->getProductReport($startDate, $endDate);
            case 'vendor':
                return $this->getVendorReport($startDate, $endDate);
            case 'customer':
                return $this->getCustomerReport($startDate, $endDate);
            default:
                return $this->getDailyReport($startDate, $endDate);
        }
    }

    /**
     * Get daily sales report
     */
    private function getDailyReport($startDate, $endDate)
    {
        return Transaction::where('status', 'completed')
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
                $item->orders = Order::whereDate('created_at', $item->period)->count();
                return $item;
            });
    }

    /**
     * Get weekly sales report
     */
    private function getWeeklyReport($startDate, $endDate)
    {
        return Transaction::where('status', 'completed')
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
                $item->orders = Order::whereBetween('created_at', [$item->week_start, $item->week_end])->count();
                return $item;
            });
    }

    /**
     * Get monthly sales report
     */
    private function getMonthlyReport($startDate, $endDate)
    {
        return Transaction::where('status', 'completed')
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
                $item->orders = Order::whereYear('created_at', Carbon::parse($item->period)->year)
                    ->whereMonth('created_at', Carbon::parse($item->period)->month)
                    ->count();
                return $item;
            });
    }

    /**
     * Get product sales report
     */
    private function getProductReport($startDate, $endDate)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
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
     * Get vendor sales report
     */
    private function getVendorReport($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'vendor_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('AVG(total) as average_order_value')
            )
            ->groupBy('vendor_id')
            ->with('vendor')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(function ($item) {
                $item->period_label = $item->vendor->name ?? 'Unknown Vendor';
                return $item;
            });
    }

    /**
     * Get customer sales report
     */
    private function getCustomerReport($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
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
