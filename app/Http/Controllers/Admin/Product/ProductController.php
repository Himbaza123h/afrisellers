<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
public function index()
{
    $user = auth()->user();

    // Get statistics
    $statsQuery = Product::query();

    // If vendor (non-admin), only show their products
    if ($user->isVendor() && !$user->hasRole('admin')) {
        $statsQuery->where('user_id', $user->id);
    }

    $stats = [
        'total' => $statsQuery->count(),
        'active' => (clone $statsQuery)->where('status', 'active')->count(),
        'inactive' => (clone $statsQuery)->where('status', 'inactive')->count(),
        'draft' => (clone $statsQuery)->where('status', 'draft')->count(),
    ];

    if ($user->hasRole('admin')) {
        $stats['verified'] = (clone $statsQuery)->where('is_admin_verified', true)->count();
        $stats['unverified'] = (clone $statsQuery)->where('is_admin_verified', false)->count();
    }

    // Calculate percentages
    $stats['active_percentage'] = $stats['total'] > 0
        ? round(($stats['active'] / $stats['total']) * 100, 1)
        : 0;
    $stats['inactive_percentage'] = $stats['total'] > 0
        ? round(($stats['inactive'] / $stats['total']) * 100, 1)
        : 0;

    // Build query with filters
    $query = Product::with(['user.vendor.businessProfile', 'productCategory', 'country', 'images', 'prices']);

    // If vendor (non-admin), only show their products
    if ($user->isVendor() && !$user->hasRole('admin')) {
        $query->where('user_id', $user->id);
    }

    // Search filter
    if (request('search')) {
        $search = request('search');
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    // Status filter
    if (request('status')) {
        $query->where('status', request('status'));
    }

    // Category filter
    if (request('category_id')) {
        $query->where('product_category_id', request('category_id'));
    }

    // Verification filter (admin only)
    if ($user->hasRole('admin') && request('verified') !== null) {
        $query->where('is_admin_verified', request('verified') === 'yes');
    }

    // Sorting
    $sortBy = request('sort_by', 'created_at');
    $sortOrder = request('sort_order', 'desc');
    $query->orderBy($sortBy, $sortOrder);

    $products = $query->paginate(15)->withQueryString();

    // Get categories for filter dropdown
    $categories = ProductCategory::where('status', 'active')->orderBy('name')->get();

    return view('admin.product.index', compact('products', 'stats', 'categories'));
}

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['user', 'productCategory', 'country',  'variations', 'images']);

        // Load vendor relationship through user if exists
        if ($product->user) {
            $product->user->load('vendor');
        }

        return view('admin.product.show', compact('product'));
    }

    /**
     * Toggle admin verification status.
     */
    public function toggleVerification(Product $product)
    {
        try {
            $product->is_admin_verified = !$product->is_admin_verified;
            $product->save();

            Log::info('Product verification toggled', [
                'product_id' => $product->id,
                'is_verified' => $product->is_admin_verified,
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.product.index')
                ->with('success', 'Product verification status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to toggle product verification', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to update verification status. Please try again.']);
        }
    }
}
