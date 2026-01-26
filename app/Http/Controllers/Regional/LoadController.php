<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoadController extends Controller
{
    /**
     * Display a listing of loads in the regional admin's region.
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

        $query = Load::with([
            'user',
            'originCountry',
            'destinationCountry',
            'assignedTransporter.user',
            'bids',
            'winningBid'
        ])
            ->where(function($q) use ($countryIds) {
                $q->whereIn('origin_country_id', $countryIds)
                  ->orWhereIn('destination_country_id', $countryIds);
            });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('load_number', 'like', "%{$search}%")
                  ->orWhere('cargo_type', 'like', "%{$search}%")
                  ->orWhere('cargo_description', 'like', "%{$search}%")
                  ->orWhere('origin_city', 'like', "%{$search}%")
                  ->orWhere('destination_city', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Country filter (origin or destination)
        if ($request->filled('country_id')) {
            $query->where(function($q) use ($request) {
                $q->where('origin_country_id', $request->country_id)
                  ->orWhere('destination_country_id', $request->country_id);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Cargo type filter
        if ($request->filled('cargo_type')) {
            $query->where('cargo_type', $request->cargo_type);
        }

        // Pricing type filter
        if ($request->filled('pricing_type')) {
            $query->where('pricing_type', $request->pricing_type);
        }

        // Origin city filter
        if ($request->filled('origin_city')) {
            $query->where('origin_city', $request->origin_city);
        }

        // Destination city filter
        if ($request->filled('destination_city')) {
            $query->where('destination_city', $request->destination_city);
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

        $loads = $query->paginate(15)->withQueryString();

        // Statistics
        $baseQuery = Load::where(function($q) use ($countryIds) {
            $q->whereIn('origin_country_id', $countryIds)
              ->orWhereIn('destination_country_id', $countryIds);
        });

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'posted' => (clone $baseQuery)->where('status', 'posted')->count(),
            'bidding' => (clone $baseQuery)->where('status', 'bidding')->count(),
            'assigned' => (clone $baseQuery)->where('status', 'assigned')->count(),
            'in_transit' => (clone $baseQuery)->where('status', 'in_transit')->count(),
            'delivered' => (clone $baseQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            'total_bids' => DB::table('load_bids')
                ->whereIn('load_id', (clone $baseQuery)->pluck('id'))
                ->count(),
        ];

        // Calculate percentages
        $stats['posted_percentage'] = $stats['total'] > 0 ? round(($stats['posted'] / $stats['total']) * 100) : 0;
        $stats['delivered_percentage'] = $stats['total'] > 0 ? round(($stats['delivered'] / $stats['total']) * 100) : 0;

        // Get countries and cities for filters
        $countries = $region->countries()->orderBy('name')->get();

        $originCities = Load::whereIn('origin_country_id', $countryIds)
            ->select('origin_city')
            ->distinct()
            ->whereNotNull('origin_city')
            ->orderBy('origin_city')
            ->pluck('origin_city');

        $destinationCities = Load::whereIn('destination_country_id', $countryIds)
            ->select('destination_city')
            ->distinct()
            ->whereNotNull('destination_city')
            ->orderBy('destination_city')
            ->pluck('destination_city');

        return view('regional.loads.index', compact(
            'loads',
            'stats',
            'region',
            'countries',
            'originCities',
            'destinationCities'
        ));
    }

    /**
 * Print loads report
 */
public function print(Request $request)
{
    $user = Auth::user();

    if (!$user->regional_admin || !$user->regional_id) {
        abort(403, 'You are not assigned to any region.');
    }

    $region = Region::with('countries')->findOrFail($user->regional_id);
    $countryIds = $region->countries->pluck('id');

    // Build query
    $query = Load::with([
        'user',
        'originCountry',
        'destinationCountry',
        'assignedTransporter.user',
        'bids'
    ])
        ->where(function($q) use ($countryIds) {
            $q->whereIn('origin_country_id', $countryIds)
              ->orWhereIn('destination_country_id', $countryIds);
        });

    // Apply filters if any
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('load_number', 'like', "%{$search}%")
              ->orWhere('cargo_type', 'like', "%{$search}%")
              ->orWhere('origin_city', 'like', "%{$search}%")
              ->orWhere('destination_city', 'like', "%{$search}%");
        });
    }

    if ($request->filled('country_id')) {
        $query->where(function($q) use ($request) {
            $q->where('origin_country_id', $request->country_id)
              ->orWhere('destination_country_id', $request->country_id);
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('cargo_type')) {
        $query->where('cargo_type', $request->cargo_type);
    }

    if ($request->filled('pricing_type')) {
        $query->where('pricing_type', $request->pricing_type);
    }

    if ($request->filled('origin_city')) {
        $query->where('origin_city', $request->origin_city);
    }

    if ($request->filled('destination_city')) {
        $query->where('destination_city', $request->destination_city);
    }

    if ($request->filled('date_range')) {
        $dates = explode(' to ', $request->date_range);
        if (count($dates) === 2) {
            $query->whereDate('created_at', '>=', $dates[0])
                  ->whereDate('created_at', '<=', $dates[1]);
        }
    }

    // Get all loads (no pagination for print)
    $loads = $query->get();

    // Calculate stats
    $stats = [
        'total' => $loads->count(),
        'posted' => $loads->where('status', 'posted')->count(),
        'bidding' => $loads->where('status', 'bidding')->count(),
        'assigned' => $loads->where('status', 'assigned')->count(),
        'in_transit' => $loads->where('status', 'in_transit')->count(),
        'delivered' => $loads->where('status', 'delivered')->count(),
        'cancelled' => $loads->where('status', 'cancelled')->count(),
        'total_bids' => $loads->sum(function($load) {
            return $load->bids->count();
        }),
    ];

    $stats['posted_percentage'] = $stats['total'] > 0
        ? round(($stats['posted'] / $stats['total']) * 100)
        : 0;

    $stats['delivered_percentage'] = $stats['total'] > 0
        ? round(($stats['delivered'] / $stats['total']) * 100)
        : 0;

    $countries = $region->countries;

    return view('regional.loads.print', compact('region', 'countries', 'loads', 'stats'));
}



    /**
     * Display the specified load.
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

        $load = Load::with([
            'user',
            'originCountry',
            'destinationCountry',
            'assignedTransporter.user',
            'assignedTransporter.businessProfile',
            'bids.transporter.user',
            'bids.transporter.businessProfile',
            'winningBid',
            'reviews.user'
        ])
            ->where(function($q) use ($countryIds) {
                $q->whereIn('origin_country_id', $countryIds)
                  ->orWhereIn('destination_country_id', $countryIds);
            })
            ->findOrFail($id);

        // Get statistics
        $stats = [
            'total_bids' => $load->bids()->count(),
            'pending_bids' => $load->bids()->where('status', 'pending')->count(),
            'accepted_bids' => $load->bids()->where('status', 'accepted')->count(),
            'rejected_bids' => $load->bids()->where('status', 'rejected')->count(),
        ];

        if ($load->bids->count() > 0) {
            $stats['average_bid'] = $load->bids()->avg('bid_amount');
            $stats['lowest_bid'] = $load->bids()->min('bid_amount');
            $stats['highest_bid'] = $load->bids()->max('bid_amount');
        }

        return view('regional.loads.show', compact('load', 'stats'));
    }
}
