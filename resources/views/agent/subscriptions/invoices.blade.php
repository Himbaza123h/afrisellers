@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    <div class="flex items-center gap-3">
        <a href="{{ route('agent.subscriptions.index') }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Invoices</h1>
            <p class="text-xs text-gray-500 mt-0.5">All your subscription invoices</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Invoice #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Plan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $sub)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono font-semibold text-gray-700">
                                    {{ $sub->generateInvoiceNumber() }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900">{{ $sub->package?->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($sub->package?->billing_cycle ?? '') }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-gray-900">${{ number_format($sub->amount_paid, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $sub->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $map = [
                                        'paid'    => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                    ];
                                    $cls = $map[$sub->payment_status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                    {{ ucfirst($sub->payment_status ?? 'unknown') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('agent.subscriptions.invoice', $sub->id) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-semibold hover:bg-amber-100">
                                    <i class="fas fa-file-invoice"></i> View Invoice
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">No invoices yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
@endsection
