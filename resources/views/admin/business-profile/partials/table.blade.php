<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 mb-3">
    <form method="GET" action="{{ route('admin.business-profile.index') }}" class="space-y-3">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by business name or owner..." class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <label class="text-xs font-medium text-gray-700">Filters:</label>

            <select name="filter" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Status</option>
                <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="verified" {{ request('filter') == 'verified' ? 'selected' : '' }}>Verified</option>
                <option value="rejected" {{ request('filter') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <select name="country" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Countries</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>

            <select name="date_range" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Time</option>
                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
            </select>

            <select name="sort_by" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="business_name" {{ request('sort_by') == 'business_name' ? 'selected' : '' }}>Business Name</option>
                <option value="verification_status" {{ request('sort_by') == 'verification_status' ? 'selected' : '' }}>Status</option>
            </select>

            <select name="sort_order" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>

            <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                <i class="fas fa-filter"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'filter', 'country', 'date_range', 'sort_by']))
                <a href="{{ route('admin.business-profile.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
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
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Business</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Owner</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Registration</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Location</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Submitted</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($businessProfiles as $profile)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg">
                                    <span class="text-xs font-semibold text-purple-700">{{ strtoupper(substr($profile->business_name, 0, 2)) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $profile->business_name }}</span>
                                    <span class="text-xs text-gray-500">{{ $profile->city ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-medium text-gray-900">{{ $profile->user->name ?? 'N/A' }}</span>
                                <span class="text-xs text-gray-500">{{ $profile->user->email ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-mono text-gray-900">{{ $profile->business_registration_number }}</span>
                                <span class="text-xs text-gray-500">{{ $profile->phone_code }} {{ $profile->phone }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-blue-600 text-xs"></i>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-900">{{ $profile->city }}</span>
                                    <span class="text-xs text-gray-500">{{ $profile->country->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm text-gray-900">{{ $profile->created_at->format('M d, Y') }}</span>
                                <span class="text-xs text-gray-500">{{ $profile->created_at->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'verified' => ['Verified', 'bg-green-100 text-green-800'],
                                    'rejected' => ['Rejected', 'bg-red-100 text-red-800'],
                                    'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                ];
                                $status = $statusColors[$profile->verification_status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.business-profile.show', $profile) }}" class="p-1.5 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                @if($profile->verification_status === 'pending')
                                    <form action="{{ route('admin.business-profile.verify', $profile) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600" title="Verify" onclick="return confirm('Verify this business profile?')">
                                            <i class="fas fa-check text-sm"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.business-profile.reject', $profile) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-600" title="Reject" onclick="return confirm('Reject this business profile?')">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>
                                    </form>
                                @endif
                                <div class="relative inline-block text-left">
                                    <button type="button" class="p-1.5 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $profile->id }}')">
                                        <i class="fas fa-ellipsis-v text-sm"></i>
                                    </button>
                                    <div id="dropdown-{{ $profile->id }}" class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            @if($profile->verification_status === 'verified')
                                                <button type="button"
                                                    onclick="dashboardSwitch.open({{ $profile->user->id }}, '{{ $profile->business_name }}', 'Vendor', '{{ route('admin.business-profiles.switch-to-vendor', $profile) }}')"
                                                    class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-sign-in-alt text-blue-600 w-4"></i>
                                                    Switch to Vendor Dashboard
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
                                    <i class="fas fa-store text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-base font-semibold text-gray-900 mb-1">No business profiles found</p>
                                <p class="text-sm text-gray-500 mb-4">Business applications will appear here</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($businessProfiles, 'hasPages') && $businessProfiles->hasPages())
        <div class="px-4 py-3 border-t bg-gray-50">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-700">Showing {{ $businessProfiles->firstItem() }}-{{ $businessProfiles->lastItem() }} of {{ $businessProfiles->total() }}</span>
                <div>{{ $businessProfiles->links() }}</div>
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

document.addEventListener('click', function() {const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
allDropdowns.forEach(d => d.classList.add('hidden'));
});
</script>

