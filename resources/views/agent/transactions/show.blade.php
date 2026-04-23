@extends('layouts.home')

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('agent.transactions.index') }}"
               class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $transaction->transaction_number }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">Transaction Detail</p>
            </div>
        </div>
        <div class="flex gap-2">
            @php
                $statusMap = [
                    'completed' => 'bg-green-100 text-green-700',
                    'pending'   => 'bg-amber-100 text-amber-700',
                    'failed'    => 'bg-red-100 text-red-700',
                    'refunded'  => 'bg-purple-100 text-purple-700',
                ];
                $cls = $statusMap[$transaction->status] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold {{ $cls }}">
                <i class="fas fa-circle text-[7px]"></i>
                {{ ucfirst($transaction->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left Column --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Amount Card --}}
            <div class="bg-teal-500 to-cyan-600 rounded-xl p-5 text-white shadow-md">
                <p class="text-xs font-semibold text-teal-100 uppercase tracking-wider mb-1">Transaction Amount</p>
                <p class="text-4xl font-bold">
                    {{ $transaction->currency ?? 'USD' }} {{ number_format($transaction->amount, 2) }}
                </p>
                @if($transaction->payment_method)
                    <p class="text-xs text-teal-100 mt-2 flex items-center gap-1">
                        <i class="fas fa-credit-card"></i>
                        via {{ ucfirst($transaction->payment_method) }}
                    </p>
                @endif
                @if($transaction->completed_at)
                    <p class="text-xs text-teal-100 mt-1 flex items-center gap-1">
                        <i class="fas fa-check-circle"></i>
                        Completed {{ $transaction->completed_at->format('M d, Y H:i') }}
                    </p>
                @endif
            </div>

            {{-- Transaction Meta --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Transaction Info</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-400">Transaction #</dt>
                        <dd class="text-sm font-mono font-bold text-gray-900 mt-0.5">{{ $transaction->transaction_number }}</dd>
                    </div>
                    @if($transaction->payment_reference)
                    <div>
                        <dt class="text-xs text-gray-400">Payment Reference</dt>
                        <dd class="text-sm font-mono text-gray-700 mt-0.5 break-all">{{ $transaction->payment_reference }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-400">Type</dt>
                        <dd class="mt-0.5">
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-semibold rounded-full">
                                {{ ucfirst($transaction->type ?? '—') }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400">Created</dt>
                        <dd class="text-sm text-gray-700 mt-0.5">{{ $transaction->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($transaction->notes)
                    <div>
                        <dt class="text-xs text-gray-400">Notes</dt>
                        <dd class="text-sm text-gray-700 mt-0.5">{{ $transaction->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Parties --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Vendor --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Vendor</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-store text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $transaction->vendor?->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400">{{ $transaction->vendor?->email }}</p>
                        </div>
                    </div>
                </div>

                {{-- Buyer --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Buyer</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $transaction->buyer?->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400">{{ $transaction->buyer?->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Details --}}
            @if($transaction->order)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Linked Order</h3>
                    <span class="text-xs font-mono font-bold text-blue-600">
                        {{ $transaction->order->order_number }}
                    </span>
                </div>

                {{-- Order Items --}}
                @if($transaction->order->items && $transaction->order->items->count())
                    <div class="space-y-2 mb-4">
                        @foreach($transaction->order->items as $item)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-box text-gray-400 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $item->quantity }} × {{ $transaction->order->currency ?? '' }} {{ number_format($item->unit_price, 2) }}
                                        @if($item->sku) &nbsp;·&nbsp; SKU: {{ $item->sku }} @endif
                                    </p>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-gray-900">
                                {{ $transaction->order->currency ?? '' }} {{ number_format($item->total, 2) }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- Order Totals --}}
                <div class="bg-gray-50 rounded-lg p-3 space-y-1.5">
                    @foreach([
                        ['Subtotal',  $transaction->order->subtotal  ?? null],
                        ['Tax',       $transaction->order->tax       ?? null],
                        ['Shipping',  $transaction->order->shipping  ?? null],
                        ['Discount',  $transaction->order->discount  ?? null],
                    ] as [$label, $val])
                        @if(!is_null($val))
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>{{ $label }}</span>
                            <span>{{ $transaction->order->currency ?? '' }} {{ number_format($val, 2) }}</span>
                        </div>
                        @endif
                    @endforeach
                    <div class="flex justify-between text-sm font-bold text-gray-900 pt-1.5 border-t border-gray-200">
                        <span>Order Total</span>
                        <span>{{ $transaction->order->currency ?? '' }} {{ number_format($transaction->order->total ?? 0, 2) }}</span>
                    </div>
                </div>

                {{-- Order Status --}}
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs text-gray-400">Order status:</span>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                        {{ ucfirst($transaction->order->status ?? '—') }}
                    </span>
                    <span class="text-xs text-gray-400 ml-auto">
                        {{ $transaction->order->created_at->format('M d, Y') }}
                    </span>
                </div>
            </div>
            @else
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-3 text-gray-400">
                <i class="fas fa-shopping-bag text-2xl"></i>
                <p class="text-sm">No linked order for this transaction.</p>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
