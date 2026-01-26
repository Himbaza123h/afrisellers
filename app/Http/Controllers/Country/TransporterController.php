<?php

namespace App\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransporterController extends Controller
{
    /**
     * Display a listing of transporters in the country admin's country.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $query = Transporter::with([
            'user',
            'country',
            'businessProfile'
        ])
            ->where('country_id', $user->country_id);

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
        $baseQuery = Transporter::where('country_id', $user->country_id);

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

        return view('country.transporters.index', compact(
            'transporters',
            'stats'
        ));
    }

    /**
 * Print transporters report for the country.
 */
public function print(Request $request)
{
    $user = Auth::user();

    // Get country admin's country
    if (!$user->country_admin || !$user->country_id) {
        abort(403, 'You are not assigned to any country.');
    }

    $country = \App\Models\Country::findOrFail($user->country_id);

    $query = Transporter::with([
        'user',
        'country',
        'businessProfile'
    ])
        ->where('country_id', $user->country_id);

    // Apply filters
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

    if ($request->filled('is_verified')) {
        $query->where('is_verified', $request->is_verified === 'verified');
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');
    $query->orderBy($sortBy, $sortOrder);

    $transporters = $query->get();

    // Statistics
    $baseQuery = Transporter::where('country_id', $user->country_id);

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

    $stats['verified_percentage'] = $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100) : 0;
    $stats['active_percentage'] = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;

    return view('country.transporters.print', compact('transporters', 'stats', 'country'));
}

    /**
     * Display the specified transporter.
     */
public function show($id)
{
    $user = Auth::user();

    if (!$user->country_admin || !$user->country_id) {
        abort(403, 'You are not assigned to any country.');
    }

    $transporter = Transporter::with([
        'user',
        'country',
        'businessProfile',
        'vehicles',
        'loads',
        'bids'
    ])
        ->where('country_id', $user->country_id)
        ->findOrFail($id);

    // Calculate statistics
    $stats = [
        'total_loads' => $transporter->loads->count(),
        'completed_loads' => $transporter->loads->where('status', 'delivered')->count(),
        'active_loads' => $transporter->loads->whereIn('status', ['assigned', 'in_transit'])->count(),
        'total_vehicles' => $transporter->vehicles->count(),
        'active_vehicles' => $transporter->vehicles->where('status', 'active')->count(),
        'total_bids' => $transporter->bids->count(),
        'average_rating' => $transporter->average_rating,
        'total_reviews' => 0, // Add if you have a reviews relationship
    ];

    return view('country.transporters.show', compact('transporter', 'stats'));
}

    /**
     * Verify a transporter.
     */
    public function verify($id)
    {
        $user = Auth::user();

        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $transporter = Transporter::where('country_id', $user->country_id)->findOrFail($id);

        $transporter->verify();

        return redirect()->route('country.transporters.show', $id)
            ->with('success', 'Transporter verified successfully.');
    }
}
