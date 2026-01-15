<!-- Filters -->
<div class="mb-6">
    <form method="GET" action="{{ route('vendor.earnings.index') }}" class="space-y-4">
        <input type="hidden" name="tab" value="list">

        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by transaction #, order #, customer..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
        </div>

        <div class="flex flex-wrap gap-3 items-center">
            <label class="text-sm font-medium text-gray-700">Filters:</label>

            <div class="relative">
                <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Date range" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg w-56 cursor-pointer bg-white">
                <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
            </div>

            <select name="payment_method" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Payment Methods</option>
                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="paypal" {{ request('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                <option value="stripe" {{ request('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                <option value="other" {{ request('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
            </select>

            <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="completed_at" {{ request('sort_by') == 'completed_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="amount" {{ request('sort_by') == 'amount' ? 'selected' : '' }}>Amount</option>
                <option value="transaction_number" {{ request('sort_by') == 'transaction_number' ? 'selected' : '' }}>Transaction #</option>
            </select>

            <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>

            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-filter"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'date_range', 'payment_method', 'sort_by']))
                <a href="{{ route('vendor.earnings.index', ['tab' => 'list']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Transactions Table -->
<div class="overflow-x-auto">
    <table class="w-full">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Transaction</th>
                <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Order</th>
                <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Customer</th>
                <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Payment Method</th>
                <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Amount</th>
                <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Date</th>
                <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($transactions as $transaction)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <span class="text-sm font-semibold text-gray-900">#{{ $transaction->transaction_number }}</span>
                            @if($transaction->payment_reference)
                                <span class="text-xs text-gray-500">Ref: {{ $transaction->payment_reference }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($transaction->order)
                            <a href="{{ route('vendor.orders.show', $transaction->order) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                #{{ $transaction->order->order_number }}
                            </a>
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full">
                                <span class="text-sm font-semibold text-blue-700">{{ substr($transaction->buyer->name, 0, 1) }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">{{ $transaction->buyer->name }}</span>
                                <span class="text-xs text-gray-500">{{ $transaction->buyer->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($transaction->payment_method)
                            @php
                                $methodIcons = [
                                    'cash' => 'fa-money-bill-wave',
                                    'credit_card' => 'fa-credit-card',
                                    'bank_transfer' => 'fa-university',
                                    'paypal' => 'fa-paypal',
                                    'stripe' => 'fa-stripe',
                                    'other' => 'fa-wallet',
                                ];
                                $icon = $methodIcons[$transaction->payment_method] ?? 'fa-wallet';
                            @endphp
                            <div class="flex items-center gap-2">
                                <i class="fas {{ $icon }} text-gray-400"></i>
                                <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-base font-bold text-green-600">${{ number_format($transaction->amount, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <span class="text-sm text-gray-900">{{ $transaction->completed_at->format('M d, Y') }}</span>
                            <span class="text-xs text-gray-500">{{ $transaction->completed_at->format('h:i A') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('vendor.transactions.show', $transaction) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($transaction->order)
                                <a href="{{ route('vendor.orders.invoice', $transaction->order) }}" target="_blank" class="p-2 text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600" title="View Invoice">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-dollar-sign text-4xl text-gray-300"></i>
                            </div>
                            <p class="text-lg font-semibold text-gray-900 mb-1">No earnings found</p>
                            <p class="text-sm text-gray-500 mb-6">Completed transactions will appear here</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if(method_exists($transactions, 'hasPages') && $transactions->hasPages())
    <div class="mt-6 pt-4 border-t">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-700">Showing {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} of {{ $transactions->total() }}</span>
            <div>{{ $transactions->appends(request()->except('page'))->links() }}</div>
        </div>
    </div>
@endif
