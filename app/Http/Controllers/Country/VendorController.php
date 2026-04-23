<?php

namespace App\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Vendor;
use App\Models\Country;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors in the country admin's country.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        // Query vendors through business profiles
        $query = Vendor::with(['user', 'businessProfile.country', 'ownerID'])
            ->whereHas('businessProfile', function($q) use ($country) {
                $q->where('country_id', $country->id);
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
        $baseQuery = Vendor::whereHas('businessProfile', function($q) use ($country) {
            $q->where('country_id', $country->id);
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

        return view('country.vendors.index', compact('vendors', 'stats', 'country'));
    }

    /**
     * Display the specified vendor.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $vendor = Vendor::with([
            'user.products',
            'businessProfile.country',
            'ownerID'
        ])
            ->whereHas('businessProfile', function($q) use ($country) {
                $q->where('country_id', $country->id);
            })
            ->findOrFail($id);

        // Get vendor statistics
        $stats = [
            'total_products' => $vendor->user->products()->count(),
            'active_products' => $vendor->user->products()->where('status', 'active')->count(),
            'total_views' => $vendor->user->products()->sum('views'),
            'verified_products' => $vendor->user->products()->where('is_admin_verified', true)->count(),
        ];

        return view('country.vendors.show', compact('vendor', 'stats'));
    }

        /**
     * Print vendors report
     */
    public function print(Request $request)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        // Query vendors through business profiles (same as index)
        $query = Vendor::with(['user', 'businessProfile.country', 'ownerID'])
            ->whereHas('businessProfile', function($q) use ($country) {
                $q->where('country_id', $country->id);
            });

        // Apply filters (same as index)
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

        if ($request->filled('account_status')) {
            $query->where('account_status', $request->account_status);
        }

        if ($request->filled('email_verified')) {
            if ($request->email_verified === 'verified') {
                $query->where('email_verified', true);
            } elseif ($request->email_verified === 'unverified') {
                $query->where('email_verified', false);
            }
        }

        if ($request->filled('business_verified')) {
            $query->whereHas('businessProfile', function($q) use ($request) {
                if ($request->business_verified === 'verified') {
                    $q->where('is_admin_verified', true);
                } else {
                    $q->where('is_admin_verified', false);
                }
            });
        }

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

        $vendors = $query->get();

        // Statistics for print
        $baseQuery = Vendor::whereHas('businessProfile', function($q) use ($country) {
            $q->where('country_id', $country->id);
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

        $stats['active_percentage'] = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;
        $stats['verified_percentage'] = $stats['total'] > 0 ? round(($stats['business_verified'] / $stats['total']) * 100) : 0;

        return view('country.vendors.print', compact('vendors', 'stats', 'country'));
    }

    /**
     * Verify a vendor's business profile.
     */
    public function verify($id)
    {
        $user = Auth::user();

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $vendor = Vendor::whereHas('businessProfile', function($q) use ($country) {
            $q->where('country_id', $country->id);
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

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $vendor = Vendor::whereHas('businessProfile', function($q) use ($country) {
            $q->where('country_id', $country->id);
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

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $country = Country::findOrFail($user->country_id);

        $vendor = Vendor::whereHas('businessProfile', function($q) use ($country) {
            $q->where('country_id', $country->id);
        })->findOrFail($id);

        $vendor->update(['account_status' => 'active']);

        return redirect()->back()->with('success', 'Vendor activated successfully.');
    }
}
