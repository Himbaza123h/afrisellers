<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegionalAdmin;
use App\Models\Region;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;


class RegionalAdminController extends Controller
{
    /**
     * Display a listing of regional admins.
     */
public function index(Request $request)
{
    $query = RegionalAdmin::with(['user', 'region']);

    // Handle status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Handle search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('user', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Handle region filter
    if ($request->filled('region')) {
        $query->where('region_id', $request->region);
    }

    // Handle sorting
    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');

    switch ($sortBy) {
        case 'name':
            $query->join('users', 'regional_admins.user_id', '=', 'users.id')
                  ->orderBy('users.name', $sortOrder)
                  ->select('regional_admins.*');
            break;
        case 'region':
            $query->join('regions', 'regional_admins.region_id', '=', 'regions.id')
                  ->orderBy('regions.name', $sortOrder)
                  ->select('regional_admins.*');
            break;
        case 'status':
            $query->orderBy('status', $sortOrder);
            break;
        default:
            $query->orderBy('created_at', $sortOrder);
    }

    $regionalAdmins = $query->paginate(15)->withQueryString();
    $regions = Region::all();

    // Calculate statistics
    $total = RegionalAdmin::count();
    $active = RegionalAdmin::where('status', 'active')->count();
    $inactive = RegionalAdmin::where('status', 'inactive')->count();
    $suspended = RegionalAdmin::where('status', 'suspended')->count();

    // Calculate regions coverage
    $regionsCovered = RegionalAdmin::where('status', 'active')
        ->distinct('region_id')
        ->count('region_id');

    $totalRegions = Region::count();
    $unassignedRegions = $totalRegions - $regionsCovered;

    $stats = [
        'total' => $total,
        'active' => $active,
        'inactive' => $inactive,
        'suspended' => $suspended,
        'active_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
        'inactive_percentage' => $total > 0 ? round(($inactive / $total) * 100, 1) : 0,
        'regions_covered' => $regionsCovered,
        'unassigned_regions' => $unassignedRegions,
    ];

    return view('admin.regional-admins.index', compact(
        'regionalAdmins',
        'regions',
        'stats'
    ));
}
    /**
     * Show the form for creating a new regional admin.
     */
    public function create()
    {
        $regions = Region::active()->get();
        $availableRegions = Region::active()
            ->whereDoesntHave('activeAdmin')
            ->get();

        return view('admin.regional-admins.create', compact('regions', 'availableRegions'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', Password::min(8)],
        'region_id' => ['required', 'exists:regions,id'],
        'phone' => ['nullable', 'string', 'max:20'],
    ]);

    try {
        DB::beginTransaction();

        // Check if region already has an active admin
        $existingAdmin = RegionalAdmin::where('region_id', $validated['region_id'])
            ->where('status', 'active')
            ->first();

        if ($existingAdmin) {
            return back()->withErrors(['region_id' => 'This region already has an active administrator.'])->withInput();
        }

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'], // ADD THIS LINE
            'regional_admin' => 1, // ADD THIS LINE
            'regional_id' => $validated['region_id'], // ADD THIS LINE
        ]);

        // Assign regional admin role
        $regionalAdminRole = Role::where('slug', 'regional_admin')->first();
        if ($regionalAdminRole) {
            $user->assignRole($regionalAdminRole);
        }

