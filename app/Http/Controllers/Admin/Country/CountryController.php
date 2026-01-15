<?php

namespace App\Http\Controllers\Admin\Country;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Region;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    /**
     * Display a listing of the countries.
     */
public function index(Request $request)
{
    $query = Country::with(['region']);

    // Handle search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }

    // Handle status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Handle region filter
    if ($request->filled('region')) {
        $query->where('region_id', $request->region);
    }

    // Handle flag filter
    if ($request->filled('has_flag')) {
        if ($request->has_flag === 'yes') {
            $query->whereNotNull('flag_url')->where('flag_url', '!=', '');
        } else {
            $query->where(function($q) {
                $q->whereNull('flag_url')->orWhere('flag_url', '');
            });
        }
    }

    // Handle sorting
    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');

    switch ($sortBy) {
        case 'name':
            $query->orderBy('name', $sortOrder);
            break;
        case 'status':
            $query->orderBy('status', $sortOrder);
            break;
        default:
            $query->orderBy('created_at', $sortOrder);
    }

    $countries = $query->paginate(15)->withQueryString();

    // Add vendors count for each country after fetching
    $countries->each(function($country) {
        $country->vendors_count = DB::table('vendors')
            ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
            ->where('business_profiles.country_id', $country->id)
            ->whereNull('vendors.deleted_at')
            ->whereNull('business_profiles.deleted_at')
            ->count();
    });

    $regions = Region::all();

    // Calculate statistics
    $total = Country::count();
    $active = Country::where('status', 'active')->count();
    $inactive = Country::where('status', 'inactive')->count();

    // Count total vendors across all countries
    $totalVendors = Vendor::whereHas('businessProfile')->count();

    // Regional stats
    $totalRegions = Region::count();
    $countriesWithFlags = Country::whereNotNull('flag_url')
        ->where('flag_url', '!=', '')
        ->count();

    $avgCountriesPerRegion = $totalRegions > 0
        ? round($total / $totalRegions, 1)
        : 0;

    $stats = [
        'total' => $total,
        'active' => $active,
        'inactive' => $inactive,
        'active_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
        'inactive_percentage' => $total > 0 ? round(($inactive / $total) * 100, 1) : 0,
        'total_vendors' => $totalVendors,
        'total_regions' => $totalRegions,
        'avg_countries_per_region' => $avgCountriesPerRegion,
        'countries_with_flags' => $countriesWithFlags,
        'flags_percentage' => $total > 0 ? round(($countriesWithFlags / $total) * 100, 1) : 0,
    ];

    return view('admin.country.index', compact(
        'countries',
        'regions',
        'stats'
    ));
}

    /**
     * Show the form for creating a new country.
     */
    public function create()
    {
        return view('admin.country.create');
    }

    /**
     * Store a newly created country in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
            'flag_url' => 'nullable|url|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Country name is required.',
            'name.unique' => 'This country already exists.',
            'flag_url.url' => 'Please provide a valid URL for the flag.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
        ]);

        try {
            $country = Country::create($validated);

            Log::info('Country created successfully', [
                'country_id' => $country->id,
                'name' => $country->name,
                'created_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.country.index')
                ->with('success', 'Country created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create country', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create country. Please try again.']);
        }
    }

    /**
     * Display the specified country.
     */
    public function show(Country $country)
    {
        return view('admin.country.show', compact('country'));
    }

    /**
     * Show the form for editing the specified country.
     */
    public function edit(Country $country)
    {
        return view('admin.country.edit', compact('country'));
    }

    /**
     * Update the specified country in storage.
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'flag_url' => 'nullable|url|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Country name is required.',
            'name.unique' => 'This country name is already taken.',
            'flag_url.url' => 'Please provide a valid URL for the flag.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
        ]);

        try {
            $country->update($validated);

            Log::info('Country updated successfully', [
                'country_id' => $country->id,
                'name' => $country->name,
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.country.index')
                ->with('success', 'Country updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update country', [
                'country_id' => $country->id,
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update country. Please try again.']);
        }
    }

    /**
     * Remove the specified country from storage.
     */
    public function destroy(Country $country)
    {
        try {
            $countryName = $country->name;
            $country->delete();

            Log::info('Country deleted successfully', [
                'country_id' => $country->id,
                'name' => $countryName,
                'deleted_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.country.index')
                ->with('success', 'Country deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete country', [
                'country_id' => $country->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to delete country. Please try again.']);
        }
    }

    /**
     * Toggle country status (active/inactive).
     */
    public function toggleStatus(Country $country)
    {
        try {
            $country->status = $country->status === 'active' ? 'inactive' : 'active';
            $country->save();

            Log::info('Country status toggled', [
                'country_id' => $country->id,
                'new_status' => $country->status,
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.country.index')
                ->with('success', 'Country status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to toggle country status', [
                'country_id' => $country->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to update country status. Please try again.']);
        }
    }
}

