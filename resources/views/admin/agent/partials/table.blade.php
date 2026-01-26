<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 mb-3">
    <form method="GET" action="{{ route('admin.agents.index') }}" class="space-y-3">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone, city, or company..." class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <label class="text-xs font-medium text-gray-700">Filters:</label>

            <select name="filter" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Status</option>
                <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="suspended" {{ request('filter') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <select name="country" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Countries</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>

            <select name="email_verified" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Email Status</option>
                <option value="1" {{ request('email_verified') == '1' ? 'selected' : '' }}>Verified</option>
                <option value="0" {{ request('email_verified') == '0' ? 'selected' : '' }}>Not Verified</option>
            </select>

            <select name="commission_range" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Commission Range</option>
                <option value="high" {{ request('commission_range') == 'high' ? 'selected' : '' }}>High (&gt; $1,000)</option>
                <option value="medium" {{ request('commission_range') == 'medium' ? 'selected' : '' }}>Medium ($500 - $1,000)</option>
                <option value="low" {{ request('commission_range') == 'low' ? 'selected' : '' }}>Low (&lt; $500)</option>
            </select>

            <select name="date_range" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Time</option>
                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
            </select>

            <select name="sort_by" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                <option value="commission_earned" {{ request('sort_by') == 'commission_earned' ? 'selected' : '' }}>Commission</option>
                <option value="total_sales" {{ request('sort_by') == 'total_sales' ? 'selected' : '' }}>Sales</option>
                <option value="account_status" {{ request('sort_by') == 'account_status' ? 'selected' : '' }}>Status</option>
            </select>

            <select name="sort_order" class="px-3 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>

            <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                <i class="fas fa-filter"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'filter', 'country', 'email_verified', 'commission_range', 'date_range', 'sort_by']))
                <a href="{{ route('admin.agents.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
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
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Agent</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Contact</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Location</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Performance</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Registered</th>
                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($agents as $agent)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-lg">
                                    <span class="text-xs font-semibold text-indigo-700">{{ strtoupper(substr($agent->user->name ?? 'NA', 0, 2)) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $agent->user->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">{{ $agent->company_name ?? 'No company' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-medium text-gray-900">{{ $agent->user->email ?? 'N/A' }}</span>
                                <span class="text-xs text-gray-500">{{ $agent->phone_code }} {{ $agent->phone }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-blue-600 text-xs"></i>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-900">{{ $agent->city ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">{{ $agent->country->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-medium text-gray-900">${{ number_format($agent->commission_earned, 2) }}</span>
                                <span class="text-xs text-gray-500">{{ $agent->total_sales }} sales • {{ $agent->commission_rate }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-2">
                                @php
                                    $statusColors = [
                                        'active' => ['Active', 'bg-green-100 text-green-800'],
                                        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                        'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                                    ];
                                    $status = $statusColors[$agent->account_status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>

                                @if($agent->email_verified)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-check-circle mr-1 text-[10px]"></i> Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-clock mr-1 text-[10px]"></i> Pending
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm text-gray-900">{{ $agent->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500">{{ $agent->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.agent.show', $agent) }}" class="p-1.5 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    @if($agent->account_status === 'pending')
                                        <form action="{{ route('admin.agents.activate', $agent) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600" title="Activate" onclick="return confirm('Activate this agent account?')">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($agent->account_status === 'active')
                                        <form action="{{ route('admin.agents.suspend', $agent) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-gray-600 rounded-lg hover:bg-orange-50 hover:text-orange-600" title="Suspend" onclick="return confirm('Suspend this agent account?')">
                                                <i class="fas fa-ban text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <div class="relative inline-block text-left">
                                        <button type="button" class="p-1.5 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $agent->id }}')">
                                            <i class="fas fa-ellipsis-v text-sm"></i>
                                        </button>
                                        <div id="dropdown-{{ $agent->id }}" class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1">
                                                @if($agent->account_status === 'suspended')
                                                    <form action="{{ route('admin.agents.activate', $agent) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left" onclick="return confirm('Reactivate this agent account?')">
                                                            <i class="fas fa-undo text-green-600 w-4"></i>
                                                            Reactivate
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(!$agent->email_verified)
                                                    <form action="{{ route('admin.agents.verify-email', $agent) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                            <i class="fas fa-envelope-circle-check text-emerald-600 w-4"></i>
                                                            Verify Email
                                                        </button>
                                                    </form>
                                                @endif
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <button type="button"
                                                    onclick="dashboardSwitch.open({{ $agent->id }}, '{{ $agent->user->name ?? 'Agent' }}', 'Agent', '{{ route('admin.agents.switch-to-agent', $agent) }}')"
                                                    class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-sign-in-alt text-blue-600 w-4"></i>
                                                    Switch to Agent Dashboard
                                                </button>
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
                                        <i class="fas fa-user-tie text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-base font-semibold text-gray-900 mb-1">No agents found</p>
                                    <p class="text-sm text-gray-500 mb-4">Registered agents will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($agents, 'hasPages') && $agents->hasPages())
            <div class="px-4 py-3 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $agents->firstItem() }}-{{ $agents->lastItem() }} of {{ $agents->total() }}</span>
                    <div>{{ $agents->links() }}</div>
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
