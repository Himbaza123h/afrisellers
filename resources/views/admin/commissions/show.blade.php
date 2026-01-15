@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.commissions.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Commission Details</h1>
            </div>
            <p class="text-sm text-gray-500">Commission ID: #{{ $commission->id }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if($commission->status === 'pending')
                <form action="{{ route('admin.commissions.approve', $commission) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        <i class="fas fa-check"></i> Approve
                    </button>
                </form>
            @endif
            @if(in_array($commission->status, ['pending', 'approved']))
                <button onclick="openPayModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    <i class="fas fa-money-bill"></i> Mark as Paid
                </button>
                <button onclick="openCancelModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    <i class="fas fa-times"></i> Cancel
                </button>
            @endif
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Commission Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Commission Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Commission Type</p>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $commission->type_badge['class'] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $commission->type_badge['text'] ?? ucfirst($commission->commission_type) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status</p>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $commission->status_badge['class'] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $commission->status_badge['text'] ?? ucfirst($commission->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Commission Amount</p>
                        <p class="text-lg font-bold text-gray-900">{{ $commission->currency }} {{ number_format($commission->commission_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Commission Rate</p>
                        <p class="text-lg font-bold text-gray-900">{{ $commission->commission_rate }}%</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Transaction Amount</p>
                        <p class="text-sm font-medium text-gray-700">{{ $commission->currency }} {{ number_format($commission->transaction_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Currency</p>
                        <p class="text-sm font-medium text-gray-700">{{ $commission->currency }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Created At</p>
                        <p class="text-sm font-medium text-gray-700">{{ $commission->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($commission->paid_at)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Paid At</p>
                            <p class="text-sm font-medium text-gray-700">{{ $commission->paid_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transaction Details -->
            @if($commission->transaction)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Related Transaction</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-3 border-b">
                            <span class="text-sm text-gray-500">Transaction Number</span>
                            <a href="{{ route('admin.transactions.show', $commission->transaction) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                {{ $commission->transaction->transaction_number }}
                            </a>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b">
                            <span class="text-sm text-gray-500">Transaction Type</span>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($commission->transaction->type) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b">
                            <span class="text-sm text-gray-500">Transaction Status</span>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($commission->transaction->status) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-gray-500">Transaction Date</span>
                            <span class="text-sm font-medium text-gray-900">{{ $commission->transaction->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Payment Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Payment Information</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-3 border-b">
                        <span class="text-sm text-gray-500">Payment Status</span>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $commission->payment_status_badge['class'] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $commission->payment_status_badge['text'] ?? ucfirst($commission->payment_status) }}
                        </span>
                    </div>
                    @if($commission->payment_method)
                        <div class="flex items-center justify-between py-3 border-b">
                            <span class="text-sm text-gray-500">Payment Method</span>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $commission->payment_method)) }}</span>
                        </div>
                    @endif
                    @if($commission->payment_reference)
                        <div class="flex items-center justify-between py-3 border-b">
                            <span class="text-sm text-gray-500">Payment Reference</span>
                            <span class="text-sm font-medium text-gray-900">{{ $commission->payment_reference }}</span>
                        </div>
                    @endif
                    @if($commission->paid_at)
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-gray-500">Payment Date</span>
                            <span class="text-sm font-medium text-gray-900">{{ $commission->paid_at->format('M d, Y h:i A') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if($commission->notes)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Notes</h2>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $commission->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Right Column - User Details -->
        <div class="space-y-6">
            <!-- Earning User -->
            @if($commission->user)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Earning User</h2>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                            <span class="text-lg font-bold text-purple-700">{{ substr($commission->user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $commission->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $commission->user->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $commission->user) }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                        View User Profile
                    </a>
                </div>
            @endif

            <!-- Vendor -->
            @if($commission->vendor)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Vendor</h2>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-store text-blue-700"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $commission->vendor->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $commission->vendor->user->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.business-profile.show', $commission->vendor) }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                        View Vendor Profile
                    </a>
                </div>
            @endif

            <!-- Buyer -->
            @if($commission->buyer)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Buyer</h2>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-green-700"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $commission->buyer->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $commission->buyer->user->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.buyers.show', $commission->buyer) }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                        View Buyer Profile
                    </a>
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl border border-blue-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Stats</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Commission ID</span>
                        <span class="text-sm font-bold text-gray-900">#{{ $commission->id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Days Since Created</span>
                        <span class="text-sm font-bold text-gray-900">{{ $commission->created_at->diffInDays(now()) }}</span>
                    </div>
                    @if($commission->paid_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Days to Payment</span>
                            <span class="text-sm font-bold text-gray-900">{{ $commission->created_at->diffInDays($commission->paid_at) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pay Modal -->
<div id="payModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Mark as Paid</h3>
            <button onclick="closePayModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.commissions.mark-as-paid', $commission) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <select name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select method...</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="paypal">PayPal</option>
                    <option value="stripe">Stripe</option>
                    <option value="check">Check</option>
                    <option value="cash">Cash</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Reference</label>
                <input type="text" name="payment_reference" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter payment reference or transaction ID">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closePayModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    Mark as Paid
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Cancel Commission</h3>
            <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.commissions.cancel', $commission) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Cancellation</label>
                <textarea name="reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Provide a reason for cancelling this commission..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    Cancel Commission
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openPayModal() {
        document.getElementById('payModal').classList.remove('hidden');
    }

    function closePayModal() {
        document.getElementById('payModal').classList.add('hidden');
    }

    function openCancelModal() {
        document.getElementById('cancelModal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    // Close modals when clicking outside
    document.getElementById('payModal')?.addEventListener('click', function(event) {
        if (event.target === this) closePayModal();
    });

    document.getElementById('cancelModal')?.addEventListener('click', function(event) {
        if (event.target === this) closeCancelModal();
    });
</script>
@endpush
@endsection
