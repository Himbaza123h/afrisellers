<!-- Bulk Actions -->
<div id="bulkActionsBar" class="hidden bg-blue-50 rounded-lg border border-blue-200 p-3 mb-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-blue-900"><span id="selectedCount">0</span> selected</span>
        </div>
        <div class="flex gap-2">
            <button onclick="bulkApprove()" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-medium">
                <i class="fas fa-check mr-1"></i>Approve Selected
            </button>
            <button onclick="openBulkPayModal()" class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs font-medium">
                <i class="fas fa-money-bill mr-1"></i>Pay Selected
            </button>
            <button onclick="clearSelection()" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-xs font-medium">
                Clear
            </button>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 mb-4">
    <form method="GET" action="{{ route('admin.commissions.index') }}" class="space-y-3">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user, transaction, reference..." class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <select name="status" class="pl-3 pr-8 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-sm">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="payment_status" class="pl-3 pr-8 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-sm">
                <option value="">Payment Status</option>
                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="processing" {{ request('payment_status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            <select name="commission_type" class="pl-3 pr-8 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-sm">
                <option value="">All Types</option>
                <option value="vendor_sale" {{ request('commission_type') == 'vendor_sale' ? 'selected' : '' }}>Vendor Sale</option>
                <option value="referral" {{ request('commission_type') == 'referral' ? 'selected' : '' }}>Referral</option>
                <option value="regional_admin" {{ request('commission_type') == 'regional_admin' ? 'selected' : '' }}>Regional Admin</option>
                <option value="platform_fee" {{ request('commission_type') == 'platform_fee' ? 'selected' : '' }}>Platform Fee</option>
            </select>

            <select name="user" class="pl-3 pr-8 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-sm">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-medium">
                <i class="fas fa-filter"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'status', 'payment_status', 'commission_type', 'user']))
                <a href="{{ route('admin.commissions.index') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-xs font-medium">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-4 py-2 text-xs font-semibold text-left text-gray-700 uppercase">Transaction</th>
                    <th class="px-4 py-2 text-xs font-semibold text-left text-gray-700 uppercase">User</th>
                    <th class="px-4 py-2 text-xs font-semibold text-left text-gray-700 uppercase">Type</th>
                    <th class="px-4 py-2 text-xs font-semibold text-left text-gray-700 uppercase">Amount</th>
                    <th class="px-4 py-2 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                    <th class="px-4 py-2 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($commissions as $commission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <input type="checkbox" class="commission-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $commission->id }}" onchange="updateBulkActions()">
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-900">{{ $commission->transaction->transaction_number ?? 'N/A' }}</span>
                                <span class="text-xs text-gray-500">{{ $commission->created_at->format('M d, Y') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-purple-700">{{ substr($commission->user->name ?? 'N', 0, 1) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $commission->user->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">{{ $commission->user->email ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $commission->type_badge['class'] }}">
                                {{ $commission->type_badge['text'] }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-bold text-gray-900">{{ $commission->currency }} {{ number_format($commission->commission_amount, 2) }}</span>
                                <span class="text-xs text-gray-500">{{ $commission->commission_rate }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex flex-col gap-0.5">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $commission->status_badge['class'] }} inline-block w-fit">
                                    {{ $commission->status_badge['text'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $commission->payment_status_badge['class'] }} inline-block w-fit">
                                    {{ $commission->payment_status_badge['text'] }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.commissions.show', $commission) }}" class="p-1 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <div class="relative inline-block text-left">
                                    <button type="button" class="p-1 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $commission->id }}')">
                                        <i class="fas fa-ellipsis-v text-sm"></i>
                                    </button>
                                    <div id="dropdown-{{ $commission->id }}" class="hidden absolute right-0 mt-1 w-40 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            @if($commission->status === 'pending')
                                                <form action="{{ route('admin.commissions.approve', $commission) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 text-left">
                                                        <i class="fas fa-check text-blue-600 w-3"></i>
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif
                                            @if(in_array($commission->status, ['pending', 'approved']))
                                                <button type="button" onclick="openPayModal('{{ $commission->id }}')" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-money-bill text-green-600 w-3"></i>
                                                    Mark as Paid
                                                </button>
                                                <button type="button" onclick="openCancelModal('{{ $commission->id }}')" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 text-left">
                                                    <i class="fas fa-times text-red-600 w-3"></i>
                                                    Cancel
                                                </button>
                                            @endif
                                            <div class="border-t border-gray-100 my-0.5"></div>
                                            <a href="{{ route('admin.commissions.show', $commission) }}" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 text-left">
                                                <i class="fas fa-info-circle text-gray-600 w-3"></i>
                                                Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-hand-holding-usd text-xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No commissions found</p>
                                <p class="text-xs text-gray-400">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($commissions->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $commissions->links() }}
        </div>
    @endif
</div>

<!-- Pay Modal -->
<div id="payModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-5 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900">Mark as Paid</h3>
            <button onclick="closePayModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="payForm" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select name="payment_method" required class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Select method...</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="paypal">PayPal</option>
                    <option value="stripe">Stripe</option>
                    <option value="check">Check</option>
                    <option value="cash">Cash</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Reference</label>
                <input type="text" name="payment_reference" required class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Enter payment reference">
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closePayModal()" class="flex-1 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                    Mark as Paid
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-5 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900">Cancel Commission</h3>
            <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="cancelForm" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                <textarea name="reason" rows="3" required class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Provide a reason..."></textarea>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeCancelModal()" class="flex-1 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                    Cancel Commission
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Pay Modal -->
<div id="bulkPayModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-5 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900">Bulk Pay</h3>
            <button onclick="closeBulkPayModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="bulkPayForm" action="{{ route('admin.commissions.bulk-pay') }}" method="POST">
            @csrf
            <input type="hidden" name="commission_ids" id="bulkPayCommissionIds">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select name="payment_method" required class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Select method...</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="paypal">PayPal</option>
                    <option value="stripe">Stripe</option>
                    <option value="check">Check</option>
                    <option value="cash">Cash</option>
                </select>
            </div>
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-xs text-blue-900">Paying <span id="bulkPayCount" class="font-bold">0</span> commission(s)</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeBulkPayModal()" class="flex-1 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                    Pay Selected
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Pay Modal
function openPayModal(commissionId) {
    const modal = document.getElementById('payModal');
    const form = document.getElementById('payForm');
    form.action = `/admin/commissions/${commissionId}/mark-as-paid`;
    modal.classList.remove('hidden');
}

function closePayModal() {
    document.getElementById('payModal').classList.add('hidden');
}

// Cancel Modal
function openCancelModal(commissionId) {
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');
    form.action = `/admin/commissions/${commissionId}/cancel`;
    modal.classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

function closeBulkPayModal() {
    document.getElementById('bulkPayModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('payModal')?.addEventListener('click', function(event) {
    if (event.target === this) closePayModal();
});

document.getElementById('cancelModal')?.addEventListener('click', function(event) {
    if (event.target === this) closeCancelModal();
});

document.getElementById('bulkPayModal')?.addEventListener('click', function(event) {
    if (event.target === this) closeBulkPayModal();
});
</script>
