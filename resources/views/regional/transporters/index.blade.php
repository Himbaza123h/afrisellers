@extends('layouts.home')

@push('styles')
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
            <h1 class="text-xl font-bold text-gray-900">Regional Transporters Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor and manage transporters across your region</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('regional.transporters.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
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
        <button onclick="switchTab('transporters')" id="tab-transporters" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-truck mr-2"></i> Transporters
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
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Transporters</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-globe mr-1 text-[8px]"></i> Regional
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-truck text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Verified</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['verified']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $stats['verified_percentage'] }}%
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
                        <p class="text-xs font-medium text-gray-600 mb-1">Active</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $stats['active_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                        <i class="fas fa-user-check text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Fleet</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_fleet']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-car mr-1 text-[8px]"></i> Vehicles
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-shipping-fast text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Unverified</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['unverified']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1 text-[8px]"></i> Review
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-hourglass-half text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Suspended</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['suspended']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-ban mr-1 text-[8px]"></i> Action
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                        <i class="fas fa-pause-circle text-orange-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Avg Rating</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                <i class="fas fa-star mr-1 text-[8px]"></i> Rating
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg">
                        <i class="fas fa-star text-teal-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Inactive</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['inactive']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-power-off mr-1 text-[8px]"></i> Offline
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg">
                        <i class="fas fa-user-slash text-gray-600"></i>
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
            <form method="GET" action="{{ route('regional.transporters.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transporters..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
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

                    <!-- Verification Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Verification</label>
                        <select name="is_verified" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All</option>
                            <option value="verified" {{ request('is_verified') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="unverified" {{ request('is_verified') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Sort By -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                            <option value="company_name" {{ request('sort_by') == 'company_name' ? 'selected' : '' }}>Company Name</option>
                            <option value="average_rating" {{ request('sort_by') == 'average_rating' ? 'selected' : '' }}>Rating</option>
                            <option value="total_deliveries" {{ request('sort_by') == 'total_deliveries' ? 'selected' : '' }}>Deliveries</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Order</label>
                        <select name="sort_order" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('regional.transporters.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Transporters Tab Content -->
    <div id="tab-transporters-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Transporters List</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ $transporters->total() }} {{ Str::plural('transporter', $transporters->total()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Company</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Owner</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Country</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Contact</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Fleet</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Rating</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Verified</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($transporters as $transporter)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="text-sm font-semibold text-gray-900">{{ $transporter->company_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $transporter->registration_number ?? 'N/A' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $transporter->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $transporter->created_at->format('M d, Y') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-900">{{ $transporter->country->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-900">{{ Str::limit($transporter->email ?? 'N/A', 20) }}</p>
                                    @if($transporter->phone)
                                        <p class="text-xs text-gray-500">{{ $transporter->phone }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-truck text-gray-400 text-xs"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $transporter->fleet_size ?? 0 }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($transporter->average_rating ?? 0, 1) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($transporter->is_verified)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Unverified</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'active' => ['Active', 'bg-green-100 text-green-800'],
                                            'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                                            'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                                        ];
                                        $status = $statusColors[$transporter->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('regional.transporters.show', $transporter->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-truck text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No transporters found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transporters->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-700">Showing {{ $transporters->firstItem() }}-{{ $transporters->lastItem() }} of {{ $transporters->total() }}</span>
                        <div class="text-sm">{{ $transporters->links() }}</div>
                    </div>
                </div>
            @endif
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
                    <span class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">
                        {{ $stats['unverified'] }} Unverified
                    </span>
                </div>
            </div>

            <!-- Verification Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="p-4 bg-gradient-to-br from-green-50 to-white rounded-lg border border-green-100">
                    <h4 class="text-sm font-semibold text-green-700 mb-2">Verified Transporters</h4>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['verified'] }}</p>
                    <div class="mt-2">
                        <div class="w-full bg-green-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['verified_percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">{{ $stats['verified_percentage'] }}% of total transporters</p>
                    </div>
                </div>

                <div class="p-4 bg-gradient-to-br from-yellow-50 to-white rounded-lg border border-yellow-100">
                    <h4 class="text-sm font-semibold text-yellow-700 mb-2">Unverified Transporters</h4>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['unverified'] }}</p>
                    <div class="mt-2">
                        <div class="w-full bg-yellow-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $stats['total'] > 0 ? round(($stats['unverified'] / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ $stats['total'] > 0 ? round(($stats['unverified'] / $stats['total']) * 100) : 0 }}% of total transporters
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pending Verifications -->
            @php
                $pendingTransporters = $transporters->where('is_verified', false)->take(5);
            @endphp

            @if($pendingTransporters->count() > 0)
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Pending Verifications</h4>
                    <div class="space-y-3">
                        @foreach($pendingTransporters as $transporter)
                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                                        <i class="fas fa-truck text-yellow-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $transporter->company_name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $transporter->country->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-1">
                                    <a href="{{ route('regional.transporters.show', $transporter->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50" title="View">
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
                            <span class="text-sm font-medium text-gray-700">Active Transporters</span>
                            <span class="text-sm font-bold text-green-700">{{ number_format($stats['active']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full transition-all duration-300" style="width: {{ $stats['active_percentage'] }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['active_percentage'] }}% of total</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Suspended</span>
                            <span class="text-sm font-bold text-orange-700">{{ number_format($stats['suspended']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-orange-600 h-3 rounded-full transition-all duration-300" style="width: {{ $stats['total'] > 0 ? round(($stats['suspended'] / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['suspended'] / $stats['total']) * 100) : 0 }}% of total</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Inactive</span>
                            <span class="text-sm font-bold text-gray-700">{{ number_format($stats['inactive']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gray-600 h-3 rounded-full transition-all duration-300" style="width: {{ $stats['total'] > 0 ? round(($stats['inactive'] / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['inactive'] / $stats['total']) * 100) : 0 }}% of total</p>
                    </div>
                </div>
            </div>

            <!-- Fleet & Performance Metrics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Fleet & Performance</h3>
                <div class="space-y-4">
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Total Fleet Size</span>
                            <i class="fas fa-truck text-purple-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_fleet']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Vehicles across all transporters</p>
                    </div>

                    <div class="p-3 bg-teal-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Average Rating</span>
                            <i class="fas fa-star text-teal-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Overall service quality</p>
                    </div>

                    <div class="p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Avg Fleet per Transporter</span>
                            <i class="fas fa-chart-line text-blue-600"></i>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-2">
                            {{ $stats['total'] > 0 ? number_format($stats['total_fleet'] / $stats['total'], 1) : 0 }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Vehicles per company</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 lg:col-span-2">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Registrations</h3>
                <div class="space-y-3">
                    @php
                        $recentTransporters = $transporters->take(5);
                    @endphp

                    @forelse($recentTransporters as $transporter)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-truck text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $transporter->company_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $transporter->country->name ?? 'N/A' }} • {{ $transporter->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $statusColors = [
                                        'active' => ['Active', 'bg-green-100 text-green-800'],
                                        'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                                        'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                                    ];
                                    $status = $statusColors[$transporter->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                                <a href="{{ route('regional.transporters.show', $transporter->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">No recent transporters</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
