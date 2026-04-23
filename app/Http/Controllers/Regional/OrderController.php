<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders in the regional admin's region.
     */
    public function index(Request $request)
    {
        // Get regional admin's region
        $regionalAdmin = Auth::user()->regionalAdmin;

        if (!$regionalAdmin) {
            abort(403, 'Regional admin profile not found.');
        }

        $region = $regionalAdmin->region;

        if (!$region) {
            abort(403, 'Region not found.');
        }

        // Get country IDs in this region
        $countryIds = $region->countries()->pluck('id');

        // Get vendor IDs from these countries
        $vendorIds = DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->whereIn('business_profiles.country_id', $countryIds)
            ->whereNull('vendors.deleted_at')
            ->whereNull('business_profiles.deleted_at')
            ->pluck('vendors.user_id');

        $query = Order::with(['buyer', 'vendor', 'items.product', 'shippingAddress'])
            ->whereIn('vendor_id', $vendorIds);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vendor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Country filter
        if ($request->filled('country_id')) {
            $countryVendorIds = DB::table('vendors')
                ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
                ->where('business_profiles.country_id', $request->country_id)
                ->whereNull('vendors.deleted_at')
                ->whereNull('business_profiles.deleted_at')
                ->pluck('vendors.user_id');

            $query->whereIn('vendor_id', $countryVendorIds);
        }

        // Date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('created_at', '>=', $request->date_from)
                  ->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Order::whereIn('vendor_id', $vendorIds)->count(),
            'pending' => Order::whereIn('vendor_id', $vendorIds)->where('status', 'pending')->count(),
            'processing' => Order::whereIn('vendor_id', $vendorIds)->where('status', 'processing')->count(),
            'completed' => Order::whereIn('vendor_id', $vendorIds)->where('status', 'delivered')->count(),
            'total_revenue' => Order::whereIn('vendor_id', $vendorIds)
                ->whereIn('status', ['delivered', 'shipped'])
                ->sum('total'),
            'cancelled' => Order::whereIn('vendor_id', $vendorIds)->where('status', 'cancelled')->count(),
            'avg_order_value' => Order::whereIn('vendor_id', $vendorIds)
                ->whereIn('status', ['delivered', 'shipped'])
                ->avg('total'),
        ];

        // Calculate percentages
        $stats['pending_percentage'] = $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100) : 0;
        $stats['processing_percentage'] = $stats['total'] > 0 ? round(($stats['processing'] / $stats['total']) * 100) : 0;
        $stats['completed_percentage'] = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0;

        // Get countries in this region for filter
        $countries = $region->countries()->where('status', 'active')->get();

        return view('regional.orders.index', compact('orders', 'stats', 'region', 'countries'));
    }

    /**
     * Print orders report
     */
    public function print(Request $request)
    {
        $user = Auth::user();
        $regionalAdmin = $user->regionalAdmin;

        if (!$regionalAdmin) {
            abort(403, 'Regional admin profile not found.');
        }

        $region = $regionalAdmin->region;

        if (!$region) {
            abort(403, 'Region not found.');
        }

        // Get country IDs in this region
        $countryIds = $region->countries()->pluck('id');

        // Get vendor IDs from these countries
        $vendorIds = DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->whereIn('business_profiles.country_id', $countryIds)
            ->whereNull('vendors.deleted_at')
            ->whereNull('business_profiles.deleted_at')
            ->pluck('vendors.user_id');

        // Build query
        $query = Order::with(['buyer', 'vendor.businessProfile.country', 'items'])
            ->whereIn('vendor_id', $vendorIds);

        // Apply filters if any
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vendor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('country_id')) {
            $countryVendorIds = DB::table('vendors')
                ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
                ->where('business_profiles.country_id', $request->country_id)
                ->whereNull('vendors.deleted_at')
                ->whereNull('business_profiles.deleted_at')
                ->pluck('vendors.user_id');

            $query->whereIn('vendor_id', $countryVendorIds);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('created_at', '>=', $request->date_from)
                  ->whereDate('created_at', '<=', $request->date_to);
        }

        // Get all orders (no pagination for print)
        $orders = $query->get();

        // Calculate stats
        $stats = [
            'total' => $orders->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'processing' => $orders->where('status', 'processing')->count(),
            'completed' => $orders->where('status', 'delivered')->count(),
            'total_revenue' => $orders->whereIn('status', ['delivered', 'shipped'])->sum('total'),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'avg_order_value' => $orders->whereIn('status', ['delivered', 'shipped'])->avg('total') ?? 0,
        ];

        $stats['pending_percentage'] = $stats['total'] > 0
            ? round(($stats['pending'] / $stats['total']) * 100)
            : 0;

        $stats['processing_percentage'] = $stats['total'] > 0
            ? round(($stats['processing'] / $stats['total']) * 100)
            : 0;

        $stats['completed_percentage'] = $stats['total'] > 0
            ? round(($stats['completed'] / $stats['total']) * 100)
            : 0;

        $countries = $region->countries;

        return view('regional.orders.print', compact('region', 'countries', 'orders', 'stats'));
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        // Get regional admin's region
        $regionalAdmin = Auth::user()->regionalAdmin;

        if (!$regionalAdmin) {
            abort(403, 'Regional admin profile not found.');
        }

        $region = $regionalAdmin->region;

        if (!$region) {
            abort(403, 'Region not found.');
        }

        // Get country IDs in this region
        $countryIds = $region->countries()->pluck('id');

        // Get vendor IDs from these countries
        $vendorIds = DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->whereIn('business_profiles.country_id', $countryIds)
            ->whereNull('vendors.deleted_at')
            ->whereNull('business_profiles.deleted_at')
            ->pluck('vendors.user_id');

        $order = Order::with(['buyer', 'vendor', 'items.product', 'shippingAddress', 'billingAddress'])
            ->whereIn('vendor_id', $vendorIds)
            ->findOrFail($id);

        return view('regional.orders.show', compact('order'));
    }
}
