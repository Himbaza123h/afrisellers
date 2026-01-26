@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .badge-action {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    @media print {
        .no-print { display: none !important; }
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
            <h1 class="text-xl font-bold text-gray-900">My Addons</h1>
            <p class="mt-1 text-xs text-gray-500">Manage your promotional addon subscriptions</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('vendor.addons.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('vendor.addons.available') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-plus"></i>
                <span>Browse Addons</span>
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            All
        </button>
        <button onclick="switchTab('stats')" id="tab-stats" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Stats
        </button>
        <button onclick="switchTab('addons')" id="tab-addons" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Addons
        </button>
    </div>

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container hidden">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Addons</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                <i class="fas fa-puzzle-piece mr-1 text-[8px]"></i> All time
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg">
                        <i class="fas fa-puzzle-piece text-xl text-pink-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Active</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['active'] }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $stats['active_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Expired</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['expired'] }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $stats['expired_percentage'] }}%
                            </span>
                            <span class="text-xs text-gray-500">of total</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                        <i class="fas fa-clock text-xl text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Pending</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['pending'] }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-hourglass-half mr-1 text-[8px]"></i> Unpaid
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                        <i class="fas fa-exclamation-circle text-xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spending Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Spent</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($stats['total_spent'], 2) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                <i class="fas fa-arrow-up mr-1 text-[8px]"></i> All purchases
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-xl text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Active Value</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($stats['active_value'], 2) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-chart-line mr-1 text-[8px]"></i> Current promotions
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-coins text-xl text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Addons Section -->
    <div id="addons-section" class="addons-container">
        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-green-50 rounded-md border border-green-200 flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 rounded-md border border-red-200 flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" action="{{ route('vendor.addons.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by location, type..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm">
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm">
                            <option value="">All Types</option>
                            <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Product</option>
                            <option value="supplier" {{ request('type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                            <option value="showroom" {{ request('type') == 'showroom' ? 'selected' : '' }}>Showroom</option>
                            <option value="tradeshow" {{ request('type') == 'tradeshow' ? 'selected' : '' }}>Tradeshow</option>
                            <option value="loadboad" {{ request('type') == 'loadboad' ? 'selected' : '' }}>Load Board</option>
                            <option value="car" {{ request('type') == 'car' ? 'selected' : '' }}>Car</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRange" placeholder="Select dates" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="dateTo" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('vendor.addons.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-4">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Addons List</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-pink-700 bg-pink-100 rounded-full">
                        {{ $addonUsers->total() }} {{ Str::plural('addon', $addonUsers->total()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Location</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Item</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Duration</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Expires</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Price</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($addonUsers as $addonUser)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
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
                                <td class="px-4 py-3">
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
                                    <span class="badge-action {{ $type[1] }}">
                                        <i class="fas {{ $type[2] }} text-xs"></i>
                                        {{ $type[0] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-600">
                                        {{ $addonUser->related_entity->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-medium text-gray-900">{{ $addonUser->paid_days }} days</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        @if($addonUser->ended_at)
                                            <span class="text-sm text-gray-900">{{ $addonUser->ended_at->format('M d, Y') }}</span>
                                            @if($addonUser->isActive() && $addonUser->days_remaining)
                                                @php $daysLeft = round($addonUser->days_remaining); @endphp
                                                <span class="text-xs text-orange-600 font-medium">{{ $daysLeft }}d left</span>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-500">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-bold text-gray-900">${{ number_format($addonUser->addon->price, 2) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($addonUser->isActive())
                                        <span class="badge-action bg-green-100 text-green-700">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            Active
                                        </span>
                                    @elseif($addonUser->isExpired())
                                        <span class="badge-action bg-red-100 text-red-700">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            Expired
                                        </span>
                                    @else
                                        <span class="badge-action bg-yellow-100 text-yellow-700">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('vendor.addons.show', $addonUser) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors" title="View Details">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        @if($addonUser->isActive() || $addonUser->isExpired())
                                            <a href="{{ route('vendor.addons.renew-form', $addonUser) }}" class="p-2 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600 transition-colors" title="Renew">
                                                <i class="fas fa-redo text-sm"></i>
                                            </a>
                                        @endif
                                        @if($addonUser->isActive())
                                            <form action="{{ route('vendor.addons.deactivate', $addonUser) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to deactivate this addon?')">
                                                @csrf
                                                <button type="submit" class="p-2 text-gray-600 rounded-lg hover:bg-yellow-50 hover:text-yellow-600 transition-colors" title="Deactivate">
                                                    <i class="fas fa-pause text-sm"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-puzzle-piece text-3xl text-gray-300"></i>
                                        </div>
                                        <p class="text-base font-semibold text-gray-900 mb-1">No addons purchased yet</p>
                                        <p class="text-sm text-gray-500 mb-4">Start promoting your products and services with addons</p>
                                        <a href="{{ route('vendor.addons.available') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-all font-medium text-sm">
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
                <div class="px-4 py-3 border-t bg-gray-50">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700">Showing {{ $addonUsers->firstItem() }}-{{ $addonUsers->lastItem() }} of {{ $addonUsers->total() }}</span>
                        <div>{{ $addonUsers->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr
    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                document.getElementById('dateFrom').value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                document.getElementById('dateTo').value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
            }
        }
    });

    // Tab switching functionality
    window.switchTab = function(tab) {
        const tabs = ['all', 'stats', 'addons'];
        const statsSection = document.getElementById('stats-section');
        const addonsSection = document.getElementById('addons-section');

        tabs.forEach(t => {
            const btn = document.getElementById(`tab-${t}`);
            if (t === tab) {
                btn.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                btn.classList.remove('text-gray-600', 'hover:text-gray-900');
            } else {
                btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                btn.classList.add('text-gray-600', 'hover:text-gray-900');
            }
        });

        if (tab === 'all') {
            statsSection.classList.remove('hidden');
            addonsSection.classList.remove('hidden');
        } else if (tab === 'stats') {
            statsSection.classList.remove('hidden');
            addonsSection.classList.add('hidden');
        } else if (tab === 'addons') {
            statsSection.classList.add('hidden');
            addonsSection.classList.remove('hidden');
        }
    };

    // Set initial tab state
    switchTab('all');
});
</script>
@endpush
@endsection
