@extends('layouts.home')

@section('title', 'Edit Order - ' . $order->order_number)

@section('page-content')
<div class="container-fluid max-w-6xl px-3 py-4 mx-auto sm:px-4 sm:py-5">
    <!-- Page Header -->
    <div class="mb-4 sm:mb-5">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('admin.orders.show', $order) }}"
               class="p-1.5 transition-colors rounded hover:bg-gray-100">
                <i class="text-gray-600 fas fa-arrow-left"></i>
            </a>
            <h1 class="text-xl font-black text-gray-900 sm:text-2xl lg:text-lg">
                Edit Order
            </h1>
        </div>
        <p class="ml-8 text-xs text-gray-600 sm:text-sm">Update order #{{ $order->order_number }}</p>
    </div>

    @if($errors->any())
        <div class="p-3 mb-4 border-l-4 border-red-500 rounded-lg bg-red-50">
            <div class="flex items-start gap-2">
                <i class="text-base text-red-500 fas fa-exclamation-circle"></i>
                <div>
                    <p class="mb-2 text-sm font-semibold text-red-800">Please fix the following errors:</p>
                    <ul class="space-y-1 text-xs text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Order Information (Read-Only) -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-md bg-[#ff0808]">
                    <i class="text-sm text-white fas fa-info-circle"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-900">Order Information</h2>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <!-- Order Number -->
                <div>
                    <label class="block mb-1 text-xs font-semibold text-gray-700">Order Number</label>
                    <div class="px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-md text-gray-900 font-mono">
                        {{ $order->order_number }}
                    </div>
                </div>

                <!-- Customer -->
                <div>
                    <label class="block mb-1 text-xs font-semibold text-gray-700">Customer</label>
                    <div class="px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-md text-gray-900">
                        {{ $order->buyer->name }}
                    </div>
                </div>

                <!-- Vendor -->
                <div>
                    <label class="block mb-1 text-xs font-semibold text-gray-700">Vendor</label>
                    <div class="px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-md text-gray-900">
                        {{ $order->vendor->name }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items (Read-Only) -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-md">
                    <i class="text-sm text-blue-600 fas fa-box"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-900">Order Items</h2>
            </div>

            <div class="space-y-2">
                @foreach($order->items as $item)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-500">SKU: {{ $item->sku }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-600">Qty: <span class="font-semibold">{{ $item->quantity }}</span></p>
                            <p class="text-xs font-bold text-gray-900">{{ $item->formatted_total }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 mt-3 space-y-2 border-t border-gray-200">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-semibold text-gray-900">{{ $order->formatted_subtotal }}</span>
                </div>
            </div>
        </div>

        <!-- Order Status & Details (Editable) -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-md">
                    <i class="text-sm text-purple-600 fas fa-edit"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-900">Update Order Details</h2>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <!-- Status -->
                <div>
                    <label for="status" class="block mb-1 text-xs font-semibold text-gray-700">
                        Order Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status"
                            name="status"
                            required
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                        <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status', $order->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        Current:
                        @if($order->status == 'pending')
                            <span class="font-semibold text-yellow-600">Pending</span>
                        @elseif($order->status == 'confirmed')
                            <span class="font-semibold text-blue-600">Confirmed</span>
                        @elseif($order->status == 'processing')
                            <span class="font-semibold text-purple-600">Processing</span>
                        @elseif($order->status == 'shipped')
                            <span class="font-semibold text-indigo-600">Shipped</span>
                        @elseif($order->status == 'delivered')
                            <span class="font-semibold text-green-600">Delivered</span>
                        @else
                            <span class="font-semibold text-red-600">Cancelled</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 mt-3 sm:grid-cols-2">
                <!-- Tax -->
                <div>
                    <label for="tax" class="block mb-1 text-xs font-semibold text-gray-700">
                        Tax Amount
                    </label>
                    <input type="number"
                           id="tax"
                           name="tax"
                           value="{{ old('tax', $order->tax) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                           placeholder="0.00">
                </div>

                <!-- Shipping Fee -->
                <div>
                    <label for="shipping_fee" class="block mb-1 text-xs font-semibold text-gray-700">
                        Shipping Fee
                    </label>
                    <input type="number"
                           id="shipping_fee"
                           name="shipping_fee"
                           value="{{ old('shipping_fee', $order->shipping_fee) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                           placeholder="0.00">
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-3">
                <label for="notes" class="block mb-1 text-xs font-semibold text-gray-700">
                    Order Notes
                </label>
                <textarea id="notes"
                          name="notes"
                          rows="3"
                          class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                          placeholder="Add any special instructions or notes...">{{ old('notes', $order->notes) }}</textarea>
            </div>
        </div>

        <!-- Order Timeline (Read-Only) -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex items-center gap-2 pb-3 mb-3 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-md">
                    <i class="text-sm text-indigo-600 fas fa-clock"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-900">Order Timeline</h2>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <p class="text-xs text-gray-500">Created At</p>
                    <p class="text-xs font-semibold text-gray-900">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>

                @if($order->confirmed_at)
                    <div>
                        <p class="text-xs text-gray-500">Confirmed At</p>
                        <p class="text-xs font-semibold text-gray-900">{{ $order->confirmed_at->format('M d, Y h:i A') }}</p>
                    </div>
                @endif

                @if($order->shipped_at)
                    <div>
                        <p class="text-xs text-gray-500">Shipped At</p>
                        <p class="text-xs font-semibold text-gray-900">{{ $order->shipped_at->format('M d, Y h:i A') }}</p>
                    </div>
                @endif

                @if($order->delivered_at)
                    <div>
                        <p class="text-xs text-gray-500">Delivered At</p>
                        <p class="text-xs font-semibold text-gray-900">{{ $order->delivered_at->format('M d, Y h:i A') }}</p>
                    </div>
                @endif

                @if($order->cancelled_at)
                    <div>
                        <p class="text-xs text-gray-500">Cancelled At</p>
                        <p class="text-xs font-semibold text-gray-900">{{ $order->cancelled_at->format('M d, Y h:i A') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between gap-3 pt-3">
            <a href="{{ route('admin.orders.show', $order) }}"
               class="px-4 py-2 text-xs font-semibold text-gray-700 transition-colors border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="mr-1 fas fa-times"></i>Cancel
            </a>

            <button type="submit"
                    class="px-4 py-2 text-xs font-semibold text-white transition-all rounded-md bg-[#ff0808] hover:bg-red-700 shadow-sm">
                <i class="mr-1 fas fa-save"></i>Update Order
            </button>
        </div>
    </form>
</div>
@endsection
