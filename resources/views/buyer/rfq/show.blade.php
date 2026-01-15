@extends('layouts.app')

@section('title', 'RFQ Details')

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
        <!-- Main Content: Vendors List & Messages -->
        <div class="flex gap-6">
            <!-- Vendors Sidebar -->
            <div class="flex-shrink-0 w-80">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-bold text-gray-900">All Vendors ({{ $vendors->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
                        <!-- All Vendors Option -->
                        <a href="{{ route('buyer.rfqs.show', $rfq) }}"
                            class="block p-4 hover:bg-gray-50 transition-colors {{ !$selectedVendorId ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }}">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white bg-blue-500 rounded-full">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">All Vendors</p>
                                    <p class="text-xs text-gray-500">{{ $allMessages->where('sender_type', 'vendor')->count() }} messages</p>
                                </div>
                            </div>
                        </a>

                        <!-- Individual Vendors -->
                        @forelse($vendors as $vendor)
                            @php
                                $vendorMessagesCount = $allMessages->where('user_id', $vendor->id)->count();
                                $isSelected = $selectedVendorId == $vendor->id;
                            @endphp
                            <a href="{{ route('buyer.rfqs.show', ['rfq' => $rfq, 'vendor_id' => $vendor->id]) }}"
                                class="block p-4 hover:bg-gray-50 transition-colors {{ $isSelected ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }}">
                                <div class="flex gap-3 items-center">
                                    <div class="w-10 h-10 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-sm font-bold">
                                        {{ strtoupper(substr($vendor->name ?? 'V', 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $vendor->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $vendorMessagesCount }} {{ $vendorMessagesCount === 1 ? 'message' : 'messages' }}</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-4 text-center">
                                <p class="text-sm text-gray-500">No vendors have responded yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-bold text-gray-900">
                            @if($selectedVendorId)
                                @php
                                    $selectedVendor = $vendors->firstWhere('id', $selectedVendorId);
                                @endphp
                                Messages from {{ $selectedVendor ? $selectedVendor->name : 'Vendor' }}
                            @else
                                All Messages & Quotes
                            @endif
                        </h3>
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
                            <div class="flex gap-3 {{ $message->sender_type === 'buyer' ? '' : 'flex-row-reverse' }}">
                                <div class="w-8 h-8 {{ $message->sender_type === 'buyer' ? 'bg-blue-500' : 'bg-[#ff0808]' }} rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($message->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-1 {{ $message->sender_type === 'buyer' ? '' : 'text-right' }}">
                                    <div class="flex items-center gap-2 mb-1 {{ $message->sender_type === 'buyer' ? '' : 'justify-end' }}">
                                        <span class="text-sm font-semibold text-gray-900">{{ $message->user ? $message->user->name : 'System' }}</span>
                                        <span class="text-xs text-gray-500">{{ $message->created_at->format('M d, h:i A') }}</span>
                                        @if($message->sender_type === 'vendor')
                                            <span class="px-2 py-0.5 text-xs text-green-800 bg-green-100 rounded-full">Vendor</span>
                                        @endif
                                    </div>
                                    <div class="inline-block {{ $message->sender_type === 'buyer' ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-200' }} rounded-lg p-3">
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $message->message }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center">
                                <p class="text-sm text-gray-500">
                                    @if($selectedVendorId)
                                        No messages from this vendor yet.
                                    @else
                                        No messages yet.
                                    @endif
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Reply Form -->
                    <div class="p-4 border-t border-gray-200">
                        <form action="{{ route('buyer.rfqs.message.store', $rfq) }}" method="POST">
                            @csrf
                            <div class="flex gap-2">
                                <textarea name="message" rows="3" required placeholder="Reply to vendors..."
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808]"></textarea>
                                <button type="submit" class="px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-colors font-semibold self-end">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

