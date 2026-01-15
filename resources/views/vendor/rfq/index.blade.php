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
            <h1 class="text-2xl font-bold text-gray-900">RFQ Management</h1>
            <p class="mt-1 text-sm text-gray-500">View and respond to customer inquiries</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if(isset($rfqLimit))
                <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 text-blue-700 rounded-lg font-medium shadow-sm">
                    <i class="fas fa-info-circle"></i>
                    <span class="text-sm"><span class="font-bold">{{ $stats['total'] ?? 0 }}</span> / {{ $rfqLimit }} RFQs</span>
                </div>
            @endif
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Total RFQs</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-file-invoice mr-1 text-[10px]"></i> Available
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-file-invoice text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $stats['pending_percentage'] ?? 0 }}%
                        </span>
                        <span class="text-xs text-gray-500">of total</span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Accepted</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['accepted'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['accepted_percentage'] ?? 0 }}%
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
                    <p class="text-sm font-medium text-gray-600 mb-1">Response Rate</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['response_rate'] ?? 0 }}%</p>
                    <div class="mt-3">
                        @if(($stats['response_rate'] ?? 0) >= 80)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-arrow-up mr-1 text-[10px]"></i> Excellent
                            </span>
                        @elseif(($stats['response_rate'] ?? 0) >= 50)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-minus mr-1 text-[10px]"></i> Good
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-arrow-down mr-1 text-[10px]"></i> Needs attention
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">With Products</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['with_product'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            <i class="fas fa-box mr-1 text-[10px]"></i> Direct matches
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl">
                    <i class="fas fa-box text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Rejected</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['rejected'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1 text-[10px]"></i> Declined
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Avg Response Time</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['avg_response_time'] ?? 0, 1) }}h</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                            <i class="fas fa-clock mr-1 text-[10px]"></i> Hours
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl">
                    <i class="fas fa-hourglass-half text-2xl text-emerald-600"></i>
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

    @if(isset($rfqLimit) && ($stats['total'] ?? 0) >= $rfqLimit)
        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200 flex items-start gap-3">
            <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
            <p class="text-sm font-medium text-yellow-900 flex-1">
                You've reached your RFQ limit ({{ $rfqLimit }}). Upgrade your plan to see more inquiries.
            </p>
            <button onclick="this.parentElement.remove()" class="text-yellow-600 hover:text-yellow-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('vendor.rfq.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by RFQ number, product name, customer..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <div class="relative">
                    <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Date range" class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg w-56 cursor-pointer bg-white">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
                </div>

                <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>

                <select name="country" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Countries</option>
                    @foreach(\App\Models\Country::orderBy('name')->get() as $country)
                        <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="priority" {{ request('sort_by') == 'priority' ? 'selected' : '' }}>Sort by Priority</option>
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                    <option value="messages_count" {{ request('sort_by') == 'messages_count' ? 'selected' : '' }}>Messages</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'date_range', 'status', 'country', 'sort_by']))
                    <a href="{{ route('vendor.rfq.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
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
                        <th class="px-6 py-4 text-left w-12"><input type="checkbox" class="w-4 h-4 rounded"></th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">RFQ Details</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Product/Category</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Customer</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Location</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Messages</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($rfqs as $rfq)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4"><input type="checkbox" class="w-4 h-4 rounded"></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">#RFQ-{{ str_pad($rfq->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-xs text-gray-500">{{ $rfq->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500">{{ $rfq->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($rfq->product)
                                        <div class="flex items-center gap-2">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-box text-blue-700"></i>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-900 block">{{ Str::limit($rfq->product->name, 30) }}</span>
                                                @if($rfq->category)
                                                    <span class="text-xs text-gray-500">{{ $rfq->category->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($rfq->category)
                                        <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800">{{ $rfq->category->name }}</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">General Inquiry</span>
                                    @endif
                                    @if($rfq->message)
                                        <p class="text-xs text-gray-500 line-clamp-2 mt-1">{{ Str::limit($rfq->message, 50) }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-full">
                                        <span class="text-sm font-semibold text-green-700">{{ substr($rfq->name ?? 'N', 0, 1) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ $rfq->name ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500">{{ $rfq->email ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($rfq->country)
                                        <span class="text-sm font-medium text-gray-900 flex items-center gap-1">
                                            <i class="fas fa-map-marker-alt text-xs text-gray-400"></i>
                                            {{ $rfq->country->name }}
                                        </span>
                                    @endif
                                    @if($rfq->city)
                                        <span class="text-xs text-gray-500">{{ $rfq->city }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 flex items-center gap-1">
                                        <i class="fas fa-comments"></i>
                                        {{ $rfq->messages_count }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                        'accepted' => ['Accepted', 'bg-green-100 text-green-800'],
                                        'rejected' => ['Rejected', 'bg-red-100 text-red-800'],
                                        'closed' => ['Closed', 'bg-gray-100 text-gray-800'],
                                    ];
                                    $status = $statusColors[$rfq->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('vendor.rfq.show', $rfq) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View Messages">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vendor.rfq.show', $rfq) }}#reply" class="p-2 text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600" title="Reply">
                                        <i class="fas fa-reply"></i>
                                    </a>
                                    @if($rfq->status === 'pending')
                                    <button onclick="quickAccept({{ $rfq->id }})" class="p-2 text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600" title="Quick Accept">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-file-invoice text-4xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No RFQs found</p>
                                    <p class="text-sm text-gray-500 mb-6">RFQs from potential customers will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($rfqs, 'hasPages') && $rfqs->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $rfqs->firstItem() }}-{{ $rfqs->lastItem() }} of {{ $rfqs->total() }}</span>
                    <div>{{ $rfqs->links() }}</div>
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

function quickAccept(rfqId) {
    if (confirm('Accept this RFQ and start conversation?')) {
        window.location.href = `/vendor/rfq/${rfqId}?action=accept`;
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('button')) {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
    }
});
</script>
@endsection
