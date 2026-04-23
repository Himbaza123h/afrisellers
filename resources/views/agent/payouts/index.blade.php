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
            <h1 class="text-xl font-bold text-gray-900">Payouts</h1>
            <p class="mt-1 text-xs text-gray-500">Manage your earnings withdrawal requests</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="{{ route('agent.payouts.print') }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i> Print
            </a>
            <a href="{{ route('agent.payouts.request') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm">
                <i class="fas fa-plus"></i> Request Payout
            </a>
        </div>
    </div>

    {{-- Available Balance Hero --}}
    <div class="bg-teal-600 rounded-xl p-6 text-white shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-cyan-100 uppercase tracking-wider mb-1">Available Balance</p>
                <p class="text-4xl font-bold">${{ number_format($stats['available'], 2) }}</p>
                <p class="text-xs text-cyan-100 mt-2">
                    Total earned minus approved/paid payouts
                </p>
            </div>
            <a href="{{ route('agent.payouts.request') }}"
               class="inline-flex items-center gap-2 px-5 py-3 bg-white text-teal-700 font-bold rounded-xl hover:bg-cyan-50 transition-colors shadow-sm text-sm self-start sm:self-center">
                <i class="fas fa-money-bill-wave"></i>
                Withdraw Funds
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['label' => 'Total Paid Out',  'value' => '$' . number_format($stats['total_paid'], 2),       'color' => 'green',  'icon' => 'fa-check-circle'],
            ['label' => 'Pending',         'value' => '$' . number_format($stats['total_pending'], 2),    'color' => 'amber',  'icon' => 'fa-clock'],
            ['label' => 'Processing',      'value' => '$' . number_format($stats['total_processing'], 2), 'color' => 'blue',   'icon' => 'fa-spinner'],
            ['label' => 'Requests',        'value' => $stats['count_pending'] . ' pending',               'color' => 'purple', 'icon' => 'fa-list'],
        ] as $card)
        <div class="stat-card bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-{{ $card['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">{{ $card['label'] }}</p>
                <p class="text-base font-bold text-gray-900 mt-0.5">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5 flex-shrink-0"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5 flex-shrink-0"></i>
            <p class="text-sm text-red-900 font-medium flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 no-print">
        <form method="GET" action="{{ route('agent.payouts.index') }}" class="flex flex-wrap gap-3">

            <div class="flex-1 min-w-[180px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search payout number…"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>

            <div>
                <select name="status"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500">
                    <option value="">All Status</option>
                    @foreach(['pending','approved','processing','paid','rejected','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-1">
                <input type="text" id="dateRange" placeholder="Date range"
                    class="w-44 px-3 py-2 border border-gray-300 rounded-lg text-sm cursor-pointer focus:ring-2 focus:ring-cyan-500" readonly>
                <input type="hidden" name="date_from" id="dateFrom" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to"   id="dateTo"   value="{{ request('date_to') }}">
            </div>

            <button type="submit"
                class="px-4 py-2 bg-cyan-600 text-white rounded-lg text-sm font-medium hover:bg-cyan-700">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('agent.payouts.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-800">Payout Requests</h2>
            <span class="px-2 py-1 text-xs font-semibold text-cyan-700 bg-cyan-100 rounded-full">
                {{ $payouts->total() }} {{ Str::plural('request', $payouts->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Payout #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Requested</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Processed</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payouts as $payout)
                        @php
                            $statusConfig = [
                                'pending'    => ['bg-amber-100 text-amber-700',  'fa-clock',         'Pending'],
                                'approved'   => ['bg-blue-100 text-blue-700',    'fa-thumbs-up',     'Approved'],
                                'processing' => ['bg-indigo-100 text-indigo-700','fa-spinner',       'Processing'],
                                'paid'       => ['bg-green-100 text-green-700',  'fa-check-circle',  'Paid'],
                                'rejected'   => ['bg-red-100 text-red-700',      'fa-times-circle',  'Rejected'],
                                'cancelled'  => ['bg-gray-100 text-gray-500',    'fa-ban',           'Cancelled'],
                            ];
                            [$sCls, $sIcon, $sLabel] = $statusConfig[$payout->status] ?? ['bg-gray-100 text-gray-500', 'fa-circle', ucfirst($payout->status)];

                            $methodLabels = [
                                'bank_transfer' => ['fa-university',    'Bank Transfer'],
                                'mobile_money'  => ['fa-mobile-alt',    'Mobile Money'],
                                'paypal'        => ['fa-paypal',        'PayPal'],
                                'wise'          => ['fa-exchange-alt',  'Wise'],
                                'crypto'        => ['fa-bitcoin',       'Crypto'],
                            ];
                            [$mIcon, $mLabel] = $methodLabels[$payout->payment_method] ?? ['fa-money-bill', ucfirst($payout->payment_method)];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono font-bold text-gray-800">{{ $payout->payout_number }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-bold text-gray-900">
                                    {{ $payout->currency }} {{ number_format($payout->amount, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 text-sm text-gray-700">
                                    <i class="fab {{ $mIcon }} text-gray-400 text-xs"></i>
                                    {{ $mLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $sCls }}">
                                    <i class="fas {{ $sIcon }} text-[10px]"></i> {{ $sLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $payout->created_at->format('M d, Y') }}
                                <p class="text-[10px] text-gray-300">{{ $payout->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $payout->processed_at ? $payout->processed_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('agent.payouts.show', $payout->id) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="View">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    @if($payout->isCancellable())
                                        <form action="{{ route('agent.payouts.cancel', $payout->id) }}" method="POST"
                                              onsubmit="return confirm('Cancel this payout request?')">
                                            @csrf
                                            <button type="submit"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="Cancel">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-14 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-money-bill-wave text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No payout requests yet</p>
                                    <p class="text-xs text-gray-400 mt-1 mb-4">Request your first payout when you have available earnings</p>
                                    <a href="{{ route('agent.payouts.request') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700">
                                        <i class="fas fa-plus"></i> Request Payout
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payouts->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500">
                    Showing {{ $payouts->firstItem() }}–{{ $payouts->lastItem() }} of {{ $payouts->total() }}
                </span>
                <div class="text-sm">{{ $payouts->links() }}</div>
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
