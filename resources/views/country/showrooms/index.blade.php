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
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Country Showrooms Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor and manage showrooms in {{ Auth::user()->country->name ?? 'your country' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('country.showrooms.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
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
        <button onclick="switchTab('cities')" id="tab-cities" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-map-marker-alt mr-2"></i> Cities
        </button>
        <button onclick="switchTab('verification')" id="tab-verification" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-shield-check mr-2"></i> Verification
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Showrooms -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Showrooms</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-flag mr-1 text-[8px]"></i> Country
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-store text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Active Showrooms -->
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

            <!-- Verified Showrooms -->
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

            <!-- Featured Showrooms -->
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

            <!-- Pending Approval -->
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

            <!-- Unverified -->
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

            <!-- Total Views -->
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

            <!-- Total Inquiries -->
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
            <div class="p-4 bg-green-50 rounded-md border border-green-200 flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <form method="GET" action="{{ route('country.showrooms.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search showrooms..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
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
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('country.showrooms.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Showrooms Tab Content (Hidden by default) -->
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
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">City</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Owner</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Business Type</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Products</th>
                            <th class="px-4 py-2text-left text-xs font-semibold text-gray-700 uppercase">Views</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Verification</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($showrooms as $showroom)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($showroom->primary_image)
                                            <img src="{{ Storage::url($showroom->primary_image) }}" alt="{{ $showroom->name }}" class="w-12 h-12 rounded-lg object-cover">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-store text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ Str::limit($showroom->name, 25) }}</p>
                                            @if($showroom->is_featured)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-star mr-1 text-[8px]"></i> Featured
                                                </span>
                                            @endif
                                            <p class="text-xs text-gray-500">{{ $showroom->showroom_number }}</p>
                                            <p class="text-xs text-gray-400">{{ $showroom->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-900">{{ $showroom->city }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-900">{{ $showroom->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $showroom->user->email ?? 'N/A' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-briefcase mr-1 text-[8px]"></i> {{ ucfirst($showroom->business_type ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-900">{{ number_format($showroom->products->count()) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-900">{{ number_format($showroom->views_count) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($showroom->is_verified)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1 text-[8px]"></i> Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1 text-[8px]"></i> Unverified
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
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">
                                        {{ $status[0] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('showrooms.show', $showroom) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!$showroom->is_verified)
                                            <form action="{{ route('country.showrooms.verify', $showroom) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-800" title="Verify">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('country.showrooms.toggle-featured', $showroom) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="Toggle Featured">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('country.showrooms.destroy', $showroom) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this showroom?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="fas fa-store text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-base font-medium">No showrooms found</p>
                                        <p class="text-sm">Showrooms from your country will appear here</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($showrooms, 'hasPages') && $showrooms->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing {{ $showrooms->firstItem() }}-{{ $showrooms->lastItem() }} of {{ $showrooms->total() }}
                    </div>
                    <div>
                        {{ $showrooms->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Cities Tab Content (Hidden by default) -->
    <div id="tab-cities-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Cities Distribution</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($cityStats ?? [] as $city => $count)
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $city }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ number_format($count) }} showrooms</p>
                            </div>
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Verification Tab Content (Hidden by default) -->
    <div id="tab-verification-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Unverified Showrooms</h2>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Showroom</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">City</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Owner</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Created</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($unverifiedShowrooms ?? [] as $showroom)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($showroom->primary_image)
                                            <img src="{{ Storage::url($showroom->primary_image) }}" alt="{{ $showroom->name }}" class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-store text-gray-400 text-sm"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $showroom->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $showroom->showroom_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-900">{{ $showroom->city }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-900">{{ $showroom->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $showroom->user->email ?? 'N/A' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-900">{{ $showroom->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $showroom->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('showrooms.show', $showroom) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('country.showrooms.verify', $showroom) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800" title="Verify">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="fas fa-check-circle text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-base font-medium">All showrooms are verified</p>
                                        <p class="text-sm">Great job!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    window.switchTab = function(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            button.classList.add('text-gray-600');
        });

        // Show selected tab content
        document.getElementById('tab-' + tabName + '-content').classList.remove('hidden');

        // Add active state to selected tab
        const activeTab = document.getElementById('tab-' + tabName);
        activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        activeTab.classList.remove('text-gray-600');
    };

    // Initialize Flatpickr for date range
    const dateRangePicker = flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        maxDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                document.getElementById('dateFrom').value = flatpickr.formatDate(selectedDates[0], "Y-m-d");
                document.getElementById('dateTo').value = flatpickr.formatDate(selectedDates[1], "Y-m-d");
            }
        }
    });

    // Set initial date range if exists
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    if (dateFrom && dateTo) {
        dateRangePicker.setDate([dateFrom, dateTo]);
    }

    // Auto-dismiss success messages
    const successAlert = document.querySelector('.bg-green-50');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.transition = 'opacity 0.3s';
            successAlert.style.opacity = '0';
            setTimeout(function() {
                successAlert.remove();
            }, 300);
        }, 5000);
    }

    // Animate statistics on page load
    const statNumbers = document.querySelectorAll('.stat-card .text-lg');
    statNumbers.forEach(stat => {
        const target = parseInt(stat.textContent.replace(/,/g, ''));
        if (!isNaN(target) && target > 0) {
            animateValue(stat, 0, target, 1000);
        }
    });

    function animateValue(element, start, end, duration) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;

        const timer = setInterval(function() {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                element.textContent = end.toLocaleString();
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
        }, 16);
    }

    // Print functionality
    window.printPage = function() {
        window.print();
    };

    // Confirmation for delete actions
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this showroom? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});

// Print styles
const printStyles = `
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            font-size: 12pt;
        }
        .tab-content {
            display: block !important;
        }
        table {
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = printStyles;
document.head.appendChild(styleSheet);
</script>
@endpush
