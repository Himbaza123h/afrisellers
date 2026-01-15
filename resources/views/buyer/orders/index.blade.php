@extends('layouts.app')

@section('title', __('messages.my_orders'))

@section('content')
    <div class="min-h-screen bg-gray-50">
        @include('buyer.partial.buyer-nav')

        <div class="px-3 py-4 mx-auto max-w-7xl sm:px-4 md:px-6 lg:px-8 sm:py-6 lg:py-8">
            <!-- Header -->
            <div class="mb-4 sm:mb-6 lg:mb-8">
                <h1 class="mb-1 text-xl font-black text-gray-900 sm:text-2xl lg:text-lg sm:mb-2">
                    {{ __('messages.my_orders') }}
                </h1>
                <p class="text-xs text-gray-600 sm:text-sm">{{ __('messages.manage_your_orders') }}</p>
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

            @if (session('error'))
                <div class="p-3 mb-4 bg-red-50 rounded-lg border border-red-200 sm:mb-6 sm:p-4">
                    <div class="flex gap-2 items-center sm:gap-3">
                        <i class="text-lg text-red-600 fas fa-exclamation-circle sm:text-xl"></i>
                        <p class="text-xs font-medium text-red-800 sm:text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Filter Tabs -->
            <div class="p-3 mb-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:p-4 sm:mb-6">
                <div class="flex gap-2 overflow-x-auto sm:gap-3 scrollbar-hide">
                    <a href="{{ route('buyer.orders', ['status' => 'all']) }}"
                       class="flex-shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors
                              {{ $status === 'all' ? 'bg-[#ff0808] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ __('messages.all') }}
                    </a>
                    @foreach($statuses as $key => $label)
                        <a href="{{ route('buyer.orders', ['status' => $key]) }}"
                           class="flex-shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors whitespace-nowrap
                                  {{ $status === $key ? 'bg-[#ff0808] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Orders List -->
            @if($orders->count() > 0)
                <div class="space-y-3 sm:space-y-4">
                    @foreach($orders as $order)
                        <div class="p-3 bg-white rounded-lg border border-gray-200 shadow-sm transition-shadow sm:p-4 lg:p-5 hover:shadow-md">
                            <!-- Order Header -->
                            <div class="flex flex-wrap gap-2 justify-between items-start pb-3 mb-3 border-b border-gray-100 sm:gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap gap-2 items-center mb-1">
                                        <h3 class="text-xs font-bold text-gray-900 sm:text-sm">{{ $order->order_number }}</h3>
                                        <span class="px-2 py-0.5 bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 rounded-full text-[9px] sm:text-xs font-bold">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] sm:text-xs text-gray-600">
                                        {{ __('messages.ordered_on') }} {{ $order->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-gray-900 sm:text-sm">{{ __('messages.total') }}</p>
                                    <p class="text-base font-black text-[#ff0808] sm:text-lg">{{ $order->formatted_total }}</p>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="space-y-2 mb-3 sm:space-y-3 sm:mb-4">
                                @foreach($order->items->take(3) as $item)
                                    @php
                                        $productImage = $item->product->images->where('is_primary', true)->first()
                                                      ?? $item->product->images->first();
                                        $imageUrl = $productImage ? $productImage->image_url : 'https://www.svgrepo.com/show/422038/product.svg';
                                    @endphp
                                    <div class="flex gap-2 items-center sm:gap-3">
                                        <img src="{{ $imageUrl }}"
                                             alt="{{ $item->product_name }}"
                                             class="object-cover flex-shrink-0 w-12 h-12 rounded-lg sm:w-16 sm:h-16">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-gray-900 truncate sm:text-sm">{{ $item->product_name }}</p>
                                            <p class="text-[10px] sm:text-xs text-gray-600">
                                                {{ __('messages.qty') }}: {{ $item->quantity }} × {{ $item->formatted_unit_price }}
                                            </p>
                                        </div>
                                        <p class="text-xs font-bold text-gray-900 sm:text-sm">{{ $item->formatted_subtotal }}</p>
                                    </div>
                                @endforeach

                                @if($order->items->count() > 3)
                                    <p class="text-[10px] sm:text-xs text-gray-500 text-center">
                                        +{{ $order->items->count() - 3 }} {{ __('messages.more_items') }}
                                    </p>
                                @endif
                            </div>

                            <!-- Shipping Address -->
                            @if($order->shippingAddress)
                                <div class="p-2 mb-3 bg-gray-50 rounded-lg sm:p-3 sm:mb-4">
                                    <p class="mb-1 text-[10px] sm:text-xs font-bold text-gray-700">{{ __('messages.shipping_address') }}</p>
                                    <p class="text-[10px] sm:text-xs text-gray-600">
                                        {{ $order->shippingAddress->address_line1 }},
                                        {{ $order->shippingAddress->city }},
                                        {{ $order->shippingAddress->state }}
                                        {{ $order->shippingAddress->postal_code }}
                                    </p>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2 sm:gap-3">
                                <a href="{{ route('buyer.orders.show', $order->id) }}"
                                   class="flex-1 py-1.5 sm:py-2 px-3 sm:px-4 bg-gray-100 text-gray-700 text-[10px] sm:text-xs font-bold text-center rounded-lg hover:bg-gray-200 transition-colors">
                                    {{ __('messages.view_details') }}
                                </a>

                                @if($order->is_cancellable)
                                    <form action="{{ route('buyer.orders.cancel', $order->id) }}" method="POST" class="flex-1"
                                          onsubmit="return confirm('{{ __('messages.confirm_cancel_order') }}')">
                                        @csrf
                                        @method('POST')
                                        <button type="submit"
                                                class="w-full py-1.5 sm:py-2 px-3 sm:px-4 bg-red-100 text-red-700 text-[10px] sm:text-xs font-bold rounded-lg hover:bg-red-200 transition-colors">
                                            {{ __('messages.cancel_order') }}
                                        </button>
                                    </form>
                                @endif

                                @if($order->status === 'delivered')
                                    <a href="{{ route('products.show', $order->items->first()->product->slug) }}"
                                       class="flex-1 py-1.5 sm:py-2 px-3 sm:px-4 bg-blue-100 text-blue-700 text-[10px] sm:text-xs font-bold text-center rounded-lg hover:bg-blue-200 transition-colors">
                                        {{ __('messages.write_review') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4 sm:mt-6">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="p-8 text-center bg-white rounded-lg border border-gray-200 sm:p-12">
                    <i class="mb-4 text-5xl text-gray-300 fas fa-box-open sm:text-6xl"></i>
                    <h3 class="mb-2 text-base font-bold text-gray-900 sm:text-lg">{{ __('messages.no_orders_found') }}</h3>
                    <p class="mb-4 text-xs text-gray-600 sm:text-sm">
                        @if($status !== 'all')
                            {{ __('messages.no_orders_with_status', ['status' => $statuses[$status]]) }}
                        @else
                            {{ __('messages.start_shopping_message') }}
                        @endif
                    </p>
                    <a href=""
                       class="inline-block px-4 sm:px-6 py-2 sm:py-3 bg-[#ff0808] text-white text-xs sm:text-sm font-bold rounded-lg hover:bg-[#cc0606] transition-colors">
                        {{ __('messages.browse_products') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection
