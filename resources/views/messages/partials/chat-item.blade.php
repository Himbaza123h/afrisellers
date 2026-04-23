@php
    $otherUser = $chat->sender_id == auth()->id() ? $chat->receiver : $chat->sender;
@endphp
<a href="javascript:void(0)"
   onclick="loadPrivateChat({{ $otherUser->id }}, event)"
   class="block hover:bg-gray-50 transition-colors border-b border-gray-100 conversation-item"
   data-user-id="{{ $otherUser->id }}">
    <div class="p-4">
        <div class="flex items-start gap-3">
            <div class="relative">
                @if($otherUser->avatar)
                    <img src="{{ asset($otherUser->avatar) }}" alt="{{ $otherUser->name }}" class="w-12 h-12 rounded-full object-cover">
                @else
                    <div class="w-12 h-12 bg-green-400 to-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-lg">{{ substr($otherUser->name, 0, 1) }}</span>
                    </div>
                @endif
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $otherUser->name }}</h3>
                    <span class="text-xs text-gray-500">{{ $chat->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-xs text-gray-600 truncate">{!! Str::limit(strip_tags($chat->message), 50) !!}</p>
            </div>
        </div>
    </div>
</a>
