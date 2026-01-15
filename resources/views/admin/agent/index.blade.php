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
            <h1 class="text-2xl font-bold text-gray-900">Agents</h1>
            <p class="mt-1 text-sm text-gray-500">Manage all registered sales agents</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-download"></i>
                <span>Export CSV</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-sm">
                <i class="fas fa-check-double"></i>
                <span>Bulk Actions</span>
            </button>
        </div>
    </div>

    <!-- Statistics Cards - Main -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Agents</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-user-tie mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-user-tie text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Agents</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['active'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['active_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending Review</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $stats['pending_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Suspended</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['suspended'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $stats['suspended_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                    <i class="fas fa-ban text-2xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance & Secondary Statistics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Commission</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_commission'], 2) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                            <i class="fas fa-dollar-sign mr-1 text-[10px]"></i> Earned
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl">
                    <i class="fas fa-hand-holding-usd text-2xl text-emerald-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Sales</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_sales']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                            <i class="fas fa-shopping-cart mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl">
                    <i class="fas fa-chart-line text-2xl text-violet-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Avg Commission</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['avg_commission_rate'] }}%</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                            <i class="fas fa-percentage mr-1 text-[10px]"></i> Rate
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl">
                    <i class="fas fa-percent text-2xl text-cyan-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">This Week</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['this_week'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-calendar-week mr-1 text-[10px]"></i> 7 days
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-calendar-check text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">This Month</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['this_month'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                            <i class="fas fa-calendar-alt mr-1 text-[10px]"></i> 30 days
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl">
                    <i class="fas fa-chart-bar text-2xl text-teal-600"></i>
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

    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                @foreach($errors->all() as $error)
                    <p class="text-sm font-medium text-red-900">{{ $error }}</p>
                @endforeach
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.agents.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone, city, or company..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <select name="filter" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="suspended" {{ request('filter') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <select name="country" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>

                <select name="email_verified" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Email Status</option>
                    <option value="1" {{ request('email_verified') == '1' ? 'selected' : '' }}>Verified</option>
                    <option value="0" {{ request('email_verified') == '0' ? 'selected' : '' }}>Not Verified</option>
                </select>

                <select name="commission_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Commission Range</option>
                    <option value="high" {{ request('commission_range') == 'high' ? 'selected' : '' }}>High (&gt; $1,000)</option>
                    <option value="medium" {{ request('commission_range') == 'medium' ? 'selected' : '' }}>Medium ($500 - $1,000)</option>
                    <option value="low" {{ request('commission_range') == 'low' ? 'selected' : '' }}>Low (&lt; $500)</option>
                </select>

                <select name="date_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="commission_earned" {{ request('sort_by') == 'commission_earned' ? 'selected' : '' }}>Commission</option>
                    <option value="total_sales" {{ request('sort_by') == 'total_sales' ? 'selected' : '' }}>Sales</option>
                    <option value="account_status" {{ request('sort_by') == 'account_status' ? 'selected' : '' }}>Status</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'filter', 'country', 'email_verified', 'commission_range', 'date_range', 'sort_by']))
                    <a href="{{ route('admin.agents.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Agent</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Contact</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Location</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Performance</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Registered</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($agents as $agent)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-lg">
                                        <span class="text-sm font-semibold text-indigo-700">{{ strtoupper(substr($agent->user->name ?? 'NA', 0, 2)) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">{{ $agent->user->name ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500">{{ $agent->company_name ?? 'No company' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium text-gray-900">{{ $agent->user->email ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">{{ $agent->phone_code }} {{ $agent->phone }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-blue-600 text-xs"></i>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-900">{{ $agent->city ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500">{{ $agent->country->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($agent->commission_earned, 2) }}</span>
                                    <span class="text-xs text-gray-500">{{ $agent->total_sales }} sales • {{ $agent->commission_rate }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    @php
                                        $statusColors = [
                                            'active' => ['Active', 'bg-green-100 text-green-800'],
                                            'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                            'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                                        ];
                                        $status = $statusColors[$agent->account_status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>

                                    @if($agent->email_verified)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-check-circle mr-1 text-[10px]"></i> Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-clock mr-1 text-[10px]"></i> Pending
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm text-gray-900">{{ $agent->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500">{{ $agent->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.agent.show', $agent) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($agent->account_status === 'pending')
                                        <form action="{{ route('admin.agent.activate', $agent) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600" title="Activate" onclick="return confirm('Activate this agent account?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($agent->account_status === 'active')
                                        <form action="{{ route('admin.agent.suspend', $agent) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-orange-50 hover:text-orange-600" title="Suspend" onclick="return confirm('Suspend this agent account?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <div class="relative inline-block text-left">
                                        <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $agent->id }}')">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="dropdown-{{ $agent->id }}" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1">
                                                @if($agent->account_status === 'suspended')
                                                    <form action="{{ route('admin.agent.activate', $agent) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left" onclick="return confirm('Reactivate this agent account?')">
                                                            <i class="fas fa-undo text-green-600 w-4"></i>
                                                            Reactivate
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-edit text-amber-600 w-4"></i>
                                                    Edit
                                                </a>
                                                @if(!$agent->email_verified)
                                                    <form action="{{ route('admin.agent.verifyEmail', $agent) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                            <i class="fas fa-envelope-circle-check text-emerald-600 w-4"></i>
                                                            Verify Email
                                                        </button>
                                                    </form>
                                                @endif
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <form action="{{ route('admin.agent.destroy', $agent) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-left" onclick="return confirm('Delete this agent? This cannot be undone.')">
                                                        <i class="fas fa-trash-alt w-4"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-user-tie text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No agents found</p>
                                    <p class="text-sm text-gray-500 mb-6">Registered agents will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($agents, 'hasPages') && $agents->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $agents->firstItem() }}-{{ $agents->lastItem() }} of {{ $agents->total() }}</span>
                    <div>{{ $agents->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function toggleDropdown(event, dropdownId) {
    event.stopPropagation();
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) d.classList.add('hidden');
    });
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function() {
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    allDropdowns.forEach(d => d.classList.add('hidden'));
});
</script>
@endsection
