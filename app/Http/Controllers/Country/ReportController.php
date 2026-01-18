<?php

namespace App\Http\Controllers\Country;

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
use App\Exports\CountryReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard for country admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $countryId = $user->country_id;

        // Get date range
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Vendors Statistics
        $vendorsQuery = Vendor::whereHas('businessProfile', function($q) use ($countryId) {
            $q->where('country_id', $countryId);
        });

        $vendorsStats = [
            'total' => (clone $vendorsQuery)->count(),
            'verified' => (clone $vendorsQuery)->where('account_status', 'verified')->count(),
            'pending' => (clone $vendorsQuery)->where('account_status', 'pending')->count(),
            'rejected' => (clone $vendorsQuery)->where('account_status', 'rejected')->count(),
            'active' => (clone $vendorsQuery)->where('account_status', 'active')->count(),
            'suspended' => (clone $vendorsQuery)->where('account_status', 'suspended')->count(),
            'inactive' => (clone $vendorsQuery)->where('account_status', 'inactive')->count(),
            'new_this_period' => (clone $vendorsQuery)->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Products Statistics
        $productsQuery = Product::where('country_id', $countryId);

        $productsStats = [
            'total' => (clone $productsQuery)->count(),
            'approved' => (clone $productsQuery)->where('is_admin_verified', true)->count(),
            'pending' => (clone $productsQuery)->where('is_admin_verified', false)->count(),
            'active' => (clone $productsQuery)->where('status', 'active')->count(),
            'inactive' => (clone $productsQuery)->where('status', 'inactive')->count(),
            'new_this_period' => (clone $productsQuery)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_views' => (clone $productsQuery)->sum('views'),
        ];

        // Showrooms Statistics
        $showroomsQuery = Showroom::where('country_id', $countryId);

        $showroomsStats = [
            'total' => (clone $showroomsQuery)->count(),
            'verified' => (clone $showroomsQuery)->where('is_verified', true)->count(),
            'active' => (clone $showroomsQuery)->where('status', 'active')->count(),
            'featured' => (clone $showroomsQuery)->where('is_featured', true)->count(),
            'authorized_dealers' => (clone $showroomsQuery)->where('is_authorized_dealer', true)->count(),
            'total_views' => (clone $showroomsQuery)->sum('views_count'),
            'total_inquiries' => (clone $showroomsQuery)->sum('inquiries_count'),
        ];

        // Orders Statistics
        $ordersQuery = Order::whereHas('vendor.businessProfile', function($q) use ($countryId) {
            $q->where('country_id', $countryId);
        });

        $ordersStats = [
            'total' => (clone $ordersQuery)->count(),
            'pending' => (clone $ordersQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $ordersQuery)->where('status', 'confirmed')->count(),
            'processing' => (clone $ordersQuery)->where('status', 'processing')->count(),
            'shipped' => (clone $ordersQuery)->where('status', 'shipped')->count(),
            'delivered' => (clone $ordersQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $ordersQuery)->where('status', 'cancelled')->count(),
            'total_value' => (clone $ordersQuery)->sum('total'),
            'period_orders' => (clone $ordersQuery)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'period_value' => (clone $ordersQuery)->whereBetween('created_at', [$startDate, $endDate])->sum('total'),
            'average_order_value' => (clone $ordersQuery)->avg('total'),
        ];

        // Loads/Transportation Statistics
        $loadsQuery = Load::where('origin_country_id', $countryId)
            ->orWhere('destination_country_id', $countryId);

        $loadsStats = [
            'total' => (clone $loadsQuery)->count(),
            'posted' => (clone $loadsQuery)->where('status', 'posted')->count(),
            'bidding' => (clone $loadsQuery)->where('status', 'bidding')->count(),
            'assigned' => (clone $loadsQuery)->where('status', 'assigned')->count(),
            'in_transit' => (clone $loadsQuery)->where('status', 'in_transit')->count(),
            'delivered' => (clone $loadsQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $loadsQuery)->where('status', 'cancelled')->count(),
            'period_loads' => (clone $loadsQuery)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_weight' => (clone $loadsQuery)->sum('weight'),
        ];

        // Transporters Statistics
        $transportersQuery = Transporter::where('country_id', $countryId);

        $transportersStats = [
            'total' => (clone $transportersQuery)->count(),
            'verified' => (clone $transportersQuery)->where('is_verified', true)->count(),
            'unverified' => (clone $transportersQuery)->where('is_verified', false)->count(),
            'active' => (clone $transportersQuery)->where('status', 'active')->count(),
            'inactive' => (clone $transportersQuery)->where('status', 'inactive')->count(),
            'suspended' => (clone $transportersQuery)->where('status', 'suspended')->count(),
            'total_fleet' => (clone $transportersQuery)->sum('fleet_size'),
            'average_rating' => (clone $transportersQuery)->avg('average_rating'),
            'total_deliveries' => (clone $transportersQuery)->sum('total_deliveries'),
        ];

        // Top Vendors by Revenue
        $topVendors = Order::select(
                'users.name',
                'business_profiles.business_name',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_revenue')
            )
            ->join('vendors', 'orders.vendor_id', '=', 'vendors.user_id')
            ->join('users', 'vendors.user_id', '=', 'users.id')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->where('business_profiles.country_id', $countryId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('vendors.id', 'users.name', 'business_profiles.business_name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Top Products by Views
        $topProducts = Product::select('products.name', 'products.views', 'product_categories.name as category')
            ->join('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->where('products.country_id', $countryId)
            ->orderBy('products.views', 'desc')
            ->limit(10)
            ->get();

        // Monthly trends
        $monthlyOrders = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereHas('vendor.businessProfile', function($q) use ($countryId) {
                $q->where('country_id', $countryId);
            })
            ->whereBetween('created_at', [Carbon::now()->subMonths(6), Carbon::now()])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Daily activity in period
        $dailyActivity = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total) as daily_revenue')
            )
            ->whereHas('vendor.businessProfile', function($q) use ($countryId) {
                $q->where('country_id', $countryId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('country.reports.index', compact(
            'vendorsStats',
            'productsStats',
            'showroomsStats',
            'ordersStats',
            'loadsStats',
            'transportersStats',
            'topVendors',
            'topProducts',
            'monthlyOrders',
            'dailyActivity',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export report data
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $format = $request->get('format', 'xlsx'); // xlsx, csv, pdf

        $fileName = 'country_report_' . $user->country->code . '_' . Carbon::now()->format('Y-m-d_His') . '.' . $format;

        return Excel::download(
            new CountryReportExport($user->country_id, $startDate, $endDate),
            $fileName
        );
    }
}
