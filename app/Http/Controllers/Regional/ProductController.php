<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products in the regional admin's region.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get regional admin's region
        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $query = Product::with(['user', 'productCategory', 'prices', 'images', 'country'])
            ->whereIn('country_id', $countryIds);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('productCategory', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Country filter
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Verification filter
        if ($request->filled('verification')) {
            if ($request->verification === 'verified') {
                $query->where('is_admin_verified', true);
            } elseif ($request->verification === 'unverified') {
                $query->where('is_admin_verified', false);
            }
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('product_category_id', $request->category);
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

        $products = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Product::whereIn('country_id', $countryIds)->count(),
            'active' => Product::whereIn('country_id', $countryIds)->where('status', 'active')->count(),
            'pending' => Product::whereIn('country_id', $countryIds)->where('status', 'pending')->count(),
            'verified' => Product::whereIn('country_id', $countryIds)->where('is_admin_verified', true)->count(),
            'unverified' => Product::whereIn('country_id', $countryIds)->where('is_admin_verified', false)->count(),
            'total_views' => Product::whereIn('country_id', $countryIds)->sum('views'),
        ];

        // Calculate percentages
        $stats['active_percentage'] = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;
        $stats['verified_percentage'] = $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100) : 0;

        // Get countries and categories for filters
        $countries = $region->countries()->orderBy('name')->get();
        $categories = DB::table('product_categories')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('regional.products.index', compact('products', 'stats', 'region', 'countries', 'categories'));
    }


    // In App\Http\Controllers\Regional\ProductController.php

public function print(Request $request)
{
    $region = auth()->user()->regionalAdmin->region;
    $countries = $region->countries;

    // Build query
    $query = Product::whereIn('country_id', $countries->pluck('id'))
        ->with(['user', 'country', 'productCategory', 'images', 'prices']);

    // Apply filters if any
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('country_id')) {
        $query->where('country_id', $request->country_id);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('verification')) {
        $verified = $request->verification === 'verified';
        $query->where('is_admin_verified', $verified);
    }

    if ($request->filled('category')) {
        $query->where('product_category_id', $request->category);
    }

    // Get all products (no pagination for print)
    $products = $query->get();

    // Calculate stats
    $stats = [
        'total' => $products->count(),
        'active' => $products->where('status', 'active')->count(),
        'pending' => $products->where('status', 'pending')->count(),
        'verified' => $products->where('is_admin_verified', true)->count(),
        'unverified' => $products->where('is_admin_verified', false)->count(),
        'total_views' => $products->sum('views'),
    ];

    $stats['active_percentage'] = $stats['total'] > 0
        ? round(($stats['active'] / $stats['total']) * 100)
        : 0;

    $stats['verified_percentage'] = $stats['total'] > 0
        ? round(($stats['verified'] / $stats['total']) * 100)
        : 0;

    return view('regional.products.print', compact('region', 'countries', 'products', 'stats'));
}

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Get regional admin's region
        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $product = Product::with([
            'user',
            'productCategory',
            'country',
            'prices',
            'images',
            'variations',
            'showrooms',
            'reviews.user',
            'orderItems.order'
        ])
            ->whereIn('country_id', $countryIds)
            ->findOrFail($id);

        // Get order statistics for this product
        $orderStats = [
            'total_orders' => $product->orderItems()->count(),
            'total_quantity_sold' => $product->orderItems()->sum('quantity'),
            'total_revenue' => $product->orderItems()->sum('subtotal'),
        ];

        return view('regional.products.show', compact('product', 'orderStats'));
    }

    /**
     * Approve a product.
     */
    public function approve($id)
    {
        $user = Auth::user();

        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $product = Product::whereIn('country_id', $countryIds)->findOrFail($id);

        $product->update([
            'is_admin_verified' => true,
            'status' => 'active'
        ]);

        return redirect()->back()->with('success', 'Product approved successfully.');
    }

    /**
     * Reject a product.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $product = Product::whereIn('country_id', $countryIds)->findOrFail($id);

        $product->update([
            'is_admin_verified' => false,
            'status' => 'inactive'
        ]);

        return redirect()->back()->with('success', 'Product rejected successfully.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $product = Product::whereIn('country_id', $countryIds)->findOrFail($id);

        $product->delete();

        return redirect()->route('regional.products.index')->with('success', 'Product deleted successfully.');
    }
}
