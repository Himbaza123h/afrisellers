<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Vendor;
use App\Models\Region;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors in the regional admin's region.
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

        // Query vendors through business profiles in the region's countries
        $query = Vendor::with(['user', 'businessProfile.country', 'ownerID'])
            ->whereHas('businessProfile', function($q) use ($countryIds) {
                $q->whereIn('country_id', $countryIds);
            });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('businessProfile', function($q) use ($search) {
                    $q->where('business_name', 'like', "%{$search}%")
                      ->orWhere('business_email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            });
        }

        // Country filter
        if ($request->filled('country_id')) {
            $query->whereHas('businessProfile', function($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }

        // Account status filter
        if ($request->filled('account_status')) {
            $query->where('account_status', $request->account_status);
        }

        // Email verification filter
        if ($request->filled('email_verified')) {
            if ($request->email_verified === 'verified') {
                $query->where('email_verified', true);
            } elseif ($request->email_verified === 'unverified') {
                $query->where('email_verified', false);
            }
        }

        // Business verification filter
        if ($request->filled('business_verified')) {
            $query->whereHas('businessProfile', function($q) use ($request) {
                if ($request->business_verified === 'verified') {
                    $q->where('is_admin_verified', true);
                } else {
                    $q->where('is_admin_verified', false);
                }
            });
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

        if ($sortBy === 'business_name') {
            $query->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
                  ->orderBy('business_profiles.business_name', $sortOrder)
                  ->select('vendors.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $vendors = $query->paginate(15)->withQueryString();

        // Statistics
        $baseQuery = Vendor::whereHas('businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        });

        $stats = [
            'total' => $baseQuery->count(),
            'active' => (clone $baseQuery)->where('account_status', 'active')->count(),
            'suspended' => (clone $baseQuery)->where('account_status', 'suspended')->count(),
            'email_verified' => (clone $baseQuery)->where('email_verified', true)->count(),
            'email_unverified' => (clone $baseQuery)->where('email_verified', false)->count(),
            'business_verified' => (clone $baseQuery)->whereHas('businessProfile', function($q) {
                $q->where('is_admin_verified', true);
            })->count(),
        ];

        // Calculate percentages
        $stats['active_percentage'] = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;
        $stats['verified_percentage'] = $stats['total'] > 0 ? round(($stats['business_verified'] / $stats['total']) * 100) : 0;

        // Get countries for filter
        $countries = $region->countries()->orderBy('name')->get();

        return view('regional.vendors.index', compact('vendors', 'stats', 'region', 'countries'));
    }

    /**
     * Display the specified vendor.
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

        $vendor = Vendor::with([
            'user.products',
            'businessProfile.country',
            'ownerID'
        ])
            ->whereHas('businessProfile', function($q) use ($countryIds) {
                $q->whereIn('country_id', $countryIds);
            })
            ->findOrFail($id);

        // Get vendor statistics
        $stats = [
            'total_products' => $vendor->user->products()->count(),
            'active_products' => $vendor->user->products()->where('status', 'active')->count(),
            'total_views' => $vendor->user->products()->sum('views'),
            'verified_products' => $vendor->user->products()->where('is_admin_verified', true)->count(),
        ];

        return view('regional.vendors.show', compact('vendor', 'stats'));
    }

    /**
     * Verify a vendor's business profile.
     */
    public function verify($id)
    {
        $user = Auth::user();

        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $vendor = Vendor::whereHas('businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        })->findOrFail($id);

        if ($vendor->businessProfile) {
            $vendor->businessProfile->update([
                'is_admin_verified' => true,
                'verification_status' => 'verified'
            ]);
        }

        return redirect()->back()->with('success', 'Vendor verified successfully.');
    }

    /**
     * Suspend a vendor's account.
     */
    public function suspend($id)
    {
        $user = Auth::user();

        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $vendor = Vendor::whereHas('businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        })->findOrFail($id);

        $vendor->update(['account_status' => 'suspended']);

        return redirect()->back()->with('success', 'Vendor suspended successfully.');
    }

    /**
     * Activate a vendor's account.
     */
    public function activate($id)
    {
        $user = Auth::user();

        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $region = Region::with('countries')->findOrFail($user->regional_id);
        $countryIds = $region->countries->pluck('id');

        $vendor = Vendor::whereHas('businessProfile', function($q) use ($countryIds) {
            $q->whereIn('country_id', $countryIds);
        })->findOrFail($id);

        $vendor->update(['account_status' => 'active']);

        return redirect()->back()->with('success', 'Vendor activated successfully.');
    }
}
