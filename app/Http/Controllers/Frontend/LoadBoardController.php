<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Load;
use App\Models\Country;
use Illuminate\Http\Request;

class LoadBoardController extends Controller
{
    /**
     * Main loadboard index with tabs
     */
    public function index($type = 'loads')
    {
        if (!in_array($type, ['cars', 'loads'])) {
            $type = 'loads';
        }

        $countries = Country::where('status', 'active')
            ->orderBy('name')
            ->get();

        if ($type === 'cars') {
            $items = $this->getCarsQuery()->paginate(12);
        } else {
            $items = $this->getLoadsQuery()->paginate(12);
        }

        return view('frontend.loadboard.index', compact('type', 'items', 'countries'));
    }

    /**
     * Cars index page
     */
    public function carsIndex(Request $request)
    {
        $query = $this->getCarsQuery();

        // Filters
        if ($request->filled('from_country')) {
            $query->where('from_country_id', $request->from_country);
        }

        if ($request->filled('to_country')) {
            $query->where(function($q) use ($request) {
                $q->where('to_country_id', $request->to_country)
                  ->orWhere('flexible_destination', true);
            });
        }

        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        if ($request->filled('min_capacity')) {
            $query->where('cargo_capacity', '>=', $request->min_capacity);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('pricing_type')) {
            $query->where('pricing_type', $request->pricing_type);
        }

        $cars = $query->paginate(12)->withQueryString();

        $countries = Country::where('status', 'active')->orderBy('name')->get();

        $vehicleTypes = Car::select('vehicle_type')
            ->distinct()
            ->pluck('vehicle_type');

        return view('frontend.loadboard.cars.index', compact('cars', 'countries', 'vehicleTypes'));
    }

    /**
     * Car detail page
     */
    public function carShow($listing_number)
    {
        $car = Car::where('listing_number', $listing_number)
            ->with(['fromCountry', 'toCountry', 'user'])
            ->firstOrFail();

        // Increment views
        $car->incrementViews();

        // Get similar cars
        $similarCars = Car::where('id', '!=', $car->id)
            ->where('from_country_id', $car->from_country_id)
            ->where('availability_status', 'available')
            ->where('is_verified', true)
            ->limit(4)
            ->get();

        return view('frontend.loadboard.cars.show', compact('car', 'similarCars'));
    }

    /**
     * Loads index page
     */
    public function loadsIndex(Request $request)
    {
        $query = $this->getLoadsQuery();

        // Filters
        if ($request->filled('origin_country')) {
            $query->where('origin_country_id', $request->origin_country);
        }

        if ($request->filled('destination_country')) {
            $query->where('destination_country_id', $request->destination_country);
        }

        if ($request->filled('cargo_type')) {
            $query->where('cargo_type', $request->cargo_type);
        }

        if ($request->filled('min_weight')) {
            $query->where('weight', '>=', $request->min_weight);
        }

        if ($request->filled('pickup_date')) {
            $query->whereDate('pickup_date', '>=', $request->pickup_date);
        }

        $loads = $query->paginate(12)->withQueryString();

        $countries = Country::where('status', 'active')->orderBy('name')->get();

        $cargoTypes = Load::select('cargo_type')
            ->distinct()
            ->pluck('cargo_type');

        return view('frontend.loadboard.loads.index', compact('loads', 'countries', 'cargoTypes'));
    }

    /**
     * Load detail page
     */
    public function loadShow($load_number)
    {
        $load = Load::where('load_number', $load_number)
            ->with(['originCountry', 'destinationCountry', 'user', 'bids'])
            ->firstOrFail();

        // Get bid count
        $bidCount = $load->bids()->count();

        // Get similar loads
        $similarLoads = Load::where('id', '!=', $load->id)
            ->where('origin_country_id', $load->origin_country_id)
            ->whereIn('status', ['posted', 'bidding'])
            ->limit(4)
            ->get();

        return view('frontend.loadboard.loads.show', compact('load', 'bidCount', 'similarLoads'));
    }

    /**
     * Car inquiry (authenticated)
     */
    public function carInquiry(Request $request, Car $car)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'pickup_date' => 'required|date|after:today',
            'delivery_date' => 'required|date|after:pickup_date',
        ]);

        // Create inquiry/booking request
        // You'll need to create a CarInquiry or CarBooking model

        $car->incrementInquiries();

        return redirect()->back()->with('success', 'Your inquiry has been sent successfully!');
    }

    /**
     * Load bid (authenticated)
     */
    public function loadBid(Request $request, Load $load)
    {
        $request->validate([
            'bid_amount' => 'required|numeric|min:0',
            'estimated_delivery_days' => 'required|integer|min:1',
            'proposal' => 'required|string|max:2000',
            'vehicle_type' => 'nullable|string',
            'insurance_coverage' => 'nullable|string',
        ]);

        // Create bid
        $load->bids()->create([
            'bid_number' => 'BID-' . strtoupper(uniqid()),
            'transporter_id' => auth()->id(), // Assuming user is transporter
            'bid_amount' => $request->bid_amount,
            'currency' => $load->currency,
            'estimated_delivery_days' => $request->estimated_delivery_days,
            'proposal' => $request->proposal,
            'vehicle_details' => [
                'type' => $request->vehicle_type,
            ],
            'insurance_details' => [
                'coverage' => $request->insurance_coverage,
            ],
            'status' => 'pending',
            'valid_until' => now()->addDays(7),
        ]);

        return redirect()->back()->with('success', 'Your bid has been submitted successfully!');
    }

    /**
     * Helper: Get cars query with common filters
     */
    private function getCarsQuery()
    {
        return Car::where('availability_status', 'available')
            ->where('is_verified', true)
            ->with(['fromCountry', 'toCountry', 'user'])
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Helper: Get loads query with common filters
     */
    private function getLoadsQuery()
    {
        return Load::whereIn('status', ['posted', 'bidding'])
            ->with(['originCountry', 'destinationCountry', 'user'])
            ->withCount('bids')
            ->orderBy('created_at', 'desc');
    }
}
