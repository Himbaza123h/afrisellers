<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tradeshow;
use App\Models\Country;
use Illuminate\Http\Request;

class TradeshowController extends Controller
{
    /**
     * Display tradeshows listing
     */
    public function index(Request $request)
    {
        $query = Tradeshow::where('status', 'published')
            ->with(['country', 'user']);

        // Filters
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('industry')) {
            $query->where('industry', $request->industry);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        // Default: show upcoming tradeshows
        if (!$request->filled('show_all')) {
            $query->where('start_date', '>=', now());
        }

        $tradeshows = $query->orderBy('start_date')->paginate(12)->withQueryString();

        $countries = Country::where('status', 'active')->orderBy('name')->get();

        $industries = Tradeshow::select('industry')
            ->distinct()
            ->whereNotNull('industry')
            ->pluck('industry');

        return view('frontend.tradeshows.index', compact('tradeshows', 'countries', 'industries'));
    }

    /**
     * Display single tradeshow
     */
    public function show(Tradeshow $tradeshow)
    {
        $tradeshow->load(['country', 'user']);

        // Increment views
        $tradeshow->increment('views_count');

        // Get similar tradeshows
        $similarTradeshows = Tradeshow::where('id', '!=', $tradeshow->id)
            ->where('status', 'published')
            ->where(function($query) use ($tradeshow) {
                $query->where('country_id', $tradeshow->country_id)
                      ->orWhere('industry', $tradeshow->industry);
            })
            ->where('start_date', '>=', now())
            ->limit(3)
            ->get();

        return view('frontend.tradeshows.show', compact('tradeshow', 'similarTradeshows'));
    }



    /**
     * Register for tradeshow (authenticated)
     */
    public function register(Request $request, Tradeshow $tradeshow)
    {
        $request->validate([
            'registration_type' => 'required|in:visitor,exhibitor',
            'company_name' => 'nullable|string|max:255',
            'attendees_count' => 'nullable|integer|min:1',
            'booth_preference' => 'nullable|string',
        ]);

        // Create registration (you'll need a TradeshowRegistration model)

        $tradeshow->increment('bookings_count');

        return redirect()->back()->with('success', 'Registration submitted successfully!');
    }

    /**
     * Send inquiry (authenticated)
     */
    public function inquiry(Request $request, Tradeshow $tradeshow)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Create inquiry (you'll need a TradeshowInquiry model)

        $tradeshow->increment('inquiries_count');

        return redirect()->back()->with('success', 'Your inquiry has been sent!');
    }
}
