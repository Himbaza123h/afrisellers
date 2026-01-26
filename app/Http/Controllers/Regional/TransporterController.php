<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransporterController extends Controller
{
    /**
     * Display a listing of transporters in the regional admin's region.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get regional admin's countries
        if (!$user->regional_admin || !$user->regional_id) {
            abort(403, 'You are not assigned to any region.');
        }


        // $user = Auth::user();

        // // Get regional admin's region
        // if (!$user->regional_admin || !$user->regional_id) {
        //     abort(403, 'You are not assigned to any region.');
        // }

        // Get countries in this region
        $regionCountries = \App\Models\Country::where('region_id', $user->region_id)->pluck('id');

        $query = Transporter::with([
            'user',
            'country',
            'businessProfile'
        ])
            ->whereIn('country_id', $regionCountries);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Country filter
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Verification status filter
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified === 'verified');
        }

        // Account status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $transporters = $query->paginate(15)->withQueryString();

        // Statistics
        $baseQuery = Transporter::whereIn('country_id', $regionCountries);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'verified' => (clone $baseQuery)->where('is_verified', true)->count(),
            'unverified' => (clone $baseQuery)->where('is_verified', false)->count(),
            'active' => (clone $baseQuery)->where('status', 'active')->count(),
            'suspended' => (clone $baseQuery)->where('status', 'suspended')->count(),
            'inactive' => (clone $baseQuery)->where('status', 'inactive')->count(),
            'total_fleet' => (clone $baseQuery)->sum('fleet_size'),
            'average_rating' => (clone $baseQuery)->avg('average_rating'),
        ];

        // Calculate percentages
        $stats['verified_percentage'] = $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100) : 0;
        $stats['active_percentage'] = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;

        // Get countries for filter
        $countries = \App\Models\Country::whereIn('id', $regionCountries)
            ->orderBy('name')
            ->get();

        return view('regional.transporters.index', compact(
            'transporters',
            'stats',
            'countries'
        ));
    }

    /**
 * Print transporters report
 */
public function print(Request $request)
{
    $user = Auth::user();

    if (!$user->regional_admin || !$user->regional_id) {
        abort(403, 'You are not assigned to any region.');
    }

    $region = \App\Models\Region::with('countries')->findOrFail($user->regional_id);
    $regionCountries = \App\Models\Country::where('region_id', $user->regional_id)->pluck('id');

    // Build query
    $query = Transporter::with([
        'user',
        'country',
        'businessProfile'
    ])->whereIn('country_id', $regionCountries);

    // Apply filters
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('company_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('registration_number', 'like', "%{$search}%");
        });
    }

    if ($request->filled('country_id')) {
        $query->where('country_id', $request->country_id);
    }

    if ($request->filled('is_verified')) {
        $query->where('is_verified', $request->is_verified === 'verified');
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Get all transporters
    $transporters = $query->get();

    // Calculate stats
    $stats = [
        'total' => $transporters->count(),
        'verified' => $transporters->where('is_verified', true)->count(),
        'unverified' => $transporters->where('is_verified', false)->count(),
        'active' => $transporters->where('status', 'active')->count(),
        'suspended' => $transporters->where('status', 'suspended')->count(),
        'inactive' => $transporters->where('status', 'inactive')->count(),
        'total_fleet' => $transporters->sum('fleet_size'),
        'average_rating' => $transporters->avg('average_rating') ?? 0,
    ];

    $stats['verified_percentage'] = $stats['total'] > 0
        ? round(($stats['verified'] / $stats['total']) * 100)
        : 0;

    $stats['active_percentage'] = $stats['total'] > 0
        ? round(($stats['active'] / $stats['total']) * 100)
        : 0;

    $countries = \App\Models\Country::whereIn('id', $regionCountries)->get();

    return view('regional.transporters.print', compact('region', 'countries', 'transporters', 'stats'));
}

    /**
     * Display the specified transporter.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Get regional admin's countries
        if (!$user->regional_admin || !$user->region_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $regionCountries = \App\Models\Country::where('region_id', $user->region_id)->pluck('id');

        $transporter = Transporter::with([
            'user',
            'country',
            'businessProfile'
        ])
            ->whereIn('country_id', $regionCountries)
            ->findOrFail($id);

        // Get statistics
        $stats = [
            'total_deliveries' => $transporter->total_deliveries,
            'successful_deliveries' => $transporter->successful_deliveries,
            'failed_deliveries' => $transporter->failed_deliveries,
            'success_rate' => $transporter->success_rate,
            'average_rating' => $transporter->average_rating,
            'fleet_size' => $transporter->fleet_size,
            'service_areas' => $transporter->service_areas,
            'vehicle_types' => $transporter->vehicle_types,
        ];

        return view('regional.transporters.show', compact('transporter', 'stats'));
    }

    /**
     * Verify a transporter.
     */
    public function verify($id)
    {
        $user = Auth::user();

        if (!$user->regional_admin || !$user->region_id) {
            abort(403, 'You are not assigned to any region.');
        }

        $regionCountries = \App\Models\Country::where('region_id', $user->region_id)->pluck('id');

        $transporter = Transporter::whereIn('country_id', $regionCountries)->findOrFail($id);

        $transporter->verify();

        return redirect()->route('regional.transporters.show', $id)
            ->with('success', 'Transporter verified successfully.');
    }
}
