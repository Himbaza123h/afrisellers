<?php

namespace App\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products in the country admin's country.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $query = Product::with(['user', 'productCategory', 'prices', 'images'])
            ->where('country_id', $country->id);

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
            'total' => Product::where('country_id', $country->id)->count(),
            'active' => Product::where('country_id', $country->id)->where('status', 'active')->count(),
            'pending' => Product::where('country_id', $country->id)->where('status', 'pending')->count(),
            'verified' => Product::where('country_id', $country->id)->where('is_admin_verified', true)->count(),
            'unverified' => Product::where('country_id', $country->id)->where('is_admin_verified', false)->count(),
            'total_views' => Product::where('country_id', $country->id)->sum('views'),
        ];

        // Calculate percentages
        $stats['active_percentage'] = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;
        $stats['verified_percentage'] = $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100) : 0;

        // Get categories for filter
        $categories = DB::table('product_categories')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('country.products.index', compact('products', 'stats', 'country', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $product = Product::with([
            'user',
            'productCategory',
            'prices',
            'images',
            'variations',
            'showrooms',
            'reviews.user',
            'orderItems.order'
        ])
            ->where('country_id', $country->id)
            ->findOrFail($id);

        // Get order statistics for this product
        $orderStats = [
            'total_orders' => $product->orderItems()->count(),
            'total_quantity_sold' => $product->orderItems()->sum('quantity'),
            'total_revenue' => $product->orderItems()->sum('subtotal'),
        ];

        return view('country.products.show', compact('product', 'orderStats'));
    }

    /**
     * Approve a product.
     */
    public function approve($id)
    {
        $user = Auth::user();

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $product = Product::where('country_id', $country->id)->findOrFail($id);

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

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $product = Product::where('country_id', $country->id)->findOrFail($id);

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

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $product = Product::where('country_id', $country->id)->findOrFail($id);

        $product->delete();

        return redirect()->route('country.products.index')->with('success', 'Product deleted successfully.');
    }
}
