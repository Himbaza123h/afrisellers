@extends('layouts.home')
@section('page-content')
<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.departments.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-arrow-left text-gray-600 text-sm"></i>
            </a>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full" style="background-color: {{ $department->color }}"></span>
                <h1 class="text-xl font-bold text-gray-900">{{ $department->name }}</h1>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.departments.edit', $department) }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                <i class="fas fa-edit"></i><span>Edit</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="h-2" style="background-color: {{ $department->color }}"></div>
                <div class="p-5">
                    <p class="text-base font-bold text-gray-900">{{ $department->name }}</p>
                    @if($department->description)
                        <p class="text-xs text-gray-500 mt-1">{{ $department->description }}</p>
                    @endif
                    <div class="mt-3 flex items-center gap-2">
                        @if($department->is_active)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">Active</span>
                        @else
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="border-t border-gray-100 px-5 py-3">
                    <p class="text-xs text-gray-500">Created {{ $department->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Members ({{ $users->total() }})</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-[#ff0808] flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-[11px] text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.departments.users.remove', [$department, $user]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Remove {{ $user->name }} from this department?')"
                                    class="px-2 py-1 text-xs text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
                                Remove
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="px-4 py-8 text-center">
                        <i class="fas fa-users text-gray-300 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-400">No admin users in this department yet.</p>
                        <p class="text-xs text-gray-400 mt-1">Assign users from <a href="{{ route('admin.manageusers.index') }}" class="text-[#ff0808] hover:underline">Admin Users</a>.</p>
                    </div>
                    @endforelse
                </div>
                @if($users->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">{{ $users->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
