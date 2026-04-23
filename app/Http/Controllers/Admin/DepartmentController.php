<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::withCount('users')->withTrashed();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->get('trashed') === 'only') {
            $query->onlyTrashed();
        } elseif ($request->get('trashed') !== 'with') {
            $query->withoutTrashed();
        }

        $departments = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'   => Department::count(),
            'active'  => Department::where('is_active', true)->count(),
            'trashed' => Department::onlyTrashed()->count(),
        ];

        return view('admin.departments.index', compact('departments', 'stats'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:7',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['color']     = $validated['color'] ?? '#6366f1';

        Department::create($validated);

        return redirect()->route('admin.departments.index')
                         ->with('success', "Department '{$validated['name']}' created successfully.");
    }

    public function show(Department $department)
    {
        $users = $department->users()
                            ->whereHas('roles', fn($q) => $q->where('slug', 'admin'))
                            ->latest()
                            ->paginate(10);

        return view('admin.departments.show', compact('department', 'users'));
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:7',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $department->update($validated);

        return redirect()->route('admin.departments.index')
                         ->with('success', "Department '{$department->name}' updated successfully.");
    }

    public function destroy(Department $department)
    {
        // Unassign all users before soft deleting
        $department->users()->update(['department_id' => null]);
        $department->delete();

        return redirect()->route('admin.departments.index')
                         ->with('success', "Department '{$department->name}' deleted. Users have been unassigned.");
    }

    public function restore($id)
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        $department->restore();

        return redirect()->route('admin.departments.index')
                         ->with('success', "Department '{$department->name}' restored.");
    }

    public function forceDelete($id)
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        $name = $department->name;
        $department->forceDelete();

        return redirect()->route('admin.departments.index')
                         ->with('success', "Department '{$name}' permanently deleted.");
    }

    public function removeUser(Department $department, User $user)
    {
        $user->update(['department_id' => null]);

        return back()->with('success', "{$user->name} removed from {$department->name}.");
    }
}
