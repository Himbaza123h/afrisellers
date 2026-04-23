<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Country;
use App\Models\Vendor\Vendor;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\Showroom;
use App\Models\Load;
use App\Models\Transporter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get the regional admin's region
        $user = auth()->user();
        $regionalAdmin = $user->regionalAdmin;

        if (!$regionalAdmin || !$regionalAdmin->region) {
            abort(403, 'No region assigned to this admin.');
        }

        $region = $regionalAdmin->region;

        // Get filter parameters
        $period = $request->get('period', 'this_month');
        $countryFilter = $request->get('country_id');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Get countries in region
        $countries = $region->countries()->active()->get();

        // Statistics
        $stats = [
            'total_countries' => $countries->count(),
            'total_vendors' => $this->getTotalVendors($region, $countryFilter),
            'active_vendors' => $this->getActiveVendors($region, $countryFilter),
            'pending_vendors' => $this->getPendingVendors($region, $countryFilter),
            'total_products' => $this->getTotalProducts($region, $countryFilter),
            'total_showrooms' => $this->getTotalShowrooms($region, $countryFilter),
            'total_loads' => $this->getTotalLoads($region, $countryFilter),
            'total_transporters' => $this->getTotalTransporters($region, $countryFilter),
            'period_revenue' => $this->getPeriodRevenue($region, $startDate, $endDate, $countryFilter),
            'period_orders' => $this->getPeriodOrders($region, $startDate, $endDate, $countryFilter),
        ];

        // Chart data
        $vendorGrowth = $this->getVendorGrowthChart($region, $period, $countryFilter);
        $productGrowth = $this->getProductGrowthChart($region, $period, $countryFilter);
        $revenueChart = $this->getRevenueChart($region, $period, $countryFilter);
        $orderStatusChart = $this->getOrderStatusChart($region, $startDate, $endDate, $countryFilter);
        $countryDistribution = $this->getCountryDistribution($region, $countryFilter);
        $topCategories = $this->getTopCategories($region, $startDate, $endDate, $countryFilter);

        // Country performance
        $countryPerformance = $this->getCountryPerformance($region, $startDate, $endDate);

        // Recent activities
        $recentVendors = $this->getRecentVendors($region, 5, $countryFilter);
        $recentProducts = $this->getRecentProducts($region, 5, $countryFilter);
        $pendingApprovals = $this->getPendingApprovals($region, $countryFilter);

        return view('regional.dashboard.index', compact(
            'region',
            'countries',
            'stats',
            'vendorGrowth',
            'productGrowth',
            'revenueChart',
            'orderStatusChart',
            'countryDistribution',
            'topCategories',
            'countryPerformance',
            'recentVendors',
            'recentProducts',
            'pendingApprovals',
            'period',
            'countryFilter'
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

    private function getCountryIds($region, $countryFilter = null)
    {
        if ($countryFilter) {
            return [$countryFilter];
        }
        return $region->countries()->pluck('id')->toArray();
    }

    private function getTotalVendors($region, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return Vendor::whereHas('businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        })->count();
    }

    private function getActiveVendors($region, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return Vendor::whereHas('businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        })->where('account_status', 'active')->count();
    }

    private function getPendingVendors($region, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return BusinessProfile::whereIn('country_id', $countryIds)
            ->where('verification_status', 'pending')
            ->count();
    }

    private function getTotalProducts($region, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return Product::whereIn('country_id', $countryIds)->count();
    }

    private function getTotalShowrooms($region, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return Showroom::whereIn('country_id', $countryIds)->count();
    }

    private function getTotalLoads($region, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return Load::where(function($q) use ($countryIds) {
            $q->whereIn('origin_country_id', $countryIds)
              ->orWhereIn('destination_country_id', $countryIds);
        })->count();
    }

    private function getTotalTransporters($region, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return Transporter::whereIn('country_id', $countryIds)->count();
    }

    private function getPeriodRevenue($region, $startDate, $endDate, $countryFilter = null)
    {
        if (!class_exists('\App\Models\Order')) {
            return 0;
        }

        $countryIds = $this->getCountryIds($region, $countryFilter);

        return \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        })->whereBetween('created_at', [$startDate, $endDate])
          ->sum('total') ?? 0;
    }

    private function getPeriodOrders($region, $startDate, $endDate, $countryFilter = null)
    {
        if (!class_exists('\App\Models\Order')) {
            return 0;
        }

        $countryIds = $this->getCountryIds($region, $countryFilter);

        return \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        })->whereBetween('created_at', [$startDate, $endDate])->count();
    }

    private function getVendorGrowthChart($region, $period, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);
        $days = $this->getDaysForPeriod($period);
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $count = Vendor::whereHas('businessProfile', function($q) use ($countryIds) {
                $q->whereIn('country_id', $countryIds);
            })->whereDate('created_at', $date)->count();

            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getProductGrowthChart($region, $period, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);
        $days = $this->getDaysForPeriod($period);
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $count = Product::whereIn('country_id', $countryIds)
                ->whereDate('created_at', $date)
                ->count();

            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getRevenueChart($region, $period, $countryFilter = null)
    {
        if (!class_exists('\App\Models\Order')) {
            return ['labels' => [], 'data' => []];
        }

        $countryIds = $this->getCountryIds($region, $countryFilter);
        $days = $this->getDaysForPeriod($period);
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $revenue = \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($countryIds) {
                $q->whereIn('country_id', $countryIds);
            })->whereDate('created_at', $date)->sum('total') ?? 0;

            $data[] = round($revenue, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getOrderStatusChart($region, $startDate, $endDate, $countryFilter = null)
    {
        if (!class_exists('\App\Models\Order')) {
            return ['labels' => [], 'data' => []];
        }

        $countryIds = $this->getCountryIds($region, $countryFilter);

        $statuses = \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        })->whereBetween('created_at', [$startDate, $endDate])
          ->select('status', DB::raw('count(*) as count'))
          ->groupBy('status')
          ->get();

        return [
            'labels' => $statuses->pluck('status')->toArray(),
            'data' => $statuses->pluck('count')->toArray()
        ];
    }

    private function getCountryDistribution($region, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        $distribution = Vendor::whereHas('businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        })->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
          ->join('countries', 'business_profiles.country_id', '=', 'countries.id')
          ->select('countries.name', DB::raw('count(*) as count'))
          ->groupBy('countries.name')
          ->get();

        return [
            'labels' => $distribution->pluck('name')->toArray(),
            'data' => $distribution->pluck('count')->toArray()
        ];
    }

    private function getTopCategories($region, $startDate, $endDate, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return Product::whereIn('country_id', $countryIds)
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

    private function getCountryPerformance($region, $startDate, $endDate)
    {
        return $region->countries()->get()->map(function($country) use ($startDate, $endDate) {
            $vendors = Vendor::whereHas('businessProfile', function($q) use ($country) {
                $q->where('country_id', $country->id);
            })->count();

            $products = Product::where('country_id', $country->id)->count();

            $revenue = 0;
            if (class_exists('\App\Models\Order')) {
                $revenue = \App\Models\Order::whereHas('vendor.businessProfile', function($q) use ($country) {
                    $q->where('country_id', $country->id);
                })->whereBetween('created_at', [$startDate, $endDate])
                  ->sum('total') ?? 0;
            }

            return [
                'name' => $country->name,
                'vendors' => $vendors,
                'products' => $products,
                'revenue' => $revenue
            ];
        });
    }

    private function getRecentVendors($region, $limit, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return Vendor::whereHas('businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        })->with('businessProfile.country', 'user')
          ->latest()
          ->limit($limit)
          ->get();
    }

    private function getRecentProducts($region, $limit, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return Product::whereIn('country_id', $countryIds)
            ->with('user', 'productCategory', 'country')
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getPendingApprovals($region, $countryFilter = null)
    {
        $countryIds = $this->getCountryIds($region, $countryFilter);

        return BusinessProfile::whereIn('country_id', $countryIds)
            ->where('verification_status', 'pending')
            ->with('user', 'country')
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
