<?php

namespace App\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use App\Models\Load;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoadController extends Controller
{
    /**
     * Display a listing of loads in the country admin's country.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

        $query = Load::with([
            'user',
            'originCountry',
            'destinationCountry',
            'assignedTransporter.user',
            'bids',
            'winningBid'
        ])
            ->where(function($q) use ($user) {
                $q->where('origin_country_id', $user->country_id)
                  ->orWhere('destination_country_id', $user->country_id);
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
        $baseQuery = Load::where(function($q) use ($user) {
            $q->where('origin_country_id', $user->country_id)
              ->orWhere('destination_country_id', $user->country_id);
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

        // Get cities for filters
        $originCities = Load::where(function($q) use ($user) {
                $q->where('origin_country_id', $user->country_id)
                  ->orWhere('destination_country_id', $user->country_id);
            })
            ->select('origin_city')
            ->distinct()
            ->whereNotNull('origin_city')
            ->orderBy('origin_city')
            ->pluck('origin_city');

        $destinationCities = Load::where(function($q) use ($user) {
                $q->where('origin_country_id', $user->country_id)
                  ->orWhere('destination_country_id', $user->country_id);
            })
            ->select('destination_city')
            ->distinct()
            ->whereNotNull('destination_city')
            ->orderBy('destination_city')
            ->pluck('destination_city');

        return view('country.loads.index', compact(
            'loads',
            'stats',
            'originCities',
            'destinationCities'
        ));
    }

    /**
     * Display the specified load.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Get country admin's country
        if (!$user->country_admin || !$user->country_id) {
            abort(403, 'You are not assigned to any country.');
        }

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
            ->where(function($q) use ($user) {
                $q->where('origin_country_id', $user->country_id)
                  ->orWhere('destination_country_id', $user->country_id);
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

        return view('country.loads.show', compact('load', 'stats'));
    }
}
