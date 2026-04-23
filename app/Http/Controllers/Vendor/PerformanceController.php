<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Article;
use App\Models\Vendor\Vendor;
use App\Models\BusinessProfile;
use App\Models\Analytics\ProductAnalytics;
use App\Models\Analytics\ProfileAnalytics;
use App\Models\Analytics\ArticleAnalytics;
use App\Models\Analytics\VendorAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    //  INDEX
    // ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        try {
            $vendor = $this->getVendor();
            if (!$vendor) {
                return redirect()->route('auth.signin')
                    ->with('error', 'Vendor profile not found.');
            }

            $userId    = $vendor->user_id;
            $vendorId  = $vendor->id;

            // ── Date range ───────────────────────────────────────
            $period    = $request->get('period', 'monthly'); // weekly|monthly|yearly|custom
            $dateRange = $request->get('date_range');
            $dates     = $this->resolveDates($period, $dateRange);

            $currentStart  = $dates['current_start'];
            $currentEnd    = $dates['current_end'];
            $previousStart = $dates['previous_start'];
            $previousEnd   = $dates['previous_end'];

            // ── Business profile ─────────────────────────────────
            $businessProfile = BusinessProfile::where('user_id', $userId)->first();
            $profileId       = $businessProfile?->id;

            // ════════════════════════════════════════════════════
            //  1. VENDOR-LEVEL ANALYTICS (store traffic etc.)
            // ════════════════════════════════════════════════════
            $vendorAlltime = $profileId
                ? VendorAnalytics::where('vendor_id', $vendorId)
                    ->where('period', 'alltime')
                    ->first()
                : null;

            $vendorPeriod = $profileId
                ? VendorAnalytics::where('vendor_id', $vendorId)
                    ->where('period', 'daily')
                    ->whereBetween('recorded_date', [$currentStart->toDateString(), $currentEnd->toDateString()])
                    ->get()
                : collect();

            $vendorPrevPeriod = $profileId
                ? VendorAnalytics::where('vendor_id', $vendorId)
                    ->where('period', 'daily')
                    ->whereBetween('recorded_date', [$previousStart->toDateString(), $previousEnd->toDateString()])
                    ->get()
                : collect();

            $storeVisitsCurrent  = $vendorPeriod->sum('store_visits');
            $storeVisitsPrevious = $vendorPrevPeriod->sum('store_visits');
            $storeVisitsChange   = $this->pctChange($storeVisitsCurrent, $storeVisitsPrevious);

            // ════════════════════════════════════════════════════
            //  2. PROFILE ANALYTICS (page visits, contact clicks)
            // ════════════════════════════════════════════════════
            $profileAlltime = $profileId
                ? ProfileAnalytics::where('business_profile_id', $profileId)
                    ->where('period', 'alltime')
                    ->first()
                : null;

            $profilePeriod = $profileId
                ? ProfileAnalytics::where('business_profile_id', $profileId)
                    ->where('period', 'daily')
                    ->whereBetween('recorded_date', [$currentStart->toDateString(), $currentEnd->toDateString()])
                    ->get()
                : collect();

            $profilePrevPeriod = $profileId
                ? ProfileAnalytics::where('business_profile_id', $profileId)
                    ->where('period', 'daily')
                    ->whereBetween('recorded_date', [$previousStart->toDateString(), $previousEnd->toDateString()])
                    ->get()
                : collect();

            $profileViews        = $profilePeriod->sum('views');
            $profileViewsPrev    = $profilePrevPeriod->sum('views');
            $profileViewsChange  = $this->pctChange($profileViews, $profileViewsPrev);

            $contactClicks       = $profilePeriod->sum('contact_clicks');
            $whatsappClicks      = $profilePeriod->sum('whatsapp_clicks');
            $websiteClicks       = $profilePeriod->sum('website_clicks');
            $profileRfqs         = $profilePeriod->sum('rfq_count');

            // Daily profile views for chart
            $profileChartData = $this->buildDailyChart(
                $profileId,
                $currentStart,
                $currentEnd,
                $period,
                fn($date) => ProfileAnalytics::where('business_profile_id', $profileId)
                    ->where('period', 'daily')
                    ->where('recorded_date', $date)
                    ->value('views') ?? 0
            );

            // ════════════════════════════════════════════════════
            //  3. PRODUCT ANALYTICS (views, clicks, cart, wishlist)
            // ════════════════════════════════════════════════════
            $myProductIds = Product::where('user_id', $userId)->pluck('id');

            // Aggregated totals for the period
            $productAnalyticsPeriod = ProductAnalytics::whereIn('product_id', $myProductIds)
                ->where('period', 'daily')
                ->whereBetween('recorded_date', [$currentStart->toDateString(), $currentEnd->toDateString()])
                ->get();

            $productAnalyticsPrev = ProductAnalytics::whereIn('product_id', $myProductIds)
                ->where('period', 'daily')
                ->whereBetween('recorded_date', [$previousStart->toDateString(), $previousEnd->toDateString()])
                ->get();

            $productViews        = $productAnalyticsPeriod->sum('views');
            $productViewsPrev    = $productAnalyticsPrev->sum('views');
            $productViewsChange  = $this->pctChange($productViews, $productViewsPrev);

            $productClicks       = $productAnalyticsPeriod->sum('clicks');
            $productImpressions  = $productAnalyticsPeriod->sum('impressions');
            $cartAdds            = $productAnalyticsPeriod->sum('cart_adds');
            $wishlistAdds        = $productAnalyticsPeriod->sum('wishlist_adds');
            $productRfqs         = $productAnalyticsPeriod->sum('rfq_count');
            $videoViews          = $productAnalyticsPeriod->sum('video_views');
            $videoWatchTime      = $productAnalyticsPeriod->sum('video_watch_time'); // seconds

            // Top products by views (period)
            $topProductsByViews = ProductAnalytics::whereIn('product_id', $myProductIds)
                ->where('period', 'alltime')
                ->with('product:id,name,status')
                ->orderByDesc('views')
                ->take(10)
                ->get()
                ->filter(fn($r) => $r->product)
                ->map(fn($r) => [
                    'name'         => $r->product->name,
                    'views'        => $r->views,
                    'clicks'       => $r->clicks,
                    'impressions'  => $r->impressions,
                    'cart_adds'    => $r->cart_adds,
                    'wishlist_adds'=> $r->wishlist_adds,
                    'rfq_count'    => $r->rfq_count,
                    'video_views'  => $r->video_views,
                ]);

            // Product views daily chart
            $productViewsChart = $this->buildAggregatedDailyChart(
                $myProductIds->toArray(),
                $currentStart,
                $currentEnd,
                $period,
                'product_analytics',
                'product_id',
                'views'
            );

            // Impressions vs Clicks chart
            $impressionClickChart = $this->buildDoubleAggregatedChart(
                $myProductIds->toArray(),
                $currentStart,
                $currentEnd,
                $period,
                'product_analytics',
                'product_id',
                'impressions',
                'clicks'
            );

            // ════════════════════════════════════════════════════
            //  4. ARTICLE ANALYTICS
            // ════════════════════════════════════════════════════
            $myArticleIds = Article::where('user_id', $userId)->pluck('id');

            $articleAnalyticsPeriod = ArticleAnalytics::whereIn('article_id', $myArticleIds)
                ->where('period', 'daily')
                ->whereBetween('recorded_date', [$currentStart->toDateString(), $currentEnd->toDateString()])
                ->get();

            $articleAnalyticsPrev = ArticleAnalytics::whereIn('article_id', $myArticleIds)
                ->where('period', 'daily')
                ->whereBetween('recorded_date', [$previousStart->toDateString(), $previousEnd->toDateString()])
                ->get();

            $articleViews       = $articleAnalyticsPeriod->sum('views');
            $articleViewsPrev   = $articleAnalyticsPrev->sum('views');
            $articleViewsChange = $this->pctChange($articleViews, $articleViewsPrev);

            $articleLikes       = $articleAnalyticsPeriod->sum('likes');
            $articleComments    = $articleAnalyticsPeriod->sum('comments_count');
            $articleShares      = $articleAnalyticsPeriod->sum('shares');
            $articleBookmarks   = $articleAnalyticsPeriod->sum('bookmarks');
            $avgReadTime        = $articleAnalyticsPeriod->avg('avg_read_time') ?? 0;
            $avgCompletionRate  = $articleAnalyticsPeriod->avg('completion_rate') ?? 0;

            // Top articles
            $topArticles = ArticleAnalytics::whereIn('article_id', $myArticleIds)
                ->where('period', 'alltime')
                ->with('article:id,title,status,published_at')
                ->orderByDesc('views')
                ->take(10)
                ->get()
                ->filter(fn($r) => $r->article)
                ->map(fn($r) => [
                    'title'           => $r->article->title,
                    'status'          => $r->article->status,
                    'published_at'    => $r->article->published_at,
                    'views'           => $r->views,
                    'likes'           => $r->likes,
                    'comments'        => $r->comments_count,
                    'shares'          => $r->shares,
                    'completion_rate' => $r->completion_rate,
                    'avg_read_time'   => $r->avg_read_time,
                ]);

            // Article views chart
            $articleViewsChart = $this->buildAggregatedDailyChart(
                $myArticleIds->toArray(),
                $currentStart,
                $currentEnd,
                $period,
                'article_analytics',
                'article_id',
                'views'
            );

            // ════════════════════════════════════════════════════
            //  5. ORDER-BASED PERFORMANCE (from orders table)
            // ════════════════════════════════════════════════════
            $totalRevenue = Order::where('vendor_id', $userId)
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->sum('total') ?? 0;

            $prevRevenue = Order::where('vendor_id', $userId)
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->sum('total') ?? 0;

            $revenueChange = $this->pctChange($totalRevenue, $prevRevenue);

            $totalOrders = Order::where('vendor_id', $userId)
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->count();

            $completedOrders = Order::where('vendor_id', $userId)
                ->where('status', 'delivered')
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->count();

            $cancelledOrders = Order::where('vendor_id', $userId)
                ->where('status', 'cancelled')
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->count();

            $conversionRate = $productViews > 0
                ? round(($totalOrders / $productViews) * 100, 2)
                : 0;

            $metrics = [
                'total_revenue'        => $totalRevenue,
                'revenue_change'       => $revenueChange,
                'total_orders'         => $totalOrders,
                'completed_orders'     => $completedOrders,
                'cancelled_orders'     => $cancelledOrders,
                'cancellation_rate'    => $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100, 1) : 0,
                'conversion_rate'      => $conversionRate,
                'avg_order_value'      => $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0,

                // Store
                'store_visits'         => $storeVisitsCurrent,
                'store_visits_change'  => $storeVisitsChange,

                // Profile
                'profile_views'        => $profileViews,
                'profile_views_change' => $profileViewsChange,
                'contact_clicks'       => $contactClicks,
                'whatsapp_clicks'      => $whatsappClicks,
                'website_clicks'       => $websiteClicks,
                'profile_rfqs'         => $profileRfqs,

                // Products
                'product_views'        => $productViews,
                'product_views_change' => $productViewsChange,
                'product_impressions'  => $productImpressions,
                'product_clicks'       => $productClicks,
                'cart_adds'            => $cartAdds,
                'wishlist_adds'        => $wishlistAdds,
                'product_rfqs'         => $productRfqs,
                'video_views'          => $videoViews,
                'video_watch_time_min' => round($videoWatchTime / 60, 1),

                // Articles
                'article_views'        => $articleViews,
                'article_views_change' => $articleViewsChange,
                'article_likes'        => $articleLikes,
                'article_comments'     => $articleComments,
                'article_shares'       => $articleShares,
                'article_bookmarks'    => $articleBookmarks,
                'avg_read_time_sec'    => round($avgReadTime),
                'avg_completion_rate'  => round($avgCompletionRate, 1),

                // Products count
                'total_products'       => Product::where('user_id', $userId)->count(),
                'active_products'      => Product::where('user_id', $userId)->where('status', 'active')->count(),
                'total_articles'       => Article::where('user_id', $userId)->count(),
                'published_articles'   => Article::where('user_id', $userId)->where('status', 'published')->count(),
            ];

            // ── Revenue chart ────────────────────────────────────
            $revenueChart = $this->buildRevenueChart($userId, $period, $currentStart, $currentEnd);

            // ── Product performance table (order-based) ──────────
            $productPerformance = Product::where('user_id', $userId)
                ->withCount(['orderItems as units_sold' => fn($q) => $q->whereHas('order',
                    fn($o) => $o->whereBetween('created_at', [$currentStart, $currentEnd]))])
                ->withCount(['orderItems as order_count' => fn($q) => $q->whereHas('order',
                    fn($o) => $o->whereBetween('created_at', [$currentStart, $currentEnd]))])
                ->withAvg(['orderItems as average_price' => fn($q) => $q->whereHas('order',
                    fn($o) => $o->whereBetween('created_at', [$currentStart, $currentEnd]))], 'unit_price')
                ->withSum(['orderItems as revenue' => fn($q) => $q->whereHas('order',
                    fn($o) => $o->whereBetween('created_at', [$currentStart, $currentEnd]))], 'unit_price')
                ->having('units_sold', '>', 0)
                ->orderByDesc('revenue')
                ->take(20)
                ->get();

            return view('vendor.performance.index', compact(
                'metrics',
                'period',
                'dateRange',
                'currentStart',
                'currentEnd',
                'revenueChart',
                'profileChartData',
                'productViewsChart',
                'impressionClickChart',
                'articleViewsChart',
                'productPerformance',
                'topProductsByViews',
                'topArticles',
            ));

        } catch (\Exception $e) {
            Log::error('PerformanceController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Could not load analytics. Please try again.');
        }
    }

    // ──────────────────────────────────────────────────────────────
    //  PRINT
    // ──────────────────────────────────────────────────────────────
    public function print(Request $request)
    {
        // Reuse the same data-building logic
        $data = $this->index($request);

        // If index redirected (error), just abort
        if (!method_exists($data, 'getData')) {
            abort(500);
        }

        return view('vendor.performance.print', $data->getData());
    }

    // ──────────────────────────────────────────────────────────────
    //  HELPERS
    // ──────────────────────────────────────────────────────────────

    private function getVendor(): ?Vendor
    {
        return Vendor::where('user_id', auth()->id())->first();
    }

    private function pctChange(float $current, float $previous): float
    {
        if ($previous <= 0) return 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function resolveDates(string $period, ?string $dateRange): array
    {
        $now = Carbon::now();

        switch ($period) {
            case 'weekly':
                $cs = $now->copy()->startOfWeek();
                $ce = $now->copy()->endOfWeek();
                $ps = $now->copy()->subWeek()->startOfWeek();
                $pe = $now->copy()->subWeek()->endOfWeek();
                break;

            case 'yearly':
                $cs = $now->copy()->startOfYear();
                $ce = $now->copy()->endOfYear();
                $ps = $now->copy()->subYear()->startOfYear();
                $pe = $now->copy()->subYear()->endOfYear();
                break;

            case 'custom':
                if ($dateRange && str_contains($dateRange, ' to ')) {
                    [$d1, $d2] = explode(' to ', $dateRange);
                    $cs = Carbon::parse($d1)->startOfDay();
                    $ce = Carbon::parse($d2)->endOfDay();
                    $diff = $cs->diffInDays($ce);
                    $pe = $cs->copy()->subDay();
                    $ps = $pe->copy()->subDays($diff);
                } else {
                    goto monthly;
                }
                break;

            case 'monthly':
            default:
                monthly:
                $cs = $now->copy()->startOfMonth();
                $ce = $now->copy()->endOfMonth();
                $ps = $now->copy()->subMonth()->startOfMonth();
                $pe = $now->copy()->subMonth()->endOfMonth();
                break;
        }

        return [
            'current_start'  => $cs,
            'current_end'    => $ce,
            'previous_start' => $ps,
            'previous_end'   => $pe,
        ];
    }

    /**
     * Build a per-day chart using a closure to fetch each day's value.
     */
    private function buildDailyChart(
        ?int     $id,
        Carbon   $start,
        Carbon   $end,
        string   $period,
        callable $getValue
    ): array {
        if (!$id) return ['labels' => [], 'data' => []];

        $labels = [];
        $data   = [];

        $intervals = $this->getChartIntervals($start, $end, $period);

        foreach ($intervals as $interval) {
            $labels[] = $interval['label'];
            $data[]   = $getValue($interval['date']);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Build an aggregated daily chart across multiple IDs from a raw table.
     */
    private function buildAggregatedDailyChart(
        array  $ids,
        Carbon $start,
        Carbon $end,
        string $period,
        string $table,
        string $fk,
        string $column
    ): array {
        if (empty($ids)) return ['labels' => [], 'data' => []];

        $rows = DB::table($table)
            ->whereIn($fk, $ids)
            ->where('period', 'daily')
            ->whereBetween('recorded_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw("recorded_date, SUM({$column}) as total")
            ->groupBy('recorded_date')
            ->orderBy('recorded_date')
            ->pluck('total', 'recorded_date');

        $intervals = $this->getChartIntervals($start, $end, $period);
        $labels    = [];
        $data      = [];

        foreach ($intervals as $interval) {
            $labels[] = $interval['label'];
            $data[]   = (float) ($rows[$interval['date']] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Build a double-series chart (e.g., impressions vs clicks).
     */
    private function buildDoubleAggregatedChart(
        array  $ids,
        Carbon $start,
        Carbon $end,
        string $period,
        string $table,
        string $fk,
        string $col1,
        string $col2
    ): array {
        if (empty($ids)) return ['labels' => [], 'data1' => [], 'data2' => []];

        $rows = DB::table($table)
            ->whereIn($fk, $ids)
            ->where('period', 'daily')
            ->whereBetween('recorded_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw("recorded_date, SUM({$col1}) as v1, SUM({$col2}) as v2")
            ->groupBy('recorded_date')
            ->orderBy('recorded_date')
            ->get()
            ->keyBy('recorded_date');

        $intervals = $this->getChartIntervals($start, $end, $period);
        $labels = $d1 = $d2 = [];

        foreach ($intervals as $interval) {
            $labels[] = $interval['label'];
            $row      = $rows[$interval['date']] ?? null;
            $d1[]     = (float) ($row->v1 ?? 0);
            $d2[]     = (float) ($row->v2 ?? 0);
        }

        return ['labels' => $labels, 'data1' => $d1, 'data2' => $d2];
    }

    /**
     * Build revenue chart from orders table.
     */
    private function buildRevenueChart(int $vendorUserId, string $period, Carbon $start, Carbon $end): array
    {
        $intervals = $this->getChartIntervals($start, $end, $period);
        $labels    = [];
        $revenue   = [];
        $orders    = [];

        foreach ($intervals as $interval) {
            $labels[]  = $interval['label'];
            $rangeStart = $interval['range_start'];
            $rangeEnd   = $interval['range_end'];

            $revenue[] = (float) Order::where('vendor_id', $vendorUserId)
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->sum('total');

            $orders[] = (int) Order::where('vendor_id', $vendorUserId)
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->count();
        }

        return ['labels' => $labels, 'revenue' => $revenue, 'orders' => $orders];
    }

    /**
     * Get chart intervals based on period.
     * Returns array of ['label', 'date' (Y-m-d), 'range_start', 'range_end'].
     */
    private function getChartIntervals(Carbon $start, Carbon $end, string $period): array
    {
        $intervals = [];

        switch ($period) {
            case 'weekly':
                // 7 individual days
                $cur = $start->copy();
                while ($cur->lte($end)) {
                    $intervals[] = [
                        'label'       => $cur->format('D d'),
                        'date'        => $cur->toDateString(),
                        'range_start' => $cur->copy()->startOfDay(),
                        'range_end'   => $cur->copy()->endOfDay(),
                    ];
                    $cur->addDay();
                }
                break;

            case 'yearly':
                // 12 months
                $cur = $start->copy()->startOfMonth();
                while ($cur->lte($end)) {
                    $intervals[] = [
                        'label'       => $cur->format('M'),
                        'date'        => $cur->toDateString(),
                        'range_start' => $cur->copy()->startOfMonth(),
                        'range_end'   => $cur->copy()->endOfMonth(),
                    ];
                    $cur->addMonth();
                }
                break;

            case 'custom':
                $totalDays = $start->diffInDays($end);
                if ($totalDays <= 14) {
                    // daily
                    $cur = $start->copy();
                    while ($cur->lte($end)) {
                        $intervals[] = [
                            'label'       => $cur->format('M d'),
                            'date'        => $cur->toDateString(),
                            'range_start' => $cur->copy()->startOfDay(),
                            'range_end'   => $cur->copy()->endOfDay(),
                        ];
                        $cur->addDay();
                    }
                } elseif ($totalDays <= 60) {
                    // weekly
                    $cur = $start->copy()->startOfWeek();
                    while ($cur->lte($end)) {
                        $intervals[] = [
                            'label'       => $cur->format('M d'),
                            'date'        => $cur->toDateString(),
                            'range_start' => $cur->copy(),
                            'range_end'   => $cur->copy()->addDays(6)->endOfDay(),
                        ];
                        $cur->addWeek();
                    }
                } else {
                    // monthly
                    $cur = $start->copy()->startOfMonth();
                    while ($cur->lte($end)) {
                        $intervals[] = [
                            'label'       => $cur->format('M Y'),
                            'date'        => $cur->toDateString(),
                            'range_start' => $cur->copy()->startOfMonth(),
                            'range_end'   => $cur->copy()->endOfMonth(),
                        ];
                        $cur->addMonth();
                    }
                }
                break;

            case 'monthly':
            default:
                // Days of the month
                $cur = $start->copy();
                while ($cur->lte($end)) {
                    $intervals[] = [
                        'label'       => $cur->format('d'),
                        'date'        => $cur->toDateString(),
                        'range_start' => $cur->copy()->startOfDay(),
                        'range_end'   => $cur->copy()->endOfDay(),
                    ];
                    $cur->addDay();
                }
                break;
        }

        return $intervals;
    }
}
