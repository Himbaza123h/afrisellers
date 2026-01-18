@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Regional Loads Management</h1>
            <p class="mt-1 text-sm text-gray-500">Monitor and track freight loads across {{ $region->name }} region ({{ $countries->count() }} countries)</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-download"></i>
                <span>Export</span>
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Loads</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-globe mr-1 text-[10px]"></i> Regional
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Posted/Bidding</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['posted'] + $stats['bidding']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $stats['posted_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                    <i class="fas fa-gavel text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">In Transit</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['in_transit']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            <i class="fas fa-shipping-fast mr-1 text-[10px]"></i> Active
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl">
                    <i class="fas fa-truck text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Delivered</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['delivered']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['delivered_percentage'] }}%
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Assigned</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['assigned']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-user-check mr-1 text-[10px]"></i> Confirmed
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-clipboard-check text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Cancelled</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['cancelled']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1 text-[10px]"></i> Inactive
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                    <i class="fas fa-ban text-2xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Posted</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['posted']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <i class="fas fa-clock mr-1 text-[10px]"></i> Awaiting
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                    <i class="fas fa-file-alt text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Bids</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_bids']) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                            <i class="fas fa-hand-holding-usd mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl">
                    <i class="fas fa-comments-dollar text-2xl text-teal-600"></i>
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

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('regional.loads.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search loads by number, cargo type, city..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <select name="country_id" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                    <option value="bidding" {{ request('status') == 'bidding' ? 'selected' : '' }}>Bidding</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <select name="cargo_type" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Cargo Types</option>
                    <option value="general" {{ request('cargo_type') == 'general' ? 'selected' : '' }}>General</option>
                    <option value="perishable" {{ request('cargo_type') == 'perishable' ? 'selected' : '' }}>Perishable</option>
                    <option value="fragile" {{ request('cargo_type') == 'fragile' ? 'selected' : '' }}>Fragile</option>
                    <option value="hazardous" {{ request('cargo_type') == 'hazardous' ? 'selected' : '' }}>Hazardous</option>
                    <option value="oversized" {{ request('cargo_type') == 'oversized' ? 'selected' : '' }}>Oversized</option>
                </select>

                <select name="pricing_type" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Pricing Types</option>
                    <option value="fixed" {{ request('pricing_type') == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                    <option value="negotiable" {{ request('pricing_type') == 'negotiable' ? 'selected' : '' }}>Negotiable</option>
                    <option value="bidding" {{ request('pricing_type') == 'bidding' ? 'selected' : '' }}>Bidding</option>
                </select>

                <select name="origin_city" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Origin Cities</option>
                    @foreach($originCities as $city)
                        <option value="{{ $city }}" {{ request('origin_city') == $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>

                <select name="destination_city" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Destination Cities</option>
                    @foreach($destinationCities as $city)
                        <option value="{{ $city }}" {{ request('destination_city') == $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>

                <div class="relative">
                    <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Date range" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg w-56 cursor-pointer bg-white">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
                </div>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                    <option value="load_number" {{ request('sort_by') == 'load_number' ? 'selected' : '' }}>Load Number</option>
                    <option value="pickup_date" {{ request('sort_by') == 'pickup_date' ? 'selected' : '' }}>Pickup Date</option>
                    <option value="budget" {{ request('sort_by') == 'budget' ? 'selected' : '' }}>Budget</option>
                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'country_id', 'status', 'cargo_type', 'pricing_type', 'origin_city', 'destination_city', 'date_range', 'sort_by']))
                    <a href="{{ route('regional.loads.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Load Info</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Route</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Shipper</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Cargo</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Pickup Date</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Budget</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Bids</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($loads as $load)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $load->load_number }}</span>
                                    <span class="text-xs text-gray-500">{{ $load->created_at->format('M d, Y') }}</span>
                                    @if($load->tracking_number)
                                        <span class="text-xs text-blue-600 font-medium">{{ $load->tracking_number }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1 text-sm">
                                        <i class="fas fa-arrow-up text-green-600 text-xs"></i>
                                        <span class="font-medium text-gray-900">{{ $load->origin_city }}</span>
                                        <span class="text-gray-500 text-xs">({{ $load->originCountry->name ?? 'N/A' }})</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-sm">
                                        <i class="fas fa-arrow-down text-red-600 text-xs"></i>
                                        <span class="font-medium text-gray-900">{{ $load->destination_city }}</span>
                                        <span class="text-gray-500 text-xs">({{ $load->destinationCountry->name ?? 'N/A' }})</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $load->user->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">{{ $load->user->email ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800 inline-block w-fit mb-1">
                                        {{ ucfirst($load->cargo_type ?? 'N/A') }}
                                    </span>
                                    @if($load->weight)
                                        <span class="text-xs text-gray-600">{{ number_format($load->weight, 2) }} {{ $load->weight_unit }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $load->pickup_date ? $load->pickup_date->format('M d, Y') : 'N/A' }}</span>
                                    @if($load->pickup_time_start)
                                        <span class="text-xs text-gray-500">{{ $load->pickup_time_start->format('h:i A') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($load->budget)
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">{{ $load->currency }} {{ number_format($load->budget, 2) }}</span>
                                        <span class="text-xs text-gray-500">{{ ucfirst($load->pricing_type) }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">Not set</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-gavel text-gray-400 text-xs"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ $load->bids->count() }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
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
                                <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('regional.loads.show', $load->id) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-truck-loading text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No loads found</p>
                                    <p class="text-sm text-gray-500">Freight loads from your region will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($loads, 'hasPages') && $loads->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $loads->firstItem() }}-{{ $loads->lastItem() }} of {{ $loads->total() }}</span>
                    <div>{{ $loads->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    locale: { rangeSeparator: " to " },
    onClose: function(dates, str, inst) {
        if (dates.length === 2) inst.element.closest('form').submit();
    }
});
</script>
@endsection
