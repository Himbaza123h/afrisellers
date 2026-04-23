<a href="javascript:void(0)"
   onclick="loadGroupChat({{ $group->id }}, event)"
   class="block hover:bg-gray-50 transition-colors border-b border-gray-100 conversation-item"
   data-group-id="{{ $group->id }}">
    <div class="p-4">
        <div class="flex items-start gap-3">
            <div class="relative">
                <div class="w-12 h-12 bg-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-white text-lg"></i>
                </div>
                @if($group->is_locked)
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-lock text-white text-xs"></i>
                    </span>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $group->name }}</h3>
                    <span class="text-xs text-gray-500">{{ $group->lastMessage?->created_at?->diffForHumans() ?? 'No messages' }}</span>
                </div>
                <p class="text-xs text-gray-600 truncate">
                    @if($group->lastMessage)
                        <span class="font-medium">{{ $group->lastMessage->sender->name }}:</span>
                        {!! Str::limit(strip_tags($group->lastMessage->message), 50) !!}
                    @else
                        No messages yet
                    @endif
                </p>
                <p class="text-xs text-gray-500 mt-1">{{ $group->members->count() }} members</p>
            </div>
        </div>
    </div>
</a>
