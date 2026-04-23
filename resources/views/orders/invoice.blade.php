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
        .dashed-line {
            border-bottom: 2px dashed #e5e7eb;
            margin: 16px 0;
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
            <!-- Header with Logo -->
            <div class="bg-white p-8 border-b-4 border-[#ff0808]">
                <div class="flex justify-between items-start">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png"
                             alt="{{ config('app.name') }}"
                             class="h-24 w-auto object-contain">
                    </div>

                    <!-- Invoice Title -->
                    <div class="text-right">
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">INVOICE</h1>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><span class="font-semibold">Invoice #:</span> {{ $order->order_number }}</p>
                            <p><span class="font-semibold">Date:</span> {{ $order->created_at->format('F d, Y') }}</p>
                            <p><span class="font-semibold">Time:</span> {{ $order->created_at->format('h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-8">
                <!-- Company and Customer Info -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <!-- From -->
                    <div>
                        <div class="mb-4 pb-2 border-b-2 border-gray-200">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">From</h3>
                        </div>
                        <div class="space-y-1 text-sm text-gray-700">
                            <p class="font-bold text-base text-gray-900">{{ $order->vendor->name }}</p>
                            <p class="flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-400 w-4"></i>
                                {{ $order->vendor->email }}
                            </p>
                            @if($order->vendor->vendor && $order->vendor->vendor->businessProfile)
                                <p class="flex items-center gap-2">
                                    <i class="fas fa-building text-gray-400 w-4"></i>
                                    {{ $order->vendor->vendor->businessProfile->business_name }}
                                </p>
                                @if($order->vendor->vendor->businessProfile->phone)
                                <p class="flex items-center gap-2">
                                    <i class="fas fa-phone text-gray-400 w-4"></i>
                                    {{ $order->vendor->vendor->businessProfile->phone }}
                                </p>
                                @endif
                                @if($order->vendor->vendor->businessProfile->tin)
                                <p class="flex items-center gap-2">
                                    <i class="fas fa-id-card text-gray-400 w-4"></i>
                                    TIN: {{ $order->vendor->vendor->businessProfile->tin }}
                                </p>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- To -->
                    <div>
                        <div class="mb-4 pb-2 border-b-2 border-gray-200">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Bill To</h3>
                        </div>
                        <div class="space-y-1 text-sm text-gray-700">
                            <p class="font-bold text-base text-gray-900">{{ $order->buyer->name }}</p>
                            <p class="flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-400 w-4"></i>
                                {{ $order->buyer->email }}
                            </p>
                            @if($order->shippingAddress)
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="font-semibold text-xs text-gray-500 uppercase mb-2">Shipping Address:</p>
                                    <p>{{ $order->shippingAddress->address_line1 }}</p>
                                    @if($order->shippingAddress->address_line2)
                                        <p>{{ $order->shippingAddress->address_line2 }}</p>
                                    @endif
                                    <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->zip_code }}</p>
                                    <p>{{ $order->shippingAddress->country }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="dashed-line"></div>

                <!-- Order Status Banner -->
                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Order Status</p>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'processing' => 'bg-purple-100 text-purple-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold {{ $statusColor }}">
                                {{ strtoupper($order->status) }}
                            </span>
                        </div>

                        <div class="h-8 w-px bg-gray-300"></div>

                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Payment Status</p>
                            @php
                                $paymentColors = [
                                    'paid' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                ];
                                $paymentColor = $paymentColors[$order->payment_status ?? 'pending'] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold {{ $paymentColor }}">
                                {{ strtoupper($order->payment_status ?? 'PENDING') }}
                            </span>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Items Count</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $order->items->count() }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-8">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-900">
                                <th class="text-left py-4 px-2 text-xs font-bold text-gray-700 uppercase tracking-wider">Item Description</th>
                                <th class="text-center py-4 px-2 text-xs font-bold text-gray-700 uppercase tracking-wider">Qty</th>
                                <th class="text-right py-4 px-2 text-xs font-bold text-gray-700 uppercase tracking-wider">Unit Price</th>
                                <th class="text-right py-4 px-2 text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-4 px-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $item->product_name }}</p>
                                        @if($item->product && $item->product->sku)
                                            <p class="text-xs text-gray-500 mt-1">SKU: {{ $item->product->sku }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <span class="inline-flex items-center justify-center w-12 h-8 bg-gray-100 rounded text-sm font-bold text-gray-900">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="py-4 px-2 text-right text-sm text-gray-700">${{ number_format($item->price, 2) }}</td>
                                <td class="py-4 px-2 text-right text-sm font-bold text-gray-900">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="dashed-line"></div>

                <!-- Totals -->
                <div class="flex justify-end mb-8">
                    <div class="w-80">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm font-semibold text-gray-700 uppercase">Subtotal:</span>
                                <span class="text-base font-bold text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                            </div>

                            @if($order->tax > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm font-semibold text-gray-700 uppercase">Tax:</span>
                                <span class="text-base font-bold text-gray-900">${{ number_format($order->tax, 2) }}</span>
                            </div>
                            @endif

                            @if($order->shipping_fee > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-sm font-semibold text-gray-700 uppercase">Shipping:</span>
                                <span class="text-base font-bold text-gray-900">${{ number_format($order->shipping_fee, 2) }}</span>
                            </div>
                            @endif

                            <div class="flex justify-between items-center py-4 bg-[#ff0808] text-white rounded-lg px-4 mt-4">
                                <span class="text-base font-bold uppercase">TOTAL:</span>
                                <span class="text-2xl font-bold">${{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($order->notes)
                <div class="mb-8 p-4 bg-amber-50 border-l-4 border-amber-400 rounded">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-sticky-note text-amber-600 mt-1"></i>
                        <div>
                            <h3 class="text-sm font-bold text-amber-900 mb-1">Order Notes:</h3>
                            <p class="text-sm text-amber-800">{{ $order->notes }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="dashed-line"></div>

                <!-- Transaction Information -->
                @if($order->transactions && $order->transactions->first())
                @php $transaction = $order->transactions->first(); @endphp
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-bold text-blue-900 mb-3 uppercase">Transaction Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-blue-700 font-semibold mb-1">Transaction #:</p>
                            <p class="text-blue-900">{{ $transaction->transaction_number }}</p>
                        </div>
                        @if($transaction->payment_method)
                        <div>
                            <p class="text-blue-700 font-semibold mb-1">Payment Method:</p>
                            <p class="text-blue-900">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</p>
                        </div>
                        @endif
                        @if($transaction->payment_reference)
                        <div>
                            <p class="text-blue-700 font-semibold mb-1">Payment Reference:</p>
                            <p class="text-blue-900">{{ $transaction->payment_reference }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-blue-700 font-semibold mb-1">Transaction Date:</p>
                            <p class="text-blue-900">{{ $transaction->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Footer -->
                <div class="text-center pt-6 border-t-2 border-gray-200">
                    <div class="space-y-2">
                        <p class="text-lg font-bold text-gray-900">Thank you for your business!</p>
                        <p class="text-sm text-gray-600">For any inquiries, please contact us at {{ $order->vendor->email }}</p>
                        <p class="text-xs text-gray-500 mt-4">This is a computer-generated invoice and does not require a signature.</p>
                    </div>
                </div>

                <!-- Powered By -->
                <div class="text-center mt-6 pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-400">Powered by {{ config('app.name') }} - Order Management System</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
