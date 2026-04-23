<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\RFQs;
use App\Models\Vendor\Vendor;
use App\Models\Analytics\VendorAnalytics;
use App\Models\Analytics\ProductAnalytics;
use App\Models\Analytics\ProfileAnalytics;
use App\Models\Analytics\ArticleAnalytics;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Initialize all variables with default values at the start
        $myRevenue          = 0;
        $revenuePercentage  = 0;
        $totalProducts      = 0;
        $activeProducts     = 0;
        $myOrders           = 0;
        $ordersPercentage   = 0;
        $pendingOrders      = 0;
        $recentOrders       = collect();
        $topProducts        = collect();
        $orderStatuses      = [];
        $salesChartData     = ['labels' => [], 'sales' => [], 'orders' => []];

        // Analytics defaults
        $vendorAnalytics        = null;
        $todayVendorAnalytics   = null;
        $topProductsByViews     = collect();
        $profileAnalytics       = null;

        try {
            $vendor = $this->getVendor();

            if (!$vendor) {
                return redirect()->route('auth.signin')
                    ->with('error', 'Vendor profile not found. Please contact support.');
            }

            // ── Load subscription features ────────────────────────────────────
            $currentSubscription = \App\Models\Subscription::where('seller_id', auth()->id())
                ->where('status', 'active')
                ->with('plan.features')
                ->first();

            $planFeatures = [
                'has_basic_stats'                  => false,
                'has_basic_analytics'              => false,
                'has_analytics'                    => false,
                'has_advanced_analytics'           => false,
                'has_weekly_analytics'             => false,
                'has_performance_reports'          => false,
                'has_monthly_reports'              => false,
                'has_roi_dashboards'               => false,
                'has_advanced_conversion_tracking' => false,
                'has_store_performance_tracking'   => false,
                'has_regional_analytics'           => false,
            ];

            // Check active trial
            $activeTrial = \App\Models\VendorTrial::where('user_id', auth()->id())
                ->where('is_active', true)
                ->where('ends_at', '>=', now())
                ->first();

            if ($activeTrial) {
                $planFeatures = array_fill_keys(array_keys($planFeatures), true);
            } elseif ($currentSubscription) {
                $featuresMap = $currentSubscription->plan->features->pluck('feature_value', 'feature_key');
                foreach (array_keys($planFeatures) as $key) {
                    $val = $featuresMap->get($key, 'false');
                    $planFeatures[$key] = in_array(strtolower($val), ['true', '1', 'yes']);
                }
            }
            // ── Date filter ───────────────────────────────────────────────────
            $filter    = $request->get('filter', 'weekly');
            $dateRange = $request->get('date_range');

            $dateRanges    = $this->getDateRanges($filter, $dateRange);
            $currentStart  = $dateRanges['currentStart'];
            $currentEnd    = $dateRanges['currentEnd'];
            $previousStart = $dateRanges['previousStart'];
            $previousEnd   = $dateRanges['previousEnd'];

            // ── Revenue ───────────────────────────────────────────────────────
            $myRevenue = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->sum('total') ?? 0;

            $previousRevenue = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->sum('total') ?? 0;

            $revenuePercentage = $previousRevenue > 0
                ? round((($myRevenue - $previousRevenue) / $previousRevenue) * 100, 1)
                : 0;

            // ── Products ──────────────────────────────────────────────────────
            $totalProducts  = Product::where('user_id', $vendor->user_id)->count();
            $activeProducts = Product::where('user_id', $vendor->user_id)
                ->where('status', 'active')
                ->where('is_admin_verified', true)
                ->count();

            // ── Orders ────────────────────────────────────────────────────────
            $myOrders = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->count();

            $previousOrders = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->count();

            $ordersPercentage = $previousOrders > 0
                ? round((($myOrders - $previousOrders) / $previousOrders) * 100, 1)
                : 0;

            $pendingOrders = Order::where('vendor_id', $vendor->user_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            // ── Recent Orders ─────────────────────────────────────────────────
            try {
                $recentOrders = Order::where('vendor_id', $vendor->user_id)
                    ->with(['buyer', 'items.product'])
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function ($order) {
                        $firstItem   = $order->items->first();
                        $productName = 'Order Items';
                        if ($firstItem && $firstItem->product) {
                            $productName = $firstItem->product->name;
                        }
                        return [
                            'id'           => $order->order_number,
                            'product'      => $productName,
                            'amount'       => $order->total,
                            'status'       => ucfirst($order->status),
                            'status_color' => $this->getStatusColor($order->status),
                            'icon'         => $this->getStatusIcon($order->status),
                            'color'        => $this->getStatusColor($order->status),
                        ];
                    });
            } catch (\Exception $e) {
                Log::error('Recent Orders Error: ' . $e->getMessage());
            }

            // ── Top Performing Products (by orders) ────────────────────────────
            $topProducts = Product::where('user_id', $vendor->user_id)
                ->where('status', 'active')
                ->withCount(['orderItems as sales_count' => function ($query) use ($currentStart, $currentEnd) {
                    $query->whereHas('order', function ($q) use ($currentStart, $currentEnd) {
                        $q->whereBetween('created_at', [$currentStart, $currentEnd]);
                    });
                }])
                ->withSum(['orderItems as revenue' => function ($query) use ($currentStart, $currentEnd) {
                    $query->whereHas('order', function ($q) use ($currentStart, $currentEnd) {
                        $q->whereBetween('created_at', [$currentStart, $currentEnd]);
                    });
                }], 'unit_price')
                ->having('sales_count', '>', 0)
                ->orderBy('revenue', 'desc')
                ->take(4)
                ->get()
                ->map(function ($product, $index) {
                    $colors      = ['green', 'blue', 'purple', 'indigo'];
                    $stockStatus = $product->stock > 100 ? 'In Stock' : ($product->stock > 0 ? 'Low Stock' : 'Out of Stock');
                    $badgeColor  = $product->stock > 100 ? null : ($product->stock > 0 ? 'orange' : 'red');
                    return [
                        'name'        => $product->name,
                        'sales'       => $product->sales_count ?? 0,
                        'revenue'     => $product->revenue ?? 0,
                        'percentage'  => min(100, ($product->sales_count ?? 0) * 5),
                        'color'       => $colors[$index] ?? 'gray',
                        'stock'       => $product->stock ?? 0,
                        'status'      => $stockStatus,
                        'badge_color' => $badgeColor,
                    ];
                });

            // ── Order Status Overview ─────────────────────────────────────────
            $orderStatuses = [
                ['status' => 'Pending',    'icon' => 'clock',          'color' => 'orange'],
                ['status' => 'Confirmed',  'icon' => 'check',          'color' => 'blue'],
                ['status' => 'Processing', 'icon' => 'cog',            'color' => 'purple'],
                ['status' => 'Shipped',    'icon' => 'shipping-fast',  'color' => 'indigo'],
                ['status' => 'Delivered',  'icon' => 'check-circle',   'color' => 'green'],
                ['status' => 'Cancelled',  'icon' => 'times-circle',   'color' => 'red'],
            ];

            $orderStatuses = array_map(function ($row) use ($vendor, $currentStart, $currentEnd) {
                $slug = strtolower($row['status']);
                $base = Order::where('vendor_id', $vendor->user_id)
                    ->where('status', $slug)
                    ->whereBetween('created_at', [$currentStart, $currentEnd]);

                $row['count']   = $base->count();
                $row['revenue'] = $base->sum('total') ?? 0;
                $row['avg']     = $base->avg('total') ?? 0;
                return $row;
            }, $orderStatuses);

            // ── Sales Chart ───────────────────────────────────────────────────
            $salesChartData = $this->getSalesChartData($vendor->user_id, $filter, $currentStart, $currentEnd);

            // ── Analytics data ────────────────────────────────────────────────
            // Alltime vendor analytics row
            $vendorAnalytics = VendorAnalytics::where('vendor_id', $vendor->id)
                ->where('period', 'alltime')
                ->whereNull('recorded_date')
                ->first();

            // Today's vendor analytics row
            $todayVendorAnalytics = VendorAnalytics::where('vendor_id', $vendor->id)
                ->where('period', 'daily')
                ->where('recorded_date', now()->toDateString())
                ->first();

            // Top 5 products by alltime views (from product_analytics)
            $topProductsByViews = ProductAnalytics::where('period', 'alltime')
                ->whereNull('recorded_date')
                ->whereHas('product', function ($q) use ($vendor) {
                    $q->where('user_id', $vendor->user_id);
                })
                ->with('product:id,name,slug')
                ->orderByDesc('views')
                ->take(5)
                ->get();

            // Business profile analytics (alltime)
            $profileAnalytics = null;
            if ($vendor->businessProfile) {
                $profileAnalytics = ProfileAnalytics::where('business_profile_id', $vendor->businessProfile->id)
                    ->where('period', 'alltime')
                    ->whereNull('recorded_date')
                    ->first();
            }

            // ── Chart data for analytics tab ──────────────────────────────────
            // Last 7 days of vendor store visits
            $analyticsChartData = $this->getAnalyticsChartData($vendor->id);



            return view('vendor.dashboard.index', compact(
                'myRevenue',
                'revenuePercentage',
                'totalProducts',
                'activeProducts',
                'myOrders',
                'ordersPercentage',
                'pendingOrders',
                'recentOrders',
                'topProducts',
                'orderStatuses',
                'salesChartData',
                'filter',
                'planFeatures',
                'currentSubscription',
                'activeTrial',
                // analytics
                'vendorAnalytics',
                'todayVendorAnalytics',
                'topProductsByViews',
                'profileAnalytics',
                'analyticsChartData'
            ));

        } catch (\Exception $e) {
            Log::error('Vendor Dashboard Error: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            $planFeatures = array_fill_keys([
                'has_basic_stats', 'has_basic_analytics', 'has_analytics',
                'has_advanced_analytics', 'has_weekly_analytics', 'has_performance_reports',
                'has_monthly_reports', 'has_roi_dashboards', 'has_advanced_conversion_tracking',
                'has_store_performance_tracking', 'has_regional_analytics',
            ], false);

            $currentSubscription  = null;
            $activeTrial          = null;
            $vendorAnalytics      = null;
            $todayVendorAnalytics = null;
            $topProductsByViews   = collect();
            $profileAnalytics     = null;
            $analyticsChartData   = ['labels' => [], 'visits' => [], 'views' => []];



            return view('vendor.dashboard.index', compact(
                'myRevenue', 'revenuePercentage', 'totalProducts', 'activeProducts',
                'myOrders', 'ordersPercentage', 'pendingOrders', 'recentOrders',
                'topProducts', 'orderStatuses', 'salesChartData',
                'planFeatures', 'currentSubscription',
                'vendorAnalytics', 'todayVendorAnalytics',
                'topProductsByViews', 'profileAnalytics', 'analyticsChartData'
            ))->with('error', 'Some dashboard data could not be loaded. Please refresh the page.');
        }
    }

    // ────────────────────────────────────────────────────────────────────────────
    // Analytics: record a store visit (call from frontend VendorController@show)
    // ────────────────────────────────────────────────────────────────────────────
    public function recordVisit(int $vendorId): void
    {
        try {
            VendorAnalytics::alltime($vendorId)->increment('store_visits');
            VendorAnalytics::today($vendorId)->increment('store_visits');
        } catch (\Exception $e) {
            Log::error('Analytics recordVisit error: ' . $e->getMessage());
        }
    }

    // ────────────────────────────────────────────────────────────────────────────
    // Build last-14-days chart data for analytics tab
    // ────────────────────────────────────────────────────────────────────────────
    private function getAnalyticsChartData(int $vendorId): array
    {
        $labels = [];
        $visits = [];
        $views  = [];

        for ($i = 13; $i >= 0; $i--) {
            $date     = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('M d');

            $row = VendorAnalytics::where('vendor_id', $vendorId)
                ->where('period', 'daily')
                ->where('recorded_date', $date)
                ->first();

            $visits[] = $row ? $row->store_visits         : 0;
            $views[]  = $row ? $row->total_product_views  : 0;
        }

        return ['labels' => $labels, 'visits' => $visits, 'views' => $views];
    }

    // ────────────────────────────────────────────────────────────────────────────
    // Print report
    // ────────────────────────────────────────────────────────────────────────────
    public function print(Request $request)
    {
        try {
            $vendor = $this->getVendor();

            if (!$vendor) {
                abort(404, 'Vendor not found');
            }

            $filter    = $request->get('filter', 'weekly');
            $dateRange = $request->get('date_range');

            $dateRanges   = $this->getDateRanges($filter, $dateRange);
            $currentStart = $dateRanges['current_start'];
            $currentEnd   = $dateRanges['current_end'];

            $myRevenue = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->sum('total') ?? 0;

            $totalProducts  = Product::where('user_id', $vendor->user_id)->count();
            $activeProducts = Product::where('user_id', $vendor->user_id)
                ->where('status', 'active')
                ->where('is_admin_verified', true)
                ->count();

            $myOrders = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->count();

            $pendingOrders = Order::where('vendor_id', $vendor->user_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            $recentOrders = Order::where('vendor_id', $vendor->user_id)
                ->with(['buyer', 'items.product'])
                ->latest()
                ->take(5)
                ->get();

            $topProducts = Product::where('user_id', $vendor->user_id)
                ->where('status', 'active')
                ->withCount(['orderItems as sales_count' => function ($query) use ($currentStart, $currentEnd) {
                    $query->whereHas('order', function ($q) use ($currentStart, $currentEnd) {
                        $q->whereBetween('created_at', [$currentStart, $currentEnd]);
                    });
                }])
                ->withSum(['orderItems as revenue' => function ($query) use ($currentStart, $currentEnd) {
                    $query->whereHas('order', function ($q) use ($currentStart, $currentEnd) {
                        $q->whereBetween('created_at', [$currentStart, $currentEnd]);
                    });
                }], 'unit_price')
                ->having('sales_count', '>', 0)
                ->orderBy('revenue', 'desc')
                ->take(4)
                ->get();

            $orderStatuses = [];
            foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status) {
                $orderStatuses[] = [
                    'status'  => ucfirst($status),
                    'count'   => Order::where('vendor_id', $vendor->user_id)->where('status', $status)->whereBetween('created_at', [$currentStart, $currentEnd])->count(),
                    'revenue' => Order::where('vendor_id', $vendor->user_id)->where('status', $status)->whereBetween('created_at', [$currentStart, $currentEnd])->sum('total') ?? 0,
                ];
            }

            $salesChartData = $this->getSalesChartData($vendor->user_id, $filter, $currentStart, $currentEnd);

            return view('vendor.dashboard.print', compact(
                'vendor', 'myRevenue', 'totalProducts', 'activeProducts',
                'myOrders', 'pendingOrders', 'recentOrders', 'topProducts',
                'orderStatuses', 'salesChartData', 'filter', 'currentStart', 'currentEnd'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Print Error: ' . $e->getMessage());
            abort(500, 'Failed to generate report');
        }
    }

    // ────────────────────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────────────────────
    private function getDateRanges($filter, $dateRange = null): array
    {
        $now = Carbon::now();

        switch ($filter) {
            case 'monthly':
                $currentStart  = $now->copy()->startOfMonth();
                $currentEnd    = $now->copy()->endOfMonth();
                $previousStart = $now->copy()->subMonth()->startOfMonth();
                $previousEnd   = $now->copy()->subMonth()->endOfMonth();
                break;

            case 'yearly':
                $currentStart  = $now->copy()->startOfYear();
                $currentEnd    = $now->copy()->endOfYear();
                $previousStart = $now->copy()->subYear()->startOfYear();
                $previousEnd   = $now->copy()->subYear()->endOfYear();
                break;

            case 'custom':
                if ($dateRange && str_contains($dateRange, ' to ')) {
                    $dates         = explode(' to ', $dateRange);
                    $currentStart  = Carbon::parse($dates[0])->startOfDay();
                    $currentEnd    = Carbon::parse($dates[1])->endOfDay();
                    $duration      = $currentStart->diffInDays($currentEnd);
                    $previousEnd   = $currentStart->copy()->subDay();
                    $previousStart = $previousEnd->copy()->subDays($duration);
                } else {
                    $currentStart  = $now->copy()->startOfWeek();
                    $currentEnd    = $now->copy()->endOfWeek();
                    $previousStart = $now->copy()->subWeek()->startOfWeek();
                    $previousEnd   = $now->copy()->subWeek()->endOfWeek();
                }
                break;

            default: // weekly
                $currentStart  = $now->copy()->startOfWeek();
                $currentEnd    = $now->copy()->endOfWeek();
                $previousStart = $now->copy()->subWeek()->startOfWeek();
                $previousEnd   = $now->copy()->subWeek()->endOfWeek();
        }

        $currentStart  = $currentStart  ?? $now->copy()->startOfWeek();
        $currentEnd    = $currentEnd    ?? $now->copy()->endOfWeek();
        $previousStart = $previousStart ?? $now->copy()->subWeek()->startOfWeek();
        $previousEnd   = $previousEnd   ?? $now->copy()->subWeek()->endOfWeek();

        return compact('currentStart', 'currentEnd', 'previousStart', 'previousEnd');
    }

    private function getVendor()
    {
        return Vendor::with(['businessProfile'])
            ->where('user_id', auth()->id())
            ->first();
    }

    private function getSalesChartData($vendorUserId, $filter, $startDate, $endDate): array
    {
        $days = $sales = $orders = [];

        switch ($filter) {
            case 'weekly':
                for ($i = 6; $i >= 0; $i--) {
                    $date     = Carbon::now()->subDays($i);
                    $days[]   = $date->format('D');
                    $sales[]  = round(Order::where('vendor_id', $vendorUserId)->whereDate('created_at', $date)->sum('total') ?? 0, 2);
                    $orders[] = Order::where('vendor_id', $vendorUserId)->whereDate('created_at', $date)->count();
                }
                break;

            case 'monthly':
                for ($i = 3; $i >= 0; $i--) {
                    $ws = Carbon::now()->subWeeks($i)->startOfWeek();
                    $we = Carbon::now()->subWeeks($i)->endOfWeek();
                    $days[]   = $ws->format('M d');
                    $sales[]  = round(Order::where('vendor_id', $vendorUserId)->whereBetween('created_at', [$ws, $we])->sum('total') ?? 0, 2);
                    $orders[] = Order::where('vendor_id', $vendorUserId)->whereBetween('created_at', [$ws, $we])->count();
                }
                break;

            case 'yearly':
                for ($i = 11; $i >= 0; $i--) {
                    $ms = Carbon::now()->subMonths($i)->startOfMonth();
                    $me = Carbon::now()->subMonths($i)->endOfMonth();
                    $days[]   = $ms->format('M');
                    $sales[]  = round(Order::where('vendor_id', $vendorUserId)->whereBetween('created_at', [$ms, $me])->sum('total') ?? 0, 2);
                    $orders[] = Order::where('vendor_id', $vendorUserId)->whereBetween('created_at', [$ms, $me])->count();
                }
                break;

            case 'custom':
                $totalDays = $startDate->diffInDays($endDate);

                if ($totalDays <= 7) {
                    $cur = $startDate->copy();
                    while ($cur <= $endDate) {
                        $days[]   = $cur->format('M d');
                        $sales[]  = round(Order::where('vendor_id', $vendorUserId)->whereDate('created_at', $cur)->sum('total') ?? 0, 2);
                        $orders[] = Order::where('vendor_id', $vendorUserId)->whereDate('created_at', $cur)->count();
                        $cur->addDay();
                    }
                } elseif ($totalDays <= 60) {
                    $weeks = ceil($totalDays / 7);
                    for ($i = 0; $i < $weeks; $i++) {
                        $ws = $startDate->copy()->addWeeks($i);
                        $we = min($ws->copy()->addWeek(), $endDate);
                        $days[]   = $ws->format('M d');
                        $sales[]  = round(Order::where('vendor_id', $vendorUserId)->whereBetween('created_at', [$ws, $we])->sum('total') ?? 0, 2);
                        $orders[] = Order::where('vendor_id', $vendorUserId)->whereBetween('created_at', [$ws, $we])->count();
                    }
                } else {
                    $cur = $startDate->copy()->startOfMonth();
                    while ($cur <= $endDate) {
                        $me = min($cur->copy()->endOfMonth(), $endDate);
                        $days[]   = $cur->format('M Y');
                        $sales[]  = round(Order::where('vendor_id', $vendorUserId)->whereBetween('created_at', [$cur, $me])->sum('total') ?? 0, 2);
                        $orders[] = Order::where('vendor_id', $vendorUserId)->whereBetween('created_at', [$cur, $me])->count();
                        $cur->addMonth()->startOfMonth();
                    }
                }
                break;
        }

        return ['labels' => $days, 'sales' => $sales, 'orders' => $orders];
    }

    private function getStatusColor($status): string
    {
        return match ($status) {
            'pending'    => 'orange',
            'confirmed'  => 'blue',
            'processing' => 'purple',
            'shipped'    => 'indigo',
            'delivered'  => 'green',
            'cancelled'  => 'red',
            default      => 'gray',
        };
    }

    private function getStatusIcon($status): string
    {
        return match ($status) {
            'pending'    => 'clock',
            'confirmed'  => 'check',
            'processing' => 'cog',
            'shipped'    => 'shipping-fast',
            'delivered'  => 'check-circle',
            'cancelled'  => 'times-circle',
            default      => 'question-circle',
        };
    }
}
