@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            <h1 class="text-2xl font-bold text-gray-900">My Addons</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your promotional addon subscriptions</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('vendor.addons.available') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-all font-medium shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Browse Addons</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Addons</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                            <i class="fas fa-puzzle-piece mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl">
                    <i class="fas fa-puzzle-piece text-2xl text-pink-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active</p>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Expired</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['expired'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $stats['expired_percentage'] }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                    <i class="fas fa-clock text-2xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-hourglass-half mr-1 text-[10px]"></i> Unpaid
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                    <i class="fas fa-exclamation-circle text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Spending Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Spent</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_spent'], 2) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                            <i class="fas fa-arrow-up mr-1 text-[10px]"></i> All purchases
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl">
                    <i class="fas fa-dollar-sign text-2xl text-emerald-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Value</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($stats['active_value'], 2) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-chart-line mr-1 text-[10px]"></i> Current promotions
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-coins text-2xl text-purple-600"></i>
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
        <form method="GET" action="{{ route('vendor.addons.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by location, type..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <div class="relative">
                    <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Date range" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg w-56 cursor-pointer bg-white">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
                </div>

                <select name="type" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Types</option>
                    <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Product</option>
                    <option value="supplier" {{ request('type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="showroom" {{ request('type') == 'showroom' ? 'selected' : '' }}>Showroom</option>
                    <option value="tradeshow" {{ request('type') == 'tradeshow' ? 'selected' : '' }}>Tradeshow</option>
                    <option value="loadboad" {{ request('type') == 'loadboad' ? 'selected' : '' }}>Load Board</option>
                    <option value="car" {{ request('type') == 'car' ? 'selected' : '' }}>Car</option>
                </select>

                <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                    <option value="paid_at" {{ request('sort_by') == 'paid_at' ? 'selected' : '' }}>Payment Date</option>
                    <option value="ended_at" {{ request('sort_by') == 'ended_at' ? 'selected' : '' }}>Expiry Date</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 text-white rounded-lg hover:bg-pink-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'date_range', 'type', 'status', 'sort_by']))
                    <a href="{{ route('vendor.addons.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Location</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Type</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Item</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Duration</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Expires</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Price</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($addonUsers as $addonUser)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">{{ $addonUser->addon->locationX }}</span>
                                    <span class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $addonUser->addon->locationY)) }}</span>
                                    @if($addonUser->addon->country)
                                        <span class="text-xs text-gray-400">{{ $addonUser->addon->country->name }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">Global</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeColors = [
                                        'product' => ['Product', 'bg-blue-100 text-blue-800', 'fa-box'],
                                        'supplier' => ['Supplier', 'bg-purple-100 text-purple-800', 'fa-store'],
                                        'showroom' => ['Showroom', 'bg-indigo-100 text-indigo-800', 'fa-building'],
                                        'tradeshow' => ['Tradeshow', 'bg-cyan-100 text-cyan-800', 'fa-calendar'],
                                        'loadboad' => ['Load Board', 'bg-orange-100 text-orange-800', 'fa-truck'],
                                        'car' => ['Car', 'bg-green-100 text-green-800', 'fa-car'],
                                    ];
                                    $type = $typeColors[$addonUser->type] ?? ['Unknown', 'bg-gray-100 text-gray-800', 'fa-question'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium {{ $type[1] }}">
                                    <i class="fas {{ $type[2] }}"></i>
                                    {{ $type[0] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">
                                    {{ $addonUser->related_entity->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $addonUser->paid_days }} days</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($addonUser->ended_at)
                                        <span class="text-sm text-gray-900">{{ $addonUser->ended_at->format('M d, Y') }}</span>
                                        @if($addonUser->isActive() && $addonUser->days_remaining)
                                            @php
                                                $daysLeft = round($addonUser->days_remaining);
                                            @endphp
                                            <span class="text-xs text-orange-600 font-medium">{{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }} left</span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-500">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900">${{ number_format($addonUser->addon->price, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($addonUser->isActive())
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @elseif($addonUser->isExpired())
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                                @else
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('vendor.addons.show', $addonUser) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($addonUser->isActive() || $addonUser->isExpired())
                                        <a href="{{ route('vendor.addons.renew-form', $addonUser) }}" class="p-2 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600" title="Renew">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    @endif
                                    @if($addonUser->isActive())
                                        <form action="{{ route('vendor.addons.deactivate', $addonUser) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to deactivate this addon?')">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-yellow-50 hover:text-yellow-600" title="Deactivate">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-puzzle-piece text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No addons purchased yet</p>
                                    <p class="text-sm text-gray-500 mb-6">Start promoting your products and services with addons</p>
                                    <a href="{{ route('vendor.addons.available') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-all font-medium">
                                        <i class="fas fa-plus"></i>
                                        <span>Browse Available Addons</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($addonUsers, 'hasPages') && $addonUsers->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $addonUsers->firstItem() }}-{{ $addonUsers->lastItem() }} of {{ $addonUsers->total() }}</span>
                    <div>{{ $addonUsers->links() }}</div>
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
