@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .tab-content { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    .showroom-image { transition: transform 0.3s; }
    .showroom-image:hover { transform: scale(1.05); }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Regional Showrooms Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor showrooms across {{ $region->name }} region ({{ $countries->count() }} countries)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('regional.showrooms.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            <i class="fas fa-chart-line mr-2"></i> Overview
        </button>
        <button onclick="switchTab('showrooms')" id="tab-showrooms" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-store mr-2"></i> Showrooms
        </button>
        <button onclick="switchTab('countries')" id="tab-countries" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-globe mr-2"></i> Countries
        </button>
        <button onclick="switchTab('verification')" id="tab-verification" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-shield-check mr-2"></i> Verification
        </button>
        <button onclick="switchTab('analytics')" id="tab-analytics" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Analytics
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Showrooms</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-globe mr-1 text-[8px]"></i> Regional
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-store text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Active Showrooms</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $stats['active_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Verified Showrooms</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['verified']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $stats['verified_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                        <i class="fas fa-shield-check text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Featured</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['featured']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $stats['featured_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Pending Approval</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-clock mr-1 text-[8px]"></i> Awaiting
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                        <i class="fas fa-clock text-orange-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Unverified</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['unverified']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1 text-[8px]"></i> Needs review
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Views</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_views']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-eye mr-1 text-[8px]"></i> All time
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Inquiries</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_inquiries']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                <i class="fas fa-comments mr-1 text-[8px]"></i> All time
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg">
                        <i class="fas fa-envelope text-teal-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-2 mt-4">
                <i class="fas fa-check-circle text-green-600 mt-0.5 text-sm"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times text-sm"></i></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <form method="GET" action="{{ route('regional.showrooms.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search showrooms..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Country</label>
                        <select name="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">City</label>
                        <select name="city" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRangePicker" placeholder="Select dates" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="dateTo" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Verification -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Verification</label>
                        <select name="verification" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All</option>
                            <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="unverified" {{ request('verification') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                        </select>
                    </div>

                    <!-- Featured -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Featured</label>
                        <select name="featured" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All</option>
                            <option value="yes" {{ request('featured') == 'yes' ? 'selected' : '' }}>Featured</option>
                            <option value="no" {{ request('featured') == 'no' ? 'selected' : '' }}>Not Featured</option>
                        </select>
                    </div>

                    <!-- Business Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Business Type</label>
                        <select name="business_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Types</option>
                            <option value="dealership" {{ request('business_type') == 'dealership' ? 'selected' : '' }}>Dealership</option>
                            <option value="showroom" {{ request('business_type') == 'showroom' ? 'selected' : '' }}>Showroom</option>
                            <option value="warehouse" {{ request('business_type') == 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                            <option value="retail" {{ request('business_type') == 'retail' ? 'selected' : '' }}>Retail</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="views_count" {{ request('sort_by') == 'views_count' ? 'selected' : '' }}>Views</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('regional.showrooms.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Showrooms Tab Content -->
    <div id="tab-showrooms-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Showrooms List</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ $showrooms->total() }} {{ Str::plural('showroom', $showrooms->total()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Showroom</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Country/City</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Owner</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Products</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Views</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Verification</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($showrooms as $showroom)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($showroom->primary_image)
                                            <img src="{{ asset('storage/' . $showroom->primary_image) }}"
                                                 alt="{{ $showroom->name }}"
                                                 class="showroom-image w-10 h-10 object-cover rounded-md border border-gray-200">
                                        @else
                                            <div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center border border-gray-200">
                                                <i class="fas fa-store text-gray-400 text-sm"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ Str::limit($showroom->name, 30) }}</p>
                                                @if($showroom->is_featured)
                                                    <i class="fas fa-star text-yellow-500 text-xs" title="Featured"></i>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500">{{ $showroom->showroom_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 block w-fit mb-1">
                                        {{ $showroom->country->name ?? 'N/A' }}
                                    </span>
                                    <span class="text-xs text-gray-900">{{ $showroom->city }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $showroom->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $showroom->user->email ?? 'N/A' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ ucfirst($showroom->business_type ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-box text-gray-400 text-xs"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($showroom->products->count()) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-eye text-gray-400 text-xs"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($showroom->views_count) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($showroom->is_verified)
                                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1 text-xs"></i> Verified
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1 text-xs"></i> Unverified
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'active' => ['Active', 'bg-green-100 text-green-800'],
                                            'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                            'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                                        ];
                                        $status = $statusColors[$showroom->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">
                                        {{ $status[0] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('regional.showrooms.show', $showroom->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!$showroom->is_verified)
                                            <form action="{{ route('regional.showrooms.verify', $showroom->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-700 text-sm font-medium px-2 py-1 rounded hover:bg-green-50" title="Verify">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('regional.showrooms.feature', $showroom->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-700 text-sm font-medium px-2 py-1 rounded hover:bg-yellow-50" title="{{ $showroom->is_featured ? 'Unfeature' : 'Feature' }}">
                                                <i class="fas fa-star {{ $showroom->is_featured ? 'text-yellow-500' : '' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('regional.showrooms.destroy', $showroom->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this showroom?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium px-2 py-1 rounded hover:bg-red-50" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-store text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No showrooms found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($showrooms->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-700">Showing {{ $showrooms->firstItem() }}-{{ $showrooms->lastItem() }} of {{ $showrooms->total() }}</span>
                        <div class="text-sm">{{ $showrooms->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Countries Tab Content -->
    <div id="tab-countries-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Showrooms by Country</h3>
                <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">
                    {{ $countries->count() }} countries
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($countries as $country)
                    @php
                        $countryShowrooms = \App\Models\Showroom::where('country_id', $country->id)->count();
                        $colors = ['blue', 'green', 'purple', 'orange', 'red', 'indigo', 'yellow'];
                        $color = $colors[$loop->index % count($colors)];
                    @endphp
                    <div class="p-4 bg-gradient-to-br from-{{ $color }}-50 to-white rounded-lg border border-{{ $color }}-100">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-{{ $color }}-100 rounded-lg">
                                <i class="fas fa-flag text-{{ $color }}-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $country->name }}</h4>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-600">{{ $countryShowrooms }} showrooms</span>
                                    @if($countryShowrooms > 0)
                                        <span class="text-xs font-medium text-{{ $color }}-700">
                                            {{ $stats['total'] > 0 ? round(($countryShowrooms / $stats['total']) * 100) : 0 }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Verification Tab Content -->
    <div id="tab-verification-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Verification Status</h3>
                <div class="flex gap-2">
                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                        {{ $stats['verified'] }} Verified
                    </span>
                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                        {{ $stats['unverified'] }} Unverified
                    </span>
                </div>
            </div>

            <!-- Verification Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="p-4 bg-gradient-to-br from-green-50 to-white rounded-lg border border-green-100">
                    <h4 class="text-sm font-semibold text-green-700 mb-2">Verified Showrooms</h4>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['verified'] }}</p>
                    <div class="mt-2">
                        <div class="w-full bg-green-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['verified_percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">{{ $stats['verified_percentage'] }}% of total showrooms</p>
                    </div>
                </div>

                <div class="p-4 bg-gradient-to-br from-red-50 to-white rounded-lg border border-red-100">
                    <h4 class="text-sm font-semibold text-red-700 mb-2">Unverified Showrooms</h4>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['unverified'] }}</p>
                    <div class="mt-2">
                        <div class="w-full bg-red-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: {{ $stats['total'] > 0 ? round(($stats['unverified'] / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ $stats['total'] > 0 ? round(($stats['unverified'] / $stats['total']) * 100) : 0 }}% of total showrooms
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pending Verifications -->
            @php
                $pendingShowrooms = \App\Models\Showroom::whereIn('country_id', $countries->pluck('id'))
                    ->where('is_verified', false)
                    ->take(5)
                    ->get();
            @endphp

            @if($pendingShowrooms->count() > 0)
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Pending Verifications</h4>
                    <div class="space-y-3">
                        @foreach($pendingShowrooms as $showroom)
                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $showroom->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $showroom->country->name ?? 'N/A' }} • {{ $showroom->city }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-1">
                                    <form action="{{ route('regional.showrooms.verify', $showroom->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-700 text-sm font-medium px-2 py-1 rounded hover:bg-green-50" title="Verify">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('regional.showrooms.show', $showroom->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Analytics Tab Content -->
    <div id="tab-analytics-content" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Status Distribution -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Status Distribution</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Active Showrooms</span>
                            <span class="text-sm font-bold text-green-700">{{ number_format($stats['active']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full transition-all duration-300" style="width: {{ $stats['active_percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['active_percentage'] }}% of total</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Pending Showrooms</span>
                            <span class="text-sm font-bold text-yellow-700">{{ number_format($stats['pending']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-yellow-600 h-3 rounded-full transition-all duration-300" style="width: {{ $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100) : 0 }}% of total</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Featured Showrooms</span>
                            <span class="text-sm font-bold text-yellow-700">{{ number_format($stats['featured']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-yellow-600 h-3 rounded-full transition-all duration-300" style="width: {{ $stats['featured_percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['featured_percentage'] }}% of total</p>
                    </div>
                </div>
            </div>

            <!-- Engagement Metrics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Engagement Metrics</h3>
                <div class="space-y-4">
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Total Views</span>
                            <i class="fas fa-eye text-purple-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_views']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Across all showrooms</p>
                    </div>

                    <div class="p-3 bg-teal-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Total Inquiries</span>
                            <i class="fas fa-envelope text-teal-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_inquiries']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Customer inquiries received</p>
                    </div>

                    <div class="p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Avg Views per Showroom</span>
                            <i class="fas fa-chart-line text-blue-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-2">
                            {{ $stats['total'] > 0 ? number_format(round($stats['total_views'] / $stats['total'])) : 0 }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Average engagement rate</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 lg:col-span-2">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Showroom Registrations</h3>
                <div class="space-y-3">
                    @php
                        $recentShowrooms = \App\Models\Showroom::whereIn('country_id', $countries->pluck('id'))
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp

                    @forelse($recentShowrooms as $showroom)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex items-center gap-3">
                                @if($showroom->primary_image)
                                    <img src="{{ asset('storage/' . $showroom->primary_image) }}"
                                         alt="{{ $showroom->name }}"
                                         class="w-10 h-10 object-cover rounded-md border border-gray-200">
                                @else
                                    <div class="w-10 h-10 bg-gray-200 rounded-md flex items-center justify-center">
                                        <i class="fas fa-store text-gray-400 text-sm"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $showroom->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $showroom->city }} • {{ $showroom->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $statusColors = [
                                        'active' => ['Active', 'bg-green-100 text-green-800'],
                                        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                        'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                                    ];
                                    $status = $statusColors[$showroom->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                                <a href="{{ route('regional.showrooms.show', $showroom->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">No recent showrooms</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Date Range Picker
    flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        showMonths: 2,
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                document.getElementById('dateFrom').value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                document.getElementById('dateTo').value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
            }
        },
        defaultDate: [
            document.getElementById('dateFrom').value,
            document.getElementById('dateTo').value
        ].filter(d => d)
    });

    // Tab Switching Function
    function switchTab(tabName) {
        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            btn.classList.add('text-gray-600');
        });

        // Add active state to selected tab
        const activeTab = document.getElementById(`tab-${tabName}`);
        activeTab.classList.remove('text-gray-600');
        activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Show selected tab content
        document.getElementById(`tab-${tabName}-content`).classList.remove('hidden');
    }

    // Initialize with Overview tab active
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('overview');
    });
</script>
@endpush
@endsection
