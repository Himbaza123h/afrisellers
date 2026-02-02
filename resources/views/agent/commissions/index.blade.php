@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Commission Management</h1>
            <p class="mt-1 text-xs text-gray-500">Track and manage your commission earnings</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="printReport()" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('agent.referrals.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-users"></i>
                <span>View Referrals</span>
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-green-600 border-b-2 border-green-600 transition-colors">
            All
        </button>
        <button onclick="switchTab('stats')" id="tab-stats" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Stats
        </button>
        <button onclick="switchTab('table')" id="tab-table" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Table
        </button>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3 no-print">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3 no-print">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container">
        <!-- Amount Statistics -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-4">
            <!-- Total Commissions -->
            <div class="stat-card p-5 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-xl text-purple-600"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600 mb-1">Total Earned</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_amount'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">All time earnings</p>
            </div>

            <!-- Paid Commissions -->
            <div class="stat-card p-5 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-xl text-green-600"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600 mb-1">Paid Out</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['paid_amount'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['paid'] }} payment(s)</p>
            </div>

            <!-- Pending Commissions -->
            <div class="stat-card p-5 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-clock text-xl text-yellow-600"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600 mb-1">Pending</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['pending_amount'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['pending'] }} pending</p>
            </div>

            <!-- Period Stats -->
            <div class="stat-card p-5 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-calendar text-xl text-blue-600"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600 mb-1">Period Earnings</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($periodStats['amount'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $periodStats['count'] }} commission(s)</p>
            </div>
        </div>

        <!-- Count Statistics -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Count -->
            <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1">Total</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-list text-purple-600"></i>
                    </div>
                </div>
            </div>

            <!-- Paid Count -->
            <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1">Paid</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['paid']) }}</p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Count -->
            <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1">Pending</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-hourglass-half text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <!-- Processing Count -->
            <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1">Processing</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['processing']) }}</p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-spinner text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div id="table-section" class="table-container">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Filters -->
            <div class="p-4 border-b border-gray-200 no-print">
                <form method="GET" action="{{ route('agent.commissions.index') }}" class="flex flex-wrap gap-3">
                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search by referral name, email, or code..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Status Filter -->
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $status == 'processing' ? 'selected' : '' }}>Processing</option>
                    </select>

                    <!-- Date Filter -->
                    <select name="date_filter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="all" {{ $dateFilter == 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="this_year" {{ $dateFilter == 'this_year' ? 'selected' : '' }}>This Year</option>
                    </select>

                    <!-- Sort By -->
                    <select name="sort_by" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Date Earned</option>
                        <option value="amount" {{ $sortBy == 'amount' ? 'selected' : '' }}>Amount</option>
                        <option value="status" {{ $sortBy == 'status' ? 'selected' : '' }}>Status</option>
                    </select>

                    <!-- Sort Order -->
                    <select name="sort_order" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>

                    <!-- Buttons -->
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all text-sm font-medium">
                        <i class="fas fa-filter"></i>
                        <span>Filter</span>
                    </button>

                    @if($search || $status != 'all' || $dateFilter != 'all' || $sortBy != 'created_at' || $sortOrder != 'desc')
                        <a href="{{ route('agent.commissions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all text-sm font-medium">
                            <i class="fas fa-times"></i>
                            <span>Clear</span>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Commission ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Referral</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date Earned</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Payment Date</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($commissions as $commission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="text-sm font-mono font-semibold text-gray-900">#{{ str_pad($commission->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $commission->referral->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $commission->referral->referral_code ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-base font-bold text-gray-900">${{ number_format($commission->amount, 2) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'paid' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$commission->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($commission->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-900">{{ $commission->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $commission->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($commission->paid_at)
                                        <p class="text-sm text-gray-900">{{ $commission->paid_at->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $commission->paid_at->diffForHumans() }}</p>
                                    @else
                                        <span class="text-sm text-gray-400">Not paid yet</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center no-print">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('agent.referrals.show', $commission->referral_id) }}" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Referral">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-dollar-sign text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 mb-1">No commissions found</p>
                                        <p class="text-xs text-gray-500 mb-4">Commissions will appear here when you earn them</p>
                                        <a href="{{ route('agent.referrals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-sm font-medium">
                                            <i class="fas fa-users"></i>
                                            <span>View Your Referrals</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($commissions->count() > 0)
                        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                            <tr class="font-bold">
                                <td colspan="2" class="px-4 py-3 text-sm text-gray-900">Total on this page:</td>
                                <td class="px-4 py-3 text-base text-gray-900">${{ number_format($commissions->sum('amount'), 2) }}</td>
                                <td colspan="4" class="px-4 py-3 text-xs text-gray-500">{{ $commissions->count() }} commission(s)</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Pagination -->
            @if($commissions->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 no-print">
                    {{ $commissions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-green-600', 'border-b-2', 'border-green-600');
        btn.classList.add('text-gray-600');
    });

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-green-600', 'border-b-2', 'border-green-600');

    // Show/hide sections based on tab
    const statsSection = document.getElementById('stats-section');
    const tableSection = document.getElementById('table-section');

    switch(tab) {
        case 'all':
            statsSection.style.display = 'block';
            tableSection.style.display = 'block';
            break;
        case 'stats':
            statsSection.style.display = 'block';
            tableSection.style.display = 'none';
            break;
        case 'table':
            statsSection.style.display = 'none';
            tableSection.style.display = 'block';
            break;
    }
}

function printReport() {
    const params = new URLSearchParams(window.location.search);
    window.open('{{ route("agent.commissions.print") }}?' + params.toString(), '_blank');
}

// Auto-hide success/error messages after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    });
}, 5000);
</script>
@endsection
