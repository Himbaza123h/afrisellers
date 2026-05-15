<?php

namespace App\Http\Controllers\Admin\Country;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::with(['region']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('region')) {
            $query->where('region_id', $request->region);
        }

        if ($request->filled('has_flag')) {
            if ($request->has_flag === 'yes') {
                $query->whereNotNull('flag_url')->where('flag_url', '!=', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('flag_url')->orWhere('flag_url', '');
                });
            }
        }

        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        match ($sortBy) {
            'name'   => $query->orderBy('name', $sortOrder),
            'status' => $query->orderBy('status', $sortOrder),
            default  => $query->orderBy('created_at', $sortOrder),
        };

        $countries = $query->paginate(10)->withQueryString();

        $countries->each(function ($country) {
            $country->vendors_count = DB::table('vendors')
                ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
                ->where('business_profiles.country_id', $country->id)
                ->whereNull('vendors.deleted_at')
                ->whereNull('business_profiles.deleted_at')
                ->count();
        });

        $regions = Region::orderBy('name')->get();

        $total  = Country::count();
        $active = Country::where('status', 'active')->count();
        $inactive = Country::where('status', 'inactive')->count();

        $totalVendors         = Vendor::whereHas('businessProfile')->count();
        $totalRegions         = Region::count();
        $countriesWithFlags   = Country::whereNotNull('flag_url')->where('flag_url', '!=', '')->count();
        $avgCountriesPerRegion = $totalRegions > 0 ? round($total / $totalRegions, 1) : 0;

        $stats = [
            'total'                   => $total,
            'active'                  => $active,
            'inactive'                => $inactive,
            'active_percentage'       => $total > 0 ? round(($active / $total) * 100, 1) : 0,
            'inactive_percentage'     => $total > 0 ? round(($inactive / $total) * 100, 1) : 0,
            'total_vendors'           => $totalVendors,
            'total_regions'           => $totalRegions,
            'avg_countries_per_region'=> $avgCountriesPerRegion,
            'countries_with_flags'    => $countriesWithFlags,
            'flags_percentage'        => $total > 0 ? round(($countriesWithFlags / $total) * 100, 1) : 0,
        ];

        return view('admin.country.index', compact('countries', 'regions', 'stats'));
    }

    public function create()
    {
        $regions = Region::orderBy('name')->get();
        return view('admin.country.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:countries,name',
            'code'      => 'nullable|string|max:3',
            'flag_url'  => 'nullable|url|max:500',
            'region_id' => 'nullable|exists:regions,id',
            'status'    => 'required|in:active,inactive',
        ], [
            'name.required' => 'Country name is required.',
            'name.unique'   => 'This country already exists.',
            'flag_url.url'  => 'Please provide a valid URL for the flag.',
            'region_id.exists' => 'Selected region does not exist.',
            'status.required'  => 'Status is required.',
            'status.in'        => 'Status must be either active or inactive.',
        ]);

        try {
            $country = Country::create($validated);

            \App\Models\Notification::create([
                'title'      => 'New Country Added',
                'content'    => $country->name . ' has been added to AfriSellers. Configure it to start accepting vendors.',
                'link_url'   => '/admin/countries/' . $country->id,
                'user_id'    => auth()->id(),
                'vendor_id'  => null,
                'country_id' => $country->id,
                'is_read'    => false,
            ]);

            Log::info('Country created', [
                'country_id' => $country->id,
                'name'       => $country->name,
                'created_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.country.index')
                ->with('success', 'Country "' . $country->name . '" created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create country', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create country. Please try again.']);
        }
    }

    public function show(Country $country)
    {
        $country->load('region');
        return view('admin.country.show', compact('country'));
    }

    public function edit(Country $country)
    {
        $regions = Region::orderBy('name')->get();
        return view('admin.country.edit', compact('country', 'regions'));
    }

    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:countries,name,' . $country->id,
            'code'      => 'nullable|string|max:3',
            'flag_url'  => 'nullable|url|max:500',
            'region_id' => 'nullable|exists:regions,id',
            'status'    => 'required|in:active,inactive',
        ], [
            'name.required'    => 'Country name is required.',
            'name.unique'      => 'This country name is already taken.',
            'flag_url.url'     => 'Please provide a valid URL for the flag.',
            'region_id.exists' => 'Selected region does not exist.',
            'status.required'  => 'Status is required.',
            'status.in'        => 'Status must be either active or inactive.',
        ]);

        try {
            $country->update($validated);

            $countryAdmin = \App\Models\User::where('country_id', $country->id)
                ->where('country_admin', true)->first();

            if ($countryAdmin) {
                \App\Models\Notification::create([
                    'title'      => 'Country Profile Updated',
                    'content'    => 'The profile for ' . $country->name . ' has been updated by the admin.',
                    'link_url'   => '/country-admin/dashboard',
                    'user_id'    => $countryAdmin->id,
                    'vendor_id'  => null,
                    'country_id' => $country->id,
                    'is_read'    => false,
                ]);
            }

            Log::info('Country updated', [
                'country_id' => $country->id,
                'name'       => $country->name,
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.country.index')
                ->with('success', 'Country "' . $country->name . '" updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update country', ['country_id' => $country->id, 'error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to update country. Please try again.']);
        }
    }

    public function destroy(Country $country)
    {
        try {
            $countryName  = $country->name;
            $countryAdmin = \App\Models\User::where('country_id', $country->id)
                ->where('country_admin', true)->first();

            if ($countryAdmin) {
                \App\Models\Notification::create([
                    'title'      => 'Country Removed',
                    'content'    => $countryName . ' has been removed from the AfriSellers platform.',
                    'link_url'   => null,
                    'user_id'    => $countryAdmin->id,
                    'vendor_id'  => null,
                    'country_id' => null,
                    'is_read'    => false,
                ]);
            }

            $country->delete();

            Log::info('Country deleted', [
                'country_id' => $country->id,
                'name'       => $countryName,
                'deleted_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.country.index')
                ->with('success', 'Country "' . $countryName . '" deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete country', ['country_id' => $country->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete country. Please try again.']);
        }
    }

    public function toggleStatus(Country $country)
    {
        try {
            $country->status = $country->status === 'active' ? 'inactive' : 'active';
            $country->save();

            $countryAdmin = \App\Models\User::where('country_id', $country->id)
                ->where('country_admin', true)->first();

            if ($countryAdmin) {
                \App\Models\Notification::create([
                    'title'      => 'Country Status Changed',
                    'content'    => $country->name . ' has been ' . $country->status . '. ' .
                        ($country->status === 'active' ? 'You can now manage this country.' : 'Contact admin for more information.'),
                    'link_url'   => $country->status === 'active' ? '/country-admin/dashboard' : null,
                    'user_id'    => $countryAdmin->id,
                    'vendor_id'  => null,
                    'country_id' => $country->id,
                    'is_read'    => false,
                ]);
            }

            Log::info('Country status toggled', [
                'country_id' => $country->id,
                'new_status' => $country->status,
                'updated_by' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.country.index')
                ->with('success', 'Country "' . $country->name . '" is now ' . $country->status . '.');
        } catch (\Exception $e) {
            Log::error('Failed to toggle country status', ['country_id' => $country->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to update country status. Please try again.']);
        }
    }

    public function print()
    {
        $countries = Country::with(['region'])->get();

        $total    = Country::count();
        $active   = Country::where('status', 'active')->count();
        $inactive = Country::where('status', 'inactive')->count();

        $totalVendors          = Vendor::whereHas('businessProfile')->count();
        $totalRegions          = Region::count();
        $countriesWithFlags    = Country::whereNotNull('flag_url')->where('flag_url', '!=', '')->count();
        $avgCountriesPerRegion = $totalRegions > 0 ? round($total / $totalRegions, 1) : 0;

        $countries->each(function ($country) {
            $country->vendors_count = DB::table('vendors')
                ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
                ->where('business_profiles.country_id', $country->id)
                ->whereNull('vendors.deleted_at')
                ->whereNull('business_profiles.deleted_at')
                ->count();
        });

        $stats = [
            'total'                    => $total,
            'active'                   => $active,
            'inactive'                 => $inactive,
            'active_percentage'        => $total > 0 ? round(($active / $total) * 100, 1) : 0,
            'inactive_percentage'      => $total > 0 ? round(($inactive / $total) * 100, 1) : 0,
            'total_vendors'            => $totalVendors,
            'total_regions'            => $totalRegions,
            'avg_countries_per_region' => $avgCountriesPerRegion,
            'countries_with_flags'     => $countriesWithFlags,
            'flags_percentage'         => $total > 0 ? round(($countriesWithFlags / $total) * 100, 1) : 0,
        ];

        return view('admin.country.print', compact('countries', 'stats'));
    }

    public function switchToCountry(Country $country)
    {
        try {
            $countryAdmin = User::where('country_id', $country->id)
                ->where('country_admin', true)
                ->first();

            if (!$countryAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Country Admin assigned to this country yet.',
                ], 404);
            }

            $countryAdminRole = Role::where('slug', 'country_admin')->first();

            if (!$countryAdminRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'Country Admin role not found in system.',
                ], 404);
            }

            if (!$countryAdmin->roles()->where('role_id', $countryAdminRole->id)->exists()) {
                $countryAdmin->roles()->attach($countryAdminRole->id);
            }

            $token = \Illuminate\Support\Str::random(60);
            \Illuminate\Support\Facades\Cache::put(
                'country_login_token_' . $token,
                $countryAdmin->id,
                now()->addMinutes(5)
            );

            return response()->json([
                'success'   => true,
                'message'   => 'Ready to switch to Country Admin Dashboard',
                'login_url' => route('auth.country.token-login', ['token' => $token]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to switch: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showAssignCountryAdmin(Country $country)
    {
        $countryAdmin = User::where('country_id', $country->id)
            ->where('country_admin', true)
            ->first();

        return view('admin.country.assign-country-admin', compact('country', 'countryAdmin'));
    }

    public function assignCountryAdmin(Request $request, Country $country)
    {
        $countryAdmin = User::where('country_id', $country->id)
            ->where('country_admin', true)
            ->first();

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users')->where(fn ($q) => $q->where('country_id', '!=', $country->id)),
            ],
        ];

        $rules['password'] = $countryAdmin
            ? ['nullable', 'confirmed', Password::min(8)]
            : ['required', 'confirmed', Password::min(8)];

        $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($countryAdmin) {
                $updateData = ['name' => $request->name, 'phone' => $request->phone];
                if ($request->email !== $countryAdmin->email) {
                    $updateData['email'] = $request->email;
                }
                $countryAdmin->update($updateData);
                if ($request->filled('password')) {
                    $countryAdmin->update(['password' => Hash::make($request->password)]);
                }
                $message     = 'Country Admin updated successfully!';
                $notifyUser  = $countryAdmin;
            } else {
                $user = User::create([
                    'name'              => $request->name,
                    'email'             => $request->email,
                    'phone'             => $request->phone,
                    'password'          => Hash::make($request->password),
                    'email_verified_at' => now(),
                    'regional_admin'    => false,
                    'country_admin'     => true,
                    'agent'             => false,
                    'regional_id'       => $country->region_id ?? null,
                    'country_id'        => $country->id,
                ]);

                $role = Role::where('slug', 'country_admin')->first();
                if ($role) {
                    $user->roles()->attach($role->id);
                }

                $message    = 'Country Admin created successfully!';
                $notifyUser = $user;
            }

            DB::commit();

            \App\Models\Notification::create([
                'title'      => $countryAdmin ? 'Your Account Was Updated' : 'Country Admin Account Created',
                'content'    => $countryAdmin
                    ? 'Your country administrator account for ' . $country->name . ' has been updated by the admin.'
                    : 'Your country administrator account has been created. You can now manage ' . $country->name . ' on AfriSellers.',
                'link_url'   => '/country-admin/dashboard',
                'user_id'    => $notifyUser->id,
                'vendor_id'  => null,
                'country_id' => $country->id,
                'is_read'    => false,
            ]);

            return redirect()
                ->route('admin.country.show', $country)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to save country admin: ' . $e->getMessage()]);
        }
    }
}
