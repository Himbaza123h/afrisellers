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
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Promo Codes</h1>
            <p class="mt-1 text-xs text-gray-500">Manage your promotional discount codes</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <button onclick="window.open('{{ route('vendor.promo-code.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('vendor.promo-code.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-plus"></i>
                <span>Add Promo Code</span>
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
        <button onclick="switchTab('promo-codes')" id="tab-promo-codes" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            Table
        </button>
    </div>

    <!-- Stats Section -->
    <div id="stats-section" class="stats-container hidden">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Codes</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-ticket-alt mr-1 text-[8px]"></i> All time
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <i class="fas fa-ticket-alt text-xl text-blue-600"></i>
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
                                <i class="fas fa-check-circle mr-1 text-[8px]"></i> Live
                            </span>
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
                        <p class="text-xs font-medium text-gray-600 mb-1">Inactive</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-pause-circle mr-1 text-[8px]"></i> Paused
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg">
                        <i class="fas fa-pause-circle text-xl text-gray-600"></i>
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
                                <i class="fas fa-calendar-times mr-1 text-[8px]"></i> Past
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-lg">
                        <i class="fas fa-calendar-times text-xl text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total Uses</p>
                        <p class="text-lg font-bold text-gray-900">{{ $stats['total_uses'] }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-chart-line mr-1 text-[8px]"></i> Usage
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <i class="fas fa-users text-xl text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Status Distribution</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @php
                    $total = $stats['total'] ?? 0;
                    $statusData = [
                        'active' => ['count' => $stats['active'] ?? 0, 'color' => 'green', 'icon' => 'fa-check-circle'],
                        'inactive' => ['count' => $stats['inactive'] ?? 0, 'color' => 'gray', 'icon' => 'fa-pause-circle'],
                        'expired' => ['count' => $stats['expired'] ?? 0, 'color' => 'red', 'icon' => 'fa-calendar-times'],
                    ];
                @endphp

                @foreach($statusData as $status => $data)
                    @if($data['count'] > 0)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-medium text-gray-600 mb-1">{{ ucfirst($status) }}</p>
                            <p class="text-lg font-bold text-gray-900">{{ $data['count'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $total > 0 ? number_format(($data['count'] / $total) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Promo Codes Section -->
    <div id="promo-codes-section" class="promo-codes-container">
        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
            <form method="GET" action="{{ route('vendor.promo-code.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code or description..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Discount Type -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Discount Type</label>
                        <select name="discount_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Types</option>
                            <option value="percentage" {{ request('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ request('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                        <input type="text" id="dateRange" placeholder="Select dates" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg cursor-pointer text-sm">
                        <input type="hidden" name="date_range" id="dateRangeValue" value="{{ request('date_range') }}">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-filter text-sm"></i> Apply
                    </button>
                    <a href="{{ route('vendor.promo-code.index') }}" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                        <i class="fas fa-undo text-sm"></i> Reset
                    </a>
                </div>
            </form>
        </div>

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

        <!-- Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-4">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Promo Code List</h2>
                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                        {{ method_exists($promoCodes, 'total') ? $promoCodes->total() : $promoCodes->count() }} {{ Str::plural('code', method_exists($promoCodes, 'total') ? $promoCodes->total() : $promoCodes->count()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Code</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Discount</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Min Purchase</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Validity</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Usage</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Products</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($promoCodes as $promo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-bold text-gray-900">{{ $promo->code }}</span>
                                        @if($promo->description)
                                            <span class="text-xs text-gray-500 line-clamp-1">{{ $promo->description }}</span>
                                        @endif
                                        <span class="text-xs text-gray-400">{{ $promo->created_at->format('M d, Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-bold text-red-600">
                                            @if($promo->discount_type === 'percentage')
                                                {{ $promo->discount_value }}% off
                                            @else
                                                {{ $promo->currency }} {{ number_format($promo->discount_value, 2) }}
                                            @endif
                                        </span>
                                        @if($promo->discount_type === 'percentage' && $promo->max_discount_amount)
                                            <span class="text-xs text-gray-500">Max: {{ $promo->currency }} {{ number_format($promo->max_discount_amount, 2) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($promo->min_purchase_amount)
                                        <span class="text-sm text-gray-900">{{ $promo->currency }} {{ number_format($promo->min_purchase_amount, 2) }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">No min</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs text-gray-600">
                                            <i class="fas fa-play text-green-600 mr-1"></i>
                                            {{ $promo->start_date->format('M d, Y') }}
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            <i class="fas fa-stop text-red-600 mr-1"></i>
                                            {{ $promo->end_date->format('M d, Y') }}
                                        </span>
                                        @php
                                            $now = now();
                                            $daysLeft = round($now->diffInDays($promo->end_date, false));
                                        @endphp
                                        @if($promo->end_date->isPast())
                                            <span class="text-xs text-red-600 font-medium">Expired</span>
                                        @elseif($promo->start_date->isFuture())
                                            <span class="text-xs text-blue-600 font-medium">Starts in {{ $promo->start_date->diffInDays($now) }}d</span>
                                        @else
                                            <span class="text-xs text-green-600 font-medium">{{ $daysLeft }}d left</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-semibold text-gray-900">{{ $promo->usage_count }}</span>
                                        @if($promo->usage_limit)
                                            <span class="text-xs text-gray-500">of {{ $promo->usage_limit }}</span>
                                            @php
                                                $percentage = ($promo->usage_count / $promo->usage_limit) * 100;
                                            @endphp
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-red-600 h-1.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-500">Unlimited</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge-action bg-blue-100 text-blue-700">
                                        <i class="fas fa-box text-xs"></i>
                                        {{ $promo->products_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $isExpired = $promo->end_date->isPast();
                                        $isExhausted = $promo->usage_limit && $promo->usage_count >= $promo->usage_limit;
                                    @endphp
                                    @if($isExpired)
                                        <span class="badge-action bg-red-100 text-red-700">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            Expired
                                        </span>
                                    @elseif($isExhausted)
                                        <span class="badge-action bg-orange-100 text-orange-700">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            Exhausted
                                        </span>
                                    @else
                                        <span class="badge-action {{ $promo->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            {{ ucfirst($promo->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('vendor.promo-code.edit', $promo) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form action="{{ route('vendor.promo-code.toggle-status', $promo) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-{{ $promo->status === 'active' ? 'gray' : 'green' }}-600 hover:text-{{ $promo->status === 'active' ? 'gray' : 'green' }}-700 text-sm font-medium px-2 py-1 rounded hover:bg-{{ $promo->status === 'active' ? 'gray' : 'green' }}-50">
                                                <i class="fas fa-{{ $promo->status === 'active' ? 'pause' : 'play' }} text-sm"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('vendor.promo-code.destroy', $promo) }}" method="POST" class="inline" onsubmit="return confirm('Delete this promo code?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium px-2 py-1 rounded hover:bg-red-50">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                            <i class="fas fa-ticket-alt text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No promo codes found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
</div>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
        <!-- Pagination -->
        @if(method_exists($promoCodes, 'hasPages') && $promoCodes->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-700">Showing {{ $promoCodes->firstItem() }}-{{ $promoCodes->lastItem() }} of {{ $promoCodes->total() }}</span>
                    <div class="text-sm">{{ $promoCodes->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Date Range Picker
flatpickr("#dateRange", {
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    onChange: function(selectedDates) {
        if (selectedDates.length === 2) {
            const start = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
            const end = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
            document.getElementById('dateRangeValue').value = start + ' to ' + end;
        }
    },
    defaultDate: document.getElementById('dateRangeValue').value ? document.getElementById('dateRangeValue').value.split(' to ') : []
});

// Tab Switching
function switchTab(tab) {
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        btn.classList.add('text-gray-600');
    });

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

    // Show/hide sections
    const statsSection = document.getElementById('stats-section');
    const promoCodesSection = document.getElementById('promo-codes-section');

    switch(tab) {
        case 'all':
            statsSection.style.display = 'none';
            promoCodesSection.style.display = 'block';
            break;
        case 'stats':
            statsSection.style.display = 'block';
            promoCodesSection.style.display = 'none';
            break;
        case 'promo-codes':
            statsSection.style.display = 'none';
            promoCodesSection.style.display = 'block';
            break;
    }
}

// Initialize with All tab active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('all');
});
</script>
@endpush
@endsection
