@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Admin User Details</h1>
            <p class="mt-1 text-xs text-gray-500">Viewing account for <span class="font-semibold text-gray-700">{{ $user->name }}</span></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.manageusers.index') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-arrow-left"></i><span>Back</span>
            </a>
            <a href="{{ route('admin.manageusers.edit', $user) }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium shadow-sm">
                <i class="fas fa-edit"></i><span>Edit</span>
            </a>
            <a href="{{ route('admin.manageusers.permissions', $user) }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium shadow-sm">
                <i class="fas fa-shield-alt"></i><span>Permissions</span>
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Left: Profile Card --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Avatar + Basic Info --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 flex flex-col items-center text-center gap-3">
                <div class="w-16 h-16 rounded-full bg-[#ff0808] flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-base font-bold text-gray-900">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</p>
                </div>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-user-shield text-[10px]"></i> Admin
                </span>
            </div>

            {{-- Account Info --}}
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Account Info</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-500">User ID</span>
                        <span class="text-xs font-medium text-gray-800">#{{ $user->id }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-500">Joined</span>
                        <span class="text-xs font-medium text-gray-800">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-500">Last Updated</span>
                        <span class="text-xs font-medium text-gray-800">{{ $user->updated_at->format('d M Y') }}</span>
                    </div>
                    @if($user->phone ?? null)
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-500">Phone</span>
                        <span class="text-xs font-medium text-gray-800">{{ $user->phone }}</span>
                    </div>
                    @endif
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-500">Roles</span>
                        <div class="flex flex-wrap gap-1 justify-end">
                            @forelse($user->roles as $role)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 capitalize">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400">None</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white rounded-lg border border-red-200 overflow-hidden">
                <div class="px-4 py-3 bg-red-50 border-b border-red-200">
                    <h3 class="text-xs font-semibold text-red-600 uppercase tracking-wide">Danger Zone</h3>
                </div>
                <div class="p-4">
                    <form action="{{ route('admin.manageusers.destroy', $user) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Delete {{ $user->name }}? This cannot be undone.')"
                                class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash mr-1"></i> Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Permissions Overview --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Permissions Summary --}}
            @php
                $allKeys = \App\Models\ManageablePermission::allPermissionKeys();
                $total   = count($allKeys);
                $granted = $permission
                    ? collect($allKeys)->filter(fn($k) => $permission->$k ?? false)->count()
                    : 0;
                $percent = $total > 0 ? round(($granted / $total) * 100) : 0;
            @endphp

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Permission Coverage</p>
                        <p class="text-xs text-gray-500">{{ $granted }} of {{ $total }} permissions granted</p>
                    </div>
                    <span class="text-2xl font-bold text-[#ff0808]">{{ $percent }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-[#ff0808] h-2 rounded-full transition-all duration-500"
                         style="width: {{ $percent }}%"></div>
                </div>
            </div>

            {{-- Permissions by Group --}}
            @foreach($groups as $groupName => $keys)
            @php
                $groupGranted = $permission
                    ? collect($keys)->filter(fn($k) => $permission->$k ?? false)->count()
                    : 0;
                $groupTotal = count($keys);
            @endphp
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-layer-group text-gray-400 text-xs"></i>
                        <h3 class="text-sm font-semibold text-gray-700 capitalize">{{ str_replace('_', ' ', $groupName) }}</h3>
                    </div>
                    <span class="text-xs {{ $groupGranted === $groupTotal ? 'text-green-600 bg-green-50' : ($groupGranted > 0 ? 'text-yellow-600 bg-yellow-50' : 'text-gray-400 bg-gray-100') }} px-2 py-0.5 rounded-full font-medium">
                        {{ $groupGranted }}/{{ $groupTotal }}
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-0 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
                    @foreach($keys as $key)
                    <div class="flex items-center gap-2 px-4 py-2.5">
                        @if($permission && ($permission->$key ?? false))
                            <i class="fas fa-check-circle text-green-500 text-xs flex-shrink-0"></i>
                        @else
                            <i class="fas fa-times-circle text-gray-300 text-xs flex-shrink-0"></i>
                        @endif
                        <span class="text-xs {{ $permission && ($permission->$key ?? false) ? 'text-gray-800 font-medium' : 'text-gray-400' }}">
                            {{ ucwords(str_replace(['_', '.'], [' ', ' › '], $key)) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>
    </div>

</div>
@endsection
