<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ManageablePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
$query = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))
             ->with('department')
             ->latest();

if ($search = $request->get('search')) {
    $query->where(function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%");
    });
}

if ($department = $request->get('department_id')) {
    $query->where('department_id', $department);
}

$users = $query->paginate(15)->withQueryString();

$departments = \App\Models\Department::where('is_active', true)->orderBy('name')->get();

$stats = [
    'total'       => User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->count(),
    'active'      => 0,
    'suspended'   => 0,
    'departments' => \App\Models\Department::count(),
];

        return view('admin.users.index', compact('users', 'stats', 'departments'));
    }

    // ─────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────
    public function create()
    {
        $groups = ManageablePermission::GROUPS;
         $departments = \App\Models\Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('groups', 'departments'));
    }

    // ─────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'nullable|string|max:20',
            'password'      => ['required', 'confirmed', Password::min(8)],
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string',
            'department_id'  => 'nullable|exists:departments,id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status'   => 'active',
            'department_id' => $validated['department_id'] ? $validated['department_id'] : null,
        ]);

        // Attach admin role
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $user->roles()->attach($adminRole->id);
        }

        // Build permission record — start all false, then flip granted ones
        $allKeys  = ManageablePermission::allPermissionKeys();
        $granted  = $validated['permissions'] ?? [];
        $permData = array_fill_keys($allKeys, false);

        foreach ($granted as $key) {
            if (array_key_exists($key, $permData)) {
                $permData[$key] = true;
            }
        }

        $permData['group']   = $this->resolveGroup($granted);
        $permData['user_id'] = $user->id;

        ManageablePermission::create($permData);

        \App\Models\Notification::create([
            'title'     => 'Admin Account Created',
            'content'   => 'Your admin account has been created on AfriSellers. You can now log in and manage the platform.',
            'link_url'  => '/admin/dashboard',
            'user_id'   => $user->id,
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        return redirect()->route('admin.manageusers.index')
                         ->with('success', "Admin user '{$user->name}' created successfully.");
    }

    // ─────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────
    public function show(User $user)
    {
        $permission = $user->manageablePermission;
        $groups     = ManageablePermission::GROUPS;
        return view('admin.users.show', compact('user', 'permission', 'groups'));
    }

    // ─────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────
    public function edit(User $user)
    {
        $permission = $user->manageablePermission
                     ?? new ManageablePermission(['user_id' => $user->id]);
        $departments = \App\Models\Department::where('is_active', true)->orderBy('name')->get();
        $groups = ManageablePermission::GROUPS;
        return view('admin.users.edit', compact('user', 'permission', 'groups', 'departments'));
    }

    // ─────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'phone'         => 'nullable|string|max:20',
            'password'      => ['nullable', 'confirmed', Password::min(8)],
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string',
            'department_id'  => 'nullable|exists:departments,id',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'department_id' => $validated['department_id'] ?? $user->department_id,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Update permissions
        $allKeys  = ManageablePermission::allPermissionKeys();
        $granted  = $validated['permissions'] ?? [];
        $permData = array_fill_keys($allKeys, false);
        foreach ($granted as $key) {
            if (array_key_exists($key, $permData)) {
                $permData[$key] = true;
            }
        }
        $permData['group'] = $this->resolveGroup($granted);

ManageablePermission::updateOrCreate(
            ['user_id' => $user->id],
            $permData
        );

        \App\Models\Notification::create([
            'title'     => 'Your Admin Profile Was Updated',
            'content'   => 'Your admin account details and permissions have been updated by a super admin.',
            'link_url'  => '/admin/dashboard',
            'user_id'   => $user->id,
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        return redirect()->route('admin.manageusers.index')
                         ->with('success', "Admin user '{$user->name}' updated successfully.");
    }

    // ─────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────
    public function destroy(User $user)
    {
        \App\Models\Notification::create([
            'title'     => 'Admin Account Removed',
            'content'   => 'Your admin account has been removed from AfriSellers. Contact the super admin if you believe this is a mistake.',
            'link_url'  => null,
            'user_id'   => $user->id,
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        $user->manageablePermission?->delete();
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.manageusers.index')
                         ->with('success', 'Admin user deleted successfully.');
    }

    // ─────────────────────────────────────────────────────────
    // SUSPEND / ACTIVATE
    // ─────────────────────────────────────────────────────────
    public function suspend(User $user)
    {
        $user->update(['status' => 'suspended']);

        \App\Models\Notification::create([
            'title'     => 'Account Suspended',
            'content'   => 'Your admin account has been suspended. Please contact the super admin for assistance.',
            'link_url'  => null,
            'user_id'   => $user->id,
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        return back()->with('success', "'{$user->name}' has been suspended.");
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);

        \App\Models\Notification::create([
            'title'     => 'Account Activated',
            'content'   => 'Your admin account has been reactivated. You now have full access to the AfriSellers admin panel.',
            'link_url'  => '/admin/dashboard',
            'user_id'   => $user->id,
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        return back()->with('success', "'{$user->name}' has been activated.");
    }

    // ─────────────────────────────────────────────────────────
    // PERMISSIONS (standalone page)
    // ─────────────────────────────────────────────────────────
    public function permissions(User $user)
    {
        $permission = $user->manageablePermission
                     ?? new ManageablePermission(['user_id' => $user->id]);
        $groups = ManageablePermission::GROUPS;
        return view('admin.users.permissions', compact('user', 'permission', 'groups'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $allKeys  = ManageablePermission::allPermissionKeys();
        $granted  = $request->input('permissions', []);
        $permData = array_fill_keys($allKeys, false);
        foreach ($granted as $key) {
            if (array_key_exists($key, $permData)) {
                $permData[$key] = true;
            }
        }
        $permData['group'] = $this->resolveGroup($granted);

        ManageablePermission::updateOrCreate(
            ['user_id' => $user->id],
            $permData
        );

        \App\Models\Notification::create([
            'title'     => 'Your Permissions Were Updated',
            'content'   => 'Your admin permissions have been updated by a super admin. Please refresh your session to apply changes.',
            'link_url'  => '/admin/dashboard',
            'user_id'   => $user->id,
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        return back()->with('success', 'Permissions updated successfully.');
    }

    // ─────────────────────────────────────────────────────────
    // PRINT
    // ─────────────────────────────────────────────────────────
    public function print()
    {
        $users = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->latest()->get();
        return view('admin.users.print', compact('users'));
    }

    // ─────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────
    private function resolveGroup(array $grantedKeys): string
    {
        foreach (ManageablePermission::GROUPS as $group => $keys) {
            foreach ($grantedKeys as $granted) {
                if (in_array($granted, $keys, true)) {
                    return $group;
                }
            }
        }
        return 'dashboard';
    }
}
