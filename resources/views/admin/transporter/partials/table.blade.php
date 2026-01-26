    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.transporters.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by company, registration, license, phone, or email..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <select name="filter" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('filter') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <select name="country" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>

                <select name="verification" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Verification</option>
                    <option value="1" {{ request('verification') == '1' ? 'selected' : '' }}>Verified</option>
                    <option value="0" {{ request('verification') == '0' ? 'selected' : '' }}>Unverified</option>
                </select>

                <select name="rating" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Ratings</option>
                    <option value="4plus" {{ request('rating') == '4plus' ? 'selected' : '' }}>4+ Stars</option>
                    <option value="3plus" {{ request('rating') == '3plus' ? 'selected' : '' }}>3+ Stars</option>
                    <option value="below3" {{ request('rating') == 'below3' ? 'selected' : '' }}>Below 3 Stars</option>
                </select>

                <select name="fleet_size" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Fleet Size</option>
                    <option value="large" {{ request('fleet_size') == 'large' ? 'selected' : '' }}>Large (20+)</option>
                    <option value="medium" {{ request('fleet_size') == 'medium' ? 'selected' : '' }}>Medium (5-19)</option>
                    <option value="small" {{ request('fleet_size') == 'small' ? 'selected' : '' }}>Small (&lt;5)</option>
                </select>

                <select name="date_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                    <option value="company_name" {{ request('sort_by') == 'company_name' ? 'selected' : '' }}>Company Name</option>
                    <option value="average_rating" {{ request('sort_by') == 'average_rating' ? 'selected' : '' }}>Rating</option>
                    <option value="total_deliveries" {{ request('sort_by') == 'total_deliveries' ? 'selected' : '' }}>Deliveries</option>
                    <option value="fleet_size" {{ request('sort_by') == 'fleet_size' ? 'selected' : '' }}>Fleet Size</option>
                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'filter', 'country', 'verification', 'rating', 'fleet_size', 'date_range', 'sort_by']))
                    <a href="{{ route('admin.transporters.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Company</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Contact</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Location</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Performance</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Fleet</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($transporters as $transporter)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg">
                                        <span class="text-sm font-semibold text-orange-700">{{ strtoupper(substr($transporter->company_name, 0, 2)) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">{{ $transporter->company_name }}</span>
                                        <span class="text-xs text-gray-500">Reg: {{ $transporter->registration_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium text-gray-900">{{ $transporter->email }}</span>
                                    <span class="text-xs text-gray-500">{{ $transporter->phone }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-blue-600 text-xs"></i>
                                    <span class="text-sm text-gray-900">{{ $transporter->country->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1">
                                        @for($i = 0; $i < 5; $i++)
                                            @if($i < floor($transporter->average_rating))
                                                <i class="fas fa-star text-amber-400 text-xs"></i>
                                            @elseif($i < ceil($transporter->average_rating) && $transporter->average_rating - floor($transporter->average_rating) >= 0.5)
                                                <i class="fas fa-star-half-alt text-amber-400 text-xs"></i>
                                            @else
                                                <i class="far fa-star text-gray-300 text-xs"></i>
                                            @endif
                                        @endfor
                                        <span class="text-xs font-medium text-gray-700 ml-1">{{ number_format($transporter->average_rating, 1) }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ number_format($transporter->total_deliveries) }} deliveries • {{ $transporter->success_rate }}% success</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-truck text-cyan-600 text-sm"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ $transporter->fleet_size }}</span>
                                    <span class="text-xs text-gray-500">vehicles</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $transporter->status_badge['class'] }}">
                                        {{ $transporter->status_badge['text'] }}
                                    </span>
                                    @if($transporter->is_verified)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-certificate mr-1 text-[10px]"></i> Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-clock mr-1 text-[10px]"></i> Pending
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.transporters.show', $transporter) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!$transporter->is_verified)
                                        <form action="{{ route('admin.transporters.verify', $transporter) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600" title="Verify" onclick="return confirm('Verify this transporter?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($transporter->status === 'active')
                                        <form action="{{ route('admin.transporters.suspend', $transporter) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-orange-50 hover:text-orange-600" title="Suspend" onclick="return confirm('Suspend this transporter?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <div class="relative inline-block text-left">
                                        <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $transporter->id }}')">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="dropdown-{{ $transporter->id }}" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1">
                                                @if($transporter->status !== 'active')
                                                    <form action="{{ route('admin.transporter.activate', $transporter) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left" onclick="return confirm('Activate this transporter?')">
                                                            <i class="fas fa-check-circle text-green-600 w-4"></i>
                                                            Activate
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($transporter->is_verified)
                                                    <form action="" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                            <i class="fas fa-times-circle text-orange-600 w-4"></i>
                                                            Revoke Verification
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-edit text-amber-600 w-4"></i>
                                                    Edit
                                                </a>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <form action="{{ route('admin.transporters.destroy', $transporter) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-left" onclick="return confirm('Delete this transporter? This cannot be undone.')">
                                                        <i class="fas fa-trash-alt w-4"></i>
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
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-truck text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No transporters found</p>
                                    <p class="text-sm text-gray-500 mb-6">Registered transporters will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($transporters, 'hasPages') && $transporters->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $transporters->firstItem() }}-{{ $transporters->lastItem() }} of {{ $transporters->total() }}</span>
                    <div>{{ $transporters->links() }}</div>
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
