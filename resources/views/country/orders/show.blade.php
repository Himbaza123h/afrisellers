@extends('layouts.home')

@push('styles')
<style>
    .timeline-step {
        position: relative;
    }
    .timeline-line {
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        z-index: 0;
    }
    .timeline-dot {
        position: relative;
        z-index: 1;
    }
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between no-print">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('country.orders.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Order Details</h1>
            </div>
            <p class="text-sm text-gray-500">Order #{{ $order->order_number }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button onclick="exportToPDF()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-file-pdf"></i>
                <span>Export PDF</span>
            </button>
        </div>
    </div>

    <!-- Order Info Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Order Number</p>
                <p class="text-lg font-bold text-gray-900">#{{ $order->order_number }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Order Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Status</p>
                @php
                    $statusColors = [
                        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                        'confirmed' => ['Confirmed', 'bg-blue-100 text-blue-800'],
                        'processing' => ['Processing', 'bg-purple-100 text-purple-800'],
                        'shipped' => ['Shipped', 'bg-indigo-100 text-indigo-800'],
                        'delivered' => ['Delivered', 'bg-green-100 text-green-800'],
                        'cancelled' => ['Cancelled', 'bg-red-100 text-red-800'],
                    ];
                    $status = $statusColors[$order->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium {{ $status[1] }}">
                    {{ $status[0] }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Payment Status</p>
                @php
                    $paymentColors = [
                        'paid' => ['Paid', 'bg-green-100 text-green-800'],
                        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                        'failed' => ['Failed', 'bg-red-100 text-red-800'],
                        'refunded' => ['Refunded', 'bg-purple-100 text-purple-800'],
                    ];
                    $payment = $paymentColors[$order->payment_status ?? 'pending'] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium {{ $payment[1] }}">
                    {{ $payment[0] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Order Status Timeline -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-8">Order Timeline</h3>

        @php
            $statuses = [
                ['key' => 'pending', 'label' => 'Pending', 'icon' => 'fa-clock', 'date' => $order->created_at],
                ['key' => 'confirmed', 'label' => 'Confirmed', 'icon' => 'fa-check-circle', 'date' => $order->confirmed_at],
                ['key' => 'processing', 'label' => 'Processing', 'icon' => 'fa-cog', 'date' => null],
                ['key' => 'shipped', 'label' => 'Shipped', 'icon' => 'fa-shipping-fast', 'date' => $order->shipped_at],
                ['key' => 'delivered', 'label' => 'Delivered', 'icon' => 'fa-box-check', 'date' => $order->delivered_at],
            ];
            $currentIndex = array_search($order->status, array_column($statuses, 'key'));
            if ($order->status === 'cancelled') {
                $currentIndex = -1;
            }
        @endphp

        <div class="relative">
            <div class="flex items-start justify-between">
                @foreach($statuses as $index => $statusItem)
                    <div class="timeline-step flex flex-col items-center" style="width: {{ 100 / count($statuses) }}%;">
                        <!-- Connector Line -->
                        @if($index > 0)
                            <div class="absolute top-6 h-0.5 {{ $currentIndex >= $index ? 'bg-green-500' : 'bg-gray-300' }}"
                                 style="left: {{ ($index - 1) * (100 / count($statuses)) }}%; width: {{ 100 / count($statuses) }}%; z-index: 0;"></div>
                        @endif

                        <!-- Status Dot -->
                        <div class="timeline-dot flex items-center justify-center w-12 h-12 rounded-full border-4 border-white shadow-md
                                    {{ $currentIndex >= $index ? 'bg-green-500' : 'bg-gray-300' }}">
                            <i class="fas {{ $statusItem['icon'] }} text-white {{ $currentIndex > $index ? 'text-sm' : 'text-base' }}"></i>
                        </div>

                        <!-- Status Label -->
                        <div class="mt-3 text-center">
                            <p class="text-sm font-semibold {{ $currentIndex >= $index ? 'text-gray-900' : 'text-gray-500' }}">
                                {{ $statusItem['label'] }}
                            </p>
                            @if($statusItem['date'])
                                <p class="text-xs text-gray-500 mt-1">{{ $statusItem['date']->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $statusItem['date']->format('h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($order->status === 'cancelled')
            <div class="mt-6 p-4 bg-red-50 rounded-lg border border-red-200">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-red-900">Order Cancelled</p>
                        @if($order->cancelled_at)
                            <p class="text-xs text-red-700 mt-1">Cancelled on {{ $order->cancelled_at->format('M d, Y \a\t h:i A') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Order Items</h3>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">
                        {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            @if($item->product && $item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                     alt="{{ $item->product_name }}"
                                     class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center border border-gray-300">
                                    <i class="fas fa-box text-2xl text-gray-400"></i>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $item->product_name }}</h4>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">Quantity:</span>
                                        <span class="font-medium text-gray-900">{{ $item->quantity }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Unit Price:</span>
                                        <span class="font-medium text-gray-900">${{ number_format($item->unit_price, 2) }}</span>
                                    </div>
                                </div>
                                @if($item->tax > 0)
                                    <p class="text-xs text-gray-500 mt-1">Tax: ${{ number_format($item->tax, 2) }}</p>
                                @endif
                            </div>

                            <div class="text-right flex flex-col justify-between">
                                <p class="font-bold text-lg text-gray-900">${{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes Section -->
            @if($order->notes)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Notes</h3>
                    <div class="p-4 bg-amber-50 rounded-lg border border-amber-200">
                        <p class="text-sm text-gray-700">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                    </div>

                    @if($order->tax > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-medium text-gray-900">${{ number_format($order->tax, 2) }}</span>
                        </div>
                    @endif

                    @if($order->shipping_fee > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping Fee</span>
                            <span class="font-medium text-gray-900">${{ number_format($order->shipping_fee, 2) }}</span>
                        </div>
                    @endif

                    <div class="pt-3 border-t-2 border-gray-200 flex justify-between">
                        <span class="font-semibold text-gray-900">Total Amount</span>
                        <span class="font-bold text-xl text-gray-900">${{ number_format($order->total, 2) }}</span>
                    </div>

                    <div class="pt-2 text-xs text-gray-500 text-center">
                        Currency: {{ $order->currency ?? 'USD' }}
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full">
                            <span class="text-lg font-bold text-blue-700">{{ substr($order->buyer->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $order->buyer->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Customer</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-envelope text-gray-400 w-5"></i>
                            <span class="text-gray-900">{{ $order->buyer->email ?? 'N/A' }}</span>
                        </div>
                        @if($order->buyer->phone ?? false)
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-phone text-gray-400 w-5"></i>
                                <span class="text-gray-900">{{ $order->buyer->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Vendor Info -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendor Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full">
                            <span class="text-lg font-bold text-purple-700">{{ substr($order->vendor->name ?? 'V', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $order->vendor->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Vendor</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-envelope text-gray-400 w-5"></i>
                            <span class="text-gray-900">{{ $order->vendor->email ?? 'N/A' }}</span>
                        </div>
                        @if($order->vendor->phone ?? false)
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-phone text-gray-400 w-5"></i>
                                <span class="text-gray-900">{{ $order->vendor->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            @if($order->shippingAddress)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h3>
                    <div class="space-y-2 text-sm text-gray-700">
                        @if($order->shippingAddress->recipient_name ?? false)
                            <p class="font-medium text-gray-900">{{ $order->shippingAddress->recipient_name }}</p>
                        @endif
                        <p>{{ $order->shippingAddress->address_line1 }}</p>
                        @if($order->shippingAddress->address_line2)
                            <p>{{ $order->shippingAddress->address_line2 }}</p>
                        @endif
                        <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                        <p class="font-medium">{{ $order->shippingAddress->country->name }}</p>
                        @if($order->shippingAddress->phone ?? false)
                            <p class="pt-2 border-t mt-2">
                                <i class="fas fa-phone text-gray-400 mr-2"></i>
                                {{ $order->shippingAddress->phone }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Billing Address -->
            @if($order->billingAddress && $order->billing_address_id !== $order->shipping_address_id)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Billing Address</h3>
                    <div class="space-y-2 text-sm text-gray-700">
                        @if($order->billingAddress->recipient_name ?? false)
                            <p class="font-medium text-gray-900">{{ $order->billingAddress->recipient_name }}</p>
                        @endif
                        <p>{{ $order->billingAddress->address_line1 }}</p>
                        @if($order->billingAddress->address_line2)
                            <p>{{ $order->billingAddress->address_line2 }}</p>
                        @endif
                        <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}</p>
                        <p class="font-medium">{{ $order->billingAddress->country }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportToPDF() {
    window.print();
}
</script>
@endpush
@endsection
