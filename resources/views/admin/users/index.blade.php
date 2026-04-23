@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print { .no-print { display: none !important; } }
</style>
@endpush

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Admin Users</h1>
            <p class="mt-1 text-xs text-gray-500">Manage admin accounts and their permissions</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('admin.manageusers.print') }}','_blank')"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i><span>Print</span>
            </button>
            <a href="{{ route('admin.manageusers.create') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm">
                <i class="fas fa-plus"></i><span>Add Admin User</span>
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3 no-print">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-sm">
    <div class="stat-card bg-white rounded-lg border border-gray-200 p-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-users text-blue-600"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Total Admins</p>
            <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
    </div>
    <div class="stat-card bg-white rounded-lg border border-gray-200 p-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-building text-violet-600"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Departments</p>
            <p class="text-xl font-bold text-gray-900">{{ $stats['departments'] }}</p>
        </div>
    </div>
</div>

    {{-- Filters --}}
<div class="bg-white rounded-lg border border-gray-200 p-3 no-print">
    <form method="GET" class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[180px]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name or email…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
        </div>
        <select name="department_id"
                class="py-2 pl-3 pr-8 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-[#ff0808] text-white text-sm rounded-lg hover:bg-red-700">Filter</button>
        @if(request()->hasAny(['search', 'department_id']))
            <a href="{{ route('admin.manageusers.index') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Clear</a>
        @endif
    </form>
</div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">#</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Name</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Email</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Permissions</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Department</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Joined</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#ff0808] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-900 text-xs">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @php
                                $perm = $user->manageablePermission;
                                $granted = $perm
                                    ? collect(\App\Models\ManageablePermission::allPermissionKeys())
                                        ->filter(fn($k) => $perm->$k ?? false)->count()
                                    : 0;
                                $total = count(\App\Models\ManageablePermission::allPermissionKeys());
                            @endphp
                            <span class="text-xs text-gray-600">
                                {{ $granted }}/{{ $total }}
                                <span class="text-gray-400">permissions</span>
                            </span>
                        </td>
                        <td class="px-4 py-3">
    @if($user->department)
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
              style="background-color: {{ $user->department->color }}22; color: {{ $user->department->color }}">
            {{ $user->department->name }}
        </span>
    @else
        <span class="text-xs text-gray-400">—</span>
    @endif
</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 no-print">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.manageusers.show', $user) }}"
                                   title="View"
                                   class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.manageusers.permissions', $user) }}"
                                   title="Permissions"
                                   class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                                    <i class="fas fa-shield-alt text-xs"></i>
                                </a>
                                <a href="{{ route('admin.manageusers.edit', $user) }}"
                                   title="Edit"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.manageusers.destroy', $user) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Delete"
                                            class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            onclick="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-500">No admin users found</p>
                                <a href="{{ route('admin.manageusers.create') }}" class="text-xs text-[#ff0808] hover:underline">Create the first one</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 no-print">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
