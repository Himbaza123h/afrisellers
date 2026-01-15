@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Escrow Management</h1>
            <p class="mt-1 text-sm text-gray-500">Manage and monitor escrow transactions</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <form action="{{ route('admin.escrow.export') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                    <i class="fas fa-download"></i>
                    <span>Export CSV</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Escrows</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-shield-alt mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-handshake text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1 text-[10px]"></i> Awaiting
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                    <i class="fas fa-hourglass-half text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-lock mr-1 text-[10px]"></i> Held
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-lock text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Released</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['released']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1 text-[10px]"></i> Complete
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Disputed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['disputed']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-triangle mr-1 text-[10px]"></i> Issues
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                    <i class="fas fa-exclamation-circle text-2xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Statistics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Held</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_held'], 2) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <i class="fas fa-vault mr-1 text-[10px]"></i> In Escrow
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl">
                    <i class="fas fa-coins text-2xl text-amber-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Released</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_released'], 2) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                            <i class="fas fa-unlock mr-1 text-[10px]"></i> Completed
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl">
                    <i class="fas fa-money-bill-wave text-2xl text-emerald-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Awaiting Release</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['awaiting_release']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            <i class="fas fa-clock mr-1 text-[10px]"></i> Ready
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl">
                    <i class="fas fa-clipboard-check text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Auto-Release Ready</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['auto_release_ready']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                            <i class="fas fa-sync mr-1 text-[10px]"></i> Scheduled
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl">
                    <i class="fas fa-robot text-2xl text-cyan-600"></i>
                </div>
            </div>
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

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
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
    <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm">
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
    </div>

    <!-- Pagination -->
    @if($escrows->hasPages())
        <div class="flex items-center justify-between px-6 py-4 bg-white rounded-xl border border-gray-200 shadow-sm">
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

<!-- Release Modal -->
<div id="releaseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Release Funds</h3>
        </div>
        <form id="releaseForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Are you sure you want to release the funds to the vendor? This action cannot be undone.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Release Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add any notes about this release..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                <button type="button" onclick="closeModal('releaseModal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    Release Funds
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Refund Escrow</h3>
        </div>
        <form id="refundForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Are you sure you want to refund this escrow to the buyer? This action cannot be undone.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Refund Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Explain why this escrow is being refunded..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                <button type="button" onclick="closeModal('refundModal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium">
                    Process Refund
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Dispute Modal -->
<div id="disputeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Mark as Disputed</h3>
        </div>
        <form id="disputeForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Mark this escrow as disputed. This will freeze the funds until the dispute is resolved.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dispute Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Describe the dispute..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                <button type="button" onclick="closeModal('disputeModal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    Mark as Disputed
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resolve Dispute Modal -->
<div id="resolveDisputeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Resolve Dispute</h3>
        </div>
        <form id="resolveDisputeForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Choose how to resolve this dispute.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Resolution Action <span class="text-red-500">*</span></label>
                    <select name="resolution_action" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select action...</option>
                        <option value="release">Release to Vendor</option>
                        <option value="refund">Refund to Buyer</option>
                        <option value="partial">Partial Split</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Resolution Notes <span class="text-red-500">*</span></label>
                    <textarea name="resolution_notes" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Explain the resolution decision..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                <button type="button" onclick="closeModal('resolveDisputeModal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Resolve Dispute
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-900">Cancel Escrow</h3>
        </div>
        <form id="cancelForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600">Are you sure you want to cancel this escrow? This action cannot be undone.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Explain why this escrow is being cancelled..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                <button type="button" onclick="closeModal('cancelModal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium">
                    Cancel Escrow
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Toggle dropdown
    function toggleDropdown(event, id) {
        event.stopPropagation();
        const dropdown = document.getElementById(id);
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');

        allDropdowns.forEach(d => {
            if (d.id !== id) {
                d.classList.add('hidden');
            }
        });

        dropdown.classList.toggle('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        allDropdowns.forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    });

    // Modal functions
    function openReleaseModal(escrowId) {
        const modal = document.getElementById('releaseModal');
        const form = document.getElementById('releaseForm');
        form.action = `/admin/escrow/${escrowId}/release`;
        modal.classList.remove('hidden');
    }

    function openRefundModal(escrowId) {
        const modal = document.getElementById('refundModal');
        const form = document.getElementById('refundForm');
        form.action = `/admin/escrow/${escrowId}/refund`;
        modal.classList.remove('hidden');
    }

    function openDisputeModal(escrowId) {
        const modal = document.getElementById('disputeModal');
        const form = document.getElementById('disputeForm');
        form.action = `/admin/escrow/${escrowId}/dispute`;
        modal.classList.remove('hidden');
    }

    function openResolveDisputeModal(escrowId) {
        const modal = document.getElementById('resolveDisputeModal');
        const form = document.getElementById('resolveDisputeForm');
        form.action = `/admin/escrow/${escrowId}/resolve-dispute`;
        modal.classList.remove('hidden');
    }

    function openCancelModal(escrowId) {
        const modal = document.getElementById('cancelModal');
        const form = document.getElementById('cancelForm');
        form.action = `/admin/escrow/${escrowId}`;
        modal.classList.remove('hidden');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
    }

    // Close modals on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = ['releaseModal', 'refundModal', 'disputeModal', 'resolveDisputeModal', 'cancelModal'];
            modals.forEach(modalId => closeModal(modalId));
        }
    });

    // Close modal when clicking outside
    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal(this.id);
            }
        });
    });
</script>
@endpush

@endsection