        // Create regional admin record
        $regionalAdmin = RegionalAdmin::create([
            'user_id' => $user->id,
            'region_id' => $validated['region_id'],
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        DB::commit();

        return redirect()->route('admin.regional-admins.index')
            ->with('success', 'Regional administrator created successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Failed to create regional administrator: ' . $e->getMessage()])->withInput();
    }
}

    /**
     * Display the specified regional admin.
     */
    public function show(RegionalAdmin $regionalAdmin)
    {
        $regionalAdmin->load(['user', 'region.countries']);

        // Get statistics for the region
        $stats = [
            'total_vendors' => $regionalAdmin->region->getTotalVendors(),
            'monthly_revenue' => $regionalAdmin->region->getMonthlyRevenue(),
            'monthly_orders' => $regionalAdmin->region->getMonthlyOrders(),
            'countries_count' => $regionalAdmin->region->countries->count(),
        ];

        // Get recent activities (you can customize this based on your activity log)
        $recentActivities = collect(); // Placeholder for activity log

        return view('admin.regional-admins.show', compact('regionalAdmin', 'stats', 'recentActivities'));
    }

    /**
     * Show the form for editing the specified regional admin.
     */
    public function edit(RegionalAdmin $regionalAdmin)
    {
        $regions = Region::active()->get();
        $availableRegions = Region::active()
            ->where(function($query) use ($regionalAdmin) {
                $query->whereDoesntHave('activeAdmin')
                    ->orWhere('id', $regionalAdmin->region_id);
            })
            ->get();

        return view('admin.regional-admins.edit', compact('regionalAdmin', 'regions', 'availableRegions'));
    }

    /**
     * Update the specified regional admin in storage.
     */
    public function update(Request $request, RegionalAdmin $regionalAdmin)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $regionalAdmin->user_id],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'region_id' => ['required', 'exists:regions,id'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        try {
            DB::beginTransaction();

            // Check if region already has another active admin
            if ($validated['region_id'] != $regionalAdmin->region_id && $validated['status'] == 'active') {
                $existingAdmin = RegionalAdmin::where('region_id', $validated['region_id'])
                    ->where('status', 'active')
                    ->where('id', '!=', $regionalAdmin->id)
                    ->first();

                if ($existingAdmin) {
                    return back()->withErrors(['region_id' => 'This region already has an active administrator.'])->withInput();
                }
            }

            // Update user
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $regionalAdmin->user->update($userData);

            // Update regional admin
            $regionalAdmin->update([
                'region_id' => $validated['region_id'],
                'status' => $validated['status'],
            ]);

            DB::commit();

            return redirect()->route('admin.regional-admins.index')
                ->with('success', 'Regional administrator updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update regional administrator: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified regional admin from storage.
     */
    public function destroy(RegionalAdmin $regionalAdmin)
    {
        try {
            DB::beginTransaction();

            // Soft delete the regional admin
            $regionalAdmin->delete();

            // Optionally, you might want to remove the regional-admin role
            $regionalAdminRole = Role::where('slug', 'regional-admin')->first();
            if ($regionalAdminRole) {
                $regionalAdmin->user->removeRole($regionalAdminRole);
            }

            DB::commit();

            return redirect()->route('admin.regional-admins.index')
                ->with('success', 'Regional administrator removed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to remove regional administrator: ' . $e->getMessage()]);
        }
    }

    /**
     * Activate a regional admin.
     */
    public function activate(RegionalAdmin $regionalAdmin)
    {
        try {
            // Check if region already has an active admin
            $existingAdmin = RegionalAdmin::where('region_id', $regionalAdmin->region_id)
                ->where('status', 'active')
                ->where('id', '!=', $regionalAdmin->id)
                ->first();

            if ($existingAdmin) {
                return back()->withErrors(['error' => 'This region already has an active administrator.']);
            }

            $regionalAdmin->activate();

            return back()->with('success', 'Regional administrator activated successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to activate regional administrator: ' . $e->getMessage()]);
        }
    }

    /**
     * Deactivate a regional admin.
     */
    public function deactivate(RegionalAdmin $regionalAdmin)
    {
        try {
            $regionalAdmin->deactivate();

            return back()->with('success', 'Regional administrator deactivated successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to deactivate regional administrator: ' . $e->getMessage()]);
        }
    }

    /**
     * Suspend a regional admin.
     */
    public function suspend(RegionalAdmin $regionalAdmin)
    {
        try {
            $regionalAdmin->suspend();

            return back()->with('success', 'Regional administrator suspended successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to suspend regional administrator: ' . $e->getMessage()]);
        }
    }





    /**
     * Show the form for assigning/editing a regional admin user
     */
    public function showAssignRegionalUser(RegionalAdmin $regionalAdmin)
    {
        // Check if regional admin already has a user assigned
        $assignedUser = $regionalAdmin->user;

        return view('admin.regional-admins.assign-regional-user', compact('regionalAdmin', 'assignedUser'));
    }

    /**
     * Assign or update a regional admin user
     */
    public function assignRegionalUser(Request $request, RegionalAdmin $regionalAdmin)
    {
        $assignedUser = $regionalAdmin->user;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];

        // Email validation - FIXED: Exclude current user's email from uniqueness check
        $rules['email'] = [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users')->where(function ($query) use ($regionalAdmin) {
                return $query->where('regional_id', '!=', $regionalAdmin->region_id);
            })
        ];

        // Password validation
        if (!$assignedUser) {
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
        } else {
            $rules['password'] = ['nullable', 'confirmed', Password::min(8)];
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($assignedUser) {
                // Update existing user
                $updateData = [
                    'name' => $request->name,
                    'phone' => $request->phone,
                ];

                // Update email if it changed
                if ($request->email !== $assignedUser->email) {
                    $updateData['email'] = $request->email;
                }

                $assignedUser->update($updateData);

                // Update password only if provided
                if ($request->filled('password')) {
                    $assignedUser->update([
                        'password' => Hash::make($request->password),
                    ]);
                }

                $message = 'Regional Admin updated successfully!';
            } else {
                // Create new regional admin user
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => now(),
                    'regional_admin' => true,
                    'country_admin' => false,
                    'agent' => false,
                    'regional_id' => $regionalAdmin->region_id,
                    'country_id' => null,
                ]);

                // Assign role
                $role = Role::where('slug', 'regional_admin')->first();

                if ($role) {
                    $user->roles()->attach($role->id);
                }

                // Link to regional admin
                $regionalAdmin->update([
                    'user_id' => $user->id,
                ]);

                $message = 'Regional Admin created successfully!';
            }

            DB::commit();

            return redirect()
                ->route('admin.regional-admins.show', $regionalAdmin)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to save regional admin: ' . $e->getMessage()]);
        }
    }

}
