@extends('layouts.home')
@section('page-content')
<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Departments</h1>
            <p class="mt-1 text-xs text-gray-500">Organise admin users into departments</p>
        </div>
        <a href="{{ route('admin.departments.create') }}"
           class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm">
            <i class="fas fa-plus"></i><span>New Department</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 max-w-lg">
        <div class="bg-white rounded-lg border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-violet-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-violet-600 text-sm"></i>
            </div>
            <div><p class="text-xs text-gray-500">Total</p><p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p></div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check text-green-600 text-sm"></i>
            </div>
            <div><p class="text-xs text-gray-500">Active</p><p class="text-lg font-bold text-gray-900">{{ $stats['active'] }}</p></div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-trash text-red-600 text-sm"></i>
            </div>
            <div><p class="text-xs text-gray-500">Deleted</p><p class="text-lg font-bold text-gray-900">{{ $stats['trashed'] }}</p></div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-lg border border-gray-200 p-3">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <div class="relative flex-1 min-w-[180px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search departments…"
                       class="w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
            </div>
            <select name="trashed" class="py-2 pl-3 pr-8 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                <option value="">Active only</option>
                <option value="with" {{ request('trashed') === 'with' ? 'selected' : '' }}>With deleted</option>
                <option value="only" {{ request('trashed') === 'only' ? 'selected' : '' }}>Deleted only</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-[#ff0808] text-white text-sm rounded-lg hover:bg-red-700">Filter</button>
            @if(request()->hasAny(['search','trashed']))
                <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Clear</a>
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
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Department</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Description</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Users</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($departments as $department)
                    <tr class="hover:bg-gray-50 transition-colors {{ $department->trashed() ? 'opacity-60 bg-red-50' : '' }}">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $department->color }}"></span>
                                <a href="{{ route('admin.departments.show', $department) }}"
                                   class="font-medium text-gray-900 text-xs hover:text-[#ff0808]">{{ $department->name }}</a>
                                @if($department->trashed())
                                    <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[10px] rounded-full font-medium">deleted</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ Str::limit($department->description, 50) ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold text-gray-800">{{ $department->users_count }}</span>
                            <span class="text-xs text-gray-400">users</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($department->is_active)
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">Active</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($department->trashed())
                                    <form action="{{ route('admin.departments.restore', $department->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="Restore"
                                                class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors text-xs">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.departments.force-delete', $department->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Permanently Delete"
                                                onclick="return confirm('Permanently delete {{ $department->name }}? Cannot be undone.')"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors text-xs">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.departments.show', $department) }}" title="View"
                                       class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.departments.edit', $department) }}" title="Edit"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Delete"
                                                onclick="return confirm('Delete {{ $department->name }}? Users will be unassigned.')"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-building text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-500">No departments found</p>
                                <a href="{{ route('admin.departments.create') }}" class="text-xs text-[#ff0808] hover:underline">Create the first one</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($departments->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">{{ $departments->links() }}</div>
        @endif
    </div>
</div>
@endsection
