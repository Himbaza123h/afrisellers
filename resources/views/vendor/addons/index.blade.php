@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Tab Styling */
    .tab-button {
        position: relative;
    }

    .tab-button.active {
        color: #ff0808;
        border-bottom-color: #ff0808;
        background-color: #fff5f5;
    }

    /* Smooth transitions */
    * {
        transition-property: color, background-color, border-color, transform, box-shadow;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    @media print {
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Addons</h1>
            <p class="mt-1 text-xs text-gray-500">Manage your promotional addon subscriptions</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('vendor.addons.print') }}' + window.location.search, '_blank')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('vendor.addons.available') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#dd0606] transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-plus"></i>
                <span>Browse Addons</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Addons -->
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Addons</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-50 to-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-puzzle-piece text-lg text-purple-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    All time
                </span>
            </div>
        </div>

        <!-- Active -->
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Active</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-50 to-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-lg text-green-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ $stats['active_percentage'] }}% of total
                </span>
            </div>
        </div>

        <!-- Expired -->
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Expired</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['expired'] }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-50 to-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-lg text-orange-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    {{ $stats['expired_percentage'] }}% of total
                </span>
            </div>
        </div>

        <!-- Pending -->
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Pending</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 to-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-lg text-yellow-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Awaiting payment
                </span>
            </div>
        </div>
    </div>

    <!-- Spending Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- Total Spent -->
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Spent</p>
                    <p class="text-xl font-bold text-gray-900">${{ number_format($stats['total_spent'], 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-50 to-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-lg text-emerald-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                    <i class="fas fa-arrow-up mr-1"></i>
                    All purchases
                </span>
            </div>
        </div>

        <!-- Active Value -->
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Active Value</p>
                    <p class="text-xl font-bold text-gray-900">${{ number_format($stats['active_value'], 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 to-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-lg text-blue-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-chart-line mr-1"></i>
                    Current promotions
                </span>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 no-print">
        <form method="GET" action="{{ route('vendor.addons.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Search</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by location, type..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] text-sm">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Type</label>
                    <select name="type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] text-sm">
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
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Status</label>
                    <select name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808] text-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Date Range</label>
                    <input type="text"
                           id="dateRange"
                           placeholder="Select dates"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
                    <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" id="dateTo" value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#dd0606] text-sm font-medium transition-all shadow-sm">
                    <i class="fas fa-filter"></i>
                    <span>Apply Filters</span>
                </button>
                <a href="{{ route('vendor.addons.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition-all">
                    <i class="fas fa-undo"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Addons Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Addons List</h2>
                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-[#ff0808] bg-red-50 rounded-full border border-red-100">
                    {{ $addonUsers->total() }} {{ Str::plural('addon', $addonUsers->total()) }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Location</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Item</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Duration</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Expires</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Price</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($addonUsers as $addonUser)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <!-- Location -->
                            <td class="px-5 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">{{ $addonUser->addon->locationX }}</span>
                                    <span class="text-xs text-gray-600">{{ ucfirst(str_replace('_', ' ', $addonUser->addon->locationY)) }}</span>
                                    @if($addonUser->addon->country)
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                            <i class="fas fa-map-marker-alt text-[10px]"></i>
                                            {{ $addonUser->addon->country->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                            <i class="fas fa-globe text-[10px]"></i>
                                            Global
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Type -->
                            <td class="px-5 py-4">
                                @php
                                    $typeConfig = [
                                        'product' => ['label' => 'Product', 'color' => 'blue', 'icon' => 'fa-box'],
                                        'supplier' => ['label' => 'Supplier', 'color' => 'purple', 'icon' => 'fa-store'],
                                        'showroom' => ['label' => 'Showroom', 'color' => 'indigo', 'icon' => 'fa-building'],
                                        'tradeshow' => ['label' => 'Tradeshow', 'color' => 'cyan', 'icon' => 'fa-calendar'],
                                        'loadboad' => ['label' => 'Load Board', 'color' => 'orange', 'icon' => 'fa-truck'],
                                        'car' => ['label' => 'Car', 'color' => 'green', 'icon' => 'fa-car'],
                                    ];
                                    $type = $typeConfig[$addonUser->type] ?? ['label' => 'Unknown', 'color' => 'gray', 'icon' => 'fa-question'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $type['color'] }}-100 text-{{ $type['color'] }}-800">
                                    <i class="fas {{ $type['icon'] }}"></i>
                                    {{ $type['label'] }}
                                </span>
                            </td>

                            <!-- Item -->
                            <td class="px-5 py-4">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $addonUser->related_entity->name ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Duration -->
                            <td class="px-5 py-4">
                                <span class="text-sm font-semibold text-gray-900">{{ $addonUser->paid_days }} days</span>
                            </td>

                            <!-- Expires -->
                            <td class="px-5 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($addonUser->ended_at)
                                        <span class="text-sm font-medium text-gray-900">{{ $addonUser->ended_at->format('M d, Y') }}</span>
                                        @if($addonUser->isActive() && $addonUser->days_remaining)
                                            @php $daysLeft = round($addonUser->days_remaining); @endphp
                                            <span class="inline-flex items-center gap-1 text-xs font-medium {{ $daysLeft <= 3 ? 'text-red-600' : 'text-orange-600' }}">
                                                <i class="fas fa-clock text-[10px]"></i>
                                                {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }} left
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-500">Not set</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="px-5 py-4">
                                <span class="text-sm font-bold text-gray-900">${{ number_format($addonUser->addon->price, 2) }}</span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4">
                                @if($addonUser->isActive())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>
                                        Active
                                    </span>
                                @elseif($addonUser->isExpired())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="w-1.5 h-1.5 bg-red-600 rounded-full"></span>
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <span class="w-1.5 h-1.5 bg-yellow-600 rounded-full"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('vendor.addons.show', $addonUser) }}"
                                       class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($addonUser->isActive() || $addonUser->isExpired())
                                        <a href="{{ route('vendor.addons.renew-form', $addonUser) }}"
                                           class="p-2 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600 transition-all"
                                           title="Renew">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    @endif
                                    @if($addonUser->isActive())
                                        <form action="{{ route('vendor.addons.deactivate', $addonUser) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to deactivate this addon?')">
                                            @csrf
                                            <button type="submit"
                                                    class="p-2 text-gray-600 rounded-lg hover:bg-orange-50 hover:text-orange-600 transition-all"
                                                    title="Deactivate">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-puzzle-piece text-4xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">No addons purchased yet</h3>
                                    <p class="text-sm text-gray-600 mb-6">Start promoting your products and services with addons</p>
                                    <a href="{{ route('vendor.addons.available') }}"
                                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-[#dd0606] transition-all font-medium shadow-sm">
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
            <div class="px-5 py-3 border-t border-gray-200 bg-gray-50">
                {{ $addonUsers->links() }}
            </div>
        @endif
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
});
</script>
@endpush
@endsection
