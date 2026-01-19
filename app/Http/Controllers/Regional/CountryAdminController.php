<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Country;
use App\Models\Role;

class CountryAdminController extends Controller
{
    /**
     * Display a listing of country admins in the region
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->regionalAdmin) {
            abort(403, 'You are not authorized to access this page.');
        }

        $regionalAdmin = $user->regionalAdmin;
        $region = $regionalAdmin->region;

        // Get all countries in this region
        $countryIds = $region->countries->pluck('id')->toArray();

        // Query country admins (users with country_admin = 1)
        $query = User::where('country_admin', 1)
            ->whereIn('country_id', $countryIds)
            ->with('country');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } else {
                $query->onlyTrashed();
            }
        }

        $countryAdmins = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total' => User::where('country_admin', 1)->whereIn('country_id', $countryIds)->count(),
            'active' => User::where('country_admin', 1)->whereIn('country_id', $countryIds)->whereNull('deleted_at')->count(),
            'inactive' => User::where('country_admin', 1)->whereIn('country_id', $countryIds)->onlyTrashed()->count(),
        ];

        $countries = $region->countries;

        return view('regional.country-admins.index', compact('countryAdmins', 'stats', 'countries', 'region'));
    }

    /**
     * Show the form for creating a new country admin
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->regionalAdmin) {
            abort(403, 'You are not authorized to access this page.');
        }

        $regionalAdmin = $user->regionalAdmin;
        $region = $regionalAdmin->region;

        // Get countries in this region that don't have an active admin
        $availableCountries = Country::where('region_id', $region->id)
            ->whereDoesntHave('countryAdmin')
            ->get();

        return view('regional.country-admins.create', compact('availableCountries', 'region'));
    }

    /**
     * Store a newly created country admin
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->regionalAdmin) {
            abort(403, 'You are not authorized to access this page.');
        }

        $regionalAdmin = $user->regionalAdmin;
        $region = $regionalAdmin->region;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'country_id' => ['required', 'exists:countries,id'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            DB::beginTransaction();

            // Verify country belongs to this region
            $country = Country::where('id', $validated['country_id'])
                ->where('region_id', $region->id)
                ->firstOrFail();

            // Check if country already has an active admin
            $existingAdmin = User::where('country_id', $validated['country_id'])
                ->where('country_admin', 1)
                ->whereNull('deleted_at')
                ->first();

            if ($existingAdmin) {
                return back()->withErrors(['country_id' => 'This country already has an active administrator.'])->withInput();
            }

            // Create user
            $newUser = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'country_admin' => 1,
                'country_id' => $validated['country_id'],
            ]);

            // Assign country admin role
            $countryAdminRole = Role::where('slug', 'country_admin')->first();
            if ($countryAdminRole) {
                $newUser->assignRole($countryAdminRole);
            }

            DB::commit();

            return redirect()->route('regional.country-admins.index')
                ->with('success', 'Country administrator created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create country administrator: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing a country admin
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->regionalAdmin) {
            abort(403, 'You are not authorized to access this page.');
        }

        $regionalAdmin = $user->regionalAdmin;
        $region = $regionalAdmin->region;

        $countryAdmin = User::where('country_admin', 1)
            ->whereHas('country', function($q) use ($region) {
                $q->where('region_id', $region->id);
            })
            ->with('country')
            ->findOrFail($id);

        $countries = $region->countries;

        return view('regional.country-admins.edit', compact('countryAdmin', 'countries', 'region'));
    }

    /**
     * Update the specified country admin
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->regionalAdmin) {
            abort(403, 'You are not authorized to access this page.');
        }

        $regionalAdmin = $user->regionalAdmin;
        $region = $regionalAdmin->region;

        $countryAdmin = User::where('country_admin', 1)
            ->whereHas('country', function($q) use ($region) {
                $q->where('region_id', $region->id);
            })
            ->with('country')
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        try {
            DB::beginTransaction();

            // Update user details
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $countryAdmin->update($updateData);

            DB::commit();

            return redirect()->route('regional.country-admins.index')
                ->with('success', 'Country administrator updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update country administrator: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified country admin
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->regionalAdmin) {
            abort(403, 'You are not authorized to access this page.');
        }

        $regionalAdmin = $user->regionalAdmin;
        $region = $regionalAdmin->region;

        $countryAdmin = User::where('country_admin', 1)
            ->whereHas('country', function($q) use ($region) {
                $q->where('region_id', $region->id);
            })
            ->findOrFail($id);

        try {
            // Soft delete the user
            $countryAdmin->delete();

            return redirect()->route('regional.country-admins.index')
                ->with('success', 'Country administrator deleted successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete country administrator: ' . $e->getMessage()]);
        }
    }
}
