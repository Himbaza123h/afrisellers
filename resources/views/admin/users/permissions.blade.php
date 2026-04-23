@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Permissions</h1>
            <p class="mt-1 text-xs text-gray-500">
                Manage permissions for <span class="font-semibold text-gray-700">{{ $user->name }}</span>
            </p>
        </div>
        <a href="{{ route('admin.manageusers.index') }}"
           class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
            <i class="fas fa-arrow-left"></i><span>Back to Users</span>
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- User Info Card --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-[#ff0808] flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
            <p class="text-xs text-gray-500">{{ $user->email }}</p>
        </div>
        <div class="ml-auto">
            @php
                $granted = $permission
                    ? collect(\App\Models\ManageablePermission::allPermissionKeys())
                        ->filter(fn($k) => $permission->$k ?? false)->count()
                    : 0;
                $total = count(\App\Models\ManageablePermission::allPermissionKeys());
            @endphp
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                <i class="fas fa-shield-alt"></i>
                {{ $granted }}/{{ $total }} granted
            </span>
        </div>
    </div>

    {{-- Permissions Form --}}
    <form action="{{ route('admin.manageusers.permissions.update', $user) }}" method="POST">
        @csrf

        <div class="space-y-4">
            @foreach($groups as $groupName => $keys)
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                {{-- Group Header --}}
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-layer-group text-gray-400 text-xs"></i>
                        <h3 class="text-sm font-semibold text-gray-700 capitalize">{{ str_replace('_', ' ', $groupName) }}</h3>
                        <span class="text-xs text-gray-400">({{ count($keys) }} permissions)</span>
                    </div>
                    {{-- Select All toggle for this group --}}
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-500 hover:text-gray-700">
                        <input type="checkbox"
                               class="group-toggle rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808]"
                               data-group="{{ $groupName }}"
                               onchange="toggleGroup('{{ $groupName }}', this.checked)">
                        <span>Select all</span>
                    </label>
                </div>

                {{-- Permission Keys --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-0 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
                    @foreach($keys as $key)
                    <label class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox"
                               name="permissions[]"
                               value="{{ $key }}"
                               class="perm-check-{{ $groupName }} rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808]"
                               {{ ($permission && ($permission->$key ?? false)) ? 'checked' : '' }}>
                        <span class="text-xs text-gray-700 font-medium">
                            {{ ucwords(str_replace(['_', '.'], [' ', ' › '], $key)) }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Save Button --}}
        <div class="mt-4 flex justify-end gap-3">
            <a href="{{ route('admin.manageusers.index') }}"
               class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-[#ff0808] text-white text-sm font-medium rounded-lg hover:bg-red-700 shadow-sm">
                <i class="fas fa-save mr-1"></i> Save Permissions
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
function toggleGroup(group, checked) {
    document.querySelectorAll('.perm-check-' + group).forEach(cb => cb.checked = checked);
}

// Sync "select all" checkbox state on load
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.group-toggle').forEach(toggle => {
        const group = toggle.dataset.group;
        const boxes = document.querySelectorAll('.perm-check-' + group);
        const allChecked = [...boxes].every(b => b.checked);
        toggle.checked = allChecked;
        toggle.indeterminate = !allChecked && [...boxes].some(b => b.checked);
    });

    // Update toggle when individual boxes change
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
        cb.addEventListener('change', function () {
            const group = this.classList.toString().match(/perm-check-(\S+)/)?.[1];
            if (!group) return;
            const toggle = document.querySelector('.group-toggle[data-group="' + group + '"]');
            const boxes  = document.querySelectorAll('.perm-check-' + group);
            const allChecked = [...boxes].every(b => b.checked);
            const someChecked = [...boxes].some(b => b.checked);
            toggle.checked       = allChecked;
            toggle.indeterminate = !allChecked && someChecked;
        });
    });
});
</script>
@endpush

@endsection
