@extends('layouts.app')

@section('title', __('messages.order_details') . ' - ' . $order->order_number)

@section('content')
    <div class="min-h-screen bg-gray-50">
        @include('buyer.partial.buyer-nav')

        <div class="px-3 py-4 mx-auto max-w-7xl sm:px-4 md:px-6 lg:px-8 sm:py-6 lg:py-8">
            <!-- Back Button -->
            <div class="mb-4 sm:mb-6">
                <a href="{{ route('buyer.orders') }}"
                   class="inline-flex gap-2 items-center text-xs font-bold text-gray-600 transition-colors sm:text-sm hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('messages.back_to_orders') }}
                </a>
            </div>

            <!-- Header -->
            <div class="flex flex-wrap gap-3 justify-between items-start mb-4 sm:gap-4 sm:mb-6">
                <div>
                    <h1 class="mb-1 text-xl font-black text-gray-900 sm:text-2xl lg:text-lg">
                        {{ __('messages.order') }} {{ $order->order_number }}
                    </h1>
                    <p class="text-xs text-gray-600 sm:text-sm">
                        {{ __('messages.placed_on') }} {{ $order->created_at->format('F d, Y \a\t h:i A') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="px-3 py-1 sm:px-4 sm:py-2 bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 rounded-full text-xs sm:text-sm font-bold">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <!-- Messages -->
            @if (session('success'))
                <div class="p-3 mb-4 bg-green-50 rounded-lg border border-green-200 sm:mb-6 sm:p-4">
                    <div class="flex gap-2 items-center sm:gap-3">
                        <i class="text-lg text-green-600 fas fa-check-circle sm:text-xl"></i>
                        <p class="text-xs font-medium text-green-800 sm:text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 sm:gap-6">
                <!-- Main Content -->
                <div class="space-y-4 lg:col-span-2 sm:space-y-6">
                    <!-- Order Status Timeline -->
                    <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:p-5 lg:p-6">
                        <h3 class="mb-4 text-base font-bold text-gray-900 sm:text-lg sm:mb-6">{{ __('messages.order_status') }}</h3>

                        <div class="relative">
                            @php
                                $statuses = [
                                    'pending' => ['icon' => 'clock', 'label' => __('messages.order_placed'), 'date' => $order->created_at],
                                    'confirmed' => ['icon' => 'check-circle', 'label' => __('messages.confirmed'), 'date' => $order->confirmed_at],
                                    'processing' => ['icon' => 'cog', 'label' => __('messages.processing'), 'date' => null],
                                    'shipped' => ['icon' => 'shipping-fast', 'label' => __('messages.shipped'), 'date' => $order->shipped_at],
                                    'delivered' => ['icon' => 'check-double', 'label' => __('messages.delivered'), 'date' => $order->delivered_at],
                                ];

                                $currentStatusIndex = array_search($order->status, array_keys($statuses));
                            @endphp

                            @foreach($statuses as $key => $statusInfo)
                                @php
                                    $index = array_search($key, array_keys($statuses));
                                    $isCompleted = $index <= $currentStatusIndex;
                                    $isCurrent = $key === $order->status;
                                @endphp

                                <div class="flex gap-3 pb-6 sm:gap-4 last:pb-0">
                                    <div class="flex flex-col items-center">
                                        <div class="flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10
                                                    {{ $isCompleted ? 'bg-green-500' : 'bg-gray-300' }}">
                                            <i class="text-xs text-white fas fa-{{ $statusInfo['icon'] }} sm:text-sm"></i>
                                        </div>
                                        @if(!$loop->last)
                                            <div class="flex-1 w-0.5 my-1 {{ $isCompleted ? 'bg-green-500' : 'bg-gray-300' }}"
                                                 style="min-height: 30px;"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 pb-2">
                                        <p class="text-xs font-bold sm:text-sm {{ $isCompleted ? 'text-gray-900' : 'text-gray-500' }}">
                                            {{ $statusInfo['label'] }}
                                        </p>
                                        @if($statusInfo['date'])
                                            <p class="text-[10px] sm:text-xs text-gray-600">
                                                {{ $statusInfo['date']->format('M d, Y \a\t h:i A') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:p-5 lg:p-6">
                        <h3 class="mb-4 text-base font-bold text-gray-900 sm:text-lg sm:mb-6">{{ __('messages.order_items') }}</h3>

                        <div class="space-y-3 sm:space-y-4">
                            @foreach($order->items as $item)
                                @php
                                    $productImage = $item->product->images->where('is_primary', true)->first()
                                                  ?? $item->product->images->first();
                                    $imageUrl = $productImage ?  $productImage->image_url : 'https://www.svgrepo.com/show/422038/product.svg';
                                @endphp

                                <div class="flex gap-3 pb-3 border-b border-gray-100 sm:gap-4 last:border-0">
                                    <img src="{{ $imageUrl }}"
                                         alt="{{ $item->product_name }}"
                                         class="object-cover flex-shrink-0 w-16 h-16 rounded-lg sm:w-20 sm:h-20">
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('products.show', $item->product->slug) }}"
                                           class="block mb-1 text-xs font-bold text-gray-900 transition-colors sm:text-sm hover:text-[#ff0808]">
                                            {{ $item->product_name }}
                                        </a>
                                        @if($item->sku)
                                            <p class="text-[10px] sm:text-xs text-gray-600 mb-1">SKU: {{ $item->sku }}</p>
                                        @endif
                                        <div class="flex flex-wrap gap-2 items-center text-[10px] sm:text-xs text-gray-600">
                                            <span>{{ __('messages.qty') }}: {{ $item->quantity }}</span>
                                            <span>×</span>
                                            <span>{{ $item->formatted_unit_price }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900 sm:text-base">{{ $item->formatted_subtotal }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Summary -->
                        <div class="pt-4 mt-4 space-y-2 border-t border-gray-200">
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-gray-600">{{ __('messages.subtotal') }}</span>
                                <span class="font-bold text-gray-900">{{ $order->formatted_subtotal }}</span>
                            </div>
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-gray-600">{{ __('messages.tax') }}</span>
                                <span class="font-bold text-gray-900">{{ $order->currency }} {{ number_format($order->tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-gray-600">{{ __('messages.shipping_fee') }}</span>
                                <span class="font-bold text-gray-900">{{ $order->currency }} {{ number_format($order->shipping_fee, 2) }}</span>
                            </div>
                            <div class="flex justify-between pt-2 text-sm border-t border-gray-200 sm:text-base">
                                <span class="font-bold text-gray-900">{{ __('messages.total') }}</span>
                                <span class="text-lg font-black text-[#ff0808] sm:text-xl">{{ $order->formatted_total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Vendor Info -->
                    @if($order->vendor)
                        <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:p-5">
                            <h3 class="mb-3 text-base font-bold text-gray-900 sm:text-lg">{{ __('messages.vendor') }}</h3>
                            <div class="space-y-2">
                                <p class="text-xs font-bold text-gray-900 sm:text-sm">{{ $order->vendor->name }}</p>
                                <p class="text-[10px] sm:text-xs text-gray-600">{{ $order->vendor->email }}</p>
                                <a href=""
                                   class="block py-2 mt-3 w-full text-xs font-bold text-center text-[#ff0808] rounded-lg border border-[#ff0808] transition-colors hover:bg-[#ff0808] hover:text-white sm:text-sm">
                                    {{ __('messages.view_vendor') }}
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Shipping Address -->
                    @if($order->shippingAddress)
                        <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:p-5">
                            <h3 class="mb-3 text-base font-bold text-gray-900 sm:text-lg">{{ __('messages.shipping_address') }}</h3>
                            <div class="text-xs text-gray-600 sm:text-sm">
                                <p class="font-bold text-gray-900">{{ $order->shippingAddress->full_name }}</p>
                                <p>{{ $order->shippingAddress->address_line1 }}</p>
                                @if($order->shippingAddress->address_line2)
                                    <p>{{ $order->shippingAddress->address_line2 }}</p>
                                @endif
                                <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                                <p>{{ $order->shippingAddress->country->name }}</p>
                                @if($order->shippingAddress->phone)
                                    <p class="mt-2">{{ __('messages.phone') }}: {{ $order->shippingAddress->phone }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Billing Address -->
                    @if($order->billingAddress && $order->billing_address_id !== $order->shipping_address_id)
                        <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:p-5">
                            <h3 class="mb-3 text-base font-bold text-gray-900 sm:text-lg">{{ __('messages.billing_address') }}</h3>
                            <div class="text-xs text-gray-600 sm:text-sm">
                                <p class="font-bold text-gray-900">{{ $order->billingAddress->full_name }}</p>
                                <p>{{ $order->billingAddress->address_line1 }}</p>
                                @if($order->billingAddress->address_line2)
                                    <p>{{ $order->billingAddress->address_line2 }}</p>
                                @endif
                                <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}</p>
                                <p>{{ $order->billingAddress->country }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Order Actions -->
                    <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:p-5">
                        <h3 class="mb-3 text-base font-bold text-gray-900 sm:text-lg">{{ __('messages.actions') }}</h3>
                        <div class="space-y-2">
                            @if($order->is_cancellable)
                                <form action="{{ route('buyer.orders.cancel', $order->id) }}" method="POST"
                                      onsubmit="return confirm('{{ __('messages.confirm_cancel_order') }}')">
                                    @csrf
                                    <button type="submit"
                                            class="w-full py-2 px-4 bg-red-100 text-red-700 text-xs sm:text-sm font-bold rounded-lg hover:bg-red-200 transition-colors">
                                        {{ __('messages.cancel_order') }}
                                    </button>
                                </form>
                            @endif

                            <button onclick="window.print()"
                                    class="w-full py-2 px-4 bg-gray-100 text-gray-700 text-xs sm:text-sm font-bold rounded-lg hover:bg-gray-200 transition-colors">
                                {{ __('messages.print_order') }}
                            </button>

                            <a href="{{ route('buyer.support') }}"
                               class="block w-full py-2 px-4 bg-blue-100 text-blue-700 text-xs sm:text-sm font-bold text-center rounded-lg hover:bg-blue-200 transition-colors">
                                {{ __('messages.contact_support') }}
                            </a>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    @if($order->notes)
                        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200 sm:p-5">
                            <h3 class="mb-2 text-sm font-bold text-yellow-900 sm:text-base">{{ __('messages.order_notes') }}</h3>
                            <p class="text-xs text-yellow-800 sm:text-sm">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
