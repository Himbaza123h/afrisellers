<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
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
        }

        // dd($vendorId, $startDate, $endDate);
        // Overview stats
        $stats = [
            'total_products' => Product::where('user_id', $vendorId)->count(),
            'active_products' => Product::where('user_id', $vendorId)->where('status', 'active')->count(),
            'total_orders' => Order::where('vendor_id', $vendorId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_revenue' => Transaction::where('vendor_id', $vendorId)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->sum('amount'),
            'total_customers' => Order::where('vendor_id', $vendorId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->distinct('buyer_id')
                ->count('buyer_id'),
        ];

        // dd($stats['total_orders']);
        // Top selling products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.vendor_id', $vendorId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Top customers
        $topCustomers = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'buyer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_spent')
            )
            ->groupBy('buyer_id')
            ->orderBy('total_spent', 'desc')
            ->with('buyer')
            ->limit(10)
            ->get();

        // Order status breakdown
        $orderStatusBreakdown = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Revenue trend (last 30 days)
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

        // Orders trend (last 30 days)
        $ordersTrend = Order::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('vendor.analytics.index', compact(
            'stats',
            'topProducts',
            'topCustomers',
            'orderStatusBreakdown',
            'revenueTrend',
            'ordersTrend',
            'startDate',
            'endDate'
        ));
    }
}
