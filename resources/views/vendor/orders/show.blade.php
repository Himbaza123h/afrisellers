@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('vendor.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
            <a href="{{ route('vendor.orders.invoice', $order) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium">
                <i class="fas fa-file-invoice"></i>
                View Invoice
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Order Items -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Product</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Quantity</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Price</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}" class="w-12 h-12 rounded object-cover">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                            @if($item->product)
                                                <p class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right text-sm text-gray-900">
                                    ${{ number_format($item->price, 2) }}
                                </td>
                                <td class="px-4 py-4 text-right text-sm font-medium text-gray-900">
                                    ${{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Subtotal:</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Tax:</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">${{ number_format($order->tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Shipping:</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">${{ number_format($order->shipping_fee, 2) }}</td>
                            </tr>
                            <tr class="border-t-2">
                                <td colspan="3" class="px-4 py-3 text-right text-base font-bold text-gray-900">Total:</td>
                                <td class="px-4 py-3 text-right text-lg font-bold text-gray-900">${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Customer & Shipping Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Customer Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                <span class="text-lg font-semibold text-blue-700">{{ substr($order->buyer->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $order->buyer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->buyer->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Shipping Address</h3>
                    @if($order->shippingAddress)
                        <div class="text-sm text-gray-700 space-y-1">
                            <p>{{ $order->shippingAddress->address_line1 }}</p>
                            @if($order->shippingAddress->address_line2)
                                <p>{{ $order->shippingAddress->address_line2 }}</p>
                            @endif
                            <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->zip_code }}</p>
                            <p>{{ $order->shippingAddress->country }}</p>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">No shipping address provided</p>
                    @endif
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Order Notes</h3>
                <p class="text-sm text-gray-700">{{ $order->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Order Status -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Order Status</h3>

                <form action="{{ route('vendor.orders.update-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Timeline -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Order Timeline</h3>

                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                            @if($order->confirmed_at || $order->shipped_at || $order->delivered_at)
                                <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
                            @endif
                        </div>
                        <div class="flex-1 pb-4">
                            <p class="text-sm font-medium text-gray-900">Order Placed</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y - h:i A') }}</p>
                        </div>
                    </div>

                    @if($order->confirmed_at)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                            @if($order->shipped_at || $order->delivered_at)
                                <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
                            @endif
                        </div>
                        <div class="flex-1 pb-4">
                            <p class="text-sm font-medium text-gray-900">Order Confirmed</p>
                            <p class="text-xs text-gray-500">{{ $order->confirmed_at->format('M d, Y - h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($order->shipped_at)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                            @if($order->delivered_at)
                                <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
                            @endif
                        </div>
                        <div class="flex-1 pb-4">
                            <p class="text-sm font-medium text-gray-900">Order Shipped</p>
                            <p class="text-xs text-gray-500">{{ $order->shipped_at->format('M d, Y - h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($order->delivered_at)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Order Delivered</p>
                            <p class="text-xs text-gray-500">{{ $order->delivered_at->format('M d, Y - h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($order->cancelled_at)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-times text-red-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Order Cancelled</p>
                            <p class="text-xs text-gray-500">{{ $order->cancelled_at->format('M d, Y - h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>

                <div class="space-y-2">
                    <a href="{{ route('vendor.orders.invoice', $order) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium text-sm">
                        <i class="fas fa-print"></i>
                        Print Invoice
                    </a>

                    <button onclick="window.print()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium text-sm">
                        <i class="fas fa-file-pdf"></i>
                        Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
