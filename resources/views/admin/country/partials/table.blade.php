<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 mb-3">
    <form method="GET" action="{{ route('admin.country.index') }}" class="space-y-3">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by country name or code..." class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <label class="text-xs font-medium text-gray-700">Filters:</label>

            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <select name="region" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}" {{ request('region') == $region->id ? 'selected' : '' }}>
                        {{ $region->name }}
                    </option>
                @endforeach
            </select>

            <select name="has_flag" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Flag Status</option>
                <option value="yes" {{ request('has_flag') == 'yes' ? 'selected' : '' }}>With Flag</option>
                <option value="no" {{ request('has_flag') == 'no' ? 'selected' : '' }}>Without Flag</option>
            </select>

            <select name="sort_by" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
            </select>

            <select name="sort_order" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>

            <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                <i class="fas fa-filter"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'status', 'region', 'has_flag', 'sort_by']))
                <a href="{{ route('admin.country.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Flag</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Country</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Region</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Vendors</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Created Date</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($countries as $country)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if($country->flag_url)
                                <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" class="w-10 h-7 object-cover rounded border border-gray-300 shadow-sm">
                            @else
                                <div class="flex items-center justify-center w-10 h-7 bg-gray-100 rounded border border-gray-300">
                                    <i class="fas fa-flag text-gray-400 text-xs"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg">
                                    <span class="text-xs font-semibold text-blue-700">{{ strtoupper(substr($country->name, 0, 2)) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $country->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $country->code ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($country->region)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-blue-600 text-xs"></i>
                                    <span class="text-sm text-gray-900">{{ $country->region->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-store text-purple-600 text-xs"></i>
                                <span class="text-sm font-medium text-gray-900">{{ $country->vendors_count ?? 0 }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm text-gray-900">{{ $country->created_at->format('M d, Y') }}</span>
                                <span class="text-xs text-gray-500">{{ $country->created_at->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'active' => ['Active', 'bg-green-100 text-green-800'],
                                    'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                                ];
                                $status = $statusColors[$country->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.country.show', $country) }}" class="p-1.5 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('admin.countries.assign-country-admin', $country) }}"
                                class="p-1.5 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600"
                                title="Assign Country Admin">
                                    <i class="fas fa-user-plus text-sm"></i>
                                </a>
                                <a href="{{ route('admin.country.edit', $country) }}" class="p-1.5 text-gray-600 rounded-lg hover:bg-amber-50 hover:text-amber-600" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <div class="relative inline-block text-left">
                                    <button type="button" class="p-1.5 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $country->id }}')">
                                        <i class="fas fa-ellipsis-v text-sm"></i>
                                    </button>
                                    <div id="dropdown-{{ $country->id }}" class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            <form action="{{ route('admin.country.toggle-status', $country) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-toggle-{{ $country->status === 'active' ? 'off' : 'on' }} text-orange-600 w-4"></i>
                                                    {{ $country->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            @if($country->countryAdmin)
                                                <button type="button"
                                                    onclick="dashboardSwitch.open({{ $country->id }}, '{{ $country->countryAdmin->name ?? $country->name }}', 'Country Admin', '{{ route('admin.countries.switch-to-country', $country) }}')"
                                                    class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-sign-in-alt text-blue-600 w-4"></i>
                                                    Switch to Country Dashboard
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-globe text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-base font-semibold text-gray-900 mb-1">No countries found</p>
                                <p class="text-sm text-gray-500 mb-4">Add a country to get started</p>
                                <a href="{{ route('admin.country.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                                    <i class="fas fa-plus"></i> Add Country
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($countries, 'hasPages') && $countries->hasPages())
        <div class="px-4 py-3 border-t bg-gray-50">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-700">Showing {{ $countries->firstItem() }}-{{ $countries->lastItem() }} of {{ $countries->total() }}</span>
                <div>{{ $countries->links() }}</div>
            </div>
        </div>
    @endif
</div>

<script>
function toggleDropdown(event, dropdownId) {
    event.stopPropagation();
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) d.classList.add('hidden');
    });
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function() {
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    allDropdowns.forEach(d => d.classList.add('hidden'));
});
</script>
