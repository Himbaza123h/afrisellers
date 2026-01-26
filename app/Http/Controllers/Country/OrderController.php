<?php

namespace App\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders in the country admin's country.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        // Get vendor IDs from this country
        $vendorIds = DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->where('business_profiles.country_id', $country->id)
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

        // Date range filter
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) === 2) {
                $query->whereDate('created_at', '>=', $dates[0])
                      ->whereDate('created_at', '<=', $dates[1]);
            }
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

        return view('country.orders.index', compact('orders', 'stats', 'country'));
    }

    /**
 * Print orders report for the country.
 */
public function print(Request $request)
{
    $user = Auth::user();

    // Get country admin's country
    if (!$user->country_admin || !$user->country_id) {
        abort(403, 'You are not assigned to any country.');
    }

    $country = Country::findOrFail($user->country_id);

    // Get vendor IDs from this country
    $vendorIds = DB::table('vendors')
        ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
        ->where('business_profiles.country_id', $country->id)
        ->whereNull('vendors.deleted_at')
        ->whereNull('business_profiles.deleted_at')
        ->pluck('vendors.user_id');

    $query = Order::with(['buyer', 'vendor', 'items.product', 'shippingAddress'])
        ->whereIn('vendor_id', $vendorIds);

    // Apply same filters as index
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

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('payment_status')) {
        $query->where('payment_status', $request->payment_status);
    }

    if ($request->filled('date_range')) {
        $dates = explode(' to ', $request->date_range);
        if (count($dates) === 2) {
            $query->whereDate('created_at', '>=', $dates[0])
                  ->whereDate('created_at', '<=', $dates[1]);
        }
    }

    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');
    $query->orderBy($sortBy, $sortOrder);

    $orders = $query->get();

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
            ->avg('total') ?? 0,
    ];

    $stats['pending_percentage'] = $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100) : 0;
    $stats['processing_percentage'] = $stats['total'] > 0 ? round(($stats['processing'] / $stats['total']) * 100) : 0;
    $stats['completed_percentage'] = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0;

    return view('country.orders.print', compact('orders', 'stats', 'country'));
}

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        // Get vendor IDs from this country
        $vendorIds = DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->where('business_profiles.country_id', $country->id)
            ->whereNull('vendors.deleted_at')
            ->whereNull('business_profiles.deleted_at')
            ->pluck('vendors.user_id');

        $order = Order::with(['buyer', 'vendor', 'items.product', 'shippingAddress', 'billingAddress'])
            ->whereIn('vendor_id', $vendorIds)
            ->findOrFail($id);

        return view('country.orders.show', compact('order'));
    }
}
