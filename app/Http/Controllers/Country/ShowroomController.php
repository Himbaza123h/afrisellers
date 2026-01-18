<?php

namespace App\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShowroomController extends Controller
{
    /**
     * Display a listing of showrooms in the country admin's country.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $query = Showroom::with(['user', 'country', 'products'])
            ->where('country_id', $user->country_id);

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
            'total' => Showroom::where('country_id', $user->country_id)->count(),
            'active' => Showroom::where('country_id', $user->country_id)->where('status', 'active')->count(),
            'pending' => Showroom::where('country_id', $user->country_id)->where('status', 'pending')->count(),
            'verified' => Showroom::where('country_id', $user->country_id)->where('is_verified', true)->count(),
            'unverified' => Showroom::where('country_id', $user->country_id)->where('is_verified', false)->count(),
            'featured' => Showroom::where('country_id', $user->country_id)->where('is_featured', true)->count(),
            'total_views' => Showroom::where('country_id', $user->country_id)->sum('views_count'),
            'total_inquiries' => Showroom::where('country_id', $user->country_id)->sum('inquiries_count'),
        ];

        // Calculate percentages
        $stats['active_percentage'] = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;
        $stats['verified_percentage'] = $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100) : 0;
        $stats['featured_percentage'] = $stats['total'] > 0 ? round(($stats['featured'] / $stats['total']) * 100) : 0;

        // Get cities for filter
        $cities = Showroom::where('country_id', $user->country_id)
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('country.showrooms.index', compact('showrooms', 'stats', 'cities'));
    }

    /**
     * Display the specified showroom.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $showroom = Showroom::with([
            'user',
            'country',
            'products.prices',
            'products.images',
            'showroomProducts'
        ])
            ->where('country_id', $user->country_id)
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

        return view('country.showrooms.show', compact('showroom', 'stats'));
    }

    /**
     * Verify a showroom.
     */
    public function verify($id)
    {
        $user = Auth::user();

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $showroom = Showroom::where('country_id', $user->country_id)->findOrFail($id);

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

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $showroom = Showroom::where('country_id', $user->country_id)->findOrFail($id);

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

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $showroom = Showroom::where('country_id', $user->country_id)->findOrFail($id);

        $showroom->delete();

        return redirect()->route('country.showrooms.index')->with('success', 'Showroom deleted successfully.');
    }
}
