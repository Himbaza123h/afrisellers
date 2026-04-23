<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;

class CountryController extends Controller
{

    /**
 * Display business profiles for a specific country.
 */
public function businessProfiles(Country $country, Request $request)
{
    // Start query
    $query = BusinessProfile::where('country_id', $country->id)
        ->where('is_admin_verified', true)
        ->with(['user']);

    // Filter by city
    if ($request->filled('city')) {
        $query->where('city', $request->city);
    }

    // Filter by search term (business name or description)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('business_name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Filter by category (through products)
    if ($request->filled('category')) {
        $query->whereHas('user.products', function($q) use ($request) {
            $q->where('product_category_id', $request->category)
              ->where('status', 'active')
              ->where('is_admin_verified', true);
        });
    }

    // Filter by rating
    if ($request->filled('rating')) {
        $minRating = (int)$request->rating;
        $query->whereHas('user.products.reviews', function($q) use ($minRating) {
            $q->where('status', true)
              ->havingRaw('AVG(mark) >= ?', [$minRating]);
        });
    }

    // Sorting
    $sortBy = $request->input('sort', 'name');
    switch ($sortBy) {
        case 'name':
            $query->orderBy('business_name', 'asc');
            break;
        case 'newest':
            $query->orderBy('created_at', 'desc');
            break;
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        default:
            $query->orderBy('business_name', 'asc');
    }

    $businessProfiles = $query->paginate(20)->withQueryString();

    // Get the first product for each business profile
    $userProducts = collect();
    foreach ($businessProfiles as $profile) {
        $firstProduct = Product::where('user_id', $profile->user_id)
            ->where('status', 'active')
            ->where('is_admin_verified', true)
            ->with(['images', 'productCategory'])
            ->first();

        if ($firstProduct) {
            $userProducts->put($profile->user_id, $firstProduct);
        }
    }

    // Get unique cities for filter
    $cities = BusinessProfile::where('country_id', $country->id)
        ->where('is_admin_verified', true)
        ->whereNotNull('city')
        ->distinct()
        ->pluck('city')
        ->sort()
        ->values();

    // Get categories that have products from suppliers in this country
    $categories = \App\Models\ProductCategory::whereHas('products', function($q) use ($country) {
        $q->whereHas('user.businessProfile', function($query) use ($country) {
            $query->where('country_id', $country->id)
                  ->where('is_admin_verified', true);
        })
        ->where('status', 'active')
        ->where('is_admin_verified', true);
    })->orderBy('name', 'asc')->get();

    return view('frontend.country.business-profiles', compact(
        'country',
        'businessProfiles',
        'userProducts',
        'cities',
        'categories'
    ));
}


/**
 * Display all products from a country grouped by supplier.
 */
public function products(Country $country, Request $request)
{
    // Get all products from this country
    $query = Product::where('country_id', $country->id)
        ->where('status', 'active')
        ->where('is_admin_verified', true)
        ->with(['images', 'user.businessProfile', 'productCategory', 'prices']);

    // Filter by search term
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Filter by category
    if ($request->filled('category')) {
        $query->where('product_category_id', $request->category);
    }

    // Filter by supplier
    if ($request->filled('supplier')) {
        $query->where('user_id', $request->supplier);
    }

    // Sorting
    $sortBy = $request->input('sort', 'latest');
    switch ($sortBy) {
        case 'latest':
            $query->orderBy('created_at', 'desc');
            break;
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        default:
            $query->orderBy('created_at', 'desc');
    }

    $products = $query->paginate(24)->withQueryString();

    // Group products by supplier for display
    $productsBySupplier = $products->getCollection()->groupBy('user_id');

    // Get all suppliers (business profiles) that have products in this country
    $suppliers = BusinessProfile::whereIn('user_id', $productsBySupplier->keys())
        ->where('country_id', $country->id)
        ->where('is_admin_verified', true)
        ->orderBy('business_name', 'asc')
        ->get();

    // Get categories that have products in this country
    $categories = ProductCategory::whereHas('products', function($q) use ($country) {
        $q->where('country_id', $country->id)
          ->where('status', 'active')
          ->where('is_admin_verified', true);
    })->orderBy('name', 'asc')->get();

    return view('frontend.country.products', compact(
        'country',
        'products',
        'productsBySupplier',
        'suppliers',
        'categories'
    ));
}


}
