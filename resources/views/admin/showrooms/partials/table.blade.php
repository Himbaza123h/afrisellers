<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
    <form method="GET" action="{{ route('admin.showrooms.index') }}" class="space-y-4">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, number, email, phone..." class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <label class="text-xs font-medium text-gray-700">Filters:</label>

            <select name="filter" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Status</option>
                <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="suspended" {{ request('filter') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <select name="country" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Countries</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>

            <select name="verification" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Verification</option>
                <option value="1" {{ request('verification') == '1' ? 'selected' : '' }}>Verified</option>
                <option value="0" {{ request('verification') == '0' ? 'selected' : '' }}>Unverified</option>
            </select>

            <select name="featured" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Featured Status</option>
                <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured</option>
                <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Not Featured</option>
            </select>

            <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                <i class="fas fa-filter text-xs"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'filter', 'country', 'verification', 'featured']))
                <a href="{{ route('admin.showrooms.index') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                    <i class="fas fa-times text-xs"></i> Clear
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
                    <th class="px-4 py-3 text-left w-10"><input type="checkbox" class="w-4 h-4 rounded"></th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Showroom</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Contact</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Location</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Performance</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($showrooms as $showroom)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><input type="checkbox" class="w-4 h-4 rounded"></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded">
                                    <span class="text-xs font-semibold text-indigo-700">{{ strtoupper(substr($showroom->name, 0, 2)) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $showroom->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $showroom->showroom_number }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-0.5">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-envelope text-blue-600 text-xs"></i>
                                    <span class="text-xs text-gray-700">{{ $showroom->email }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-phone text-green-600 text-xs"></i>
                                    <span class="text-xs text-gray-700">{{ $showroom->phone }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-medium text-gray-900">{{ $showroom->city }}</span>
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-map-marker-alt text-red-600 text-xs"></i>
                                    <span class="text-xs text-gray-500">{{ $showroom->country->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-star text-yellow-500 text-xs"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($showroom->rating ?? 0, 1) }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-eye text-cyan-600 text-xs"></i>
                                    <span class="text-xs text-gray-500">{{ number_format($showroom->views_count ?? 0) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $showroom->status_badge['class'] }}">
                                    {{ $showroom->status_badge['text'] }}
                                </span>
                                @if($showroom->is_verified)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-certificate mr-0.5 text-[8px]"></i> Verified
                                    </span>
                                @endif
                                @if($showroom->is_featured)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                        <i class="fas fa-star mr-0.5 text-[8px]"></i> Featured
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.showrooms.show', $showroom) }}" class="p-1 text-gray-600 rounded hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if($showroom->status === 'pending')
                                    <form action="{{ route('admin.showrooms.activate', $showroom) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1 text-gray-600 rounded hover:bg-green-50 hover:text-green-600" title="Activate" onclick="return confirm('Activate this showroom?')">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(!$showroom->is_verified)
                                    <form action="{{ route('admin.showrooms.verify', $showroom) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1 text-gray-600 rounded hover:bg-emerald-50 hover:text-emerald-600" title="Verify" onclick="return confirm('Verify this showroom?')">
                                            <i class="fas fa-certificate text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.showrooms.feature', $showroom) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1 text-gray-600 rounded hover:bg-amber-50 hover:text-amber-600" title="{{ $showroom->is_featured ? 'Unfeature' : 'Feature' }}">
                                        <i class="fas fa-star text-xs"></i>
                                    </button>
                                </form>
                                <div class="relative inline-block">
                                    <button type="button" class="p-1 text-gray-600 rounded hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $showroom->id }}')">
                                        <i class="fas fa-ellipsis-v text-xs"></i>
                                    </button>
                                    <div id="dropdown-{{ $showroom->id }}" class="hidden absolute right-0 mt-2 w-40 rounded shadow bg-white border border-gray-200 z-10">
                                        <div class="py-1">
                                            @if($showroom->status === 'active')
                                                <form action="{{ route('admin.showrooms.suspend', $showroom) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 text-left" onclick="return confirm('Suspend this showroom?')">
                                                        <i class="fas fa-ban text-orange-600 w-3.5"></i>
                                                        Suspend
                                                    </button>
                                                </form>
                                            @endif
                                            @if($showroom->is_verified)
                                                <form action="{{ route('admin.showrooms.unverify', $showroom) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 text-left">
                                                        <i class="fas fa-times-circle text-orange-600 w-3.5"></i>
                                                        Revoke Verify
                                                    </button>
                                                </form>
                                            @endif
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <form action="{{ route('admin.showrooms.destroy', $showroom) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 text-left" onclick="return confirm('Delete this showroom?')">
                                                    <i class="fas fa-trash w-3.5"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-store-slash text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 mb-1">No showrooms found</p>
                                <p class="text-xs text-gray-500 mb-4">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($showrooms->hasPages())
        <div class="px-4 py-3 border-t bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-gray-700">
                    Showing {{ $showrooms->firstItem() }} to {{ $showrooms->lastItem() }} of {{ $showrooms->total() }}
                </div>
                <div>{{ $showrooms->links() }}</div>
            </div>
        </div>
    @endif
</div>

<script>
function toggleDropdown(event, dropdownId) {
    event.stopPropagation();
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');

    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('hidden');
        }
    });

    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    allDropdowns.forEach(dropdown => {
        dropdown.classList.add('hidden');
    });
});
</script>
