@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transaction #{{ $transaction->transaction_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">Created on {{ $transaction->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('vendor.transactions.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
            @if($transaction->order)
            <a href="{{ route('vendor.orders.show', $transaction->order) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium">
                <i class="fas fa-shopping-cart"></i>
                View Order
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Transaction Details -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Transaction Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Transaction Number</label>
                        <p class="text-base font-medium text-gray-900">#{{ $transaction->transaction_number }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Transaction Type</label>
                        @php
                            $typeColors = [
                                'order' => 'bg-blue-100 text-blue-800',
                                'refund' => 'bg-purple-100 text-purple-800',
                                'adjustment' => 'bg-gray-100 text-gray-800',
                            ];
                            $typeColor = $typeColors[$transaction->type] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $typeColor }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Amount</label>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($transaction->amount, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ strtoupper($transaction->currency) }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Status</label>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                            ];
                            $statusColor = $statusColors[$transaction->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $statusColor }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>

                    @if($transaction->payment_method)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Payment Method</label>
                        <div class="flex items-center gap-2">
                            @php
                                $methodIcons = [
                                    'cash' => 'fa-money-bill-wave',
                                    'credit_card' => 'fa-credit-card',
                                    'bank_transfer' => 'fa-university',
                                    'paypal' => 'fa-paypal',
                                    'stripe' => 'fa-stripe',
                                    'other' => 'fa-wallet',
                                ];
                                $icon = $methodIcons[$transaction->payment_method] ?? 'fa-wallet';
                            @endphp
                            <i class="fas {{ $icon }} text-gray-400"></i>
                            <span class="text-base font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span>
                        </div>
                    </div>
                    @endif

                    @if($transaction->payment_reference)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Payment Reference</label>
                        <p class="text-base font-medium text-gray-900">{{ $transaction->payment_reference }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Created Date</label>
                        <p class="text-sm text-gray-700">{{ $transaction->created_at->format('F d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $transaction->created_at->format('h:i A') }}</p>
                    </div>

                    @if($transaction->completed_at)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Completed Date</label>
                        <p class="text-sm text-gray-700">{{ $transaction->completed_at->format('F d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $transaction->completed_at->format('h:i A') }}</p>
                    </div>
                    @endif
                </div>

                @if($transaction->notes)
                <div class="mt-6 pt-6 border-t">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Notes</label>
                    <p class="text-sm text-gray-700">{{ $transaction->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Related Order Details -->
            @if($transaction->order)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Related Order</h2>
                    <a href="{{ route('vendor.orders.show', $transaction->order) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        View Full Order <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Order #{{ $transaction->order->order_number }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $transaction->order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">${{ number_format($transaction->order->total, 2) }}</p>
                            @php
                                $orderStatusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'processing' => 'bg-purple-100 text-purple-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $orderStatusColor = $orderStatusColors[$transaction->order->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $orderStatusColor }} mt-2">
                                {{ ucfirst($transaction->order->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Order Items Preview -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Order Items</h3>
                        <div class="space-y-2">
                            @foreach($transaction->order->items->take(3) as $item)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    @if($item->product && $item->product->images->first())
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="{{ $item->product_name }}" class="w-10 h-10 rounded object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">${{ number_format($item->subtotal, 2) }}</span>
                            </div>
                            @endforeach

                            @if($transaction->order->items->count() > 3)
                            <div class="text-center pt-2">
                                <a href="{{ route('vendor.orders.show', $transaction->order) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                    +{{ $transaction->order->items->count() - 3 }} more items
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Customer Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Customer Information</h2>

                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-semibold text-blue-700">{{ substr($transaction->buyer->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-base font-semibold text-gray-900">{{ $transaction->buyer->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $transaction->buyer->email }}</p>
                        @if($transaction->buyer->buyer)
                            <p class="text-sm text-gray-600 mt-1">Customer ID: #{{ $transaction->buyer->id }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Quick Summary -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Summary</h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-sm text-gray-600">Transaction Amount</span>
                        <span class="text-lg font-bold text-gray-900">${{ number_format($transaction->amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-sm text-gray-600">Type</span>
                        <span class="text-sm font-medium text-gray-900">{{ ucfirst($transaction->type) }}</span>
                    </div>

                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="text-sm font-medium {{ $transaction->status === 'completed' ? 'text-green-600' : ($transaction->status === 'failed' ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>

                    @if($transaction->payment_method)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Method</span>
                        <span class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Transaction Timeline -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Timeline</h3>

                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                            @if($transaction->completed_at)
                                <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
                            @endif
                        </div>
                        <div class="flex-1 pb-4">
                            <p class="text-sm font-medium text-gray-900">Transaction Created</p>
                            <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y - h:i A') }}</p>
                        </div>
                    </div>

                    @if($transaction->completed_at)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Transaction Completed</p>
                            <p class="text-xs text-gray-500">{{ $transaction->completed_at->format('M d, Y - h:i A') }}</p>
                        </div>
                    </div>
                    @elseif($transaction->status === 'failed')
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-times text-red-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Transaction Failed</p>
                            <p class="text-xs text-gray-500">{{ $transaction->updated_at->format('M d, Y - h:i A') }}</p>
                        </div>
                    </div>
                    @elseif($transaction->status === 'cancelled')
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-ban text-gray-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Transaction Cancelled</p>
                            <p class="text-xs text-gray-500">{{ $transaction->updated_at->format('M d, Y - h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Actions</h3>

                <div class="space-y-2">
                    @if($transaction->order)
                    <a href="{{ route('vendor.orders.show', $transaction->order) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium text-sm">
                        <i class="fas fa-shopping-cart"></i>
                        View Related Order
                    </a>

                    <a href="{{ route('vendor.orders.invoice', $transaction->order) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium text-sm">
                        <i class="fas fa-file-invoice"></i>
                        View Invoice
                    </a>
                    @endif

                    <button onclick="window.print()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium text-sm">
                        <i class="fas fa-print"></i>
                        Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
