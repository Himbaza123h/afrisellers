@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    @media print { .no-print { display: none !important; } }
</style>
@endpush

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Transactions</h1>
            <p class="mt-1 text-xs text-gray-500">All transactions across your onboarded vendors</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="{{ route('agent.transactions.print') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i> Print
            </a>
            <form action="{{ route('agent.transactions.export') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to"   value="{{ request('date_to') }}">
                <input type="hidden" name="status"    value="{{ request('status') }}">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-sm">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

        {{-- Total Volume --}}
        <div class="stat-card col-span-2 bg-cyan-600 rounded-xl p-5 text-white shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-teal-100 uppercase tracking-wider">Total Volume</p>
                    <p class="text-3xl font-bold mt-1">${{ number_format($stats['total_volume'], 2) }}</p>
                    <p class="text-xs text-teal-100 mt-1">{{ $stats['completed_count'] }} completed transactions</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-wallet text-2xl text-white"></i>
                </div>
            </div>
        </div>

        {{-- This Month --}}
        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">This Month</p>
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-blue-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">${{ number_format($stats['this_month'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ now()->format('F Y') }}</p>
        </div>

        {{-- Pending --}}
        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500">Pending</p>
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600 text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['pending_count'] }}</p>
            <p class="text-xs text-gray-400 mt-1">awaiting completion</p>
        </div>
    </div>

    {{-- Mini stat row --}}
    <div class="grid grid-cols-3 gap-3">
        @foreach([
            ['label' => 'Total',     'value' => $stats['total_count'],     'color' => 'blue',   'icon' => 'fa-list'],
            ['label' => 'Completed', 'value' => $stats['completed_count'], 'color' => 'green',  'icon' => 'fa-check-circle'],
            ['label' => 'Failed',    'value' => $stats['failed_count'],    'color' => 'red',    'icon' => 'fa-times-circle'],
        ] as $c)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-3 flex items-center gap-3">
            <div class="w-8 h-8 bg-{{ $c['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $c['icon'] }} text-{{ $c['color'] }}-600 text-xs"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400">{{ $c['label'] }}</p>
                <p class="text-lg font-bold text-gray-900">{{ $c['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm text-red-900 font-medium flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 no-print">
        <form method="GET" action="{{ route('agent.transactions.index') }}" class="flex flex-wrap gap-3">

            <div class="flex-1 min-w-[180px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Transaction #, order # or reference…"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>

            <div>
                <select name="vendor_id"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">All Vendors</option>
                    @foreach($myVendors as $v)
                        <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->businessProfile?->business_name ?? $v->user?->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">All Status</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed"    {{ request('status') == 'failed'    ? 'selected' : '' }}>Failed</option>
                    <option value="refunded"  {{ request('status') == 'refunded'  ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            @if($types->isNotEmpty())
            <div>
                <select name="type"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex gap-1">
                <input type="text" id="dateRange" placeholder="Date range"
                    class="w-44 px-3 py-2 border border-gray-300 rounded-lg text-sm cursor-pointer focus:ring-2 focus:ring-teal-500" readonly>
                <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to"   id="dateTo"   value="{{ request('date_to') }}">
            </div>

            <button type="submit"
                class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('agent.transactions.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-800">Transaction List</h2>
            <span class="px-2 py-1 text-xs font-semibold text-teal-700 bg-teal-100 rounded-full">
                {{ $transactions->total() }} {{ Str::plural('record', $transactions->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Transaction</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Buyer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $txn)
                        @php
                            $statusMap = [
                                'completed' => ['bg-green-100 text-green-700',  'fa-check-circle'],
                                'pending'   => ['bg-amber-100 text-amber-700',  'fa-clock'],
                                'failed'    => ['bg-red-100 text-red-700',      'fa-times-circle'],
                                'refunded'  => ['bg-purple-100 text-purple-700','fa-undo'],
                            ];
                            [$statusCls, $statusIcon] = $statusMap[$txn->status] ?? ['bg-gray-100 text-gray-600', 'fa-circle'];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono font-semibold text-gray-800">
                                    {{ $txn->transaction_number }}
                                </span>
                                @if($txn->order)
                                    <p class="text-[10px] text-gray-400 mt-0.5">
                                        Order: {{ $txn->order->order_number }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-store text-purple-500 text-xs"></i>
                                    </div>
                                    <span class="text-sm text-gray-800">{{ $txn->vendor?->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-800">{{ $txn->buyer?->name ?? 'N/A' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $txn->buyer?->email }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded-full">
                                    {{ ucfirst($txn->type ?? '—') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-gray-900">
                                    {{ $txn->currency ?? 'USD' }} {{ number_format($txn->amount, 2) }}
                                </p>
                                @if($txn->payment_method)
                                    <p class="text-[10px] text-gray-400 mt-0.5">via {{ ucfirst($txn->payment_method) }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $statusCls }}">
                                    <i class="fas {{ $statusIcon }} text-[10px]"></i>
                                    {{ ucfirst($txn->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs text-gray-600">{{ $txn->created_at->format('M d, Y') }}</p>
                                <p class="text-[10px] text-gray-400">{{ $txn->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('agent.transactions.show', $txn->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-teal-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors">
                                    <i class="fas fa-eye text-[10px]"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-14 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-wallet text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No transactions found</p>
                                    <p class="text-xs text-gray-400 mt-1">Transactions appear here as your vendors make sales</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500">
                    Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}
                </span>
                <div class="text-sm">{{ $transactions->links() }}</div>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#dateRange', {
    mode: 'range',
    dateFormat: 'Y-m-d',
    onChange(dates) {
        if (dates.length === 2) {
            document.getElementById('dateFrom').value = flatpickr.formatDate(dates[0], 'Y-m-d');
            document.getElementById('dateTo').value   = flatpickr.formatDate(dates[1], 'Y-m-d');
        }
    },
    defaultDate: [
        document.getElementById('dateFrom').value,
        document.getElementById('dateTo').value,
    ].filter(Boolean),
});
</script>
@endpush
