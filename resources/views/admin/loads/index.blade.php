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
            <h1 class="text-2xl font-bold text-gray-900">Load Management</h1>
            <p class="mt-1 text-sm text-gray-500">Manage all freight loads and shipments</p>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Loads</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-truck mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-truck-loading text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Posted</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['posted'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            <i class="fas fa-bullhorn mr-1 text-[10px]"></i> Active
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl">
                    <i class="fas fa-bullhorn text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">In Transit</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['in_transit'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            {{ $stats['in_transit_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                    <i class="fas fa-shipping-fast text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Delivered</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['delivered'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['delivered_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">completed</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Statistics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Cancelled</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['cancelled'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-ban mr-1 text-[10px]"></i> Lost
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Bids</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_bids']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-gavel mr-1 text-[10px]"></i> Bids
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-gavel text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Avg Bids/Load</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['avg_bids'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                            <i class="fas fa-chart-line mr-1 text-[10px]"></i> Average
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl">
                    <i class="fas fa-chart-line text-2xl text-cyan-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Weight</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_weight'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                            <i class="fas fa-weight mr-1 text-[10px]"></i> KG
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl">
                    <i class="fas fa-weight-hanging text-2xl text-teal-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">This Month</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['this_month'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                            <i class="fas fa-calendar-check mr-1 text-[10px]"></i> New
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl">
                    <i class="fas fa-calendar-check text-2xl text-pink-600"></i>
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
        <form method="GET" action="{{ route('admin.loads.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by load number, cargo type, city, or tracking number..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <select name="filter" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="posted" {{ request('filter') == 'posted' ? 'selected' : '' }}>Posted</option>
                    <option value="bidding" {{ request('filter') == 'bidding' ? 'selected' : '' }}>Bidding</option>
                    <option value="assigned" {{ request('filter') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_transit" {{ request('filter') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="delivered" {{ request('filter') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('filter') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <select name="origin_country" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Origin Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('origin_country') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>

                <select name="destination_country" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Destination Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('destination_country') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>

                <select name="cargo_type" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Cargo Type</option>
                    <option value="general" {{ request('cargo_type') == 'general' ? 'selected' : '' }}>General</option>
                    <option value="perishable" {{ request('cargo_type') == 'perishable' ? 'selected' : '' }}>Perishable</option>
                    <option value="hazardous" {{ request('cargo_type') == 'hazardous' ? 'selected' : '' }}>Hazardous</option>
                    <option value="fragile" {{ request('cargo_type') == 'fragile' ? 'selected' : '' }}>Fragile</option>
                    <option value="bulk" {{ request('cargo_type') == 'bulk' ? 'selected' : '' }}>Bulk</option>
                </select>

                <select name="weight_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Weight Range</option>
                    <option value="heavy" {{ request('weight_range') == 'heavy' ? 'selected' : '' }}>Heavy (10,000+ kg)</option>
                    <option value="medium" {{ request('weight_range') == 'medium' ? 'selected' : '' }}>Medium (1,000-9,999 kg)</option>
                    <option value="light" {{ request('weight_range') == 'light' ? 'selected' : '' }}>Light (&lt;1,000 kg)</option>
                </select>

                <select name="assignment" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Assignment Status</option>
                    <option value="assigned" {{ request('assignment') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="unassigned" {{ request('assignment') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                </select>

                <select name="pickup_date" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Pickup Date</option>
                    <option value="upcoming" {{ request('pickup_date') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="overdue" {{ request('pickup_date') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>

                <select name="date_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Created Date</option>
                    <option value="load_number" {{ request('sort_by') == 'load_number' ? 'selected' : '' }}>Load Number</option>
                    <option value="pickup_date" {{ request('sort_by') == 'pickup_date' ? 'selected' : '' }}>Pickup Date</option>
                    <option value="weight" {{ request('sort_by') == 'weight' ? 'selected' : '' }}>Weight</option>
                    <option value="budget" {{ request('sort_by') == 'budget' ? 'selected' : '' }}>Budget</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'filter', 'origin_country', 'destination_country', 'cargo_type', 'weight_range', 'assignment', 'pickup_date', 'date_range', 'sort_by']))
                    <a href="{{ route('admin.loads.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Load Info</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Route</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Cargo Details</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Pickup Date</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Transporter</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($loads as $load)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg">
                                        <i class="fas fa-truck text-blue-700"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">{{ $load->load_number }}</span>
                                        <span class="text-xs text-gray-500">Posted by {{ $load->user->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-green-600 text-xs"></i>
                                        <span class="text-xs font-medium text-gray-900">{{ $load->origin_city }}</span>
                                        <span class="text-xs text-gray-500">({{ $load->originCountry->name ?? 'N/A' }})</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-arrow-down text-gray-400 text-xs"></i>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-red-600 text-xs"></i>
                                        <span class="text-xs font-medium text-gray-900">{{ $load->destination_city }}</span>
                                        <span class="text-xs text-gray-500">({{ $load->destinationCountry->name ?? 'N/A' }})</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 inline-block w-fit">
                                        {{ ucfirst($load->cargo_type ?? 'N/A') }}
                                    </span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <i class="fas fa-weight-hanging text-teal-600 text-xs"></i>
                                        <span class="text-xs text-gray-700">{{ number_format($load->weight ?? 0) }} kg</span>
                                    </div>
                                    @if($load->budget)
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-dollar-sign text-green-600 text-xs"></i>
                                            <span class="text-xs text-gray-700">${{ number_format($load->budget, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($load->pickup_date)
                                        <span class="text-sm font-medium text-gray-900">{{ $load->pickup_date->format('M d, Y') }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $load->urgency_badge['class'] }} inline-block w-fit">
                                            {{ $load->urgency_badge['text'] }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500">Not set</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($load->assignedTransporter)
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-semibold text-green-700">{{ substr($load->assignedTransporter->name, 0, 2) }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-900">{{ $load->assignedTransporter->name }}</span>
                                            <span class="text-xs text-gray-500">Assigned</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-500 italic">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $load->status_badge['class'] }}">
                                    {{ $load->status_badge['text'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.loads.show', $load) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <div class="relative inline-block text-left">
                                        <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $load->id }}')">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="dropdown-{{ $load->id }}" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1">
                                                @if(!in_array($load->status, ['delivered', 'cancelled']))
                                                    <button type="button" onclick="openCancelModal('{{ $load->id }}')" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                        <i class="fas fa-ban text-orange-600 w-4"></i>
                                                        Cancel Load
                                                    </button>
                                                @endif
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <form action="{{ route('admin.loads.destroy', $load) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-left" onclick="return confirm('Are you sure you want to delete this load? This action cannot be undone.')">
                                                        <i class="fas fa-trash w-4"></i>
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
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-truck-loading text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No loads found</p>
                                    <p class="text-sm text-gray-400">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($loads->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $loads->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Cancel Load Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Cancel Load</h3>
            <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="cancelForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason</label>
                <textarea name="reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Please provide a reason for cancellation..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    Confirm Cancellation
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleDropdown(event, dropdownId) {
        event.stopPropagation();
        const dropdown = document.getElementById(dropdownId);
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');

        // Close all other dropdowns
        allDropdowns.forEach(d => {
            if (d.id !== dropdownId) {
                d.classList.add('hidden');
            }
        });

        // Toggle current dropdown
        dropdown.classList.toggle('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        allDropdowns.forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    });

    function openCancelModal(loadId) {
        const modal = document.getElementById('cancelModal');
        const form = document.getElementById('cancelForm');
        form.action = `/admin/loads/${loadId}/cancel`;
        modal.classList.remove('hidden');
    }

    function closeCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('cancelModal')?.addEventListener('click', function(event) {
        if (event.target === this) {
            closeCancelModal();
        }
    });
</script>
@endpush
@endsection
