@extends('layouts.app')

@section('title', 'RFQ Messages')

@section('content')
<div class="min-h-screen bg-gray-50">

    @include('buyer.partial.buyer-nav')

    <!-- Content -->
    <div class="px-4 py-6 mx-auto max-w-7xl  sm:px-6 lg:px-8">
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
                <a href="{{ route('buyer.rfqs.vendors', $rfq) }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl font-black text-gray-900 sm:text-2xl lg:text-lg">Messages</h1>
                    <p class="mt-1 text-xs text-gray-600 sm:text-sm">
                        {{ $vendor->name }} - #RFQ-{{ str_pad($rfq->id, 6, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
            </div>

            <!-- Vendor Info Card -->
            <div class="p-4 mb-6 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr($vendor->name ?? 'V', 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">{{ $vendor->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $vendor->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-sm font-bold text-gray-900">Conversation</h3>
            </div>

            <div class="p-4 space-y-4 max-h-[500px] overflow-y-auto">
                <!-- Initial RFQ Message -->
                <div class="flex gap-3">
                    <div class="flex flex-shrink-0 justify-center items-center w-8 h-8 text-xs font-bold text-white bg-blue-500 rounded-full">
                        {{ strtoupper(substr($rfq->name ?? 'B', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex gap-2 items-center mb-1">
                            <span class="text-sm font-semibold text-gray-900">{{ $rfq->name ?? 'You' }}</span>
                            <span class="text-xs text-gray-500">{{ $rfq->created_at->format('M d, h:i A') }}</span>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $rfq->message }}</p>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                @forelse($messages as $message)
                    <div class="flex gap-3 {{ $message->sender_type === 'buyer' ? 'flex-row-reverse' : '' }}">
                        <div class="w-8 h-8 {{ $message->sender_type === 'buyer' ? 'bg-blue-500' : 'bg-[#ff0808]' }} rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($message->user ? $message->user->name : 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1 {{ $message->sender_type === 'buyer' ? 'text-right' : '' }}">
                            <div class="flex items-center gap-2 mb-1 {{ $message->sender_type === 'buyer' ? 'justify-end' : '' }}">
                                <span class="text-sm font-semibold text-gray-900">{{ $message->user ? $message->user->name : 'System' }}</span>
                                <span class="text-xs text-gray-500">{{ $message->created_at->format('M d, h:i A') }}</span>
                                @if($message->sender_type === 'vendor')
                                    <span class="px-2 py-0.5 text-xs text-green-800 bg-green-100 rounded-full">Vendor</span>
                                @elseif($message->sender_type === 'buyer')
                                    <span class="px-2 py-0.5 text-xs text-blue-800 bg-blue-100 rounded-full">You</span>
                                @endif
                            </div>
                            <div class="inline-block {{ $message->sender_type === 'buyer' ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-200' }} rounded-lg p-3">
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $message->message }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <p class="text-sm text-gray-500">No messages from this vendor yet.</p>
                    </div>
                @endforelse
            </div>

            <!-- Reply Form -->
            <div class="p-4 border-t border-gray-200">
                @if($rfq->isClosed())
                    <div class="p-3 bg-gray-100 border border-gray-300 rounded-lg text-center">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-lock mr-2"></i>
                            This RFQ is closed. No further messages can be sent.
                        </p>
                    </div>
                @else
                    <form action="{{ route('buyer.rfqs.message.store', $rfq) }}" method="POST">
                        @csrf
                        <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                        <div class="flex gap-2">
                            <textarea name="message" rows="3" required placeholder="Reply to vendor..."
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]"></textarea>
                            <button type="submit" class="px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-colors font-semibold self-end">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

