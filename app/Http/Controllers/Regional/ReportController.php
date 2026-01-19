<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor\Vendor;
use App\Models\Product;
use App\Models\Showroom;
use App\Models\Order;
use App\Models\Load;
use App\Models\Transporter;
use App\Models\Country;
use App\Exports\RegionalReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard for regional admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get regional admin's region
        if (!$user->regionalAdmin) {
            abort(403, 'You are not authorized to access this page.');
        }

        $regionalAdmin = $user->regionalAdmin;
        $region = $regionalAdmin->region;

        // Get date range
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $countryFilter = $request->get('country_id');

        // Get all countries in the region
        $countries = $region->countries;
        $countryIds = $countries->pluck('id')->toArray();

        // Apply country filter if specified
        if ($countryFilter) {
            $countryIds = [$countryFilter];
        }

        // Vendors Statistics
        $vendorsQuery = Vendor::whereHas('businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        });

        $vendorsStats = [
            'total' => (clone $vendorsQuery)->count(),
            'verified' => (clone $vendorsQuery)->where('account_status', 'verified')->count(),
            'pending' => (clone $vendorsQuery)->where('account_status', 'pending')->count(),
            'active' => (clone $vendorsQuery)->where('account_status', 'active')->count(),
            'suspended' => (clone $vendorsQuery)->where('account_status', 'suspended')->count(),
            'new_this_month' => (clone $vendorsQuery)->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Products Statistics
        $productsQuery = Product::whereIn('country_id', $countryIds);

        $productsStats = [
            'total' => (clone $productsQuery)->count(),
            'approved' => (clone $productsQuery)->where('is_admin_verified', true)->count(),
            'pending' => (clone $productsQuery)->where('is_admin_verified', false)->count(),
            'new_this_month' => (clone $productsQuery)->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Showrooms Statistics
        $showroomsQuery = Showroom::whereIn('country_id', $countryIds);

        $showroomsStats = [
            'total' => (clone $showroomsQuery)->count(),
            'verified' => (clone $showroomsQuery)->where('is_verified', true)->count(),
            'active' => (clone $showroomsQuery)->where('status', 'active')->count(),
            'featured' => (clone $showroomsQuery)->where('is_featured', true)->count(),
        ];

        // Orders Statistics
        $ordersQuery = Order::whereHas('vendor.businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        });

        $ordersStats = [
            'total' => (clone $ordersQuery)->count(),
            'pending' => (clone $ordersQuery)->where('status', 'pending')->count(),
            'processing' => (clone $ordersQuery)->where('status', 'processing')->count(),
            'shipped' => (clone $ordersQuery)->where('status', 'shipped')->count(),
            'delivered' => (clone $ordersQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $ordersQuery)->where('status', 'cancelled')->count(),
            'total_value' => (clone $ordersQuery)->sum('total'),
            'period_orders' => (clone $ordersQuery)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'period_value' => (clone $ordersQuery)->whereBetween('created_at', [$startDate, $endDate])->sum('total'),
        ];

        // Loads/Transportation Statistics
        $loadsQuery = Load::whereIn('origin_country_id', $countryIds)
            ->orWhereIn('destination_country_id', $countryIds);

        $loadsStats = [
            'total' => (clone $loadsQuery)->count(),
            'posted' => (clone $loadsQuery)->where('status', 'posted')->count(),
            'assigned' => (clone $loadsQuery)->where('status', 'assigned')->count(),
            'in_transit' => (clone $loadsQuery)->where('status', 'in_transit')->count(),
            'delivered' => (clone $loadsQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $loadsQuery)->where('status', 'cancelled')->count(),
            'period_loads' => (clone $loadsQuery)->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Transporters Statistics
        $transportersQuery = Transporter::whereIn('country_id', $countryIds);

        $transportersStats = [
            'total' => (clone $transportersQuery)->count(),
            'verified' => (clone $transportersQuery)->where('is_verified', true)->count(),
            'active' => (clone $transportersQuery)->where('status', 'active')->count(),
            'total_fleet' => (clone $transportersQuery)->sum('fleet_size'),
        ];

        // Revenue by Country
        $revenueByCountry = Order::select('countries.name as country_name', DB::raw('SUM(orders.total) as total_revenue'))
            ->join('vendors', 'orders.vendor_id', '=', 'vendors.user_id')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->join('countries', 'business_profiles.country_id', '=', 'countries.id')
            ->whereIn('countries.id', $countryIds)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('countries.id', 'countries.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Monthly trends
        $monthlyOrders = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereHas('vendor.businessProfile', function($q) use ($countryIds) {
                $q->whereIn('country_id', $countryIds);
            })
            ->whereBetween('created_at', [Carbon::now()->subMonths(6), Carbon::now()])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('regional.reports.index', compact(
            'region',
            'vendorsStats',
            'productsStats',
            'showroomsStats',
            'ordersStats',
            'loadsStats',
            'transportersStats',
            'revenueByCountry',
            'monthlyOrders',
            'countries',
            'startDate',
            'endDate',
            'countryFilter'
        ));
    }

    /**
     * Export report data
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        if (!$user->regionalAdmin) {
            abort(403, 'You are not authorized to access this page.');
        }

        $regionalAdmin = $user->regionalAdmin;
        $region = $regionalAdmin->region;

        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $countryFilter = $request->get('country_id');
        $format = $request->get('format', 'xlsx'); // xlsx, csv, pdf

        $fileName = 'regional_report_' . Carbon::now()->format('Y-m-d_His') . '.' . $format;

        return Excel::download(
            new RegionalReportExport($region->id, $startDate, $endDate, $countryFilter),
            $fileName
        );
    }
}
