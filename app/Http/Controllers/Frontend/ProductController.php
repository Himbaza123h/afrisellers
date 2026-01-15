<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUserReview;
use App\Models\BusinessProfile;
use App\Models\Performance;
use App\Models\PerformanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function search($type, $slug, Request $request)
    {
        $searchQuery = ucwords(str_replace('-', ' ', $slug));
        $products = collect();
        $suppliers = collect();
        $category = null;
        $tab = $request->get('tab', 'products');

        if ($type === 'category') {
            $category = ProductCategory::where('status', 'active')
                ->get()
                ->first(function($cat) use ($slug) {
                    return \Illuminate\Support\Str::slug($cat->name) === $slug;
                });

            if (!$category) {
                abort(404, 'Category not found');
            }

            $searchQuery = $category->name;

            if ($tab === 'suppliers') {
                $suppliers = $this->getFilteredSuppliers($category, $request, $searchQuery);
                $products = collect();
            } elseif ($tab === 'worldwide') {
                $products = $this->getWorldwideProducts($category, $request);
            } else {
                $products = $this->getFilteredProducts($category, $request);
            }
        } else {
            if ($tab === 'suppliers') {
                $suppliers = $this->getFilteredSuppliersByKeyword($searchQuery, $request);
                $products = collect();
            } else {
                $products = $this->getFilteredProductsByKeyword($searchQuery, $request);
            }
        }

        return view('frontend.products.index', [
            'type' => $type,
            'slug' => $slug,
            'products' => $products,
            'suppliers' => $suppliers,
            'category' => $category,
            'tab' => $tab,
            'totalProducts' => is_object($products) && method_exists($products, 'total') ? $products->total() : 0,
            'totalSuppliers' => is_object($suppliers) && method_exists($suppliers, 'total') ? $suppliers->total() : 0,
            'searchQuery' => $searchQuery
        ]);
    }


        public function trackClick(Product $product, Request $request)
    {
        // Get or create performance record
        $performance = Performance::firstOrCreate(
            [
                'product_id' => $product->id,
                'vendor_id' => optional($product->user->vendor)->business_profile_id,
                'country_id' => $product->country_id,
            ],
            [
                'clicks' => 0,
                'impressions' => 0,
            ]
        );

        $performance->incrementClick();

        return response()->json(['success' => true]);
    }

        public function trackImpression(Product $product, Request $request)
        {
            try {
                $ipAddress = $request->ip();
                $today = now()->toDateString();

                Log::info('=== Impression Tracking Attempt ===', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'ip' => $ipAddress,
                    'date' => $today,
                    'user_agent' => $request->userAgent()
                ]);

                // Check if this IP already impressed today
                $alreadyTracked = PerformanceLog::where('product_id', $product->id)
                    ->where('ip_address', $ipAddress)
                    ->where('type', 'impression')
                    ->where('tracked_date', $today)
                    ->exists();

                if ($alreadyTracked) {
                    Log::warning('Impression NOT tracked - already tracked today', [
                        'product_id' => $product->id,
                        'ip' => $ipAddress,
                        'date' => $today
                    ]);
                    return response()->json(['success' => true, 'message' => 'Already tracked today']);
                }

                // Log the impression
                $performanceLog = PerformanceLog::create([
                    'product_id' => $product->id,
                    'ip_address' => $ipAddress,
                    'type' => 'impression',
                    'tracked_date' => $today,
                ]);

                Log::info('PerformanceLog created', [
                    'log_id' => $performanceLog->id,
                    'product_id' => $product->id,
                    'ip' => $ipAddress,
                    'type' => 'impression'
                ]);

                // Update performance aggregate
                $performance = Performance::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'vendor_id' => optional($product->user->vendor)->business_profile_id,
                        'country_id' => $product->country_id,
                    ],
                    [
                        'clicks' => 0,
                        'impressions' => 0,
                    ]
                );

                $performance->incrementImpression();

                Log::info('Impression tracked successfully', [
                    'product_id' => $product->id,
                    'performance_id' => $performance->id,
                    'total_clicks' => $performance->clicks,
                    'total_impressions' => $performance->impressions,
                    'ctr' => $performance->ctr
                ]);

                return response()->json(['success' => true]);

            } catch (\Exception $e) {
                Log::error('IMPRESSION TRACKING FAILED', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'product_id' => $product->id,
                    'ip' => $request->ip()
                ]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

    private function getFilteredProducts($category, $request)
    {
        $query = Product::where('product_category_id', $category->id)
            ->where('status', 'active')
            ->where('is_admin_verified', true)
            ->with(['images', 'country', 'productCategory',  'user.vendor.businessProfile']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Apply sorting
        $query = $this->applySorting($query, $request->get('sort', 'latest'));

        return $query->paginate(24)->withQueryString();
    }

    private function getFilteredProductsByKeyword($searchQuery, $request)
    {
        $query = Product::where('status', 'active')
            ->where('is_admin_verified', true)
            ->where(function($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                  ->orWhere('description', 'like', '%' . $searchQuery . '%')
                  ->orWhere('short_description', 'like', '%' . $searchQuery . '%');
            })
            ->with(['images', 'country', 'productCategory',  'user.vendor.businessProfile']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Apply sorting
        $query = $this->applySorting($query, $request->get('sort', 'latest'));

        return $query->paginate(24)->withQueryString();
    }

    private function getWorldwideProducts($category, $request)
    {
        $query = Product::where('product_category_id', $category->id)
            ->where('status', 'active')
            ->where('is_admin_verified', true)
            ->with(['images', 'country', 'productCategory',  'user.vendor.businessProfile'])
            ->whereHas('country');

        // Apply filters
        $query = $this->applyFilters($query, $request);

        return $query->latest()->paginate(24)->withQueryString();
    }

    private function getFilteredSuppliers($category, $request, $searchQuery)
    {
        $query = BusinessProfile::whereHas('user.products', function($q) use ($category) {
            $q->where('product_category_id', $category->id)
              ->where('status', 'active')
              ->where('is_admin_verified', true);
        })
        ->where('verification_status', 'verified')
        ->where('is_admin_verified', true)
        ->with(['user', 'country'])
        ->withCount(['user as products_count' => function($q) use ($category) {
            $q->whereHas('products', function($query) use ($category) {
                $query->where('product_category_id', $category->id)
                      ->where('status', 'active')
                      ->where('is_admin_verified', true);
            });
        }]);

        // Apply supplier filters
        $query = $this->applySupplierFilters($query, $request);

        return $query->paginate(24)->withQueryString();
    }

    private function getFilteredSuppliersByKeyword($searchQuery, $request)
    {
        $query = BusinessProfile::whereHas('user.products', function($q) use ($searchQuery) {
            $q->where('status', 'active')
              ->where('is_admin_verified', true)
              ->where(function($query) use ($searchQuery) {
                  $query->where('name', 'like', '%' . $searchQuery . '%')
                        ->orWhere('description', 'like', '%' . $searchQuery . '%');
              });
        })
        ->where('verification_status', 'verified')
        ->where('is_admin_verified', true)
        ->with(['user', 'country']);

        // Apply supplier filters
        $query = $this->applySupplierFilters($query, $request);

        return $query->paginate(24)->withQueryString();
    }

    private function applySupplierFilters($query, $request)
    {
        // Verified supplier filter
        if ($request->has('verified_supplier') && $request->verified_supplier == '1') {
            $query->where('is_admin_verified', true);
        }

        // Verified PRO filter
        if ($request->has('verified_pro') && $request->verified_pro == '1') {
            $query->where('is_verified_pro', true);
        }

        // Country filters
        if ($request->has('countries') && is_array($request->countries) && count($request->countries) > 0) {
            $query->whereHas('country', function($q) use ($request) {
                $countryNames = array_map(function($country) {
                    return ucwords(str_replace('_', ' ', $country));
                }, $request->countries);
                $q->whereIn('name', $countryNames);
            });
        }

        // Response time filters
        if ($request->has('response_within_24h') && $request->response_within_24h == '1') {
            $query->where('response_time', '<=', 24);
        }

        if ($request->has('response_within_1h') && $request->response_within_1h == '1') {
            $query->where('response_time', '<=', 1);
        }

        return $query;
    }

    private function applyFilters($query, $request)
    {
        // Verified supplier filter
        if ($request->has('verified_supplier') && $request->verified_supplier == '1') {
            $query->whereHas('user.vendor.businessProfile', function($q) {
                $q->where('is_admin_verified', true);
            });
        }

        // Verified PRO filter
        if ($request->has('verified_pro') && $request->verified_pro == '1') {
            $query->whereHas('user.vendor.businessProfile', function($q) {
                $q->where('is_verified_pro', true);
            });
        }

        // Price range filter
    if ($request->has('price_range')) {
        $priceRange = $request->price_range;

        if ($priceRange === '0-100') {
            $query->whereHas('prices', function($q) {
                $q->where('price', '<=', 100);
            });
        } elseif ($priceRange === '100-500') {
            $query->whereHas('prices', function($q) {
                $q->whereBetween('price', [100, 500]);
            });
        } elseif ($priceRange === '500-1000') {
            $query->whereHas('prices', function($q) {
                $q->whereBetween('price', [500, 1000]);
            });
        } elseif ($priceRange === '1000-5000') {
            $query->whereHas('prices', function($q) {
                $q->whereBetween('price', [1000, 5000]);
            });
        } elseif ($priceRange === '5000-plus') {
            $query->whereHas('prices', function($q) {
                $q->where('price', '>=', 5000);
            });
        }
    }

        // MOQ filters
        if ($request->has('moq_1_10') && $request->moq_1_10 == '1') {
            $query->where('min_order_quantity', '<=', 10);
        }
        if ($request->has('moq_11_50') && $request->moq_11_50 == '1') {
            $query->whereBetween('min_order_quantity', [11, 50]);
        }
        if ($request->has('moq_51_100') && $request->moq_51_100 == '1') {
            $query->whereBetween('min_order_quantity', [51, 100]);
        }
        if ($request->has('moq_100_plus') && $request->moq_100_plus == '1') {
            $query->where('min_order_quantity', '>=', 100);
        }

        // Rating filter
        if ($request->has('rating')) {
            $ratingValue = floatval(str_replace('_up', '', $request->rating));
            $query->whereIn('id', function($subQuery) use ($ratingValue) {
                $subQuery->select('product_id')
                    ->from('product_user_reviews')
                    ->where('status', 1)
                    ->groupBy('product_id')
                    ->havingRaw('AVG(mark) >= ?', [$ratingValue]);
            });
        }

        // Country filters
        if ($request->has('countries') && is_array($request->countries) && count($request->countries) > 0) {
            $query->whereHas('country', function($q) use ($request) {
                $countryNames = array_map(function($country) {
                    return ucwords(str_replace('_', ' ', $country));
                }, $request->countries);
                $q->whereIn('name', $countryNames);
            });
        }

        // Product feature filters
        if ($request->has('paid_samples') && $request->paid_samples == '1') {
            $query->where('paid_samples', true);
        }

        if ($request->has('customizable') && $request->customizable == '1') {
            $query->where('customizable', true);
        }

        if ($request->has('eco_friendly') && $request->eco_friendly == '1') {
            $query->where('eco_friendly', true);
        }

        if ($request->has('ready_to_ship') && $request->ready_to_ship == '1') {
            $query->where('ready_to_ship', true);
        }

        // Shipping options
        if ($request->has('free_shipping') && $request->free_shipping == '1') {
            $query->where('free_shipping', true);
        }

        if ($request->has('fast_dispatch') && $request->fast_dispatch == '1') {
            $query->where('dispatch_days', '<=', 7);
        }

        return $query;
    }

private function applySorting($query, $sortBy)
{
    switch ($sortBy) {
        case 'price_low':
            // FIXED: Add relation name and column name
            $query->withMin('prices as min_price', 'price')->orderBy('min_price', 'asc');
            break;
        case 'price_high':
            // FIXED: Add relation name and column name
            $query->withMax('prices as max_price', 'price')->orderBy('max_price', 'desc');
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

    return $query;
}

public function show($slug, Request $request)
{
    Log::info('=== Product Show Method Called ===', [
        'slug' => $slug,
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent()
    ]);

    $product = Product::where('slug', $slug)
        ->where('status', 'active')
        ->where('is_admin_verified', true)
        ->with(['images', 'country', 'user', 'productCategory', 'variations'])
        ->firstOrFail();

    Log::info('Product found', [
        'product_id' => $product->id,
        'product_name' => $product->name
    ]);

    // Track Click with IP-based daily restriction
    try {
        $ipAddress = $request->ip();
        $today = now()->toDateString();

        Log::info('Starting click tracking', [
            'product_id' => $product->id,
            'ip' => $ipAddress,
            'date' => $today
        ]);

        // Check if this IP already clicked today
        $alreadyTracked = PerformanceLog::where('product_id', $product->id)
            ->where('ip_address', $ipAddress)
            ->where('type', 'click')
            ->where('tracked_date', $today)
            ->exists();

        if ($alreadyTracked) {
            Log::warning('Click NOT tracked - already tracked today', [
                'product_id' => $product->id,
                'ip' => $ipAddress,
                'date' => $today
            ]);
        } else {
            // Log the click
            PerformanceLog::create([
                'product_id' => $product->id,
                'ip_address' => $ipAddress,
                'type' => 'click',
                'tracked_date' => $today,
            ]);

            Log::info('PerformanceLog created', [
                'product_id' => $product->id,
                'ip' => $ipAddress,
                'type' => 'click'
            ]);

            // Update performance aggregate
            $performance = Performance::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'vendor_id' => optional($product->user->vendor)->business_profile_id,
                    'country_id' => $product->country_id,
                ],
                [
                    'clicks' => 0,
                    'impressions' => 0,
                ]
            );

            $performance->incrementClick();

            Log::info('Click tracked successfully', [
                'product_id' => $product->id,
                'performance_id' => $performance->id,
                'total_clicks' => $performance->clicks,
                'total_impressions' => $performance->impressions
            ]);
        }

    } catch (\Exception $e) {
        Log::error('CLICK TRACKING FAILED', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'product_id' => $product->id,
            'ip' => $request->ip()
        ]);
    }

    try {
        $allReviews = $product->reviews()
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->orderBy('created_at', 'desc')
            ->get();
    } catch (\Exception $e) {
        Log::error('Error loading reviews: ' . $e->getMessage());
        $allReviews = collect();
    }

    $relatedProducts = Product::where('status', 'active')
        ->where('is_admin_verified', true)
        ->where('product_category_id', $product->product_category_id)
        ->where('id', '!=', $product->id)
        ->with(['images', 'country', 'prices'])
        ->limit(5)
        ->get();

    return view('frontend.products.show', compact('product', 'relatedProducts', 'allReviews'));
}

    public function storeReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'mark' => 'required|integer|min:1|max:5',
            'message' => 'required|string|max:1000',
        ]);

        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Please login to submit a review.');
        }

        $existingReview = ProductUserReview::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this product.');
        }

        $review = ProductUserReview::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'mark' => $request->mark,
            'message' => $request->message,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'Thank you for your review!');
    }
}
