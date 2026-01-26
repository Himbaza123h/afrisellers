<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-4">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order number, customer..." class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <label class="text-xs font-medium text-gray-700">Filters:</label>

            <select name="status" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="payment_status" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Payment Status</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>

            <select name="sort_by" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="order_number" {{ request('sort_by') == 'order_number' ? 'selected' : '' }}>Order Number</option>
                <option value="total" {{ request('sort_by') == 'total' ? 'selected' : '' }}>Total Amount</option>
                <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
            </select>

            <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                <i class="fas fa-filter text-xs"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'status', 'payment_status', 'sort_by']))
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
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
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Order Details</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Customer</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Items</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Total</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><input type="checkbox" class="w-4 h-4 rounded"></td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-semibold text-gray-900">#{{ $order->order_number }}</span>
                                <span class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</span>
                                <span class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full">
                                    <span class="text-xs font-semibold text-blue-700">{{ substr($order->buyer->name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $order->buyer->name ?? 'Unknown' }}</span>
                                    <span class="text-xs text-gray-500">{{ $order->buyer->email ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-bold text-gray-900">{{ $order->formatted_total ?? '$0.00' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                    'confirmed' => ['Confirmed', 'bg-blue-100 text-blue-800'],
                                    'processing' => ['Processing', 'bg-purple-100 text-purple-800'],
                                    'shipped' => ['Shipped', 'bg-indigo-100 text-indigo-800'],
                                    'delivered' => ['Delivered', 'bg-green-100 text-green-800'],
                                    'cancelled' => ['Cancelled', 'bg-red-100 text-red-800'],
                                ];
                                $status = $statusColors[$order->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="px-2.5 py-1 rounded text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.orders.show', $order) }}" class="p-1.5 text-gray-600 rounded hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="p-1.5 text-gray-600 rounded hover:bg-purple-50 hover:text-purple-600" title="Invoice">
                                    <i class="fas fa-file-invoice text-xs"></i>
                                </a>
                                @if(in_array($order->status, ['pending', 'confirmed', 'processing']))
                                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="processing">
                                    <button type="submit" class="p-1.5 text-gray-600 rounded hover:bg-green-50 hover:text-green-600" title="Process Order">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-shopping-cart text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 mb-1">No orders found</p>
                                <p class="text-xs text-gray-500 mb-4">Orders will appear here</p>
                                @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium text-sm">
                                        <i class="fas fa-plus text-xs"></i>
                                        <span>Create Order</span>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($orders, 'hasPages') && $orders->hasPages())
        <div class="px-4 py-3 border-t bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-gray-700">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }}
                </div>
                <div>{{ $orders->links() }}</div>
            </div>
        </div>
    @endif
</div>
