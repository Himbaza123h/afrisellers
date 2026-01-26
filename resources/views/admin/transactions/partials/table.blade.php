<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('admin.transactions.index') }}" class="space-y-4">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by transaction number, reference, buyer, vendor, or notes..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
        </div>

        <div class="flex flex-wrap gap-3 items-center">
            <label class="text-sm font-medium text-gray-700">Filters:</label>

            <select name="filter" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Status</option>
                <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('filter') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="completed" {{ request('filter') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('filter') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('filter') == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>

            <select name="type" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Types</option>
                <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Payment</option>
                <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
                <option value="commission" {{ request('type') == 'commission' ? 'selected' : '' }}>Commission</option>
                <option value="payout" {{ request('type') == 'payout' ? 'selected' : '' }}>Payout</option>
                <option value="subscription" {{ request('type') == 'subscription' ? 'selected' : '' }}>Subscription</option>
            </select>

            <select name="payment_method" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Payment Method</option>
                <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                <option value="debit_card" {{ request('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="paypal" {{ request('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                <option value="stripe" {{ request('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
            </select>

            <select name="currency" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Currencies</option>
                <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                <option value="EUR" {{ request('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                <option value="GBP" {{ request('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                <option value="RWF" {{ request('currency') == 'RWF' ? 'selected' : '' }}>RWF</option>
            </select>

            <select name="amount_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">Amount Range</option>
                <option value="high" {{ request('amount_range') == 'high' ? 'selected' : '' }}>High ($10,000+)</option>
                <option value="medium" {{ request('amount_range') == 'medium' ? 'selected' : '' }}>Medium ($1,000-$9,999)</option>
                <option value="low" {{ request('amount_range') == 'low' ? 'selected' : '' }}>Low (&lt;$1,000)</option>
            </select>

            <select name="vendor" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Vendors</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" {{ request('vendor') == $vendor->id ? 'selected' : '' }}>
                        {{ $vendor->name }}
                    </option>
                @endforeach
            </select>

            <select name="buyer" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Buyers</option>
                @foreach($buyers as $buyer)
                    <option value="{{ $buyer->id }}" {{ request('buyer') == $buyer->id ? 'selected' : '' }}>
                        {{ $buyer->name }}
                    </option>
                @endforeach
            </select>

            <select name="date_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Time</option>
                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                <option value="quarter" {{ request('date_range') == 'quarter' ? 'selected' : '' }}>This Quarter</option>
            </select>

            <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="transaction_number" {{ request('sort_by') == 'transaction_number' ? 'selected' : '' }}>Transaction Number</option>
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

            @if(request()->hasAny(['search', 'filter', 'type', 'payment_method', 'currency', 'amount_range', 'vendor', 'buyer', 'date_range', 'sort_by']))
                <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Transaction</th>
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Parties</th>
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Type</th>
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Amount</th>
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Payment</th>
                    <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-lg">
                                    <i class="fas fa-receipt text-green-700"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $transaction->transaction_number }}</span>
                                    <span class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                    <span class="text-xs font-medium text-gray-900">{{ $transaction->buyer->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-store text-purple-600 text-xs"></i>
                                    <span class="text-xs font-medium text-gray-900">{{ $transaction->vendor->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $transaction->type_badge['class'] }}">
                                {{ $transaction->type_badge['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-bold text-gray-900">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</span>
                                @if($transaction->payment_reference)
                                    <span class="text-xs text-gray-500">Ref: {{ $transaction->payment_reference }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method ?? 'N/A')) }}</span>
                                @if($transaction->completed_at)
                                    <span class="text-xs text-gray-500">{{ $transaction->completed_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $transaction->status_badge['class'] }}">
                                {{ $transaction->status_badge['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.transactions.show', $transaction) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <div class="relative inline-block text-left">
                                    <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $transaction->id }}')">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="dropdown-{{ $transaction->id }}" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            @if($transaction->status === 'completed' && $transaction->type === 'payment')
                                                <button type="button" onclick="openRefundModal('{{ $transaction->id }}', '{{ $transaction->amount }}')" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-undo text-orange-600 w-4"></i>
                                                    Issue Refund
                                                </button>
                                            @endif
                                            @if(in_array($transaction->status, ['pending', 'processing']))
                                                <button type="button" onclick="openStatusModal('{{ $transaction->id }}')" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-edit text-blue-600 w-4"></i>
                                                    Update Status
                                                </button>
                                            @endif
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <a href="{{ route('admin.transactions.show', $transaction) }}" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                <i class="fas fa-info-circle text-gray-600 w-4"></i>
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
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-receipt text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No transactions found</p>
                                <p class="text-sm text-gray-400">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($transactions->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

<!-- Refund Modal -->
<div id="refundModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Issue Refund</h3>
            <button onclick="closeRefundModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="refundForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Refund Amount</label>
                <input type="number" name="amount" id="refundAmount" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Leave empty for full refund">
                <p class="mt-1 text-xs text-gray-500">Maximum refundable: $<span id="maxRefund">0.00</span></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                <textarea name="reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Provide a reason for the refund..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRefundModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium">
                    Issue Refund
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Update Transaction Status</h3>
            <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="statusForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add any additional notes..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeStatusModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>
