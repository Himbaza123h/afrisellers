@extends('layouts.home')

@section('page-content')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="max-w-7xl mx-auto h-[calc(100vh-10rem)]">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden h-full flex flex-col">
        <!-- Chat Header -->
        <div class="flex-shrink-0 p-3 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('messages.index') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="w-10 h-10 bg-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-gray-900">{{ $group->name }}</h2>
                            @if($group->is_locked)
                                <span class="px-2 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-lock mr-1"></i>Locked
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600">{{ $group->members->count() }} members</p>
                    </div>
                </div>
                <a href="{{ route('messages.group.settings', $group->id) }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg text-sm">
                    <i class="fas fa-cog"></i>
                </a>
            </div>

            @if($group->description)
                <div class="mt-2 p-2 bg-white rounded-lg">
                    <p class="text-xs text-gray-600 line-clamp-1">{!! $group->description !!}</p>
                </div>
            @endif
        </div>

        <!-- Messages Area -->
        <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
            @foreach($group->messages as $message)
                @php
                    $isOwn = $message->sender_id == auth()->id();
                    $senderIsAdmin = $group->isAdmin($message->sender_id);
                @endphp
                <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                    <div class="flex gap-2 max-w-[70%] {{ $isOwn ? 'flex-row-reverse' : '' }}">
                        @if(!$isOwn)
                            <div class="w-7 h-7 bg-green-400 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold text-xs">{{ substr($message->sender->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            @if(!$isOwn)
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-xs font-semibold text-gray-700">{{ $message->sender->name }}</p>
                                    @if($senderIsAdmin)
                                        <span class="px-1.5 py-0.5 bg-purple-100 text-purple-800 text-xs font-medium rounded">Admin</span>
                                    @endif
                                </div>
                            @endif
                            <div class="px-3 py-2 rounded-lg {{ $isOwn ? 'bg-blue-600 text-white' : 'bg-white text-gray-900 border border-gray-200' }}">
                                <div class="text-sm">{!! $message->message !!}</div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 {{ $isOwn ? 'text-right' : '' }}">
                                {{ $message->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Message Input -->
        <div class="flex-shrink-0 border-t border-gray-200 bg-white">
            @if($canSendMessage)
                <div class="p-2">
                    <div id="messageEditor" class="bg-white border border-gray-300 rounded-lg" style="height: 60px;"></div>
                </div>
                <div class="px-3 pb-3 flex items-center gap-2">
                    <button type="button" onclick="openMessageEmojiPicker()" class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm">
                        <i class="far fa-smile"></i>
                    </button>
                    <button type="button" onclick="sendMessage()" class="flex-1 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                        <i class="fas fa-paper-plane mr-2"></i>Send
                    </button>
                </div>
            @else
                <div class="p-4 bg-yellow-50 border-t border-yellow-200">
                    <p class="text-sm text-yellow-800 text-center">
                        <i class="fas fa-lock mr-2"></i>Only admins can send messages in this group
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Emoji Picker Modal -->
<div id="emojiPickerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-4 shadow-xl">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-900">Select Emoji</h3>
            <button onclick="closeMessageEmojiPicker()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <emoji-picker id="messageEmojiPicker"></emoji-picker>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script type="module">
  import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';
</script>

<style>
.ql-editor {
    min-height: 30px !important;
    max-height: 30px !important;
    font-size: 14px;
    padding: 8px 10px;
}
.ql-toolbar.ql-snow {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    padding: 4px 8px;
}
.ql-container.ql-snow {
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}
</style>

<script>
let messageQuill;

document.addEventListener('DOMContentLoaded', function() {
    @if($canSendMessage)
    messageQuill = new Quill('#messageEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline']
            ]
        },
        placeholder: 'Type a message...'
    });
    window.messageQuill = messageQuill;

    messageQuill.on('selection-change', function(range) {
        if (range) closeMessageEmojiPicker();
    });

    const picker = document.getElementById('messageEmojiPicker');
    if (picker) {
        picker.addEventListener('emoji-click', event => {
            const quill = window.messageQuill;
            const range = quill.getSelection(true) || { index: quill.getLength() };
            quill.insertText(range.index, event.detail.unicode);
            quill.setSelection(range.index + event.detail.unicode.length);
            closeMessageEmojiPicker();
        });
    }

    messageQuill.root.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    @endif

    const messagesContainer = document.getElementById('messagesContainer');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
});

function openMessageEmojiPicker() {
    document.getElementById('emojiPickerModal').classList.remove('hidden');
}

function closeMessageEmojiPicker() {
    document.getElementById('emojiPickerModal').classList.add('hidden');
}

function sendMessage() {
    const message = messageQuill.root.innerHTML;

    if (!message || message === '<p><br></p>') return;

    fetch('{{ route("messages.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            group_id: {{ $group->id }},
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const messagesDiv = document.getElementById('messagesContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex justify-end';
            messageDiv.innerHTML = `
                <div class="flex gap-2 max-w-[70%] flex-row-reverse">
                    <div>
                        <div class="px-3 py-2 rounded-lg bg-blue-600 text-white">
                            <div class="text-sm">${data.message.message}</div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 text-right">Just now</p>
                    </div>
                </div>
            `;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
            messageQuill.setContents([]);
        } else {
            alert(data.error || 'Failed to send message');
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
