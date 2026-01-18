<?php

namespace App\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Vendor\Vendor;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\User;
use App\Models\Showroom;
use App\Models\Load;
use App\Models\Transporter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get the country admin's country
        $user = auth()->user();
        $country = Country::find($user->country_id);

        if (!$country) {
            abort(403, 'No country assigned to this admin.');
        }

        // Get filter parameters
        $period = $request->get('period', 'this_month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Statistics
        $stats = [
            'total_vendors' => $this->getTotalVendors($country->id),
            'active_vendors' => $this->getActiveVendors($country->id),
            'pending_vendors' => $this->getPendingVendors($country->id),
            'total_products' => $this->getTotalProducts($country->id),
            'total_showrooms' => $this->getTotalShowrooms($country->id),
            'total_loads' => $this->getTotalLoads($country->id),
            'total_transporters' => $this->getTotalTransporters($country->id),
            'period_revenue' => $this->getPeriodRevenue($country->id, $startDate, $endDate),
            'period_orders' => $this->getPeriodOrders($country->id, $startDate, $endDate),
        ];

        // Chart data
        $vendorGrowth = $this->getVendorGrowthChart($country->id, $period);
        $productGrowth = $this->getProductGrowthChart($country->id, $period);
        $revenueChart = $this->getRevenueChart($country->id, $period);
        $orderStatusChart = $this->getOrderStatusChart($country->id, $startDate, $endDate);
        $topCategories = $this->getTopCategories($country->id, $startDate, $endDate);

        // Recent activities
        $recentVendors = $this->getRecentVendors($country->id, 5);
        $recentProducts = $this->getRecentProducts($country->id, 5);
        $pendingApprovals = $this->getPendingApprovals($country->id);

        return view('country.dashboard.index', compact(
            'country',
            'stats',
            'vendorGrowth',
            'productGrowth',
            'revenueChart',
            'orderStatusChart',
            'topCategories',
            'recentVendors',
            'recentProducts',
            'pendingApprovals',
            'period'
        ));
    }

    private function getStartDate($period)
    {
        return match($period) {
            'today' => now()->startOfDay(),
            'yesterday' => now()->subDay()->startOfDay(),
            'this_week' => now()->startOfWeek(),
            'last_week' => now()->subWeek()->startOfWeek(),
            'this_month' => now()->startOfMonth(),
            'last_month' => now()->subMonth()->startOfMonth(),
            'this_quarter' => now()->startOfQuarter(),
            'last_quarter' => now()->subQuarter()->startOfQuarter(),
            'this_year' => now()->startOfYear(),
            'last_year' => now()->subYear()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }

    private function getTotalVendors($countryId)
    {
        return Vendor::whereHas('businessProfile', function($q) use ($countryId) {
            $q->where('country_id', $countryId);
        })->count();
    }

    private function getActiveVendors($countryId)
    {
        return Vendor::whereHas('businessProfile', function($q) use ($countryId) {
            $q->where('country_id', $countryId);
        })->where('account_status', 'active')->count();
    }

    private function getPendingVendors($countryId)
    {
        return BusinessProfile::where('country_id', $countryId)
            ->where('verification_status', 'pending')
            ->count();
    }

    private function getTotalProducts($countryId)
    {
        return Product::where('country_id', $countryId)->count();
    }

    private function getTotalShowrooms($countryId)
    {
        return Showroom::where('country_id', $countryId)->count();
    }

    private function getTotalLoads($countryId)
    {
        return Load::where(function($q) use ($countryId) {
            $q->where('origin_country_id', $countryId)
              ->orWhere('destination_country_id', $countryId);
        })->count();
    }

    private function getTotalTransporters($countryId)
    {
        return Transporter::where('country_id', $countryId)->count();
    }

    private function getPeriodRevenue($countryId, $startDate, $endDate)
    {
        if (!class_exists('\App\Models\Order')) {
            return 0;
        }

        return \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($countryId) {
            $q->where('country_id', $countryId);
        })->whereBetween('created_at', [$startDate, $endDate])
          ->sum('total') ?? 0;
    }

    private function getPeriodOrders($countryId, $startDate, $endDate)
    {
        if (!class_exists('\App\Models\Order')) {
            return 0;
        }

        return \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($countryId) {
            $q->where('country_id', $countryId);
        })->whereBetween('created_at', [$startDate, $endDate])->count();
    }

    private function getVendorGrowthChart($countryId, $period)
    {
        $days = $this->getDaysForPeriod($period);
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $count = Vendor::whereHas('businessProfile', function($q) use ($countryId) {
                $q->where('country_id', $countryId);
            })->whereDate('created_at', $date)->count();

            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getProductGrowthChart($countryId, $period)
    {
        $days = $this->getDaysForPeriod($period);
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $count = Product::where('country_id', $countryId)
                ->whereDate('created_at', $date)
                ->count();

            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getRevenueChart($countryId, $period)
    {
        if (!class_exists('\App\Models\Order')) {
            return ['labels' => [], 'data' => []];
        }

        $days = $this->getDaysForPeriod($period);
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $revenue = \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($countryId) {
                $q->where('country_id', $countryId);
            })->whereDate('created_at', $date)->sum('total') ?? 0;

            $data[] = round($revenue, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getOrderStatusChart($countryId, $startDate, $endDate)
    {
        if (!class_exists('\App\Models\Order')) {
            return ['labels' => [], 'data' => []];
        }

        $statuses = \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($countryId) {
            $q->where('country_id', $countryId);
        })->whereBetween('created_at', [$startDate, $endDate])
          ->select('status', DB::raw('count(*) as count'))
          ->groupBy('status')
          ->get();

        return [
            'labels' => $statuses->pluck('status')->toArray(),
            'data' => $statuses->pluck('count')->toArray()
        ];
    }

    private function getTopCategories($countryId, $startDate, $endDate)
    {
        return Product::where('country_id', $countryId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('product_category_id', DB::raw('count(*) as count'))
            ->groupBy('product_category_id')
            ->with('productCategory')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->productCategory->name ?? 'Unknown',
                    'count' => $item->count
                ];
            });
    }

    private function getRecentVendors($countryId, $limit)
    {
        return Vendor::whereHas('businessProfile', function($q) use ($countryId) {
            $q->where('country_id', $countryId);
        })->with('businessProfile', 'user')
          ->latest()
          ->limit($limit)
          ->get();
    }

    private function getRecentProducts($countryId, $limit)
    {
        return Product::where('country_id', $countryId)
            ->with('user', 'productCategory')
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getPendingApprovals($countryId)
    {
        return BusinessProfile::where('country_id', $countryId)
            ->where('verification_status', 'pending')
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();
    }

    private function getDaysForPeriod($period)
    {
        return match($period) {
            'today' => 1,
            'yesterday' => 1,
            'this_week' => 7,
            'last_week' => 7,
            'this_month' => 30,
            'last_month' => 30,
            'this_quarter' => 90,
            'last_quarter' => 90,
            'this_year' => 365,
            'last_year' => 365,
            default => 30,
        };
    }
}
