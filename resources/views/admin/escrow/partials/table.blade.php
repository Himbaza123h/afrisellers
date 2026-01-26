<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('admin.escrow.index') }}" class="space-y-4">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by escrow number, buyer, vendor, or notes..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
        </div>

        <div class="flex flex-wrap gap-3 items-center">
            <label class="text-sm font-medium text-gray-700">Filters:</label>

            <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="released" {{ request('status') == 'released' ? 'selected' : '' }}>Released</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                <option value="disputed" {{ request('status') == 'disputed' ? 'selected' : '' }}>Disputed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="escrow_type" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Types</option>
                <option value="order" {{ request('escrow_type') == 'order' ? 'selected' : '' }}>Order</option>
                <option value="service" {{ request('escrow_type') == 'service' ? 'selected' : '' }}>Service</option>
                <option value="milestone" {{ request('escrow_type') == 'milestone' ? 'selected' : '' }}>Milestone</option>
                <option value="custom" {{ request('escrow_type') == 'custom' ? 'selected' : '' }}>Custom</option>
            </select>

            <select name="disputed" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Dispute Status</option>
                <option value="yes" {{ request('disputed') == 'yes' ? 'selected' : '' }}>Disputed</option>
                <option value="no" {{ request('disputed') == 'no' ? 'selected' : '' }}>Not Disputed</option>
            </select>

            <select name="release_condition" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Release Condition</option>
                <option value="auto_release" {{ request('release_condition') == 'auto_release' ? 'selected' : '' }}>Auto Release</option>
                <option value="manual_approval" {{ request('release_condition') == 'manual_approval' ? 'selected' : '' }}>Manual Approval</option>
                <option value="delivery_confirmation" {{ request('release_condition') == 'delivery_confirmation' ? 'selected' : '' }}>Delivery Confirmation</option>
                <option value="milestone_completion" {{ request('release_condition') == 'milestone_completion' ? 'selected' : '' }}>Milestone Completion</option>
            </select>

            <select name="buyer" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Buyers</option>
                @foreach($buyers as $buyer)
                    <option value="{{ $buyer->id }}" {{ request('buyer') == $buyer->id ? 'selected' : '' }}>
                        {{ $buyer->name }}
                    </option>
                @endforeach
            </select>

            <select name="vendor" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Vendors</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" {{ request('vendor') == $vendor->id ? 'selected' : '' }}>
                        {{ $vendor->name }}
                    </option>
                @endforeach
            </select>

            <select name="amount_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Amount Range</option>
                <option value="high" {{ request('amount_range') == 'high' ? 'selected' : '' }}>High ($10,000+)</option>
                <option value="medium" {{ request('amount_range') == 'medium' ? 'selected' : '' }}>Medium ($1,000-$9,999)</option>
                <option value="low" {{ request('amount_range') == 'low' ? 'selected' : '' }}>Low (&lt;$1,000)</option>
            </select>

            <select name="date_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Time</option>
                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
            </select>

            <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="amount" {{ request('sort_by') == 'amount' ? 'selected' : '' }}>Amount</option>
                <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
            </select>

            <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>

            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-filter"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'status', 'escrow_type', 'disputed', 'release_condition', 'buyer', 'vendor', 'amount_range', 'date_range', 'sort_by']))
                <a href="{{ route('admin.escrow.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Escrow</th>
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Parties</th>
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Amount</th>
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Type</th>
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Timeline</th>
                    <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($escrows as $escrow)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg">
                                    <i class="fas fa-handshake text-blue-700"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $escrow->escrow_number }}</span>
                                    <span class="text-xs text-gray-500">{{ $escrow->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                    <span class="text-xs font-medium text-gray-900">{{ $escrow->buyer->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-store text-purple-600 text-xs"></i>
                                    <span class="text-xs font-medium text-gray-900">{{ $escrow->vendor->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-bold text-gray-900">{{ $escrow->currency }} {{ number_format($escrow->amount, 2) }}</span>
                                <span class="text-xs text-gray-500">Vendor: ${{ number_format($escrow->vendor_amount, 2) }}</span>
                                @if($escrow->platform_fee > 0)
                                    <span class="text-xs text-gray-400">Fee: ${{ number_format($escrow->platform_fee, 2) }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $escrow->type_badge['class'] }} inline-block w-fit">
                                    {{ $escrow->type_badge['text'] }}
                                </span>
                                @if($escrow->disputed)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 inline-flex items-center gap-1 w-fit">
                                        <i class="fas fa-exclamation-triangle text-[10px]"></i> Disputed
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $escrow->status_badge['class'] }}">
                                {{ $escrow->status_badge['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                @if($escrow->held_at)
                                    <span class="text-xs text-gray-500">Held: {{ $escrow->days_held }} days</span>
                                @endif
                                @if($escrow->expected_release_at && $escrow->status === 'active')
                                    <span class="text-xs text-gray-500">Release: {{ $escrow->expected_release_at->format('M d') }}</span>
                                @endif
                                @if($escrow->released_at)
                                    <span class="text-xs text-green-600">Released: {{ $escrow->released_at->format('M d') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.escrow.show', $escrow) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <div class="relative inline-block text-left">
                                    <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $escrow->id }}')">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="dropdown-{{ $escrow->id }}" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            @if($escrow->canBeReleased())
                                                <button type="button" onclick="openReleaseModal('{{ $escrow->id }}')" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-unlock text-green-600 w-4"></i>
                                                    Release Funds
                                                </button>
                                            @endif
                                            @if(in_array($escrow->status, ['pending', 'active']) && !$escrow->disputed)
                                                <button type="button" onclick="openRefundModal('{{ $escrow->id }}')" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-undo text-orange-600 w-4"></i>
                                                    Refund
                                                </button>
                                            @endif
                                            @if($escrow->status === 'active' && !$escrow->disputed)
                                                <button type="button" onclick="openDisputeModal('{{ $escrow->id }}')" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-exclamation-triangle text-red-600 w-4"></i>
                                                    Mark as Disputed
                                                </button>
                                            @endif
                                            @if($escrow->disputed)
                                                <button type="button" onclick="openResolveDisputeModal('{{ $escrow->id }}')" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-check-circle text-blue-600 w-4"></i>
                                                    Resolve Dispute
                                                </button>
                                            @endif
                                            @if($escrow->status === 'pending')
                                                <button type="button" onclick="openCancelModal('{{ $escrow->id }}')" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-times-circle text-gray-600 w-4"></i>
                                                    Cancel
                                                </button>
                                            @endif
                                            <a href="{{ route('admin.escrow.show', $escrow) }}" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="fas fa-eye text-blue-600 w-4"></i>
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full">
                                    <i class="fas fa-inbox text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-900">No escrows found</p>
                                <p class="text-xs text-gray-500">Try adjusting your filters or search terms</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($escrows->hasPages())
        <div class="flex items-center justify-between px-6 py-4 bg-white border-t">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Showing</span>
                <span class="font-semibold text-gray-900">{{ $escrows->firstItem() }}</span>
                <span>to</span>
                <span class="font-semibold text-gray-900">{{ $escrows->lastItem() }}</span>
                <span>of</span>
                <span class="font-semibold text-gray-900">{{ $escrows->total() }}</span>
                <span>results</span>
            </div>
            <div>
                {{ $escrows->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Include Modals (same as before) -->
@include('admin.escrow.partials.modals')
