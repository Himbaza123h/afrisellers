<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\RFQs;
use App\Models\Vendor\Vendor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Initialize all variables with default values at the start
        $myRevenue = 0;
        $revenuePercentage = 0;
        $totalProducts = 0;
        $activeProducts = 0;
        $myOrders = 0;
        $ordersPercentage = 0;
        $pendingOrders = 0;
        $recentOrders = collect();
        $topProducts = collect();
        $orderStatuses = [];
        $salesChartData = ['labels' => [], 'sales' => [], 'orders' => []];

        try {
            $vendor = $this->getVendor();

            if (!$vendor) {
                return redirect()->route('auth.signin')
                    ->with('error', 'Vendor profile not found. Please contact support.');
            }

            // Get filter parameters
            $filter = $request->get('filter', 'weekly'); // weekly, monthly, yearly, custom
            $dateRange = $request->get('date_range');

            // Calculate date ranges based on filter
            $dateRanges = $this->getDateRanges($filter, $dateRange);
            $currentStart = $dateRanges['current_start'];
            $currentEnd = $dateRanges['current_end'];
            $previousStart = $dateRanges['previous_start'];
            $previousEnd = $dateRanges['previous_end'];

            // My Revenue - Current period
            $myRevenue = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->sum('total') ?? 0;

            // Previous period revenue for percentage calculation
            $previousRevenue = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->sum('total') ?? 0;

            $revenuePercentage = $previousRevenue > 0
                ? round((($myRevenue - $previousRevenue) / $previousRevenue) * 100, 1)
                : 0;

            // My Products
            $totalProducts = Product::where('user_id', $vendor->user_id)->count();
            $activeProducts = Product::where('user_id', $vendor->user_id)
                ->where('status', 'active')
                ->where('is_admin_verified', true)
                ->count();

            // My Orders - Current period
            $myOrders = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->count();

            // Previous period orders for percentage calculation
            $previousOrders = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->count();

            $ordersPercentage = $previousOrders > 0
                ? round((($myOrders - $previousOrders) / $previousOrders) * 100, 1)
                : 0;

            // Pending Orders (always current, not filtered)
            $pendingOrders = Order::where('vendor_id', $vendor->user_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            // Recent Orders (Last 5, always current)
            $recentOrders = collect();

            try {
                $recentOrders = Order::where('vendor_id', $vendor->user_id)
                    ->with(['buyer', 'items.product'])
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function($order) {
                        $firstItem = $order->items->first();
                        $productName = 'Order Items';

                        if ($firstItem && $firstItem->product) {
                            $productName = $firstItem->product->name;
                        }

                        return [
                            'id' => $order->order_number,
                            'product' => $productName,
                            'amount' => $order->total,
                            'status' => ucfirst($order->status),
                            'status_color' => $this->getStatusColor($order->status),
                            'icon' => $this->getStatusIcon($order->status),
                            'color' => $this->getStatusColor($order->status)
                        ];
                    });
            } catch (\Exception $e) {
                Log::error('Recent Orders Error: ' . $e->getMessage());
            }

            // Top Performing Products - Based on selected period
            $topProducts = Product::where('user_id', $vendor->user_id)
                ->where('status', 'active')
                ->withCount(['orderItems as sales_count' => function($query) use ($currentStart, $currentEnd) {
                    $query->whereHas('order', function($q) use ($currentStart, $currentEnd) {
                        $q->whereBetween('created_at', [$currentStart, $currentEnd]);
                    });
                }])
                ->withSum(['orderItems as revenue' => function($query) use ($currentStart, $currentEnd) {
                    $query->whereHas('order', function($q) use ($currentStart, $currentEnd) {
                        $q->whereBetween('created_at', [$currentStart, $currentEnd]);
                    });
                }], 'unit_price')
                ->having('sales_count', '>', 0)
                ->orderBy('revenue', 'desc')
                ->take(4)
                ->get()
                ->map(function($product, $index) {
                    $colors = ['green', 'blue', 'purple', 'indigo'];
                    $stockStatus = $product->stock > 100 ? 'In Stock' : ($product->stock > 0 ? 'Low Stock' : 'Out of Stock');
                    $badgeColor = $product->stock > 100 ? null : ($product->stock > 0 ? 'orange' : 'red');

                    return [
                        'name' => $product->name,
                        'sales' => $product->sales_count ?? 0,
                        'revenue' => $product->revenue ?? 0,
                        'percentage' => min(100, ($product->sales_count ?? 0) * 5),
                        'color' => $colors[$index] ?? 'gray',
                        'stock' => $product->stock ?? 0,
                        'status' => $stockStatus,
                        'badge_color' => $badgeColor
                    ];
                });

            // Order Status Overview - Based on selected period
            $orderStatuses = [
                [
                    'status' => 'Pending',
                    'icon' => 'clock',
                    'color' => 'orange',
                    'count' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'pending')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->count(),
                    'revenue' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'pending')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->sum('total') ?? 0,
                    'avg' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'pending')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->avg('total') ?? 0
                ],
                [
                    'status' => 'Confirmed',
                    'icon' => 'check',
                    'color' => 'blue',
                    'count' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'confirmed')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->count(),
                    'revenue' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'confirmed')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->sum('total') ?? 0,
                    'avg' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'confirmed')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->avg('total') ?? 0
                ],
                [
                    'status' => 'Processing',
                    'icon' => 'cog',
                    'color' => 'purple',
                    'count' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'processing')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->count(),
                    'revenue' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'processing')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->sum('total') ?? 0,
                    'avg' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'processing')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->avg('total') ?? 0
                ],
                [
                    'status' => 'Shipped',
                    'icon' => 'shipping-fast',
                    'color' => 'indigo',
                    'count' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'shipped')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->count(),
                    'revenue' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'shipped')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->sum('total') ?? 0,
                    'avg' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'shipped')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->avg('total') ?? 0
                ],
                [
                    'status' => 'Delivered',
                    'icon' => 'check-circle',
                    'color' => 'green',
                    'count' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'delivered')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->count(),
                    'revenue' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'delivered')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->sum('total') ?? 0,
                    'avg' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'delivered')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->avg('total') ?? 0
                ],
                [
                    'status' => 'Cancelled',
                    'icon' => 'times-circle',
                    'color' => 'red',
                    'count' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'cancelled')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->count(),
                    'revenue' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'cancelled')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->sum('total') ?? 0,
                    'avg' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', 'cancelled')
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->avg('total') ?? 0
                ]
            ];

            // Sales Chart Data based on filter
            $salesChartData = $this->getSalesChartData($vendor->user_id, $filter, $currentStart, $currentEnd);

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
                'filter'
            ));

        } catch (\Exception $e) {
            Log::error('Vendor Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            // Return view with default values instead of redirecting
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
            ))->with('error', 'Some dashboard data could not be loaded. Please refresh the page.');
        }
    }


        /**
     * Print dashboard report
     */
    public function print(Request $request)
    {
        try {
            $vendor = $this->getVendor();

            if (!$vendor) {
                abort(404, 'Vendor not found');
            }

            // Get filter parameters
            $filter = $request->get('filter', 'weekly');
            $dateRange = $request->get('date_range');

            // Calculate date ranges
            $dateRanges = $this->getDateRanges($filter, $dateRange);
            $currentStart = $dateRanges['current_start'];
            $currentEnd = $dateRanges['current_end'];

            // Get all stats for print
            $myRevenue = Order::where('vendor_id', $vendor->user_id)
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->sum('total') ?? 0;

            $totalProducts = Product::where('user_id', $vendor->user_id)->count();
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

            // Recent Orders
            $recentOrders = Order::where('vendor_id', $vendor->user_id)
                ->with(['buyer', 'items.product'])
                ->latest()
                ->take(5)
                ->get();

            // Top Products
            $topProducts = Product::where('user_id', $vendor->user_id)
                ->where('status', 'active')
                ->withCount(['orderItems as sales_count' => function($query) use ($currentStart, $currentEnd) {
                    $query->whereHas('order', function($q) use ($currentStart, $currentEnd) {
                        $q->whereBetween('created_at', [$currentStart, $currentEnd]);
                    });
                }])
                ->withSum(['orderItems as revenue' => function($query) use ($currentStart, $currentEnd) {
                    $query->whereHas('order', function($q) use ($currentStart, $currentEnd) {
                        $q->whereBetween('created_at', [$currentStart, $currentEnd]);
                    });
                }], 'unit_price')
                ->having('sales_count', '>', 0)
                ->orderBy('revenue', 'desc')
                ->take(4)
                ->get();

            // Order Status Overview
            $orderStatuses = [];
            $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

            foreach ($statuses as $status) {
                $orderStatuses[] = [
                    'status' => ucfirst($status),
                    'count' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', $status)
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->count(),
                    'revenue' => Order::where('vendor_id', $vendor->user_id)
                        ->where('status', $status)
                        ->whereBetween('created_at', [$currentStart, $currentEnd])
                        ->sum('total') ?? 0,
                ];
            }

            // Sales Chart Data
            $salesChartData = $this->getSalesChartData($vendor->user_id, $filter, $currentStart, $currentEnd);

            return view('vendor.dashboard.print', compact(
                'vendor',
                'myRevenue',
                'totalProducts',
                'activeProducts',
                'myOrders',
                'pendingOrders',
                'recentOrders',
                'topProducts',
                'orderStatuses',
                'salesChartData',
                'filter',
                'currentStart',
                'currentEnd'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Print Error: ' . $e->getMessage());
            abort(500, 'Failed to generate report');
        }
    }

    /**
     * Get date ranges based on filter type
     */
    private function getDateRanges($filter, $dateRange = null)
    {
        $now = Carbon::now();

        switch ($filter) {
            case 'weekly':
                $currentStart = $now->copy()->startOfWeek();
                $currentEnd = $now->copy()->endOfWeek();
                $previousStart = $now->copy()->subWeek()->startOfWeek();
                $previousEnd = $now->copy()->subWeek()->endOfWeek();
                break;

            case 'monthly':
                $currentStart = $now->copy()->startOfMonth();
                $currentEnd = $now->copy()->endOfMonth();
                $previousStart = $now->copy()->subMonth()->startOfMonth();
                $previousEnd = $now->copy()->subMonth()->endOfMonth();
                break;

            case 'yearly':
                $currentStart = $now->copy()->startOfYear();
                $currentEnd = $now->copy()->endOfYear();
                $previousStart = $now->copy()->subYear()->startOfYear();
                $previousEnd = $now->copy()->subYear()->endOfYear();
                break;

            case 'custom':
                if ($dateRange && str_contains($dateRange, ' to ')) {
                    $dates = explode(' to ', $dateRange);
                    $currentStart = Carbon::parse($dates[0])->startOfDay();
                    $currentEnd = Carbon::parse($dates[1])->endOfDay();

                    // Calculate the same duration for previous period
                    $duration = $currentStart->diffInDays($currentEnd);
                    $previousEnd = $currentStart->copy()->subDay();
                    $previousStart = $previousEnd->copy()->subDays($duration);
                } else {
                    // Default to current week if invalid custom range
                    $currentStart = $now->copy()->startOfWeek();
                    $currentEnd = $now->copy()->endOfWeek();
                    $previousStart = $now->copy()->subWeek()->startOfWeek();
                    $previousEnd = $now->copy()->subWeek()->endOfWeek();
                }
                break;

            default:
                $currentStart = $now->copy()->startOfWeek();
                $currentEnd = $now->copy()->endOfWeek();
                $previousStart = $now->copy()->subWeek()->startOfWeek();
                $previousEnd = $now->copy()->subWeek()->endOfWeek();
        }

        return [
            'current_start' => $currentStart,
            'current_end' => $currentEnd,
            'previous_start' => $previousStart,
            'previous_end' => $previousEnd,
        ];
    }

    /**
     * Get the authenticated vendor
     */
    private function getVendor()
    {
        return Vendor::with(['businessProfile'])
            ->where('user_id', auth()->id())
            ->first();
    }

    /**
     * Get sales chart data based on filter
     */
    private function getSalesChartData($vendorUserId, $filter, $startDate, $endDate)
    {
        $days = [];
        $sales = [];
        $orders = [];

        switch ($filter) {
            case 'weekly':
                // Show 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $days[] = $date->format('D');

                    $dailySales = Order::where('vendor_id', $vendorUserId)
                        ->whereDate('created_at', $date)
                        ->sum('total') ?? 0;
                    $sales[] = round($dailySales, 2);

                    $dailyOrders = Order::where('vendor_id', $vendorUserId)
                        ->whereDate('created_at', $date)
                        ->count();
                    $orders[] = $dailyOrders;
                }
                break;

            case 'monthly':
                // Show last 30 days grouped by week
                for ($i = 3; $i >= 0; $i--) {
                    $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                    $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
                    $days[] = $weekStart->format('M d');

                    $weeklySales = Order::where('vendor_id', $vendorUserId)
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->sum('total') ?? 0;
                    $sales[] = round($weeklySales, 2);

                    $weeklyOrders = Order::where('vendor_id', $vendorUserId)
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->count();
                    $orders[] = $weeklyOrders;
                }
                break;

            case 'yearly':
                // Show 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
                    $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
                    $days[] = $monthStart->format('M');

                    $monthlySales = Order::where('vendor_id', $vendorUserId)
                        ->whereBetween('created_at', [$monthStart, $monthEnd])
                        ->sum('total') ?? 0;
                    $sales[] = round($monthlySales, 2);

                    $monthlyOrders = Order::where('vendor_id', $vendorUserId)
                        ->whereBetween('created_at', [$monthStart, $monthEnd])
                        ->count();
                    $orders[] = $monthlyOrders;
                }
                break;

            case 'custom':
                // Divide the custom range into appropriate intervals
                $totalDays = $startDate->diffInDays($endDate);

                if ($totalDays <= 7) {
                    // Show daily for 7 days or less
                    $current = $startDate->copy();
                    while ($current <= $endDate) {
                        $days[] = $current->format('M d');

                        $dailySales = Order::where('vendor_id', $vendorUserId)
                            ->whereDate('created_at', $current)
                            ->sum('total') ?? 0;
                        $sales[] = round($dailySales, 2);

                        $dailyOrders = Order::where('vendor_id', $vendorUserId)
                            ->whereDate('created_at', $current)
                            ->count();
                        $orders[] = $dailyOrders;

                        $current->addDay();
                    }
                } elseif ($totalDays <= 60) {
                    // Show weekly for 2 months or less
                    $weeks = ceil($totalDays / 7);
                    for ($i = 0; $i < $weeks; $i++) {
                        $weekStart = $startDate->copy()->addWeeks($i);
                        $weekEnd = min($weekStart->copy()->addWeek(), $endDate);
                        $days[] = $weekStart->format('M d');

                        $weeklySales = Order::where('vendor_id', $vendorUserId)
                            ->whereBetween('created_at', [$weekStart, $weekEnd])
                            ->sum('total') ?? 0;
                        $sales[] = round($weeklySales, 2);

                        $weeklyOrders = Order::where('vendor_id', $vendorUserId)
                            ->whereBetween('created_at', [$weekStart, $weekEnd])
                            ->count();
                        $orders[] = $weeklyOrders;
                    }
                } else {
                    // Show monthly for longer periods
                    $current = $startDate->copy()->startOfMonth();
                    while ($current <= $endDate) {
                        $monthEnd = min($current->copy()->endOfMonth(), $endDate);
                        $days[] = $current->format('M Y');

                        $monthlySales = Order::where('vendor_id', $vendorUserId)
                            ->whereBetween('created_at', [$current, $monthEnd])
                            ->sum('total') ?? 0;
                        $sales[] = round($monthlySales, 2);

                        $monthlyOrders = Order::where('vendor_id', $vendorUserId)
                            ->whereBetween('created_at', [$current, $monthEnd])
                            ->count();
                        $orders[] = $monthlyOrders;

                        $current->addMonth()->startOfMonth();
                    }
                }
                break;
        }

        return [
            'labels' => $days,
            'sales' => $sales,
            'orders' => $orders
        ];
    }

    /**
     * Get status color
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'orange',
            'confirmed' => 'blue',
            'processing' => 'purple',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status icon
     */
    private function getStatusIcon($status)
    {
        return match($status) {
            'pending' => 'clock',
            'confirmed' => 'check',
            'processing' => 'cog',
            'shipped' => 'shipping-fast',
            'delivered' => 'check-circle',
            'cancelled' => 'times-circle',
            default => 'question-circle'
        };
    }
}
