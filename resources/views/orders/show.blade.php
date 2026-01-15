@extends('layouts.home')

@section('title', 'Order Details - ' . $order->order_number)

@section('page-content')
<div class="container-fluid px-3 py-4 sm:px-4 sm:py-5">
    <!-- Page Header -->
    <div class="mb-4 sm:mb-5">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ auth()->user()->hasRole('admin') ? route('admin.orders.index') : route('vendor.orders.index') }}"
               class="p-1.5 transition-colors rounded hover:bg-gray-100">
                <i class="text-gray-600 fas fa-arrow-left"></i>
            </a>
            <h1 class="text-xl font-black text-gray-900 sm:text-2xl lg:text-lg">
                Order Details
            </h1>
        </div>
        <p class="ml-8 text-xs text-gray-600 sm:text-sm">Order #{{ $order->order_number }}</p>
    </div>

    @if(session('success'))
        <div class="p-3 mb-4 border-l-4 border-green-500 rounded-lg bg-green-50">
            <div class="flex items-center gap-2">
                <i class="text-base text-green-500 fas fa-check-circle"></i>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3 mb-4 border-l-4 border-red-500 rounded-lg bg-red-50">
            <div class="flex items-center gap-2">
                <i class="text-base text-red-500 fas fa-exclamation-circle"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <!-- Left Column: Order Details -->
        <div class="space-y-4 lg:col-span-2">
            <!-- Order Status & Actions -->
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-[#ff0808]">
                            <i class="text-white fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Order Status</p>
                            @if($order->status == 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @elseif($order->status == 'confirmed')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                    <i class="fas fa-check"></i> Confirmed
                                </span>
                            @elseif($order->status == 'processing')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full">
                                    <i class="fas fa-sync"></i> Processing
                                </span>
                            @elseif($order->status == 'shipped')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-1 text-xs font-semibold text-indigo-800 bg-indigo-100 rounded-full">
                                    <i class="fas fa-truck"></i> Shipped
                                </span>
                            @elseif($order->status == 'delivered')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                    <i class="fas fa-check-circle"></i> Delivered
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                    <i class="fas fa-times-circle"></i> Cancelled
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.orders.edit', $order) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif

                        @if($order->status == 'pending' && !auth()->user()->hasRole('admin'))
                            <form action="{{ route('vendor.orders.accept', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Accept this order?')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                    <i class="fas fa-check"></i> Accept Order
                                </button>
                            </form>
                        @endif

                        @if(in_array($order->status, ['pending', 'confirmed']) && !auth()->user()->hasRole('admin'))
                            <form action="{{ route('vendor.orders.process', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-[#ff0808] rounded-md hover:bg-purple-700">
                                    <i class="fas fa-sync"></i> Process
                                </button>
                            </form>
                        @endif

                        @if(in_array($order->status, ['confirmed', 'processing']) && !auth()->user()->hasRole('admin'))
                            <form action="{{ route('vendor.orders.ship', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                    <i class="fas fa-truck"></i> Ship
                                </button>
                            </form>
                        @endif

                        @if($order->is_cancellable)
                            <form action="{{ auth()->user()->hasRole('admin') ? route('admin.orders.cancel', $order) : route('vendor.orders.cancel', $order) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to cancel this order?')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </form>
                        @endif

                        <a href="{{ auth()->user()->hasRole('admin') ? route('admin.orders.invoice', $order) : route('vendor.orders.invoice', $order) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            <i class="fas fa-file-invoice"></i> Invoice
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-md">
                        <i class="text-sm text-blue-600 fas fa-box"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">Order Items</h2>
                </div>

                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex gap-3 p-3 border border-gray-200 rounded-lg">
                            @if($item->product && $item->product->image)
                                <img src="{{ $item->product->image }}"
                                     alt="{{ $item->product_name }}"
                                     class="object-cover w-16 h-16 border border-gray-200 rounded-md">
                            @else
                                <div class="flex items-center justify-center w-16 h-16 bg-gray-100 border border-gray-200 rounded-md">
                                    <i class="text-xl text-gray-400 fas fa-image"></i>
                                </div>
                            @endif

                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $item->sku }}</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <p class="text-xs text-gray-600">Qty: <span class="font-semibold">{{ $item->quantity }}</span></p>
                                    <p class="text-xs text-gray-600">Unit Price: <span class="font-semibold">{{ $item->formatted_unit_price }}</span></p>
                                    <p class="text-xs font-bold text-gray-900">{{ $item->formatted_total }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Totals -->
                <div class="pt-3 mt-3 space-y-2 border-t border-gray-200">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold text-gray-900">{{ $order->formatted_subtotal }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600">Tax:</span>
                        <span class="font-semibold text-gray-900">{{ $order->currency }} {{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600">Shipping Fee:</span>
                        <span class="font-semibold text-gray-900">{{ $order->currency }} {{ number_format($order->shipping_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between pt-2 text-sm border-t border-gray-200">
                        <span class="font-bold text-gray-900">Total:</span>
                        <span class="font-bold text-[#ff0808]">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                        <div class="flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-md">
                            <i class="text-sm text-yellow-600 fas fa-sticky-note"></i>
                        </div>
                        <h2 class="text-sm font-bold text-gray-900">Order Notes</h2>
                    </div>
                    <p class="text-xs text-gray-700">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Right Column: Customer & Shipping Info -->
        <div class="space-y-4">
            <!-- Customer Information -->
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-md">
                        <i class="text-sm text-purple-600 fas fa-user"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">Customer Info</h2>
                </div>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-500">Name</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $order->buyer->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="text-sm text-gray-900">{{ $order->buyer->email }}</p>
                    </div>
                </div>
            </div>

            @if(auth()->user()->hasRole('admin'))
                <!-- Vendor Information -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                        <div class="flex items-center justify-center w-8 h-8 rounded-md bg-emerald-100">
                            <i class="text-sm fas fa-store text-emerald-600"></i>
                        </div>
                        <h2 class="text-sm font-bold text-gray-900">Vendor Info</h2>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500">Business Name</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $order->vendor->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm text-gray-900">{{ $order->vendor->email }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Shipping Address -->
            @if($order->shippingAddress)
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-md">
                            <i class="text-sm text-green-600 fas fa-map-marker-alt"></i>
                        </div>
                        <h2 class="text-sm font-bold text-gray-900">Shipping Address</h2>
                    </div>
                    <p class="text-xs leading-relaxed text-gray-700">
                        {{ $order->shippingAddress->address_line_1 }}<br>
                        @if($order->shippingAddress->address_line_2)
                            {{ $order->shippingAddress->address_line_2 }}<br>
                        @endif
                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}<br>
                        {{ $order->shippingAddress->postal_code }}<br>
                        {{ $order->shippingAddress->country }}
                    </p>
                </div>
            @endif

            <!-- Order Timeline -->
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-md">
                        <i class="text-sm text-indigo-600 fas fa-clock"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">Timeline</h2>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-2">
                        <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 bg-blue-100 rounded-full">
                            <i class="text-xs text-blue-600 fas fa-plus"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-900">Order Placed</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    @if($order->confirmed_at)
                        <div class="flex items-start gap-2">
                            <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 bg-green-100 rounded-full">
                                <i class="text-xs text-green-600 fas fa-check"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">Order Confirmed</p>
                                <p class="text-xs text-gray-500">{{ $order->confirmed_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($order->shipped_at)
                        <div class="flex items-start gap-2">
                            <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 bg-purple-100 rounded-full">
                                <i class="text-xs text-purple-600 fas fa-truck"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">Order Shipped</p>
                                <p class="text-xs text-gray-500">{{ $order->shipped_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($order->delivered_at)
                        <div class="flex items-start gap-2">
                            <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 rounded-full bg-emerald-100">
                                <i class="text-xs fas fa-check-circle text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">Order Delivered</p>
                                <p class="text-xs text-gray-500">{{ $order->delivered_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($order->cancelled_at)
                        <div class="flex items-start gap-2">
                            <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 bg-red-100 rounded-full">
                                <i class="text-xs text-red-600 fas fa-times"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">Order Cancelled</p>
                                <p class="text-xs text-gray-500">{{ $order->cancelled_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
