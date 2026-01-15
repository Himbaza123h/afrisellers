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
            <h1 class="text-2xl font-bold text-gray-900">Promo Codes</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your promotional discount codes</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('vendor.promo-code.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#ff0808] hover:bg-[#e60707] text-white rounded-lg transition-all font-medium shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Create Promo Code</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Codes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $promoCodes->total() }}</p>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-ticket-alt mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-ticket-alt text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1 text-[10px]"></i> Live
                        </span>
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Inactive</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-pause-circle mr-1 text-[10px]"></i> Paused
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                    <i class="fas fa-pause-circle text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Expired</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['expired'] }}</p>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-calendar-times mr-1 text-[10px]"></i> Past
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                    <i class="fas fa-calendar-times text-2xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Uses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_uses'] }}</p>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-chart-line mr-1 text-[10px]"></i> Usage
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-users text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
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
        <form method="GET" action="{{ route('vendor.promo-code.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code or description..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <div class="relative">
                    <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Validity date range" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg w-56 cursor-pointer bg-white">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
                </div>

                <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <select name="discount_type" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Discount Types</option>
                    <option value="percentage" {{ request('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                    <option value="fixed" {{ request('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                </select>

                <select name="validity" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Validity</option>
                    <option value="upcoming" {{ request('validity') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="current" {{ request('validity') === 'current' ? 'selected' : '' }}>Currently Valid</option>
                    <option value="expired" {{ request('validity') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>

                <select name="usage" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">Usage Status</option>
                    <option value="unused" {{ request('usage') === 'unused' ? 'selected' : '' }}>Never Used</option>
                    <option value="used" {{ request('usage') === 'used' ? 'selected' : '' }}>Has Been Used</option>
                    <option value="exhausted" {{ request('usage') === 'exhausted' ? 'selected' : '' }}>Limit Reached</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Sort by Date Created</option>
                    <option value="code" {{ request('sort_by') === 'code' ? 'selected' : '' }}>Code (A-Z)</option>
                    <option value="discount_value" {{ request('sort_by') === 'discount_value' ? 'selected' : '' }}>Discount Value</option>
                    <option value="usage_count" {{ request('sort_by') === 'usage_count' ? 'selected' : '' }}>Usage Count</option>
                    <option value="end_date" {{ request('sort_by') === 'end_date' ? 'selected' : '' }}>Expiry Date</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'date_range', 'status', 'discount_type', 'validity', 'usage', 'sort_by']))
                    <a href="{{ route('vendor.promo-code.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Code</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Discount</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Min Purchase</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Validity Period</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Usage</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Products</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($promoCodes as $promo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-bold text-gray-900">{{ $promo->code }}</span>
                                    @if($promo->description)
                                        <span class="text-xs text-gray-500 line-clamp-1">{{ $promo->description }}</span>
                                    @endif
                                    <span class="text-xs text-gray-400">Created {{ $promo->created_at->format('M d, Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-bold text-red-600">
                                        @if($promo->discount_type === 'percentage')
                                            {{ $promo->discount_value }}% off
                                        @else
                                            {{ $promo->currency }} {{ number_format($promo->discount_value, 2) }} off
                                        @endif
                                    </span>
                                    @if($promo->discount_type === 'percentage' && $promo->max_discount_amount)
                                        <span class="text-xs text-gray-500">Max: {{ $promo->currency }} {{ number_format($promo->max_discount_amount, 2) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($promo->min_purchase_amount)
                                    <span class="text-sm text-gray-900">{{ $promo->currency }} {{ number_format($promo->min_purchase_amount, 2) }}</span>
                                @else
                                    <span class="text-sm text-gray-400">No minimum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
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
                                        <span class="text-xs text-green-600 font-medium">{{ $daysLeft }}d remaining</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">{{ $promo->usage_count }}</span>
                                    @if($promo->usage_limit)
                                        <span class="text-xs text-gray-500">of {{ $promo->usage_limit }} limit</span>
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
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-box mr-1"></i>
                                    {{ $promo->products_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $isExpired = $promo->end_date->isPast();
                                    $isExhausted = $promo->usage_limit && $promo->usage_count >= $promo->usage_limit;
                                @endphp
                                @if($isExpired)
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                                @elseif($isExhausted)
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Exhausted</span>
                                @else
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $promo->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($promo->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('vendor.promo-code.edit', $promo) }}" class="p-2 text-blue-600 rounded-lg hover:bg-blue-50" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('vendor.promo-code.toggle-status', $promo) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 {{ $promo->status === 'active' ? 'text-gray-600 hover:bg-gray-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg" title="{{ $promo->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $promo->status === 'active' ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('vendor.promo-code.destroy', $promo) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this promo code?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 rounded-lg hover:bg-red-50" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-ticket-alt text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No promo codes found</p>
                                    <p class="text-sm text-gray-500 mb-6">
                                        @if(request()->hasAny(['search', 'status', 'discount_type', 'validity', 'usage']))
                                            No promo codes match your filters. Try adjusting your search criteria.
                                        @else
                                            Start creating promo codes to offer discounts to your customers.
                                        @endif
                                    </p>
                                    @if(!request()->hasAny(['search', 'status', 'discount_type', 'validity', 'usage']))
                                        <a href="{{ route('vendor.promo-code.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-colors font-medium">
                                            <i class="fas fa-plus"></i>
                                            <span>Create Your First Promo Code</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($promoCodes, 'hasPages') && $promoCodes->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $promoCodes->firstItem() }}-{{ $promoCodes->lastItem() }} of {{ $promoCodes->total() }}</span>
                    <div>{{ $promoCodes->links() }}</div>
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

@push('styles')
<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection
