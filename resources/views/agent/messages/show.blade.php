@extends('layouts.home')

@push('styles')
<style>
    #chat-body { scroll-behavior: smooth; }
    .bubble-sent { border-radius: 18px 18px 4px 18px; }
    .bubble-recv { border-radius: 18px 18px 18px 4px; }
    .msg-actions { opacity: 0; transition: opacity 0.15s; }
    .msg-row:hover .msg-actions { opacity: 1; }
</style>
@endpush

@section('page-content')
@php
    $isGroup      = !is_null($group);
    $title        = $isGroup ? $group->name : ($otherUser?->name ?? 'Conversation');
    $subtitle     = $isGroup
        ? ($group->members->count() . ' members')
        : ($otherUser?->email ?? '');
    $conversationId = $isGroup ? $group->id : $otherUser?->id;
@endphp

<div class="flex flex-col h-[calc(100vh-120px)] max-h-[780px]">

    {{-- Chat Header --}}
    <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-t-xl px-4 py-3 shadow-sm flex-shrink-0">
        <a href="{{ route('agent.messages.index') }}"
           class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 transition-colors flex-shrink-0">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        {{-- Avatar --}}
        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
            {{ $isGroup ? 'bg-purple-400 to-purple-600' : 'bg-blue-400 to-blue-600' }}">
            @if($isGroup && $group->avatar)
                <img src="{{ $group->avatar }}" class="w-10 h-10 rounded-full object-cover" alt="">
            @elseif($isGroup)
                <i class="fas fa-users text-white text-sm"></i>
            @else
                <span class="text-white font-bold text-sm">{{ strtoupper(substr($title, 0, 1)) }}</span>
            @endif
        </div>

        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900 truncate">{{ $title }}</p>
            <p class="text-xs text-gray-400 truncate">{{ $subtitle }}</p>
        </div>

        {{-- Group members preview --}}
        @if($isGroup)
            <div class="flex -space-x-2 flex-shrink-0">
                @foreach($group->members->take(4) as $member)
                    <div class="w-7 h-7 rounded-full bg-gray-300 border-2 border-white flex items-center justify-center"
                         title="{{ $member->name }}">
                        <span class="text-[10px] font-bold text-gray-600">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </span>
                    </div>
                @endforeach
                @if($group->members->count() > 4)
                    <div class="w-7 h-7 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center">
                        <span class="text-[10px] font-bold text-gray-500">+{{ $group->members->count() - 4 }}</span>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Messages Body --}}
    <div id="chat-body"
         class="flex-1 overflow-y-auto bg-gray-50 border-x border-gray-200 px-4 py-4 space-y-3">

        @if($messages->isEmpty())
            <div class="flex flex-col items-center justify-center h-full text-center py-10">
                <div class="w-14 h-14 bg-gray-200 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-comment-dots text-3xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium">No messages yet</p>
                <p class="text-xs text-gray-400 mt-1">Start the conversation below</p>
            </div>
        @endif

        @php $prevDate = null; @endphp
        @foreach($messages as $msg)
            @php
                $isMine   = $msg->sender_id === auth()->id();
                $msgDate  = $msg->created_at->toDateString();
            @endphp

            {{-- Date separator --}}
            @if($msgDate !== $prevDate)
                <div class="flex items-center gap-3 my-2">
                    <div class="flex-1 border-t border-gray-200"></div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                        {{ $msg->created_at->isToday() ? 'Today' : ($msg->created_at->isYesterday() ? 'Yesterday' : $msg->created_at->format('M d, Y')) }}
                    </span>
                    <div class="flex-1 border-t border-gray-200"></div>
                </div>
                @php $prevDate = $msgDate; @endphp
            @endif

            <div class="msg-row flex {{ $isMine ? 'justify-end' : 'justify-start' }} items-end gap-2 group">

                {{-- Received: avatar --}}
                @if(!$isMine)
                    <div class="w-7 h-7 rounded-full bg-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0 mb-0.5">
                        <span class="text-white text-[10px] font-bold">
                            {{ strtoupper(substr($msg->sender?->name ?? '?', 0, 1)) }}
                        </span>
                    </div>
                @endif

                <div class="max-w-[70%] min-w-[80px]">

                    {{-- Reply preview --}}
                    @if($msg->replyTo)
                        <div class="mb-1 px-3 py-1.5 rounded-lg border-l-4 bg-gray-100 border-gray-400 text-xs text-gray-500">
                            <p class="font-semibold text-gray-700">{{ $msg->replyTo->sender?->name }}</p>
                            <p class="truncate">{{ Str::limit($msg->replyTo->message, 60) }}</p>
                        </div>
                    @endif

                    {{-- Bubble --}}
                    <div class="relative px-4 py-2.5 text-sm shadow-sm
                        {{ $isMine
                            ? 'bubble-sent bg-blue-600 text-white'
                            : 'bubble-recv bg-white text-gray-800 border border-gray-200' }}">

                        @if($isGroup && !$isMine)
                            <p class="text-[10px] font-bold mb-1
                                {{ $isMine ? 'text-blue-200' : 'text-blue-600' }}">
                                {{ $msg->sender?->name }}
                            </p>
                        @endif

                        <p class="leading-relaxed break-words">{{ $msg->message }}</p>

                        <p class="text-[10px] mt-1 text-right
                            {{ $isMine ? 'text-blue-200' : 'text-gray-400' }}">
                            {{ $msg->created_at->format('H:i') }}
                            @if($isMine)
                                &nbsp;
                                @if($msg->is_read)
                                    <i class="fas fa-check-double"></i>
                                @else
                                    <i class="fas fa-check"></i>
                                @endif
                            @endif
                        </p>
                    </div>

                    {{-- Actions (reply / delete) --}}
                    <div class="msg-actions flex gap-2 mt-1 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <button onclick="setReply({{ $msg->id }}, '{{ addslashes(Str::limit($msg->message, 50)) }}', '{{ addslashes($msg->sender?->name ?? 'You') }}')"
                            class="text-[11px] text-gray-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-reply"></i> Reply
                        </button>
                        @if($isMine)
                            <form action="{{ route('agent.messages.destroy', $msg->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete this message?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[11px] text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Sent: avatar --}}
                @if($isMine)
                    <div class="w-7 h-7 rounded-full bg-blue-500 to-blue-700 flex items-center justify-center flex-shrink-0 mb-0.5">
                        <span class="text-white text-[10px] font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Reply Preview Banner --}}
    <div id="reply-banner" class="hidden bg-blue-50 border-x border-blue-200 px-4 py-2 flex items-center gap-3 flex-shrink-0">
        <div class="flex-1 min-w-0">
            <p class="text-[10px] font-bold text-blue-600" id="reply-name"></p>
            <p class="text-xs text-gray-600 truncate" id="reply-preview"></p>
        </div>
        <button onclick="clearReply()" class="text-gray-400 hover:text-red-500 p-1">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('error'))
        <div class="px-4 py-2 bg-red-50 border-x border-red-200 text-sm text-red-700 flex items-center gap-2 flex-shrink-0">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Reply Input --}}
    <form action="{{ route('agent.messages.reply', $conversationId) }}" method="POST"
          class="flex items-end gap-3 bg-white border border-gray-200 rounded-b-xl px-4 py-3 shadow-sm flex-shrink-0">
        @csrf
        <input type="hidden" name="reply_to" id="replyToInput">

        <div class="flex-1">
            <textarea name="message" id="messageInput" rows="1" required
                placeholder="Type a message…"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none overflow-hidden leading-relaxed"
                oninput="autoResize(this)"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit();}"></textarea>
            @error('message')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="flex-shrink-0 w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-md transition-colors">
            <i class="fas fa-paper-plane text-sm"></i>
        </button>
    </form>

</div>
@endsection

@push('scripts')
<script>
// Auto-scroll to bottom
const chatBody = document.getElementById('chat-body');
if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;

// Auto-resize textarea
function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

// Reply logic
function setReply(id, preview, name) {
    document.getElementById('replyToInput').value = id;
    document.getElementById('reply-name').textContent = 'Replying to ' + name;
    document.getElementById('reply-preview').textContent = preview;
    document.getElementById('reply-banner').classList.remove('hidden');
    document.getElementById('messageInput').focus();
}

function clearReply() {
    document.getElementById('replyToInput').value = '';
    document.getElementById('reply-banner').classList.add('hidden');
}
</script>
@endpush
