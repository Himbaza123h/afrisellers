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
            <h1 class="text-2xl font-bold text-gray-900">Showrooms</h1>
            <p class="mt-1 text-sm text-gray-500">Manage all registered showrooms and exhibitions</p>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Showrooms</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-store mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-store text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1 text-[10px]"></i> Review
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
                    <p class="text-sm font-medium text-gray-600 mb-1">This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-calendar-plus mr-1 text-[10px]"></i> New
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-calendar-alt text-2xl text-purple-600"></i>
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
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                            {{ $stats['verified_percentage'] }}%
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl">
                    <i class="fas fa-certificate text-2xl text-emerald-600"></i>
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
                            {{ $stats['featured_percentage'] }}%
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Views</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_views']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                            <i class="fas fa-eye mr-1 text-[10px]"></i> Views
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl">
                    <i class="fas fa-eye text-2xl text-cyan-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Inquiries</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_inquiries']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                            <i class="fas fa-envelope mr-1 text-[10px]"></i> Total
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl">
                    <i class="fas fa-envelope text-2xl text-violet-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Avg Rating</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['avg_rating'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <i class="fas fa-star mr-1 text-[10px]"></i> Rating
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                    <i class="fas fa-star-half-alt text-2xl text-orange-600"></i>
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
        <form method="GET" action="{{ route('admin.showrooms.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, number, email, phone, city, or business type..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <select name="filter" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="suspended" {{ request('filter') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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

                <select name="business_type" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Business Type</option>
                    <option value="manufacturer" {{ request('business_type') == 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                    <option value="distributor" {{ request('business_type') == 'distributor' ? 'selected' : '' }}>Distributor</option>
                    <option value="retailer" {{ request('business_type') == 'retailer' ? 'selected' : '' }}>Retailer</option>
                    <option value="wholesaler" {{ request('business_type') == 'wholesaler' ? 'selected' : '' }}>Wholesaler</option>
                </select>

                <select name="size" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Showroom Size</option>
                    <option value="large" {{ request('size') == 'large' ? 'selected' : '' }}>Large (1000+ sqm)</option>
                    <option value="medium" {{ request('size') == 'medium' ? 'selected' : '' }}>Medium (500-999 sqm)</option>
                    <option value="small" {{ request('size') == 'small' ? 'selected' : '' }}>Small (&lt;500 sqm)</option>
                </select>

                <select name="rating" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Rating</option>
                    <option value="4plus" {{ request('rating') == '4plus' ? 'selected' : '' }}>4+ Stars</option>
                    <option value="3plus" {{ request('rating') == '3plus' ? 'selected' : '' }}>3+ Stars</option>
                    <option value="below3" {{ request('rating') == 'below3' ? 'selected' : '' }}>Below 3 Stars</option>
                </select>

                <select name="date_range" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Created Date</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>Rating</option>
                    <option value="views_count" {{ request('sort_by') == 'views_count' ? 'selected' : '' }}>Views</option>
                    <option value="inquiries_count" {{ request('sort_by') == 'inquiries_count' ? 'selected' : '' }}>Inquiries</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'filter', 'country', 'verification', 'featured', 'business_type', 'size', 'rating', 'date_range', 'sort_by']))
                    <a href="{{ route('admin.showrooms.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Showroom</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Contact</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Location</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Business Type</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Performance</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($showrooms as $showroom)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-lg">
                                        <span class="text-sm font-semibold text-indigo-700">{{ strtoupper(substr($showroom->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">{{ $showroom->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $showroom->showroom_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-envelope text-blue-600 text-xs"></i>
                                        <span class="text-xs text-gray-700">{{ $showroom->email }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-phone text-green-600 text-xs"></i>
                                        <span class="text-xs text-gray-700">{{ $showroom->phone }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium text-gray-900">{{ $showroom->city }}</span>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-red-600 text-xs"></i>
                                        <span class="text-xs text-gray-500">{{ $showroom->country->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ ucfirst($showroom->business_type ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-star text-yellow-500 text-xs"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($showroom->rating ?? 0, 1) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-eye text-cyan-600 text-xs"></i>
                                        <span class="text-xs text-gray-500">{{ number_format($showroom->views_count ?? 0) }} views</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-envelope text-violet-600 text-xs"></i>
                                        <span class="text-xs text-gray-500">{{ number_format($showroom->inquiries_count ?? 0) }} inquiries</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $showroom->status_badge['class'] }}">
                                        {{ $showroom->status_badge['text'] }}
                                    </span>
                                    @if($showroom->is_verified)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-certificate mr-1 text-[10px]"></i> Verified
                                        </span>
                                    @endif
                                    @if($showroom->is_featured)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <i class="fas fa-star mr-1 text-[10px]"></i> Featured
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.showrooms.show', $showroom) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($showroom->status === 'pending')
                                        <form action="{{ route('admin.showrooms.activate', $showroom) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600" title="Activate" onclick="return confirm('Activate this showroom?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if(!$showroom->is_verified)
                                        <form action="{{ route('admin.showrooms.verify', $showroom) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-emerald-50 hover:text-emerald-600" title="Verify" onclick="return confirm('Verify this showroom?')">
                                                <i class="fas fa-certificate"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.showrooms.feature', $showroom) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-amber-50 hover:text-amber-600" title="{{ $showroom->is_featured ? 'Unfeature' : 'Feature' }}">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                    <div class="relative inline-block text-left">
                                        <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleDropdown(event, 'dropdown-{{ $showroom->id }}')">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="dropdown-{{ $showroom->id }}" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1">
                                                @if($showroom->status === 'active')
                                                    <form action="{{ route('admin.showrooms.suspend', $showroom) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left" onclick="return confirm('Suspend this showroom?')">
                                                            <i class="fas fa-ban text-orange-600 w-4"></i>
                                                            Suspend
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($showroom->is_verified)
                                                    <form action="{{ route('admin.showrooms.unverify', $showroom) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                                            <i class="fas fa-times-circle text-orange-600 w-4"></i>
                                                            Revoke Verification
                                                        </button>
                                                    </form>
                                                @endif
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <form action="{{ route('admin.showrooms.destroy', $showroom) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-left" onclick="return confirm('Are you sure you want to delete this showroom? This action cannot be undone.')">
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
                                        <i class="fas fa-store-slash text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No showrooms found</p>
                                    <p class="text-sm text-gray-400">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($showrooms->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $showrooms->links() }}
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
