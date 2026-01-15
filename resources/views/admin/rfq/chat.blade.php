@extends('layouts.home')

@section('page-content')
<div class="flex flex-col h-[calc(100vh-120px)] lg:h-[calc(100vh-100px)]">
    <div class="flex flex-1 overflow-hidden border border-gray-200 rounded-xl bg-white shadow-sm">
        <!-- Left Sidebar - RFQs List -->
        <div class="w-full lg:w-80 border-r border-gray-200 flex flex-col bg-gray-50">
            <!-- Sidebar Header -->
            <div class="p-4 border-b border-gray-200 bg-white">
                <h2 class="text-lg font-bold text-gray-900 mb-3">RFQs</h2>
                <!-- Search -->
                <div class="relative">
                    <input
                        type="text"
                        id="rfq-search"
                        placeholder="Search"
                        class="w-full px-3 py-2 pl-9 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                    >
                    <i class="absolute left-3 top-2.5 text-gray-400 fas fa-search"></i>
                </div>
            </div>

            <!-- RFQs List -->
            <div class="flex-1 overflow-y-auto">
                @forelse($allRFQs as $rfqItem)
                    @php
                        $isActive = $rfqItem->id === $rfq->id;
                        $lastMessage = $rfqItem->messages()->latest()->first();
                        $productName = $rfqItem->product ? \Illuminate\Support\Str::limit($rfqItem->product->name, 30) : 'General Inquiry';
                        $timeAgo = $rfqItem->created_at->diffForHumans();
                        // Calculate minutes ago (convert to integer to avoid decimals)
                        $minutesAgo = (int) $rfqItem->created_at->diffInMinutes(now());
                        $timeDisplay = $minutesAgo < 60 ? $minutesAgo . 'min' : $rfqItem->created_at->format('M d');
                    @endphp
                    <a
                        href="{{ route('admin.rfq.show', $rfqItem) }}"
                        class="block p-3 border-b border-gray-200 hover:bg-white transition-colors {{ $isActive ? 'bg-white border-l-4 border-l-blue-500' : '' }}"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $productName }}
                                    </h3>
                                    <span class="text-xs text-green-600 font-medium whitespace-nowrap">({{ $timeDisplay }})</span>
                                </div>
                                @if($lastMessage)
                                    <p class="text-xs text-gray-600 truncate">
                                        {{ \Illuminate\Support\Str::limit($lastMessage->message, 40) }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-600 truncate">
                                        {{ \Illuminate\Support\Str::limit($rfqItem->message, 40) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-4 text-center text-sm text-gray-500">
                        No RFQs found
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Middle Sidebar - Vendors List -->
        <div class="hidden lg:block w-64 border-r border-gray-200 flex flex-col bg-gray-50">
            <div class="p-4 border-b border-gray-200 bg-white">
                <h3 class="text-sm font-bold text-gray-900">Vendors ({{ $vendors->count() }})</h3>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-gray-200">
                <!-- All Vendors Option -->
                <a href="{{ route('admin.rfq.show', $rfq) }}"
                    class="block p-3 hover:bg-white transition-colors {{ !$selectedVendorId ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }}">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900 truncate">All Vendors</p>
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
                    <a href="{{ route('admin.rfq.show', ['rfq' => $rfq, 'vendor_id' => $vendor->id]) }}"
                        class="block p-3 hover:bg-white transition-colors {{ $isSelected ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }}">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($vendor->name ?? 'V', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $vendor->name }}</p>
                                <p class="text-xs text-gray-500">{{ $vendorMessagesCount }} {{ $vendorMessagesCount === 1 ? 'msg' : 'msgs' }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-3 text-center">
                        <p class="text-xs text-gray-500">No vendors responded</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Side - Chat Area -->
        <div class="flex-1 flex flex-col bg-white">
            <!-- Chat Header -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($rfq->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">
                                @if($selectedVendorId)
                                    @php
                                        $selectedVendor = $vendors->firstWhere('id', $selectedVendorId);
                                    @endphp
                                    {{ $selectedVendor ? $selectedVendor->name : 'Vendor' }} - {{ $rfq->name ?? 'Unknown' }}
                                @else
                                    {{ $rfq->name ?? 'Unknown' }}
                                @endif
                            </h3>
                            <p class="text-xs text-gray-600">
                                @if($rfq->product)
                                    {{ $rfq->product->name }}
                                @else
                                    General Inquiry
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($rfq->status === 'pending')
                            <span class="px-2.5 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">Pending</span>
                        @elseif($rfq->status === 'accepted')
                            <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Accepted</span>
                        @elseif($rfq->status === 'rejected')
                            <span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Rejected</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="messages-container">
                <!-- Initial RFQ Message -->
                <div class="flex gap-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($rfq->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900">{{ $rfq->name ?? 'Buyer' }}</span>
                            <span class="text-xs text-gray-500">{{ $rfq->created_at->format('h:i A') }}</span>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $initialMessage }}</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                @forelse($messages as $message)
                    @if($message->sender_type === 'admin')
                        <!-- Admin Message (Right) -->
                        <div class="flex gap-3 justify-end">
                            <div class="flex-1 max-w-[70%]">
                                <div class="flex items-center gap-2 mb-1 justify-end">
                                    <span class="text-xs text-gray-500">{{ $message->created_at->format('M d, h:i A') }}</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $message->user ? $message->user->name : 'Admin' }}</span>
                                    <span class="px-2 py-0.5 text-xs bg-purple-100 text-purple-800 rounded-full">Admin</span>
                                </div>
                                <div class="bg-[#ff0808] text-white rounded-lg p-3 ml-auto">
                                    <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($message->user ? $message->user->name : 'A', 0, 1)) }}
                            </div>
                        </div>
                    @elseif($message->sender_type === 'vendor')
                        <!-- Vendor Message (Right) -->
                        <div class="flex gap-3 justify-end">
                            <div class="flex-1 max-w-[70%]">
                                <div class="flex items-center gap-2 mb-1 justify-end">
                                    <span class="text-xs text-gray-500">{{ $message->created_at->format('M d, h:i A') }}</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $message->user ? $message->user->name : 'Vendor' }}</span>
                                    <span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Vendor</span>
                                </div>
                                <div class="bg-gray-100 rounded-lg p-3 ml-auto border border-gray-200">
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $message->message }}</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($message->user ? $message->user->name : 'V', 0, 1)) }}
                            </div>
                        </div>
                    @else
                        <!-- Buyer Message (Left) -->
                        <div class="flex gap-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($rfq->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-1 max-w-[70%]">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-gray-900">{{ $rfq->name ?? 'Buyer' }}</span>
                                    <span class="text-xs text-gray-500">{{ $message->created_at->format('M d, h:i A') }}</span>
                                </div>
                                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $message->message }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-8">
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

            <!-- Reply Input -->
            <div class="p-4 border-t border-gray-200 bg-white">
                <form id="reply-form" action="{{ route('admin.rfq.message.store', $rfq) }}" method="POST">
                    @csrf
                    <div class="flex gap-2 items-end">
                        <button type="button" class="p-2 text-gray-400 hover:text-gray-600 transition-colors" title="Attach file">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <textarea
                            name="message"
                            id="message-input"
                            rows="1"
                            placeholder="Reply..."
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff0808] focus:border-transparent resize-none"
                            required
                        ></textarea>
                        <button type="button" class="p-2 text-gray-400 hover:text-gray-600 transition-colors" title="Voice message">
                            <i class="fas fa-microphone"></i>
                        </button>
                        <button type="submit" class="p-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-colors" title="Send">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    const replyForm = document.getElementById('reply-form');
    const messageInput = document.getElementById('message-input');

    if (messageInput) {
        // Auto-resize textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Scroll to bottom on load
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Handle form submission with AJAX
        if (replyForm) {
            replyForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const message = messageInput.value.trim();
                if (!message) return;

                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');
                const originalHTML = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add message to chat
                        const messageHTML = `
                            <div class="flex gap-3 justify-end">
                                <div class="flex-1 max-w-[70%]">
                                    <div class="flex items-center gap-2 mb-1 justify-end">
                                        <span class="text-xs text-gray-500">Just now</span>
                                        <span class="text-sm font-semibold text-gray-900">You</span>
                                    </div>
                                    <div class="bg-[#ff0808] text-white rounded-lg p-3 ml-auto">
                                        <p class="text-sm whitespace-pre-wrap">${message}</p>
                                    </div>
                                </div>
                                <div class="w-8 h-8 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </div>
                        `;
                        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        messageInput.value = '';
                        messageInput.style.height = 'auto';
                    } else {
                        alert('Failed to send message. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to send message. Please try again.');
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalHTML;
                });
            });
        }
    }

    // Search functionality
    const searchInput = document.getElementById('rfq-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rfqItems = document.querySelectorAll('[href*="/admin/rfqs/"]');

            rfqItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm) || searchTerm === '') {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush
@endsection

