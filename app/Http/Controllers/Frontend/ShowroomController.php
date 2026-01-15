<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Models\Country;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ShowroomController extends Controller
{
    /**
     * Display showrooms listing
     */
    public function index(Request $request)
    {
        $query = Showroom::where('status', 'active')
            ->with(['country', 'user']);

        // Filters
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('industry')) {
            $query->where('industry', $request->industry);
        }

        if ($request->filled('business_type')) {
            $query->where('business_type', $request->business_type);
        }

        if ($request->filled('verified_only')) {
            $query->where('is_verified', true);
        }

        if ($request->filled('authorized_dealer')) {
            $query->where('is_authorized_dealer', true);
        }

        $showrooms = $query->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->paginate(12)->withQueryString();

        $countries = Country::where('status', 'active')->orderBy('name')->get();

        $industries = Showroom::select('industry')
            ->distinct()
            ->whereNotNull('industry')
            ->pluck('industry');

        $cities = Showroom::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city');

        return view('frontend.showrooms.index', compact('showrooms', 'countries', 'industries', 'cities'));
    }

    /**
     * Display single showroom
     */
    public function show(Showroom $showroom)
    {
        $showroom->load(['country', 'user']);

        // Increment views
        $showroom->increment('views_count');

        // Get similar showrooms
        $similarShowrooms = Showroom::where('id', '!=', $showroom->id)
            ->where('status', 'active')
            ->where(function($query) use ($showroom) {
                $query->where('city', $showroom->city)
                      ->orWhere('industry', $showroom->industry);
            })
            ->limit(4)
            ->get();

        return view('frontend.showrooms.show', compact('showroom', 'similarShowrooms'));
    }

    /**
     * Display showroom products
     */
    public function products(Request $request, Showroom $showroom)
    {
        // Build query for products in this showroom
        $query = $showroom->products()
            ->where('products.status', 'active')
            ->where('products.is_admin_verified', true)
            ->with(['images', 'productCategory', 'country', 'prices']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                  ->orWhere('products.description', 'like', "%{$search}%")
                  ->orWhere('products.short_description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('products.product_category_id', $request->category);
        }

        // Price filters
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereHas('prices', function($q) use ($request) {
                $q->whereBetween('price', [$request->min_price, $request->max_price]);
            });
        } elseif ($request->filled('min_price')) {
            $query->whereHas('prices', function($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        } elseif ($request->filled('max_price')) {
            $query->whereHas('prices', function($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        // MOQ filter
        if ($request->filled('min_moq')) {
            $query->where('products.min_order_quantity', '>=', $request->min_moq);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('products.created_at', 'asc');
                break;
            case 'price_low':
                $query->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                    ->select('products.*')
                    ->orderBy('product_prices.price', 'asc');
                break;
            case 'price_high':
                $query->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                    ->select('products.*')
                    ->orderBy('product_prices.price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('products.name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('products.name', 'desc');
                break;
            default: // latest
                $query->orderBy('products.created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        // Get categories for filter
        $categories = ProductCategory::where('status', 'active')
            ->orderBy('name')
            ->get();

        $showroom->load(['country', 'user']);

        return view('frontend.showrooms.products', compact('showroom', 'products', 'categories'));
    }

    /**
     * Send inquiry (authenticated)
     */
    public function inquiry(Request $request, Showroom $showroom)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'inquiry_type' => 'required|in:product,service,general',
        ]);

        // Create inquiry (you'll need a ShowroomInquiry model)

        $showroom->increment('inquiries_count');

        return redirect()->back()->with('success', 'Your inquiry has been sent!');
    }

    /**
     * Schedule visit (authenticated)
     */
    public function scheduleVisit(Request $request, Showroom $showroom)
    {
        $request->validate([
            'visit_date' => 'required|date|after:today',
            'visit_time' => 'required',
            'purpose' => 'required|string|max:500',
        ]);

        // Create visit schedule (you'll need a ShowroomVisit model)

        $showroom->increment('visits_count');

        return redirect()->back()->with('success', 'Visit scheduled successfully!');
    }
}
