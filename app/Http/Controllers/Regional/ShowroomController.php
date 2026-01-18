<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShowroomController extends Controller
{
    /**
     * Display a listing of showrooms in the regional admin's region.
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

        $query = Showroom::with(['user', 'country', 'products'])
            ->whereIn('country_id', $countryIds);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('showroom_number', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
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
                $query->where('is_verified', true);
            } elseif ($request->verification === 'unverified') {
                $query->where('is_verified', false);
            }
        }

        // Featured filter
        if ($request->filled('featured')) {
            if ($request->featured === 'yes') {
                $query->where('is_featured', true);
            } elseif ($request->featured === 'no') {
                $query->where('is_featured', false);
            }
        }

        // Business type filter
        if ($request->filled('business_type')) {
            $query->where('business_type', $request->business_type);
        }

        // City filter
        if ($request->filled('city')) {
            $query->where('city', $request->city);
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

        $showrooms = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Showroom::whereIn('country_id', $countryIds)->count(),
            'active' => Showroom::whereIn('country_id', $countryIds)->where('status', 'active')->count(),
            'pending' => Showroom::whereIn('country_id', $countryIds)->where('status', 'pending')->count(),
            'verified' => Showroom::whereIn('country_id', $countryIds)->where('is_verified', true)->count(),
            'unverified' => Showroom::whereIn('country_id', $countryIds)->where('is_verified', false)->count(),
            'featured' => Showroom::whereIn('country_id', $countryIds)->where('is_featured', true)->count(),
            'total_views' => Showroom::whereIn('country_id', $countryIds)->sum('views_count'),
            'total_inquiries' => Showroom::whereIn('country_id', $countryIds)->sum('inquiries_count'),
        ];

        // Calculate percentages
        $stats['active_percentage'] = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;
        $stats['verified_percentage'] = $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100) : 0;
        $stats['featured_percentage'] = $stats['total'] > 0 ? round(($stats['featured'] / $stats['total']) * 100) : 0;

        // Get countries and cities for filters
        $countries = $region->countries()->orderBy('name')->get();
        $cities = Showroom::whereIn('country_id', $countryIds)
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('regional.showrooms.index', compact('showrooms', 'stats', 'region', 'countries', 'cities'));
    }

    /**
     * Display the specified showroom.
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

        $showroom = Showroom::with([
            'user',
            'country',
            'products.prices',
            'products.images',
            'showroomProducts'
        ])
            ->whereIn('country_id', $countryIds)
            ->findOrFail($id);

        // Get statistics
        $stats = [
            'total_products' => $showroom->products()->count(),
            'active_products' => $showroom->products()->where('status', 'active')->count(),
            'verified_products' => $showroom->products()->where('is_admin_verified', true)->count(),
            'total_views' => $showroom->views_count,
            'total_inquiries' => $showroom->inquiries_count,
            'total_visits' => $showroom->visits_count,
        ];

        return view('regional.showrooms.show', compact('showroom', 'stats'));
    }

    /**
     * Verify a showroom.
     */
    public function verify($id)
    {
        $user = Auth::user();

        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $showroom = Showroom::whereIn('country_id', $countryIds)->findOrFail($id);

        $showroom->update([
            'is_verified' => true,
            'status' => 'active'
        ]);

        return redirect()->back()->with('success', 'Showroom verified successfully.');
    }

    /**
     * Feature/unfeature a showroom.
     */
    public function feature($id)
    {
        $user = Auth::user();

        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $showroom = Showroom::whereIn('country_id', $countryIds)->findOrFail($id);

        $showroom->update([
            'is_featured' => !$showroom->is_featured
        ]);

        $message = $showroom->is_featured ? 'Showroom featured successfully.' : 'Showroom unfeatured successfully.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified showroom.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $showroom = Showroom::whereIn('country_id', $countryIds)->findOrFail($id);

        $showroom->delete();

        return redirect()->route('regional.showrooms.index')->with('success', 'Showroom deleted successfully.');
    }
}
