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
            <h1 class="text-xl font-bold text-gray-900">Referrals Management</h1>
            <p class="mt-1 text-xs text-gray-500">Track and manage all your referrals</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="printReport()" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('agent.referrals.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-plus"></i>
                <span>Add Referral</span>
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
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

    @if($errors->any())
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3 no-print">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                @foreach($errors->all() as $error)
                    <p class="text-sm font-medium text-red-900">{{ $error }}</p>
                @endforeach
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <!-- Total Referrals -->
            <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600 mb-1">Total Referrals</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
            </div>

            <!-- Active Referrals -->
            <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600 mb-1">Active</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
            </div>

            <!-- Pending Referrals -->
            <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600 mb-1">Pending</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
            </div>

            <!-- Inactive Referrals -->
            <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg">
                        <i class="fas fa-user-slash text-gray-600"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600 mb-1">Inactive</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['inactive']) }}</p>
            </div>

            <!-- Rejected Referrals -->
            <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600 mb-1">Rejected</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($stats['rejected']) }}</p>
            </div>

            <!-- Total Commissions -->
            <div class="stat-card p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-purple-600"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-600 mb-1">Total Commissions</p>
                <p class="text-xl font-bold text-gray-900">${{ number_format($stats['total_commissions'], 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div id="table-section" class="table-container">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Filters -->
            <div class="p-4 border-b border-gray-200 no-print">
                <form method="GET" action="{{ route('agent.referrals.index') }}" class="flex flex-wrap gap-3">
                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, email, phone, or code..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Status Filter -->
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>

                    <!-- Sort By -->
                    <select name="sort_by" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Date Added</option>
                        <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="status" {{ $sortBy == 'status' ? 'selected' : '' }}>Status</option>
                    </select>

                    <!-- Sort Order -->
                    <select name="sort_order" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>

                    <!-- Buttons -->
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-sm font-medium">
                        <i class="fas fa-filter"></i>
                        <span>Filter</span>
                    </button>

                    @if($search || $status != 'all' || $sortBy != 'created_at' || $sortOrder != 'desc')
                        <a href="{{ route('agent.referrals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all text-sm font-medium">
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ref Code</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Commissions</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date Added</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($referrals as $referral)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="text-sm font-mono font-semibold text-gray-900">{{ $referral->referral_code }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $referral->name }}</p>
                                        @if($referral->user)
                                            <p class="text-xs text-gray-500">Registered User</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm">
                                        <p class="text-gray-900">{{ $referral->email }}</p>
                                        @if($referral->phone)
                                            <p class="text-gray-500">{{ $referral->phone }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $referral->status_badge }}">
                                        {{ ucfirst($referral->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm">
                                        <p class="font-semibold text-gray-900">${{ number_format($referral->commissions->sum('amount'), 2) }}</p>
                                        <p class="text-xs text-gray-500">{{ $referral->commissions->count() }} commission(s)</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-900">{{ $referral->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $referral->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-3 text-center no-print">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('agent.referrals.show', $referral->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('agent.referrals.edit', $referral->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('agent.referrals.destroy', $referral->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this referral?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-users text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 mb-1">No referrals found</p>
                                        <p class="text-xs text-gray-500 mb-4">Start adding referrals to see them here</p>
                                        <a href="{{ route('agent.referrals.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-sm font-medium">
                                            <i class="fas fa-plus"></i>
                                            <span>Add Your First Referral</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($referrals->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 no-print">
                    {{ $referrals->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        btn.classList.add('text-gray-600');
    });

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

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
    window.open('{{ route("agent.referrals.print") }}', '_blank');
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
