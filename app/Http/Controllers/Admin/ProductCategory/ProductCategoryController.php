<?php

namespace App\Http\Controllers\Admin\ProductCategory;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the product categories.
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total' => ProductCategory::count(),
            'active' => ProductCategory::where('status', 'active')->count(),
            'inactive' => ProductCategory::where('status', 'inactive')->count(),
            'with_products' => ProductCategory::has('products')->count(),
            'main_categories' => ProductCategory::whereNull('parent_id')->count(),
            'sub_categories' => ProductCategory::whereNotNull('parent_id')->count(),
        ];

        // Calculate percentages
        $stats['active_percentage'] = $stats['total'] > 0
            ? round(($stats['active'] / $stats['total']) * 100, 1)
            : 0;
        $stats['inactive_percentage'] = $stats['total'] > 0
            ? round(($stats['inactive'] / $stats['total']) * 100, 1)
            : 0;
        $stats['sub_category_percentage'] = $stats['total'] > 0
            ? round(($stats['sub_categories'] / $stats['total']) * 100, 1)
            : 0;

        // Build query with filters
        $query = ProductCategory::with(['countries', 'products', 'parent', 'children']);

        // Search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Country filter
        if (request('country_id')) {
            $query->whereHas('countries', function($q) {
                $q->where('countries.id', request('country_id'));
            });
        }

        // Category type filter
        if (request('type')) {
            if (request('type') === 'main') {
                $query->whereNull('parent_id');
            } elseif (request('type') === 'sub') {
                $query->whereNotNull('parent_id');
            }
        }

        // Sorting
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $categories = $query->paginate(15)->withQueryString();

        // Get all countries for filter dropdown
        $countries = Country::where('status', 'active')->orderBy('name')->get();

        return view('admin.product-category.index', compact('categories', 'stats', 'countries'));
    }

    /**
     * Print report
     */
    public function print()
    {
        $categories = ProductCategory::with(['countries', 'products', 'parent', 'children'])->orderBy('parent_id')->orderBy('name')->get();

        // Get statistics
        $stats = [
            'total' => ProductCategory::count(),
            'active' => ProductCategory::where('status', 'active')->count(),
            'inactive' => ProductCategory::where('status', 'inactive')->count(),
            'with_products' => ProductCategory::has('products')->count(),
            'main_categories' => ProductCategory::whereNull('parent_id')->count(),
            'sub_categories' => ProductCategory::whereNotNull('parent_id')->count(),
            'active_percentage' => ProductCategory::count() > 0 ? round((ProductCategory::where('status', 'active')->count() / ProductCategory::count()) * 100, 1) : 0,
            'sub_category_percentage' => ProductCategory::count() > 0 ? round((ProductCategory::whereNotNull('parent_id')->count() / ProductCategory::count()) * 100, 1) : 0,
        ];

        return view('admin.product-category.print', compact('categories', 'stats'));
    }

    /**
     * Show the form for creating a new product category.
     */
    public function create()
    {
        $countries = Country::where('status', 'active')
            ->orderBy('name')
            ->get();

        $parentCategories = ProductCategory::where('status', 'active')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.product-category.create', compact('countries', 'parentCategories'));
    }

    /**
     * Store a newly created product category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:product_categories,id',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Category name is required.',
            'parent_id.exists' => 'Selected parent category does not exist.',
            'country_ids.*.exists' => 'One or more selected countries do not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
        ]);

        try {
            $categoryData = $request->only(['name', 'description', 'status', 'parent_id']);
            $category = ProductCategory::create($categoryData);

            // Sync countries (many-to-many)
            if ($request->has('country_ids')) {
                $category->countries()->sync($request->country_ids);
            } else {
                $category->countries()->sync([]);
            }

            Log::info('Product category created successfully', [
                'category_id' => $category->id,
                'name' => $category->name,
                'parent_id' => $category->parent_id,
                'countries' => $request->country_ids ?? [],
                'created_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.product-category.index')
                ->with('success', 'Product category created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create product category', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create product category. Please try again.']);
        }
    }

    /**
     * Display the specified product category.
     */
    public function show(ProductCategory $productCategory)
    {
        $productCategory->load(['countries', 'parent', 'children']);
        return view('admin.product-category.show', compact('productCategory'));
    }

    /**
     * Show the form for editing the specified product category.
     */
    public function edit(ProductCategory $productCategory)
    {
        $productCategory->load('countries');
        $countries = Country::where('status', 'active')
            ->orderBy('name')
            ->get();

        $parentCategories = ProductCategory::where('status', 'active')
            ->whereNull('parent_id')
            ->where('id', '!=', $productCategory->id)
            ->orderBy('name')
            ->get();

        return view('admin.product-category.edit', compact('productCategory', 'countries', 'parentCategories'));
    }

    /**
     * Update the specified product category in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        // Prevent setting a category as its own parent
        $request->validate([
            'parent_id' => 'nullable|exists:product_categories,id|not_in:' . $productCategory->id,
        ], [
            'parent_id.not_in' => 'A category cannot be its own parent.',
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:product_categories,id',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Category name is required.',
            'parent_id.exists' => 'Selected parent category does not exist.',
            'country_ids.*.exists' => 'One or more selected countries do not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
        ]);

        try {
            $categoryData = $request->only(['name', 'description', 'status', 'parent_id']);
            $productCategory->update($categoryData);

            // Sync countries (many-to-many)
            if ($request->has('country_ids')) {
                $productCategory->countries()->sync($request->country_ids);
            } else {
                $productCategory->countries()->sync([]);
            }

            Log::info('Product category updated successfully', [
                'category_id' => $productCategory->id,
                'name' => $productCategory->name,
                'parent_id' => $productCategory->parent_id,
                'countries' => $request->country_ids ?? [],
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.product-category.index')
                ->with('success', 'Product category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update product category', [
                'category_id' => $productCategory->id,
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update product category. Please try again.']);
        }
    }

    /**
     * Remove the specified product category from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        try {
            // Check if category has subcategories
            if ($productCategory->children()->exists()) {
                return back()
                    ->withErrors(['error' => 'Cannot delete category because it has subcategories. Please delete or reassign the subcategories first.']);
            }

            $categoryName = $productCategory->name;
            $productCategory->delete();

            Log::info('Product category deleted successfully', [
                'category_id' => $productCategory->id,
                'name' => $categoryName,
                'deleted_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.product-category.index')
                ->with('success', 'Product category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete product category', [
                'category_id' => $productCategory->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to delete product category. Please try again.']);
        }
    }

    /**
     * Toggle product category status (active/inactive).
     */
    public function toggleStatus(ProductCategory $productCategory)
    {
        try {
            $productCategory->status = $productCategory->status === 'active' ? 'inactive' : 'active';
            $productCategory->save();

            Log::info('Product category status toggled', [
                'category_id' => $productCategory->id,
                'new_status' => $productCategory->status,
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.product-category.index')
                ->with('success', 'Product category status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to toggle product category status', [
                'category_id' => $productCategory->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to update product category status. Please try again.']);
        }
    }
}
