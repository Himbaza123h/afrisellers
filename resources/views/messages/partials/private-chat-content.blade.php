<!-- Chat Header -->
<div class="flex-shrink-0 p-3 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
    <div class="flex items-center gap-3">
        <div class="relative">
            @if($otherUser->avatar)
                <img src="{{ asset($otherUser->avatar) }}" alt="{{ $otherUser->name }}" class="w-10 h-10 rounded-full object-cover">
            @else
                <div class="w-10 h-10 bg-green-400 to-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold">{{ substr($otherUser->name, 0, 1) }}</span>
                </div>
            @endif
            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
        </div>
        <div>
            <h2 class="text-base font-bold text-gray-900">{{ $otherUser->name }}</h2>
            <p class="text-xs text-gray-600">{{ $otherUser->email }}</p>
        </div>
    </div>
</div>

<!-- Messages Area -->
<div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" style="max-height: 500px;">
    @forelse($messages as $message)
        @php
            $isOwn = $message->sender_id == auth()->id();
        @endphp
        <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-[70%]">
                <div class="px-3 py-2 rounded-lg {{ $isOwn ? 'bg-blue-600 text-white' : 'bg-white text-gray-900 border border-gray-200' }}">
                    <div class="text-sm whitespace-pre-wrap">{!! $message->message !!}</div>
                </div>
                <p class="text-xs text-gray-500 mt-1 {{ $isOwn ? 'text-right' : '' }}">
                    {{ $message->created_at->format('H:i') }}
                    @if($isOwn && $message->is_read)
                        <i class="fas fa-check-double text-blue-500 ml-1"></i>
                    @endif
                </p>
            </div>
        </div>
    @empty
        <div class="text-center py-12">
            <i class="fas fa-comments text-4xl text-gray-300 mb-3"></i>
            <p class="text-sm text-gray-600">No messages yet</p>
            <p class="text-xs text-gray-500 mt-1">Start the conversation by sending a message</p>
        </div>
    @endforelse
</div>

<!-- Message Input -->
<div class="flex-shrink-0 border-t border-gray-200 bg-white">
    <div class="p-2">
        <div id="messageEditor" class="bg-white border border-gray-300 rounded-lg" style="height: 60px;"></div>
    </div>
    <div class="px-3 pb-3 flex items-center gap-2">
        <button type="button" onclick="openMessageEmojiPicker()" class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm">
            <i class="far fa-smile"></i>
        </button>
        <button type="button" onclick="sendPrivateMessage({{ $otherUser->id }})" class="flex-1 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
            <i class="fas fa-paper-plane mr-2"></i>Send
        </button>
    </div>
</div>
<!-- NO SCRIPT TAG HERE - REMOVED -->
