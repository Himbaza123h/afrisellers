<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">

    {{-- Section header --}}
    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-200">
        <div class="w-9 h-9 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-shield-alt text-white text-sm"></i>
        </div>
        <div>
            <h2 class="text-base font-bold text-gray-900">Permissions</h2>
            <p class="text-xs text-gray-500">Control what this admin user can access</p>
        </div>
    </div>

    {{-- Master controls --}}
    <div class="flex flex-wrap items-center gap-3 mb-5 p-3 bg-gray-50 rounded-lg border border-gray-200">
        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Quick Select:</span>
        <button type="button" onclick="selectAllPermissions(true)"
                class="px-3 py-1.5 text-xs font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            <i class="fas fa-check-double mr-1"></i>Grant All
        </button>
        <button type="button" onclick="selectAllPermissions(false)"
                class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            <i class="fas fa-times mr-1"></i>Revoke All
        </button>
        <div class="ml-auto text-xs text-gray-500">
            <span id="perm-count">0</span> selected
        </div>
    </div>

    {{-- Permission groups --}}
    <div class="space-y-3">
        @foreach($groups as $group => $keys)
        @php
            $label = ucwords(str_replace('_', ' ', $group));
        @endphp
        <div class="border border-gray-200 rounded-lg overflow-hidden" id="group-block-{{ $group }}">

            {{-- Group header --}}
            <div class="flex items-center gap-3 px-4 py-2.5 bg-gray-50 border-b border-gray-200">
                <input type="checkbox"
                       id="group-check-{{ $group }}"
                       class="group-master w-4 h-4 rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808] cursor-pointer"
                       data-group="{{ $group }}"
                       onchange="toggleGroup('{{ $group }}', this.checked)">
                <label for="group-check-{{ $group }}"
                       class="flex-1 text-xs font-bold text-gray-800 uppercase tracking-wide cursor-pointer select-none">
                    {{ $label }}
                </label>
                <span class="text-xs text-gray-400" id="group-count-{{ $group }}">
                    0/{{ count($keys) }}
                </span>
                <button type="button"
                        onclick="toggleGroupAccordion('{{ $group }}')"
                        class="text-gray-400 hover:text-gray-600 transition-colors ml-1"
                        id="group-toggle-{{ $group }}">
                    <i class="fas fa-chevron-down text-xs transition-transform" id="group-icon-{{ $group }}"></i>
                </button>
            </div>

            {{-- Group permissions grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-0 divide-y divide-gray-100"
                 id="group-items-{{ $group }}">
                @foreach($keys as $key)
                @php
                    $checked = isset($permission) && ($permission->$key ?? false);
                    $label   = ucfirst(str_replace(['can_','_'], ['','  '], $key));
                @endphp
                <label class="flex items-center gap-2.5 px-4 py-2 hover:bg-gray-50 cursor-pointer transition-colors">
                    <input type="checkbox"
                           name="permissions[]"
                           value="{{ $key }}"
                           class="perm-checkbox group-{{ $group }} w-3.5 h-3.5 rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808]"
                           {{ $checked ? 'checked' : '' }}
                           onchange="updateGroupState('{{ $group }}'); updateCount()">
                    <span class="text-xs text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Init all group states and counts
    @foreach(array_keys($groups) as $group)
    updateGroupState('{{ $group }}');
    @endforeach
    updateCount();
});

function selectAllPermissions(state) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = state);
    @foreach(array_keys($groups) as $group)
    updateGroupState('{{ $group }}');
    @endforeach
    updateCount();
}

function toggleGroup(group, state) {
    document.querySelectorAll('.group-' + group).forEach(cb => cb.checked = state);
    updateGroupState(group);
    updateCount();
}

function updateGroupState(group) {
    const boxes   = document.querySelectorAll('.group-' + group);
    const checked = [...boxes].filter(b => b.checked).length;
    const master  = document.getElementById('group-check-' + group);
    const counter = document.getElementById('group-count-' + group);

    if (master) {
        master.checked       = checked === boxes.length && boxes.length > 0;
        master.indeterminate = checked > 0 && checked < boxes.length;
    }
    if (counter) counter.textContent = checked + '/' + boxes.length;
}

function updateCount() {
    const total = document.querySelectorAll('.perm-checkbox:checked').length;
    const el    = document.getElementById('perm-count');
    if (el) el.textContent = total;
}

function toggleGroupAccordion(group) {
    const items = document.getElementById('group-items-' + group);
    const icon  = document.getElementById('group-icon-' + group);
    if (!items) return;

    const hidden = items.classList.contains('hidden');
    items.classList.toggle('hidden', !hidden);
    if (icon) icon.style.transform = hidden ? 'rotate(0deg)' : 'rotate(-90deg)';
}

// Start all groups collapsed
document.addEventListener('DOMContentLoaded', function () {
    @foreach(array_keys($groups) as $group)
    document.getElementById('group-items-{{ $group }}')?.classList.add('hidden');
    const icon{{ $loop->index }} = document.getElementById('group-icon-{{ $group }}');
    if (icon{{ $loop->index }}) icon{{ $loop->index }}.style.transform = 'rotate(-90deg)';
    @endforeach
});
</script>
@endpush
@endonce
