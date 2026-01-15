@extends('layouts.app')

@section('title', __('messages.my_rfqs'))

@section('content')
    <div class="min-h-screen bg-gray-50">
        @include('buyer.partial.buyer-nav')

        <div class="px-3 py-4 mx-auto max-w-7xl sm:px-4 md:px-6 lg:px-8 sm:py-6 lg:py-8">
            <!-- Header -->
            <div class="mb-4 sm:mb-6 lg:mb-8">
                <h1 class="mb-1 text-xl font-black text-gray-900 sm:text-2xl lg:text-lg sm:mb-2">
                    {{ __('messages.my_rfqs') }}
                </h1>
                <p class="text-xs text-gray-600 sm:text-sm">View and manage your RFQs</p>
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
                    <a href="{{ route('buyer.rfqs.index', ['status' => 'all']) }}"
                       class="flex-shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors
                              {{ request('status', 'all') === 'all' ? 'bg-[#ff0808] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All
                    </a>
                    <a href="{{ route('buyer.rfqs.index', ['status' => 'pending']) }}"
                       class="flex-shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors whitespace-nowrap
                              {{ request('status') === 'pending' ? 'bg-[#ff0808] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Pending
                    </a>
                    <a href="{{ route('buyer.rfqs.index', ['status' => 'accepted']) }}"
                       class="flex-shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors whitespace-nowrap
                              {{ request('status') === 'accepted' ? 'bg-[#ff0808] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Accepted
                    </a>
                    <a href="{{ route('buyer.rfqs.index', ['status' => 'rejected']) }}"
                       class="flex-shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors whitespace-nowrap
                              {{ request('status') === 'rejected' ? 'bg-[#ff0808] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Rejected
                    </a>
                    <a href="{{ route('buyer.rfqs.index', ['status' => 'closed']) }}"
                       class="flex-shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-bold rounded-lg transition-colors whitespace-nowrap
                              {{ request('status') === 'closed' ? 'bg-[#ff0808] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Closed
                    </a>
                </div>
            </div>

            <!-- RFQs List -->
            @if($rfqs->count() > 0)
                <div class="space-y-3 sm:space-y-4">
                    @foreach($rfqs as $rfq)
                        <div class="p-3 bg-white rounded-lg border border-gray-200 shadow-sm transition-shadow sm:p-4 lg:p-5 hover:shadow-md">
                            <!-- RFQ Header -->
                            <div class="flex flex-wrap gap-2 justify-between items-start pb-3 mb-3 border-b border-gray-100 sm:gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap gap-2 items-center mb-1">
                                        <h3 class="text-xs font-bold text-gray-900 sm:text-sm">
                                            {{ $rfq->product ? $rfq->product->name : 'General Inquiry' }}
                                        </h3>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-xs font-bold
                                            @if ($rfq->status === 'pending') bg-yellow-100 text-yellow-700
                                            @elseif($rfq->status === 'accepted') bg-green-100 text-green-700
                                            @elseif($rfq->status === 'closed') bg-gray-100 text-gray-700
                                            @else bg-red-100 text-red-700 @endif">
                                            {{ ucfirst($rfq->status) }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] sm:text-xs text-gray-600">
                                        #RFQ-{{ str_pad($rfq->id, 6, '0', STR_PAD_LEFT) }} • {{ $rfq->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-gray-900 sm:text-sm">Quotes</p>
                                    <p class="text-base font-black text-[#ff0808] sm:text-lg">{{ $rfq->messages_count }}</p>
                                </div>
                            </div>

                            <!-- RFQ Message -->
                            @if ($rfq->message)
                                <div class="p-2 mb-3 bg-gray-50 rounded-lg sm:p-3 sm:mb-4">
                                    <p class="mb-1 text-[10px] sm:text-xs font-bold text-gray-700">Message</p>
                                    <p class="text-[10px] sm:text-xs text-gray-600 line-clamp-2">
                                        {{ \Illuminate\Support\Str::limit($rfq->message, 150) }}
                                    </p>
                                </div>
                            @endif

                            <!-- RFQ Details -->
                            @if($rfq->category || $rfq->city)
                                <div class="p-2 mb-3 bg-gray-50 rounded-lg sm:p-3 sm:mb-4">
                                    <div class="flex flex-wrap gap-3 text-[10px] sm:text-xs text-gray-600">
                                        @if($rfq->category)
                                            <div class="flex gap-1 items-center">
                                                <i class="fas fa-tag"></i>
                                                <span>{{ $rfq->category->name }}</span>
                                            </div>
                                        @endif
                                        @if($rfq->city)
                                            <div class="flex gap-1 items-center">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span>{{ $rfq->city }}</span>
                                            </div>
                                        @endif
                                        <div class="flex gap-1 items-center">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ $rfq->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2 sm:gap-3">
                                <a href="{{ route('buyer.rfqs.vendors', $rfq) }}"
                                   class="flex-1 py-1.5 sm:py-2 px-3 sm:px-4 bg-[#ff0808] text-white text-[10px] sm:text-xs font-bold text-center rounded-lg hover:bg-[#cc0606] transition-colors">
                                    View Vendors
                                </a>
                                <a href="{{ route('rfqs.create') }}"
                                   class="flex-1 py-1.5 sm:py-2 px-3 sm:px-4 bg-gray-100 text-gray-700 text-[10px] sm:text-xs font-bold text-center rounded-lg hover:bg-gray-200 transition-colors">
                                    Create Similar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if (method_exists($rfqs, 'hasPages') && $rfqs->hasPages())
                    <div class="mt-4 sm:mt-6">
                        {{ $rfqs->links() }}
                    </div>
                @endif
            @else
                <div class="p-8 text-center bg-white rounded-lg border border-gray-200 sm:p-12">
                    <i class="mb-4 text-5xl text-gray-300 fas fa-file-invoice sm:text-6xl"></i>
                    <h3 class="mb-2 text-base font-bold text-gray-900 sm:text-lg">No RFQs found</h3>
                    <p class="mb-4 text-xs text-gray-600 sm:text-sm">
                        @if(request('status') && request('status') !== 'all')
                            No {{ request('status') }} RFQs found
                        @else
                            Get started by creating your first RFQ
                        @endif
                    </p>
                    <a href="{{ route('rfqs.create') }}"
                       class="inline-block px-4 sm:px-6 py-2 sm:py-3 bg-[#ff0808] text-white text-xs sm:text-sm font-bold rounded-lg hover:bg-[#cc0606] transition-colors">
                        Create RFQ
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
