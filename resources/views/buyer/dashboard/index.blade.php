@extends('layouts.app')

@section('title', __('messages.buyer_dashboard_title'))

@section('content')
    <div class="min-h-screen bg-gray-50">

        @include('buyer.partial.buyer-nav')

        <!-- Main Content -->
        <div class="px-3 py-4 mx-auto max-w-7xl sm:px-4 md:px-6 lg:px-8 sm:py-6 lg:py-8">
            <!-- Welcome Section -->
            <div class="mb-4 sm:mb-6 lg:mb-8">
                <h1 class="mb-1 text-xl font-black text-gray-900 sm:text-2xl lg:text-lg sm:mb-2">
                    {{ __('messages.welcome_back') }}, {{ auth()->user()->name }}!
                </h1>
                <p class="text-xs text-gray-600 sm:text-sm">{{ __('messages.buyer_dashboard_subtitle') }}</p>
            </div>

            <!-- Success/Error Messages -->
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

            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-2 gap-3 mb-4 lg:grid-cols-3 sm:gap-4 lg:gap-6 sm:mb-6 lg:mb-8">
                <!-- Total Orders -->
                <div class="p-3 bg-white rounded-lg border border-gray-200 transition-shadow sm:rounded-xl sm:p-5 lg:p-6 hover:shadow-lg">
                    <div class="flex justify-between items-center mb-2 sm:mb-4">
                        <div class="flex justify-center items-center w-8 h-8 bg-blue-100 rounded-lg sm:w-12 sm:h-12 sm:rounded-xl">
                            <i class="text-sm text-blue-600 fas fa-box sm:text-xl"></i>
                        </div>
                        <span class="text-blue-600 text-[10px] sm:text-sm font-bold">{{ __('messages.all_time') }}</span>
                    </div>
                    <p class="text-gray-600 text-[10px] sm:text-sm mb-1">{{ __('messages.total_orders') }}</p>
                    <p class="text-xl font-black text-gray-900 sm:text-lg">{{ $stats['total_orders'] }}</p>
                    <p class="text-[9px] sm:text-xs text-gray-500 mt-1 sm:mt-2">{{ __('messages.since_joining') }}</p>
                </div>

                <!-- Pending Orders -->
                <div class="p-3 bg-white rounded-lg border border-gray-200 transition-shadow sm:rounded-xl sm:p-5 lg:p-6 hover:shadow-lg">
                    <div class="flex justify-between items-center mb-2 sm:mb-4">
                        <div class="flex justify-center items-center w-8 h-8 bg-orange-100 rounded-lg sm:w-12 sm:h-12 sm:rounded-xl">
                            <i class="text-sm text-orange-600 fas fa-clock sm:text-xl"></i>
                        </div>
                        <span class="text-orange-600 text-[10px] sm:text-sm font-bold">{{ __('messages.pending') }}</span>
                    </div>
                    <p class="text-gray-600 text-[10px] sm:text-sm mb-1">{{ __('messages.pending_orders') }}</p>
                    <p class="text-xl font-black text-gray-900 sm:text-lg">{{ $stats['pending_orders'] }}</p>
                    <p class="text-[9px] sm:text-xs text-gray-500 mt-1 sm:mt-2">{{ __('messages.awaiting_delivery') }}</p>
                </div>

                <!-- Active RFQs -->
                <div class="p-3 bg-white rounded-lg border border-gray-200 transition-shadow sm:rounded-xl sm:p-5 lg:p-6 hover:shadow-lg">
                    <div class="flex justify-between items-center mb-2 sm:mb-4">
                        <div class="flex justify-center items-center w-8 h-8 bg-green-100 rounded-lg sm:w-12 sm:h-12 sm:rounded-xl">
                            <i class="text-sm text-green-600 fas fa-file-invoice sm:text-xl"></i>
                        </div>
                        <span class="text-green-600 text-[10px] sm:text-sm font-bold">{{ __('messages.active') }}</span>
                    </div>
                    <p class="text-gray-600 text-[10px] sm:text-sm mb-1">{{ __('messages.active_rfqs') }}</p>
                    <p class="text-xl font-black text-gray-900 sm:text-lg">{{ $stats['active_rfqs'] }}</p>
                    <p class="text-[9px] sm:text-xs text-gray-500 mt-1 sm:mt-2">{{ __('messages.receiving_quotes') }}</p>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 gap-4 mb-4 lg:grid-cols-3 sm:gap-6 lg:gap-8 sm:mb-6 lg:mb-8">
                <!-- Recent Orders -->
                <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm lg:col-span-2 sm:rounded-xl sm:p-5 lg:p-6">
                    <div class="flex justify-between items-center mb-4 sm:mb-6">
                        <h3 class="text-base font-bold text-gray-900 sm:text-lg">{{ __('messages.recent_orders') }}</h3>
                        <a href="{{ route('buyer.orders') }}" class="text-[#ff0808] font-bold hover:underline text-xs sm:text-sm">
                            {{ __('messages.view_all') }}
                        </a>
                    </div>

                    @if($recentOrders->count() > 0)
                        <div class="space-y-3 sm:space-y-4">
                            @foreach($recentOrders as $order)
                                @php
                                    $firstItem = $order->items->first();
                                    $productImage = $firstItem->product->images->where('is_primary', true)->first()
                                                    ?? $firstItem->product->images->first();
                                    $imageUrl = $productImage ? $productImage->image_url : 'https://www.svgrepo.com/show/422038/product.svg';
                                @endphp
                                <div class="flex gap-3 items-start pb-3 border-b border-gray-100 sm:gap-4 sm:pb-4 last:border-0">
                                    <img src="{{ $imageUrl }}"
                                         alt="{{ $firstItem->product_name }}"
                                         class="object-cover flex-shrink-0 w-16 h-16 rounded-lg sm:w-20 sm:h-20">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start mb-1 sm:mb-2">
                                            <div class="flex-1 mr-2 min-w-0">
                                                <p class="text-xs font-bold text-gray-900 truncate sm:text-sm">
                                                    {{ $firstItem->product_name }}
                                                    @if($order->items->count() > 1)
                                                        <span class="text-gray-500">+{{ $order->items->count() - 1 }} more</span>
                                                    @endif
                                                </p>
                                                <p class="text-[10px] sm:text-xs text-gray-600">{{ $order->order_number }}</p>
                                            </div>
                                            <span class="px-2 py-0.5 sm:py-1 bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 rounded-full text-[9px] sm:text-xs font-bold whitespace-nowrap">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center mb-1 sm:mb-2">
                                            <p class="text-sm font-black text-gray-900 sm:text-base">{{ $order->formatted_total }}</p>
                                            <p class="text-[10px] sm:text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <a href="{{ route('buyer.orders.show', $order->id) }}"
                                           class="text-[#ff0808] text-[10px] sm:text-xs font-bold hover:underline">
                                            {{ __('messages.view_details') }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center">
                            <i class="mb-3 text-4xl text-gray-300 fas fa-box-open"></i>
                            <p class="text-sm text-gray-500">{{ __('messages.no_orders_yet') }}</p>
                            <a href="" class="inline-block mt-3 text-xs font-bold text-[#ff0808] hover:underline">
                                {{ __('messages.start_shopping') }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions & Account -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Quick Actions -->
                    <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:rounded-xl sm:p-5 lg:p-6">
                        <h3 class="mb-3 text-base font-bold text-gray-900 sm:text-lg sm:mb-4">
                            {{ __('messages.quick_actions') }}
                        </h3>
                        <div class="space-y-2 sm:space-y-3">
                            <a href=""
                               class="flex gap-2 items-center p-2.5 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg transition-all sm:gap-3 sm:p-3 hover:shadow-md group">
                                <div class="flex flex-shrink-0 justify-center items-center w-8 h-8 bg-blue-100 rounded-lg transition-transform sm:w-10 sm:h-10 group-hover:scale-110">
                                    <i class="text-sm text-blue-600 fas fa-search sm:text-base"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate sm:text-sm">
                                        {{ __('messages.browse_products') }}
                                    </p>
                                    <p class="text-[10px] sm:text-xs text-gray-600 truncate">
                                        {{ __('messages.discover_new_items') }}
                                    </p>
                                </div>
                            </a>

                            <a href="{{ route('buyer.rfqs.create') }}"
                               class="flex gap-2 items-center p-2.5 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg transition-all sm:gap-3 sm:p-3 hover:shadow-md group">
                                <div class="flex flex-shrink-0 justify-center items-center w-8 h-8 bg-green-100 rounded-lg transition-transform sm:w-10 sm:h-10 group-hover:scale-110">
                                    <i class="text-sm text-green-600 fas fa-file-invoice sm:text-base"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate sm:text-sm">
                                        {{ __('messages.post_rfq') }}
                                    </p>
                                    <p class="text-[10px] sm:text-xs text-gray-600 truncate">
                                        {{ __('messages.request_quotes') }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:rounded-xl sm:p-5 lg:p-6">
                        <h3 class="mb-3 text-base font-bold text-gray-900 sm:text-lg sm:mb-4">
                            {{ __('messages.account_details') }}
                        </h3>
                        <div class="space-y-2 sm:space-y-3">
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100 sm:py-2">
                                <span class="text-xs font-medium text-gray-600 sm:text-sm">{{ __('messages.name') }}:</span>
                                <span class="ml-2 text-xs font-bold text-gray-900 truncate sm:text-sm">{{ auth()->user()->name }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100 sm:py-2">
                                <span class="text-xs font-medium text-gray-600 sm:text-sm">{{ __('messages.email') }}:</span>
                                <span class="ml-2 text-xs text-gray-900 truncate sm:text-sm">{{ auth()->user()->email }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1.5 sm:py-2">
                                <span class="text-xs font-medium text-gray-600 sm:text-sm">{{ __('messages.status') }}:</span>
                                <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-green-100 text-green-700">
                                    <span class="mr-1 w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse sm:w-2 sm:h-2 sm:mr-2"></span>
                                    {{ __('messages.active') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('buyer.profile.edit') }}"
                           class="block py-2 mt-3 w-full text-xs font-bold text-center text-gray-700 rounded-lg border border-gray-300 transition-colors sm:mt-4 hover:bg-gray-50 sm:text-sm">
                            {{ __('messages.edit_profile') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Active RFQs Section -->
            @if($activeRfqs->count() > 0)
                <div class="p-4 mb-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:rounded-xl sm:p-5 lg:p-6 sm:mb-6 lg:mb-8">
                    <div class="flex justify-between items-center mb-4 sm:mb-6">
                        <h3 class="text-base font-bold text-gray-900 sm:text-lg">{{ __('messages.my_active_rfqs') }}</h3>
                        <a href="{{ route('buyer.rfqs.create') }}"
                           class="text-[#ff0808] font-bold hover:underline text-xs sm:text-sm whitespace-nowrap">
                            {{ __('messages.create_new_rfq') }}
                        </a>
                    </div>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 sm:gap-4">
                        @foreach($activeRfqs as $rfq)
                            <div class="p-3 rounded-lg border border-gray-200 transition-shadow sm:p-4 hover:shadow-md">
                                <div class="flex justify-between items-start mb-2 sm:mb-3">
                                    <div class="flex-1 mr-2 min-w-0">
                                        <p class="text-xs font-bold text-gray-900 truncate sm:text-sm">{{ $rfq->title }}</p>
                                        <p class="text-[10px] sm:text-xs text-gray-600">#{{ $rfq->id }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 sm:py-1 bg-green-100 text-green-700 rounded-full text-[9px] sm:text-xs font-bold whitespace-nowrap">
                                        {{ ucfirst($rfq->status) }}
                                    </span>
                                </div>
                                <p class="text-[10px] sm:text-xs text-gray-600 mb-2 sm:mb-3 line-clamp-2">
                                    {{ Str::limit($rfq->description, 100) }}
                                </p>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-[10px] sm:text-xs text-gray-500">{{ __('messages.quotes_received') }}:</p>
                                        <p class="text-sm sm:text-base font-black text-[#ff0808]">{{ $rfq->quotes_count ?? 0 }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] sm:text-xs text-gray-500">{{ __('messages.created') }}:</p>
                                        <p class="text-xs font-bold text-gray-900 sm:text-sm">{{ $rfq->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('buyer.rfqs.show', $rfq->id) }}"
                                   class="block w-full mt-2 sm:mt-3 py-1.5 sm:py-2 bg-[#ff0808] text-white text-[10px] sm:text-xs font-bold text-center rounded-lg hover:bg-[#cc0606] transition-colors">
                                    {{ __('messages.view_quotes') }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recommended Products Section -->
            @if($recommendedProducts->count() > 0)
                <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:rounded-xl sm:p-5 lg:p-6">
                    <div class="flex justify-between items-center mb-4 sm:mb-6">
                        <h3 class="text-base font-bold text-gray-900 sm:text-lg">{{ __('messages.recommended_for_you') }}</h3>
                        <a href=""
                           class="text-[#ff0808] font-bold hover:underline text-xs sm:text-sm">
                            {{ __('messages.view_all') }}
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4 sm:gap-4">
                        @foreach($recommendedProducts as $product)
                            @php
                                $productImage = $product->images->where('is_primary', true)->first()
                                              ?? $product->images->first();
                                $imageUrl = $productImage ? $productImage->image_url : 'https://www.svgrepo.com/show/422038/product.svg';
                                $minPrice = $product->prices->min('unit_price');
                            @endphp
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="block p-3 bg-white rounded-lg border border-gray-200 transition-all sm:p-4 hover:shadow-lg group">
                                <div class="overflow-hidden relative mb-2 rounded-lg aspect-square sm:mb-3">
                                    <img src="{{ $imageUrl }}"
                                         alt="{{ $product->name }}"
                                         class="object-cover w-full h-full transition-transform group-hover:scale-110">
                                </div>
                                <p class="mb-1 text-xs font-bold text-gray-900 line-clamp-2 sm:text-sm sm:mb-2">
                                    {{ $product->name }}
                                </p>
                                @if($product->productCategory)
                                    <p class="mb-1 text-[9px] sm:text-xs text-gray-500">
                                        {{ $product->productCategory->name }}
                                    </p>
                                @endif
                                @if($minPrice)
                                    <p class="text-sm font-black text-[#ff0808] sm:text-base">
                                        ${{ number_format($minPrice, 2) }}+
                                    </p>
                                @endif
                                <p class="text-[9px] sm:text-xs text-gray-500 mt-1">
                                    MOQ: {{ $product->min_order_quantity }} units
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
