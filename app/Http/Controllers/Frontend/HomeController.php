<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Country;
use App\Models\ProductCategory;
use App\Models\Load;
use App\Models\Car;
use App\Models\Performance;
use App\Models\Product;
use App\Models\RFQs;
use App\Models\Setting;
use App\Models\Showroom;
use App\Models\Tradeshow;
use App\Models\GlobalSearchIndex;
use App\Models\RecentSearch;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vendor\Vendor;

class HomeController extends Controller
{

    public function index()
{
    // Get active countries for regional showcase
    $countries = Country::where('status', 'active')
        ->orderBy('name')
        ->limit(7)
        ->get();

    // Get active categories with product counts
    $categories = ProductCategory::where('status', 'active')
        ->whereHas('products', function($query) {
            $query->where('status', 'active')
                  ->where('is_admin_verified', true);
        })
        ->withCount(['products' => function($query) {
            $query->where('status', 'active')
                  ->where('is_admin_verified', true);
        }])
        ->orderBy('name')
        ->limit(16)
        ->get();

    // Get new arrival products (last 2 days)
    $newArrivalProducts = Product::where('status', 'active')
        ->where('is_admin_verified', true)
        ->where('created_at', '>=', now()->subDays(2))
        ->with(['images', 'productCategory', 'user'])
        ->latest()
        ->limit(10)
        ->get();

    // Get new arrival companies (vendors who added products in last 2 days)
    $newArrivalCompanies = User::whereHas('products', function($query) {
            $query->where('status', 'active')
                ->where('is_admin_verified', true)
                ->where('created_at', '>=', now()->subDays(2));
        })
        ->with(['vendor.businessProfile', 'products' => function($query) {
            $query->where('status', 'active')
                ->where('is_admin_verified', true)
                ->where('created_at', '>=', now()->subDays(2))
                ->with('images')
                ->latest()
                ->limit(1);
        }])
        ->withCount(['products as new_products_count' => function($query) {
            $query->where('status', 'active')
                ->where('is_admin_verified', true)
                ->where('created_at', '>=', now()->subDays(2));
        }])
        ->orderBy('new_products_count', 'desc')
        ->limit(10)
        ->get();

    // Get featured businesses for hero slider (3 businesses with one product each)
   $featuredBusinesses = $this->getFeaturedBusinessesWithProducts(3, true);


    // Get available loads (status = 'posted')
    $availableLoads = Load::where('status', 'posted')
        ->with(['originCountry', 'destinationCountry'])
        ->latest()
        ->limit(8)
        ->get();

    // Get available cars (vehicles for hire)
    $availableCars = Car::where('availability_status', 'available')
        ->where('is_verified', true)
        ->with(['fromCountry', 'toCountry', 'user'])
        ->select([
            'id', 'listing_number', 'user_id', 'make', 'model', 'year',
            'vehicle_type', 'cargo_capacity', 'cargo_capacity_unit',
            'from_city', 'from_country_id', 'to_city', 'to_country_id',
            'flexible_destination', 'price', 'pricing_type', 'currency',
            'price_negotiable', 'mileage', 'images', 'driver_included',
            'rating', 'completed_trips'
        ])
        ->orderByDesc('is_featured')
        ->orderByDesc('rating')
        ->limit(8)
        ->get();

    // Get upcoming tradeshows (next 6 months)
    $upcomingTradeshows = Tradeshow::where('status', 'published')
        ->where('start_date', '>=', now())
        ->where('start_date', '<=', now()->addMonths(6))
        ->with(['country', 'user'])
        ->orderBy('start_date')
        ->limit(3)
        ->get();

    // Get featured showrooms
    $featuredShowrooms = Showroom::where('status', 'active')
        ->where('is_featured', true)
        ->where('is_verified', true)
        ->with(['country', 'user'])
        ->orderByDesc('rating')
        ->limit(4)
        ->get();

    // Check if user can see top RFQs (default: check if authenticated)
    // TODO: Later change this to check for specific permissions/subscription
    $canSeeTopRfqs = auth()->check();

        // Fetch top RFQs from database
        $topRFQs = RFQs::with(['user', 'product.productCategory', 'country'])
            ->withCount('messages')
            ->latest()
            ->limit(3)
            ->get();
        // $settings = Setting::all();
    $vendors = Vendor::where('account_status', 'active')->get();
    $products = Product::where('status', 'active')->get();
    $countries = Country::where('status', 'active')->get();

    $regions = Region::where('status', 'active')
    ->orderByRaw("
        CASE name
            WHEN 'All Regions' THEN 1
            WHEN 'East Africa' THEN 2
            WHEN 'West Africa' THEN 3
            WHEN 'Southern Africa' THEN 4
            WHEN 'North Africa' THEN 5
            WHEN 'Central Africa' THEN 6
            WHEN 'Region Diaspora' THEN 7
            ELSE 8
        END
    ")
    ->get();

    // Get countries grouped by region with supplier counts
    $countriesByRegion = [];

    foreach ($regions as $region) {
        $countries = Country::where('status', 'active')
            ->where('region_id', $region->id)
            ->withCount(['businessProfiles' => function($query) {
                $query->where('verification_status', 'verified');
            }])
            ->orderByDesc('business_profiles_count')
            ->get()
            ->map(function($country) {
                // Add supplier count alias for easier template usage
                $country->suppliers_count = $country->business_profiles_count ?? 0;
                return $country;
            });

        if ($countries->count() > 0) {
            $countriesByRegion[$region->id] = $countries;
        }
    }



    return view('frontend.home.index', compact(
        'countries',
        'categories',
        'newArrivalProducts',
        'newArrivalCompanies',
        'featuredBusinesses',
        'availableLoads',
        'availableCars',
        'upcomingTradeshows',
        'featuredShowrooms',
        'canSeeTopRfqs',
        // 'settings',
        'vendors',
        'products',
        'countries',
        'topRFQs',
        'regions',              // Add this
        'countriesByRegion'

    ));
}







public function searchSuggestions(Request $request)
{
    $query = $request->input('query');

    if (strlen($query) < 2) {
        return response()->json([]);
    }

    $results = GlobalSearchIndex::where('title', 'LIKE', "%{$query}%")
        ->orWhere('search_content', 'LIKE', "%{$query}%")
        ->orderByRaw("CASE WHEN title LIKE '{$query}%' THEN 1 ELSE 2 END")
        ->limit(8)
        ->get(['id', 'searchable_type', 'title', 'description', 'url']);

    return response()->json($results->map(function($item) {
        return [
            'id' => $item->id,
            'type' => class_basename($item->searchable_type),
            'title' => $item->title,
            'description' => $item->description,
            'url' => $item->url,
        ];
    }));
}

public function globalSearch(Request $request)
{
    $query = $request->input('query');

    if (empty($query)) {
        return redirect()->route('home');
    }

    $results = GlobalSearchIndex::where('title', 'LIKE', "%{$query}%")
        ->orWhere('search_content', 'LIKE', "%{$query}%")
        ->orderByRaw("CASE WHEN title LIKE '{$query}%' THEN 1 ELSE 2 END")
        ->paginate(20);

    // Record the search
    RecentSearch::recordSearch($query, $results->total(), auth()->id());

    return view('frontend.search-results', compact('results', 'query'));
}




    /**
     * Get featured businesses with one product each for hero slider
     */
private function getFeaturedBusinessesWithProducts($limit = 3, $onlyWithActiveAddons = false)
{
    $query = User::query();

    if ($onlyWithActiveAddons) {
        $query->whereHas('vendor.addonUsers', function($q) {
            $q->whereNotNull('supplier_id')
              ->whereNotNull('paid_at')
              ->where(function ($q) {
                  $q->whereNull('ended_at')
                    ->orWhere('ended_at', '>', now());
              });
        });
    }

    return $query->with(['products' => function($query) {
            $query->where('status', 'active')
                  ->where('is_admin_verified', true)
                  ->with('images')
                  ->latest()
                  ->limit(1);
        }, 'vendor.businessProfile'])
        ->whereHas('products', function($query) {
            $query->where('status', 'active')
                  ->where('is_admin_verified', true);
        })
        ->whereHas('vendor.businessProfile')
        ->limit($limit)
        ->get()
        ->map(function($user) {
            $product = $user->products->first();
            $image = $product?->images?->first();
            $business = $user->vendor->businessProfile;
            return [
                'business' => $business,
                'product' => $product,
                'image' => $image
            ];
        });
}

    public function livestream()
    {
        return view('frontend.home.livestream');
    }

    /**
     * Show business profiles for a specific country
     */
    public function countryBusinessProfiles($countryId)
    {
        $country = Country::findOrFail($countryId);

        $businessProfiles = BusinessProfile::where('country_id', $countryId)
            ->where('verification_status', 'verified')
            ->where('is_admin_verified', true)
            ->with(['user', 'country'])
            ->latest()
            ->paginate(12);

        // Get first product image for each business profile
        $userIds = $businessProfiles->pluck('user_id');
        $userProducts = Product::whereIn('user_id', $userIds)
            ->where('status', 'active')
            ->where('is_admin_verified', true)
            ->with(['images' => function($imgQuery) {
                $imgQuery->orderBy('is_primary', 'desc')
                    ->orderBy('sort_order', 'asc')
                    ->limit(1);
            }, 'productCategory'])
            ->select('user_id', 'id', 'product_category_id')
            ->get()
            ->groupBy('user_id')
            ->map(function($products) {
                return $products->first();
            });

        return view('frontend.country.business-profiles', compact('country', 'businessProfiles', 'userProducts'));
    }

    /**
     * Show all products for a specific business profile
     */
    public function businessProfileProducts($businessProfileId, Request $request)
    {
        $businessProfile = BusinessProfile::with(['user', 'country'])
            ->findOrFail($businessProfileId);

        if ($businessProfile->verification_status !== 'verified' || !$businessProfile->is_admin_verified) {
            abort(404, 'Business profile not found or not verified.');
        }

        // Get filter parameters
        $search = $request->get('search', '');
        $categoryId = $request->get('category', '');
        $minPrice = $request->get('min_price', '');
        $maxPrice = $request->get('max_price', '');
        $minMOQ = $request->get('min_moq', '');
        $sortBy = $request->get('sort', 'latest');

        // Start building query
        $query = Product::where('user_id', $businessProfile->user_id)
            ->where('status', 'active')
            ->where('is_admin_verified', true)
            ->with(['images', 'country', 'productCategory', 'prices']);

        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('short_description', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply category filter
        if (!empty($categoryId)) {
            $query->where('product_category_id', $categoryId);
        }

        // Apply MOQ filter
        if (!empty($minMOQ)) {
            $query->where('min_order_quantity', '>=', $minMOQ);
        }

        // Apply price filter (using price tiers)
        if (!empty($minPrice) || !empty($maxPrice)) {
            $query->whereHas( function($q) use ($minPrice, $maxPrice) {
                if (!empty($minPrice)) {
                    $q->where('price', '>=', $minPrice);
                }
                if (!empty($maxPrice)) {
                    $q->where('price', '<=', $maxPrice);
                }
            });
        }

        // Apply sorting
        switch ($sortBy) {
            case 'price_low':
                $query->withMin( 'price')->orderBy('price_tiers_min_price', 'asc');
                break;
            case 'price_high':
                $query->withMax( 'price')->orderBy('price_tiers_max_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Get all categories for filter dropdown
        $categories = ProductCategory::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('frontend.business-profile.products', compact('businessProfile', 'products', 'categories'));
    }

    /**
     * Show all featured suppliers (verified business profiles)
     */
    public function featuredSuppliers(Request $request)
    {
        // Get filter parameters
        $search = $request->get('search', '');
        $countryId = $request->get('country', '');
        $sortBy = $request->get('sort', 'latest');

        // Start building query
        $query = BusinessProfile::where('verification_status', 'verified')
            ->where('is_admin_verified', true)
            ->with(['user', 'country']);

        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'like', '%' . $search . '%')
                  ->orWhere('business_description', 'like', '%' . $search . '%');
            });
        }

        // Apply country filter
        if (!empty($countryId)) {
            $query->where('country_id', $countryId);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('business_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('business_name', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $businessProfiles = $query->paginate(12)->withQueryString();

        // Get first product image for each business profile
        $userIds = $businessProfiles->pluck('user_id');
        $userProducts = Product::whereIn('user_id', $userIds)
            ->where('status', 'active')
            ->where('is_admin_verified', true)
            ->with(['images' => function($imgQuery) {
                $imgQuery->orderBy('is_primary', 'desc')
                    ->orderBy('sort_order', 'asc')
                    ->limit(1);
            }, 'productCategory'])
            ->select('user_id', 'id', 'product_category_id')
            ->get()
            ->groupBy('user_id')
            ->map(function($products) {
                return $products->first();
            });

        // Get all countries for filter
        $countries = Country::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('frontend.featured-suppliers', compact('businessProfiles', 'userProducts', 'countries'));
    }

    /**
     * Show all countries with business profiles
     */
    public function allCountries()
    {
        // Get all active countries that have verified business profiles
        $countries = Country::where('status', 'active')
            ->whereHas('businessProfiles', function($query) {
                $query->where('verification_status', 'verified')
                      ->where('is_admin_verified', true);
            })
            ->withCount(['businessProfiles' => function($query) {
                $query->where('verification_status', 'verified')
                      ->where('is_admin_verified', true);
            }])
            ->orderBy('name')
            ->get();

        return view('frontend.countries.index', compact('countries'));
    }
}
