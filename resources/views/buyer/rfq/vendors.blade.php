@extends('layouts.app')

@section('title', 'RFQ Vendors')

@section('content')
<div class="min-h-screen bg-gray-50">

    @include('buyer.partial.buyer-nav')

    <!-- Content -->
    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="p-4 mb-4 bg-green-50 rounded-lg border border-green-300">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 bg-red-50 rounded-lg border border-red-300">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('buyer.rfqs.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl font-black text-gray-900 sm:text-2xl lg:text-lg">Vendors</h1>
                    <p class="mt-1 text-xs text-gray-600 sm:text-sm">
                        #RFQ-{{ str_pad($rfq->id, 6, '0', STR_PAD_LEFT) }} -
                        {{ $rfq->product ? $rfq->product->name : 'General Inquiry' }}
                    </p>
                </div>
            </div>

        <!-- RFQ Info Card -->
        <div class="p-4 mb-6 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Status:</span>
                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full
                            @if($rfq->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($rfq->status === 'accepted') bg-green-100 text-green-800
                            @elseif($rfq->status === 'closed') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($rfq->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Location:</span>
                        <span class="ml-2 font-medium text-gray-900">{{ $rfq->country ? $rfq->country->name : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Vendors Responded:</span>
                        <span class="ml-2 font-medium text-gray-900">{{ $vendors->count() }}</span>
                    </div>
                </div>
                @if(!$rfq->isClosed())
                    <form action="{{ route('buyer.rfqs.close', $rfq) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Are you sure you want to close this RFQ? No further messages can be sent.')"
                            class="px-4 py-2 text-sm font-semibold text-white bg-gray-600 rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-lock mr-2"></i>Close RFQ
                        </button>
                    </form>
                @endif
            </div>
        </div>
        </div>

        <!-- Vendors List -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($vendors as $vendor)
                <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm transition-shadow hover:shadow-md">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr($vendor->name ?? 'V', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-gray-900 truncate">{{ $vendor->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $vendor->email }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-3 text-xs text-gray-500">
                        <span><i class="mr-1 fas fa-comments"></i>{{ $vendor->messages_count }} {{ $vendor->messages_count === 1 ? 'message' : 'messages' }}</span>
                    </div>

                    <a href="{{ route('buyer.rfqs.messages', ['rfq' => $rfq, 'vendor' => $vendor->id]) }}"
                        class="block w-full py-2 text-center text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-colors">
                        View Messages
                    </a>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="p-12 text-center bg-white rounded-lg border border-gray-200">
                        <i class="mb-4 text-5xl text-gray-300 fas fa-users"></i>
                        <p class="mb-2 font-medium text-gray-600">No vendors have responded yet</p>
                        <p class="mb-4 text-sm text-gray-500">Vendors will appear here once they respond to your RFQ</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

