<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddonController extends Controller
{
    /**
     * Display a listing of addons.
     */
    public function index(Request $request)
    {
        $query = Addon::with(['country', 'addonUsers']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('locationX', 'like', "%{$search}%")
                  ->orWhere('locationY', 'like', "%{$search}%")
                  ->orWhereHas('country', function($countryQuery) use ($search) {
                      $countryQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Country filter
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Location X filter
        if ($request->filled('locationX')) {
            $query->where('locationX', $request->locationX);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $addons = $query->paginate(15);

        // Calculate statistics
        $stats = [
            'total' => Addon::count(),
            'global' => Addon::whereNull('country_id')->count(),
            'country_specific' => Addon::whereNotNull('country_id')->count(),
            'active_subscriptions' => Addon::whereHas('activeAddonUsers')->count(),
        ];

        $countries = Country::where('status', 'active')->orderBy('name')->get();

        return view('admin.addons.index', compact('addons', 'stats', 'countries'));
    }

    /**
     * Show the form for creating a new addon.
     */
    public function create()
    {
        $countries = Country::where('status', 'active')->orderBy('name')->get();

        $locations = [
            'Homepage' => ['herosection', 'featuredsuppliers', 'trendingproducts'],
            'Products' => ['sidebar', 'top_banner', 'bottom_banner'],
            'Suppliers' => ['featured_section', 'directory_top'],
            'Showrooms' => ['featured_section', 'top_banner'],
            'Tradeshows' => ['featured_section', 'upcoming_events'],
        ];

        return view('admin.addons.create', compact('countries', 'locations'));
    }

    /**
     * Store a newly created addon.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'nullable|exists:countries,id',
            'locationX' => 'required|string|max:255',
            'locationY' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:999999.99',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check for duplicate
        $exists = Addon::where('locationX', $request->locationX)
            ->where('locationY', $request->locationY)
            ->where('country_id', $request->country_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'An addon with this location already exists for the selected country.')->withInput();
        }

        $addon = Addon::create([
            'country_id' => $request->country_id,
            'locationX' => $request->locationX,
            'locationY' => $request->locationY,
            'price' => $request->price,
        ]);

        return redirect()->route('admin.addons.index')
            ->with('success', 'Addon created successfully!');
    }

    public function print()
{
    // Get all addons for print (no pagination) with necessary relationships
    $addons = Addon::with(['country', 'addonUsers'])
        ->latest()
        ->get();

    // Calculate statistics
    $stats = [
        'total' => Addon::count(),
        'global' => Addon::whereNull('country_id')->count(),
        'country_specific' => Addon::whereNotNull('country_id')->count(),
        'active_subscriptions' => Addon::whereHas('activeAddonUsers')->count(),
        'total_revenue' => $addons->sum(function($addon) {
            // Count only paid subscriptions (those with paid_at)
            $paidCount = $addon->addonUsers->whereNotNull('paid_at')->count();
            return $addon->price * $paidCount;
        }),
    ];

    return view('admin.addons.print', compact('addons', 'stats'));
}

    /**
     * Display the specified addon.
     */
    public function show($id)
    {
        $addon = Addon::with(['country', 'addonUsers.user', 'addonUsers.product', 'addonUsers.supplier'])->findOrFail($id);

        $stats = [
            'total_subscriptions' => $addon->addonUsers()->count(),
            'active_subscriptions' => $addon->activeAddonUsers()->count(),
            'total_revenue' => $addon->addonUsers()->whereNotNull('paid_at')->count() * $addon->price,
        ];

        return view('admin.addons.show', compact('addon', 'stats'));
    }

    /**
     * Show the form for editing the specified addon.
     */
    public function edit($id)
    {
        $addon = Addon::with('country')->findOrFail($id);
        $countries = Country::where('status', 'active')->orderBy('name')->get();

        $locations = [
            'Homepage' => ['herosection', 'featuredsuppliers', 'trendingproducts'],
            'Products' => ['sidebar', 'top_banner', 'bottom_banner'],
            'Suppliers' => ['featured_section', 'directory_top'],
            'Showrooms' => ['featured_section', 'top_banner'],
            'Tradeshows' => ['featured_section', 'upcoming_events'],
        ];

        return view('admin.addons.edit', compact('addon', 'countries', 'locations'));
    }

    /**
     * Update the specified addon.
     */
    public function update(Request $request, $id)
    {
        $addon = Addon::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'country_id' => 'nullable|exists:countries,id',
            'locationX' => 'required|string|max:255',
            'locationY' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:999999.99',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check for duplicate (excluding current addon)
        $exists = Addon::where('locationX', $request->locationX)
            ->where('locationY', $request->locationY)
            ->where('country_id', $request->country_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'An addon with this location already exists for the selected country.')->withInput();
        }

        $addon->update([
            'country_id' => $request->country_id,
            'locationX' => $request->locationX,
            'locationY' => $request->locationY,
            'price' => $request->price,
        ]);

        return redirect()->route('admin.addons.index')
            ->with('success', 'Addon updated successfully!');
    }

    /**
     * Remove the specified addon.
     */
    public function destroy($id)
    {
        $addon = Addon::findOrFail($id);

        // Check if addon has active subscriptions
        if ($addon->activeAddonUsers()->count() > 0) {
            return back()->with('error', 'Cannot delete addon with active subscriptions.');
        }

        $addon->delete();

        return back()->with('success', 'Addon deleted successfully!');
    }
}
