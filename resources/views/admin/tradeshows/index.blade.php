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
            <h1 class="text-2xl font-bold text-gray-900">Tradeshows</h1>
            <p class="mt-1 text-sm text-gray-500">Manage all registered tradeshows and exhibitions</p>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Tradeshows</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-calendar-alt mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-calendar-alt text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Upcoming</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['upcoming'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ $stats['upcoming_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-clock text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Ongoing</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['ongoing'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                            <i class="fas fa-play mr-1 text-[10px]"></i> Active
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl">
                    <i class="fas fa-play-circle text-2xl text-emerald-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Completed</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['completed'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-check mr-1 text-[10px]"></i> Past
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Statistics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Verified</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['verified'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['verified_percentage'] }}%
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                    <i class="fas fa-certificate text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Featured</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['featured'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <i class="fas fa-star mr-1 text-[10px]"></i> Special
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl">
                    <i class="fas fa-star text-2xl text-amber-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Expected Visitors</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_expected_visitors']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                            <i class="fas fa-users mr-1 text-[10px]"></i> Total
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl">
                    <i class="fas fa-users text-2xl text-violet-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Avg Visitors</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['avg_visitors'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                            <i class="fas fa-chart-bar mr-1 text-[10px]"></i> Average
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl">
                    <i class="fas fa-chart-bar text-2xl text-cyan-600"></i>
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
                            <i class="fas fa-calendar-check mr-1 text-[10px]"></i> New
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl">
                    <i class="fas fa-calendar-check text-2xl text-teal-600"></i>
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
        <form method="GET" action="{{ route('admin.tradeshows.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, number, venue, city, industry, or organizer..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <select name="filter" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="published" {{ request('filter') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('filter') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="suspended" {{ request('filter') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <select name="event_status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Event Status</option>
                    <option value="upcoming" {{ request('event_status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ request('event_status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('event_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>

                <select name="country" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>

                <select name="verification" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Verification</option>
                    <option value="1" {{ request('verification') == '1' ? 'selected' : '' }}>Verified</option>
                    <option value="0" {{ request('verification') == '0' ? 'selected' : '' }}>Unverified</option>
                </select>

                <select name="featured" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Featured Status</option>
                    <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured</option>
                    <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Not Featured</option>
                </select>

                <select name="size" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Event Size</option>
                    <option value="large" {{ request('size') == 'large' ? 'selected' : '' }}>Large (10k+ visitors)</option>
                    <option value="medium" {{ request('size') == 'medium' ? 'selected' : '' }}>Medium (1k-10k)</option>
                    <option value="small" {{ request('size') == 'small' ? 'selected' : '' }}>Small (&lt;1k)</option>
                </select>

                <select name="date_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Time</option>
                    <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="next_month" {{ request('date_range') == 'next_month' ? 'selected' : '' }}>Next Month</option>
                    <option value="this_quarter" {{ request('date_range') == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="start_date" {{ request('sort_by') == 'start_date' ? 'selected' : '' }}>Sort by Start Date</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="expected_visitors" {{ request('sort_by') == 'expected_visitors' ? 'selected' : '' }}>Expected Visitors</option>
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'filter', 'event_status', 'country', 'verification', 'featured', 'size', 'date_range', 'sort_by']))
                    <a href="{{ route('admin.tradeshows.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Event</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Venue & Location</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Dates</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Expected Visitors</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Event Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($tradeshows as $tradeshow)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg">
                                        <span class="text-sm font-semibold text-purple-700">{{ strtoupper(substr($tradeshow->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">{{ $tradeshow->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $tradeshow->tradeshow_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium text-gray-900">{{ $tradeshow->venue_name }}</span>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-blue-600 text-xs"></i>
                                        <span class="text-xs text-gray-500">{{ $tradeshow->city }}, {{ $tradeshow->country->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium text-gray-900">{{ $tradeshow->start_date->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500">to {{ $tradeshow->end_date->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500">({{ $tradeshow->duration_days }} days)</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-users text-violet-600 text-sm"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($tradeshow->expected_visitors) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $tradeshow->event_status_badge['class'] }}">
                                    {{ $tradeshow->event_status_badge['text'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $tradeshow->status_badge['class'] }}">
                                        {{ $tradeshow->status_badge['text'] }}
                                    </span>
                                    @if($tradeshow->is_verified)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-certificate mr-1 text-[10px]"></i> Verified
                                        </span>
                                    @endif
                                    @if($tradeshow->is_featured)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <i class="fas fa-star mr-1 text-[10px]"></i> Featured
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.tradeshows.show', $tradeshow) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($tradeshow->status === 'pending')
                                        <form action="{{ route('admin.tradeshows.approve', $tradeshow) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600" title="Approve" onclick="return confirm('Approve this tradeshow?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if(!$tradeshow->is_verified)
                                        <form action="{{ route('admin.tradeshows.verify', $tradeshow) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-emerald-50 hover:text-emerald-600" title="Verify" onclick="return confirm('Verify this tradeshow?')">
                                                <i class="fas fa-certificate"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.tradeshows.feature', $tradeshow) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-amber-50 hover:text-amber-600" title="{{ $tradeshow->is_featured ? 'Unfeature' : 'Feature' }}">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                    <div class="relative inline-block text-left">
                                        <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $tradeshow->id }}')">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="dropdown-{{ $tradeshow->id }}" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1">
                                                @if($tradeshow->status === 'published')
                                                    <form action="{{ route('admin.tradeshows.suspend', $tradeshow) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left" onclick="return confirm('Suspend this tradeshow?')">
                                                            <i class="fas fa-ban text-orange-600 w-4"></i>
                                                            Suspend
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($tradeshow->is_verified)
                                                    <form action="{{ route('admin.tradeshows.unverify', $tradeshow) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                            <i class="fas fa-times-circle text-orange-600 w-4"></i>
                                                            Revoke Verification
                                                        </button>
                                                    </form>
                                                @endif
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <form action="{{ route('admin.tradeshows.destroy', $tradeshow) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-left" onclick="return confirm('Are you sure you want to delete this tradeshow? This action cannot be undone.')">
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
                                        <i class="fas fa-calendar-times text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No tradeshows found</p>
                                    <p class="text-sm text-gray-400">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tradeshows->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $tradeshows->links() }}
            </div>
        @endif
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
</script>
@endpush
@endsection
