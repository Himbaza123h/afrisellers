<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
    <form method="GET" action="{{ route('admin.rfq.index') }}" class="space-y-4">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone, product..." class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <label class="text-xs font-medium text-gray-700">Filters:</label>

            <select name="status" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>

            <select name="sort_by" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Buyer Name</option>
                <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
            </select>

            <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                <i class="fas fa-filter text-xs"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'status', 'sort_by']))
                <a href="{{ route('admin.rfq.index') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
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
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">RFQ Details</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Product</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Buyer</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Messages</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($rfqs as $rfq)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><input type="checkbox" class="w-4 h-4 rounded"></td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-semibold text-gray-900">#RFQ-{{ str_pad($rfq->id, 6, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-xs text-gray-500">{{ $rfq->created_at->format('M d, Y') }}</span>
                                <span class="text-xs text-gray-500">{{ $rfq->created_at->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-medium text-gray-900">{{ $rfq->product ? $rfq->product->name : 'General Inquiry' }}</span>
                                @if($rfq->product && $rfq->product->sku)
                                    <span class="text-xs text-gray-500">{{ $rfq->product->sku }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full">
                                    <span class="text-xs font-semibold text-blue-700">{{ substr($rfq->name ?? 'N', 0, 1) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $rfq->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">{{ $rfq->email ?? 'No email' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $rfq->messages_count }} {{ Str::plural('message', $rfq->messages_count) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                    'accepted' => ['Accepted', 'bg-green-100 text-green-800'],
                                    'rejected' => ['Rejected', 'bg-red-100 text-red-800'],
                                    'closed' => ['Closed', 'bg-gray-100 text-gray-800'],
                                ];
                                $status = $statusColors[$rfq->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="px-2.5 py-1 rounded text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.rfq.vendors', $rfq) }}" class="p-1.5 text-gray-600 rounded hover:bg-blue-50 hover:text-blue-600" title="View Vendors">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if($rfq->user)
                                    <a href="{{ route('admin.rfq.messages', ['rfq' => $rfq->id, 'vendor' => $rfq->user->id]) }}" class="p-1.5 text-gray-600 rounded hover:bg-purple-50 hover:text-purple-600" title="View Message">
                                        <i class="fas fa-envelope text-xs"></i>
                                    </a>
                                @else
                                    <span class="p-1.5 text-gray-300 rounded cursor-not-allowed" title="No user associated">
                                        <i class="fas fa-envelope text-xs"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-file-invoice text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 mb-1">No RFQs found</p>
                                <p class="text-xs text-gray-500 mb-4">Request for Quotations will appear here</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($rfqs, 'hasPages') && $rfqs->hasPages())
        <div class="px-4 py-3 border-t bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-gray-700">
                    Showing {{ $rfqs->firstItem() }} to {{ $rfqs->lastItem() }} of {{ $rfqs->total() }}
                </div>
                <div>{{ $rfqs->links() }}</div>
            </div>
        </div>
    @endif
</div>
