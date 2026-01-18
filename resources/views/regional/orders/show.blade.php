@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ request()->routeIs('regional.*') ? route('regional.orders.index') : route('country.orders.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
            </div>
            <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span></button>
        </div>
    </div>
<!-- Order Status Timeline -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Status</h3>
    <div class="flex items-center justify-between">
        @php
            $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
            $currentIndex = array_search($order->status, $statuses);
            if ($order->status === 'cancelled') {
                $currentIndex = -1;
            }
        @endphp
        @foreach($statuses as $index => $status)
            <div class="flex flex-col items-center flex-1">
                <div class="flex items-center w-full">
                    @if($index > 0)
                        <div class="flex-1 h-1 {{ $currentIndex >= $index ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                    @endif
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentIndex >= $index ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                        @if($currentIndex > $index)
                            <i class="fas fa-check"></i>
                        @else
                            <i class="fas fa-circle text-xs"></i>
                        @endif
                    </div>
                    @if($index < count($statuses) - 1)
                        <div class="flex-1 h-1 {{ $currentIndex > $index ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                    @endif
                </div>
                <span class="text-xs font-medium text-gray-700 mt-2 capitalize">{{ $status }}</span>
            </div>
        @endforeach
    </div>
    @if($order->status === 'cancelled')
        <div class="mt-4 p-4 bg-red-50 rounded-lg border border-red-200">
            <p class="text-sm font-medium text-red-900">This order has been cancelled</p>
        </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Order Items -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex gap-4 p-4 bg-gray-50 rounded-lg">
                        @if($item->product && $item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}" class="w-20 h-20 object-cover rounded-lg">
                        @else
                            <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-2xl text-gray-400"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $item->product_name }}</h4>
                            <p class="text-sm text-gray-500 mt-1">Quantity: {{ $item->quantity }}</p>
                            <p class="text-sm text-gray-500">Unit Price: ${{ number_format($item->unit_price, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">${{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tax</span>
                    <span class="font-medium text-gray-900">${{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Shipping</span>
                    <span class="font-medium text-gray-900">${{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <div class="pt-3 border-t flex justify-between">
                    <span class="font-semibold text-gray-900">Total</span>
                    <span class="font-bold text-lg text-gray-900">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="font-medium text-gray-900">{{ $order->buyer->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-medium text-gray-900">{{ $order->buyer->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Vendor Info -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendor</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="font-medium text-gray-900">{{ $order->vendor->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-medium text-gray-900">{{ $order->vendor->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        @if($order->shippingAddress)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h3>
                <div class="text-sm text-gray-700">
                    <p>{{ $order->shippingAddress->address_line1 }}</p>
                    @if($order->shippingAddress->address_line2)
                        <p>{{ $order->shippingAddress->address_line2 }}</p>
                    @endif
                    <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                    <p>{{ $order->shippingAddress->country }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
</div>
@endsection
