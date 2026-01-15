<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Escrow;
use App\Models\Performance;
use App\Models\PerformanceLog;
use App\Models\Country;
use App\Models\RFQ;
use App\Models\RFQs;
use App\Models\Showroom;
use App\Models\Tradeshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard
     */
    public function index(Request $request)
    {
        // Date range handling
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());

        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                $startDate = Carbon::parse($dates[0])->startOfDay();
                $endDate = Carbon::parse($dates[1])->endOfDay();
            }
        }

        // Overview Statistics
        $stats = $this->getOverviewStats($startDate, $endDate);

        // Performance Metrics
        $performanceMetrics = $this->getPerformanceMetrics($startDate, $endDate);

        // Revenue & Order Trends
        $revenueTrend = $this->getRevenueTrend($startDate, $endDate);
        $ordersTrend = $this->getOrdersTrend($startDate, $endDate);

        // User Growth Trend
        $userGrowthTrend = $this->getUserGrowthTrend($startDate, $endDate);

        // Top Performing Products
        $topProducts = $this->getTopProducts($startDate, $endDate, 10);

        // Top Vendors
        $topVendors = $this->getTopVendors($startDate, $endDate, 10);

        // Top Buyers
        $topBuyers = $this->getTopBuyers($startDate, $endDate, 10);

        // Regional Analytics
        $regionalStats = $this->getRegionalStats($startDate, $endDate);

        // Order Status Distribution
        $orderStatusDistribution = $this->getOrderStatusDistribution($startDate, $endDate);

        // Product Category Distribution
        $categoryDistribution = $this->getCategoryDistribution();

        // Transaction Stats
        $transactionStats = $this->getTransactionStats($startDate, $endDate);

        // Escrow Stats
        $escrowStats = $this->getEscrowStats($startDate, $endDate);

        // Platform Activity
        $platformActivity = $this->getPlatformActivity($startDate, $endDate);

        return view('admin.analytics.index', compact(
            'stats',
            'performanceMetrics',
            'revenueTrend',
            'ordersTrend',
            'userGrowthTrend',
            'topProducts',
            'topVendors',
            'topBuyers',
            'regionalStats',
            'orderStatusDistribution',
            'categoryDistribution',
            'transactionStats',
            'escrowStats',
            'platformActivity',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats($startDate, $endDate)
    {
        return [
            // User Stats
            'total_users' => User::count(),
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_users' => User::count(),

            // Vendor Stats
            'total_vendors' => BusinessProfile::count(),
            'verified_vendors' => BusinessProfile::where('verification_status', 'verified')->count(),
            'new_vendors' => BusinessProfile::whereBetween('created_at', [$startDate, $endDate])->count(),

            // Buyer Stats
            'total_buyers' => User::whereHas('buyer')->count(),
            'active_buyers' => User::whereHas('buyer')->count(),

            // Product Stats
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'new_products' => Product::whereBetween('created_at', [$startDate, $endDate])->count(),

            // Order Stats
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'pending_orders' => Order::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_orders' => Order::where('status', 'delivered')->whereBetween('created_at', [$startDate, $endDate])->count(),

            // Revenue Stats
            'total_revenue' => Transaction::where('status', 'completed')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->sum('amount'),
            'amounts' => Transaction::where('status', 'completed')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->sum('amount'),

            // RFQ Stats
            'total_rfqs' => RFQs::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_rfqs' => RFQs::where('status', 'open')->count(),

            // Showroom Stats
            'total_showrooms' => Showroom::count(),
            'verified_showrooms' => Showroom::where('is_verified', true)->count(),

            // Tradeshow Stats
            'total_tradeshows' => Tradeshow::count(),
            'active_tradeshows' => Tradeshow::where('status', 'active')->count(),
        ];
    }

    /**
     * Get performance metrics using both Performance and PerformanceLog models
     */
    private function getPerformanceMetrics($startDate, $endDate)
    {
        // Get aggregated performance data
        $performance = Performance::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalClicks = $performance->sum('clicks');
        $totalImpressions = $performance->sum('impressions');
        $ctr = $totalImpressions > 0 ? ($totalClicks / $totalImpressions) * 100 : 0;

        // Get performance log data for detailed tracking
        $performanceLogClicks = PerformanceLog::where('type', 'click')
            ->whereBetween('tracked_date', [$startDate, $endDate])
            ->count();

        $performanceLogViews = PerformanceLog::where('type', 'view')
            ->whereBetween('tracked_date', [$startDate, $endDate])
            ->count();

        return [
            'total_clicks' => $totalClicks,
            'total_impressions' => $totalImpressions,
            'ctr' => round($ctr, 2),
            'avg_ctr' => round($performance->avg('ctr') ?? 0, 2),
            'log_clicks' => $performanceLogClicks,
            'log_views' => $performanceLogViews,
        ];
    }

    /**
     * Get revenue trend
     */
    private function getRevenueTrend($startDate, $endDate)
    {
        return Transaction::where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('SUM(amount) as fees')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Get orders trend
     */
    private function getOrdersTrend($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total_value')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Get user growth trend
     */
    private function getUserGrowthTrend($startDate, $endDate)
    {
        return User::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Get top performing products
     */
    private function getTopProducts($startDate, $endDate, $limit = 10)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('products.id', 'products.name', 'products.slug')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top vendors
     */
/**
 * Get top vendors
 */
private function getTopVendors($startDate, $endDate, $limit = 10)
{
    return Order::whereBetween('created_at', [$startDate, $endDate])
        ->select(
            'vendor_id',
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total) as total_revenue')
        )
        ->groupBy('vendor_id')
        ->orderBy('total_revenue', 'desc')
        ->with('vendor') // Just load vendor (which is already a User)
        ->limit($limit)
        ->get();
}

    /**
     * Get top buyers
     */
    private function getTopBuyers($startDate, $endDate, $limit = 10)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'buyer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_spent')
            )
            ->groupBy('buyer_id')
            ->orderBy('total_spent', 'desc')
            ->with('buyer')
            ->limit($limit)
            ->get();
    }

    /**
     * Get regional statistics
     */
private function getRegionalStats($startDate, $endDate)
{
    return Country::with('region')
        ->get()
        ->map(function ($country) use ($startDate, $endDate) {
            $country->vendors_count = $country->vendors()->count();

            $country->products_count = \App\Models\Product::whereHas('user.vendor.businessProfile', function($q) use ($country) {
                $q->where('country_id', $country->id);
            })->count();

            $country->orders_count = \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($country) {
                $q->where('country_id', $country->id);
            })->whereBetween('created_at', [$startDate, $endDate])->count();

            $country->total_revenue = \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($country) {
                $q->where('country_id', $country->id);
            })->whereBetween('created_at', [$startDate, $endDate])->sum('total');

            return $country;
        })
        ->sortByDesc('total_revenue')
        ->take(10);
}

    /**
     * Get order status distribution
     */
    private function getOrderStatusDistribution($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
    }


    /**
     * Get product category distribution
     */
    private function getCategoryDistribution()
    {
        return DB::table('products')
            ->join('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->select(
                'product_categories.name',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get transaction statistics
     */
    private function getTransactionStats($startDate, $endDate)
    {
        return [
            'total_transactions' => Transaction::whereBetween('created_at', [$startDate, $endDate])->count(),
            'successful' => Transaction::where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'pending' => Transaction::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'failed' => Transaction::where('status', 'failed')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'refunded' => Transaction::where('status', 'refunded')->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];
    }

    /**
     * Get escrow statistics
     */
    private function getEscrowStats($startDate, $endDate)
    {
        return [
            'total_escrows' => Escrow::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active' => Escrow::where('status', 'active')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'released' => Escrow::where('status', 'released')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'disputed' => Escrow::where('disputed', true)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_held' => Escrow::where('status', 'active')->sum('amount'),
        ];
    }

    /**
     * Get platform activity
     */
    private function getPlatformActivity($startDate, $endDate)
    {
        return [
            'product_views' => PerformanceLog::where('type', 'view')
                ->whereBetween('tracked_date', [$startDate, $endDate])
                ->count(),
            'product_clicks' => PerformanceLog::where('type', 'click')
                ->whereBetween('tracked_date', [$startDate, $endDate])
                ->count(),
            'rfq_submitted' => RFQs::whereBetween('created_at', [$startDate, $endDate])->count(),
            'orders_placed' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];
    }

    /**
     * Show regional analytics
     */
    public function regional(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());

        $regionalData = Country::withCount([
            'vendors',
            'buyers',
            'products',
            'orders' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])
        ->with(['orders' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->get()
        ->map(function ($country) {
            $country->total_revenue = $country->orders->sum('total');
            return $country;
        });

        return view('admin.analytics.regional', compact('regionalData', 'startDate', 'endDate'));
    }

    /**
     * Show product analytics
     */
    public function products(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());

        $productStats = $this->getTopProducts($startDate, $endDate, 50);
        $categoryDistribution = $this->getCategoryDistribution();

        return view('admin.analytics.products', compact('productStats', 'categoryDistribution', 'startDate', 'endDate'));
    }

    /**
     * Show performance analytics
     */
    public function performance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());

        $performanceData = Performance::whereBetween('created_at', [$startDate, $endDate])
            ->with(['product', 'vendor', 'country'])
            ->orderBy('clicks', 'desc')
            ->paginate(50);

        $topPerformingProducts = Performance::whereBetween('created_at', [$startDate, $endDate])
            ->select('product_id', DB::raw('SUM(clicks) as total_clicks'), DB::raw('SUM(impressions) as total_impressions'))
            ->groupBy('product_id')
            ->with('product')
            ->orderBy('total_clicks', 'desc')
            ->limit(20)
            ->get();

        return view('admin.analytics.performance', compact('performanceData', 'topPerformingProducts', 'startDate', 'endDate'));
    }
}
