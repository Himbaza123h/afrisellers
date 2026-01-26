<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    /**
     * Display performance metrics
     */
    public function index(Request $request)
    {
        $vendorId = Auth::id();

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

        // Calculate metrics
        $metrics = $this->calculateMetrics($vendorId, $startDate, $endDate);

        // Get comparison data
        $comparison = $this->getComparisonData($vendorId, $startDate, $endDate);

        // Performance trends
        $trends = $this->getPerformanceTrends($vendorId, $startDate, $endDate);

        // Product performance
        $productPerformance = $this->getProductPerformance($vendorId, $startDate, $endDate);

        return view('vendor.performance.index', compact(
            'metrics',
            'comparison',
            'trends',
            'productPerformance',
            'startDate',
            'endDate'
        ));
    }


    /**
 * Print performance report.
 */
public function print(Request $request)
{
    try {
        $vendorId = Auth::id();

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

        // Calculate metrics
        $metrics = $this->calculateMetrics($vendorId, $startDate, $endDate);

        // Get comparison data
        $comparison = $this->getComparisonData($vendorId, $startDate, $endDate);

        // Product performance
        $productPerformance = $this->getProductPerformance($vendorId, $startDate, $endDate);

        // Performance trends (simplified for print)
        $trends = $this->getPerformanceTrends($vendorId, $startDate, $endDate);

        // Customer distribution
        $customerDistribution = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('buyer_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total) as total_spent'))
            ->groupBy('buyer_id')
            ->with('buyer')
            ->orderBy('order_count', 'desc')
            ->limit(10)
            ->get();

        // Order status distribution
        $orderStatusDistribution = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get();

        return view('vendor.performance.print', compact(
            'metrics',
            'comparison',
            'productPerformance',
            'trends',
            'customerDistribution',
            'orderStatusDistribution',
            'startDate',
            'endDate'
        ));
    } catch (\Exception $e) {
        Log::error('Performance Print Error: ' . $e->getMessage());
        abort(500, 'An error occurred while generating the print report.');
    }
}

    /**
     * Calculate performance metrics
     */
    private function calculateMetrics($vendorId, $startDate, $endDate)
    {
        // Orders data
        $totalOrders = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $completedOrders = Order::where('vendor_id', $vendorId)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $cancelledOrders = Order::where('vendor_id', $vendorId)
            ->where('status', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Revenue data
        $totalRevenue = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->sum('amount');

        // Customer data
        $totalCustomers = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('buyer_id')
            ->count('buyer_id');

        $repeatCustomers = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('buyer_id', DB::raw('COUNT(*) as order_count'))
            ->groupBy('buyer_id')
            ->having('order_count', '>', 1)
            ->count();

        // Calculate rates
        $conversionRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
        $cancellationRate = $totalOrders > 0 ? ($cancelledOrders / $totalOrders) * 100 : 0;
        $repeatCustomerRate = $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0;
        $averageOrderValue = $completedOrders > 0 ? $totalRevenue / $completedOrders : 0;

        // Product metrics
        $totalProducts = Product::where('user_id', $vendorId)->count();
        $activeProducts = Product::where('user_id', $vendorId)->where('status', 'active')->count();

        return [
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'total_revenue' => $totalRevenue,
            'total_customers' => $totalCustomers,
            'repeat_customers' => $repeatCustomers,
            'conversion_rate' => round($conversionRate, 2),
            'cancellation_rate' => round($cancellationRate, 2),
            'repeat_customer_rate' => round($repeatCustomerRate, 2),
            'average_order_value' => $averageOrderValue,
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
        ];
    }

    /**
     * Get comparison data with previous period
     */
    private function getComparisonData($vendorId, $startDate, $endDate)
    {
        $daysDiff = $startDate->diffInDays($endDate);
        $previousStart = $startDate->copy()->subDays($daysDiff + 1);
        $previousEnd = $startDate->copy()->subDay();

        // Current period
        $currentRevenue = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->sum('amount');

        $currentOrders = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Previous period
        $previousRevenue = Transaction::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$previousStart, $previousEnd])
            ->sum('amount');

        $previousOrders = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();

        // Calculate changes
        $revenueChange = $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        $ordersChange = $previousOrders > 0
            ? (($currentOrders - $previousOrders) / $previousOrders) * 100
            : 0;

        return [
            'revenue_change' => round($revenueChange, 2),
            'orders_change' => round($ordersChange, 2),
            'previous_revenue' => $previousRevenue,
            'previous_orders' => $previousOrders,
        ];
    }

    /**
     * Get performance trends over time
     */
    private function getPerformanceTrends($vendorId, $startDate, $endDate)
    {
        $diffInDays = $startDate->diffInDays($endDate);

        if ($diffInDays <= 31) {
            // Daily trends
            $groupBy = DB::raw('DATE(created_at) as period');
        } elseif ($diffInDays <= 90) {
            // Weekly trends
            $groupBy = DB::raw('YEARWEEK(created_at) as period');
        } else {
            // Monthly trends
            $groupBy = DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period');
        }

        $ordersTrend = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                $groupBy,
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        $labels = [];
        $orders = [];
        $revenue = [];

        foreach ($ordersTrend as $item) {
            if ($diffInDays <= 31) {
                $labels[] = Carbon::parse($item->period)->format('M d');
            } elseif ($diffInDays <= 90) {
                $labels[] = 'Week ' . date('W', strtotime($item->period));
            } else {
                $labels[] = Carbon::parse($item->period . '-01')->format('M Y');
            }

            $orders[] = $item->count;
            $revenue[] = (float) $item->revenue;
        }

        return [
            'labels' => $labels,
            'orders' => $orders,
            'revenue' => $revenue,
        ];
    }

    /**
     * Get product performance data
     */
    private function getProductPerformance($vendorId, $startDate, $endDate)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.vendor_id', $vendorId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('AVG(order_items.unit_price) as average_price')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();
    }
}
