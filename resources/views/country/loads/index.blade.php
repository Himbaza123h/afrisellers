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
            <h1 class="text-xl font-bold text-gray-900">Country Loads Management</h1>
            <p class="mt-1 text-xs text-gray-500">Monitor freight loads in {{ Auth::user()->country->name ?? 'your country' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('country.loads.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
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
        <button onclick="switchTab('loads')" id="tab-loads" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-truck-loading mr-2"></i> Loads
        </button>
        <button onclick="switchTab('analytics')" id="tab-analytics" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Analytics
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Loads -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Loads</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-flag mr-1 text-[8px]"></i> Country
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-truck-loading text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Posted/Bidding -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Posted/Bidding</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['posted'] + $stats['bidding']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $stats['posted_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-gavel text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <!-- In Transit -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">In Transit</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['in_transit']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <i class="fas fa-shipping-fast mr-1 text-[8px]"></i> Active
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg">
                        <i class="fas fa-truck text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <!-- Delivered -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Delivered</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['delivered']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $stats['delivered_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Assigned -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Assigned</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['assigned']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-user-check mr-1 text-[8px]"></i> Confirmed
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-clipboard-check text-purple-600"></i>
                    </div>
                </div>
            </div>

            <!-- Cancelled -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Cancelled</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['cancelled']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1 text-[8px]"></i> Inactive
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                        <i class="fas fa-ban text-red-600"></i>
                    </div>
                </div>
            </div>

            <!-- Posted -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Posted</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['posted']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-clock mr-1 text-[8px]"></i> Awaiting
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                        <i class="fas fa-file-alt text-orange-600"></i>
                    </div>
                </div>
            </div>

            <!-- Total Bids -->
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Bids</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_bids']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                <i class="fas fa-hand-holding-usd mr-1 text-[8px]"></i> All time
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg">
                        <i class="fas fa-comments-dollar text-teal-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-green-50 rounded-md border border-green-200 flex items-start gap-3 mt-4">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <form method="GET" action="{{ route('country.loads.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search loads..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                            <option value="bidding" {{ request('status') == 'bidding' ? 'selected' : '' }}>Bidding</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Cargo Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Cargo Type</label>
                        <select name="cargo_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Types</option>
                            <option value="general" {{ request('cargo_type') == 'general' ? 'selected' : '' }}>General</option>
                            <option value="perishable" {{ request('cargo_type') == 'perishable' ? 'selected' : '' }}>Perishable</option>
                            <option value="fragile" {{ request('cargo_type') == 'fragile' ? 'selected' : '' }}>Fragile</option>
                            <option value="hazardous" {{ request('cargo_type') == 'hazardous' ? 'selected' : '' }}>Hazardous</option>
                            <option value="oversized" {{ request('cargo_type') == 'oversized' ? 'selected' : '' }}>Oversized</option>
                        </select>
                    </div>

                    <!-- Origin City -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Origin</label>
                        <select name="origin_city" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Cities</option>
                            @foreach($originCities as $city)
                                <option value="{{ $city }}" {{ request('origin_city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('country.loads.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Loads Tab Content (Hidden by default) -->
    <div id="tab-loads-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Loads List</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ $loads->total() }} {{ Str::plural('load', $loads->total()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Load</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Route</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Shipper</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Cargo</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Pickup</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Budget</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Bids</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($loads as $load)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-semibold text-gray-900">{{ $load->load_number }}</span>
                                        <span class="text-xs text-gray-500">{{ $load->created_at->format('M d, Y') }}</span>
                                        @if($load->tracking_number)
                                            <span class="text-xs text-blue-600">{{ $load->tracking_number }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1 text-sm">
                                            <i class="fas fa-arrow-up text-green-600 text-xs"></i>
                                            <span class="font-medium">{{ $load->origin_city }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 text-sm">
                                            <i class="fas fa-arrow-down text-red-600 text-xs"></i>
                                            <span class="font-medium">{{ $load->destination_city }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ Str::limit($load->user->name ?? 'N/A', 20) }}</span>
                                        <span class="text-xs text-gray-500">{{ Str::limit($load->user->email ?? 'N/A', 25) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ ucfirst($load->cargo_type ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-900">{{ $load->pickup_date ? $load->pickup_date->format('M d, Y') : 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($load->budget)
                                        <span class="text-sm font-bold text-gray-900">{{ $load->currency }} {{ number_format($load->budget, 2) }}</span>
                                    @else
                                        <span class="text-sm text-gray-500">Not set</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-medium text-gray-900">{{ $load->bids->count() }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'posted' => ['Posted', 'bg-blue-100 text-blue-800'],
                                            'bidding' => ['Bidding', 'bg-yellow-100 text-yellow-800'],
                                            'assigned' => ['Assigned', 'bg-purple-100 text-purple-800'],
                                            'in_transit' => ['In Transit', 'bg-indigo-100 text-indigo-800'],
                                            'delivered' => ['Delivered', 'bg-green-100 text-green-800'],
                                            'cancelled' => ['Cancelled', 'bg-red-100 text-red-800'],
                                        ];
                                        $status = $statusColors[$load->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('country.loads.show', $load->id) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="fas fa-truck-loading text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-base font-medium">No loads found</p>
                                        <p class="text-sm">Loads from your country will appear here</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($loads, 'hasPages') && $loads->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing {{ $loads->firstItem() }}-{{ $loads->lastItem() }} of {{ $loads->total() }}
                    </div>
                    <div>
                        {{ $loads->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Analytics Tab Content (Hidden by default) -->
    <div id="tab-analytics-content" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Load Analytics</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-sm font-medium text-gray-900 mb-2">Status Distribution</p>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Posted:</span>
                            <span class="font-medium">{{ number_format($stats['posted']) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>In Transit:</span>
                            <span class="font-medium">{{ number_format($stats['in_transit']) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Delivered:</span>
                            <span class="font-medium">{{ number_format($stats['delivered']) }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-sm font-medium text-gray-900 mb-2">Bidding Activity</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_bids']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total bids received</p>
                </div>
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
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            button.classList.add('text-gray-600');
        });

        document.getElementById('tab-' + tabName + '-content').classList.remove('hidden');

        const activeTab = document.getElementById('tab-' + tabName);
        activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        activeTab.classList.remove('text-gray-600');
    };

    // Initialize Flatpickr
    flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        maxDate: "today"
    });
});
</script>
@endpush
