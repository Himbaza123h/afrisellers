<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Order #{{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto p-8">
        <!-- Print Button -->
        <div class="no-print mb-6 flex justify-end gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium">
                <i class="fas fa-print"></i>
                Print Invoice
            </button>
            <button onclick="window.close()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all font-medium">
                <i class="fas fa-times"></i>
                Close
            </button>
        </div>

        <!-- Invoice -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-lg font-bold mb-2">INVOICE</h1>
                        <p class="text-blue-100">Order #{{ $order->order_number }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold mb-1">Your Company Name</div>
                        <p class="text-sm text-blue-100">{{ config('app.name') }}</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-8">
                <!-- Info Section -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <!-- From -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">From</h3>
                        <div class="text-sm text-gray-700">
                            <p class="font-semibold text-gray-900 mb-1">{{ $order->vendor->name }}</p>
                            <p>{{ $order->vendor->email }}</p>
                            @if($order->vendor->vendor && $order->vendor->vendor->businessProfile)
                                <p>{{ $order->vendor->vendor->businessProfile->business_name }}</p>
                                <p>{{ $order->vendor->vendor->businessProfile->phone }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- To -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Bill To</h3>
                        <div class="text-sm text-gray-700">
                            <p class="font-semibold text-gray-900 mb-1">{{ $order->buyer->name }}</p>
                            <p>{{ $order->buyer->email }}</p>
                            @if($order->shippingAddress)
                                <p class="mt-2">{{ $order->shippingAddress->address_line1 }}</p>
                                @if($order->shippingAddress->address_line2)
                                    <p>{{ $order->shippingAddress->address_line2 }}</p>
                                @endif
                                <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->zip_code }}</p>
                                <p>{{ $order->shippingAddress->country }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="grid grid-cols-3 gap-6 mb-8 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Invoice Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ $order->created_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Payment Status</p>
                        <p class="text-sm font-medium">
                            @php
                                $paymentColors = [
                                    'paid' => 'text-green-600',
                                    'pending' => 'text-yellow-600',
                                    'failed' => 'text-red-600',
                                ];
                                $color = $paymentColors[$order->payment_status ?? 'pending'] ?? 'text-gray-600';
                            @endphp
                            <span class="{{ $color }}">{{ ucfirst($order->payment_status ?? 'Pending') }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Order Status</p>
                        <p class="text-sm font-medium">
                            @php
                                $statusColors = [
                                    'pending' => 'text-yellow-600',
                                    'confirmed' => 'text-blue-600',
                                    'processing' => 'text-purple-600',
                                    'shipped' => 'text-indigo-600',
                                    'delivered' => 'text-green-600',
                                    'cancelled' => 'text-red-600',
                                ];
                                $color = $statusColors[$order->status] ?? 'text-gray-600';
                            @endphp
                            <span class="{{ $color }}">{{ ucfirst($order->status) }}</span>
                        </p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-8">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Item</th>
                                <th class="text-center py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Qty</th>
                                <th class="text-right py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Price</th>
                                <th class="text-right py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr class="border-b border-gray-200">
                                <td class="py-4 px-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                        @if($item->product)
                                            <p class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center text-sm text-gray-700">{{ $item->quantity }}</td>
                                <td class="py-4 px-4 text-right text-sm text-gray-700">${{ number_format($item->price, 2) }}</td>
                                <td class="py-4 px-4 text-right text-sm font-medium text-gray-900">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="flex justify-end">
                    <div class="w-72">
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-medium text-gray-900">${{ number_format($order->tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="font-medium text-gray-900">${{ number_format($order->shipping_fee, 2) }}</span>
                            </div>
                            <div class="border-t-2 border-gray-300 pt-3 flex justify-between">
                                <span class="text-base font-bold text-gray-900">Total:</span>
                                <span class="text-xl font-bold text-gray-900">${{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($order->notes)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Notes:</h3>
                    <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                </div>
                @endif

                <!-- Footer -->
                <div class="mt-12 pt-6 border-t border-gray-200 text-center text-xs text-gray-500">
                    <p>Thank you for your business!</p>
                    <p class="mt-2">This is a computer-generated invoice and does not require a signature.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
