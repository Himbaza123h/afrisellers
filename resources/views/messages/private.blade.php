@extends('layouts.home')

@section('page-content')
<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.css">

<div class="max-w-7xl mx-auto h-[calc(100vh-10rem)]">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden h-full flex flex-col">
        <!-- Chat Header -->
        <div class="flex-shrink-0 p-3 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
            <div class="flex items-center gap-3">
                <a href="{{ route('messages.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
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
        <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
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
                <button type="button" onclick="sendMessage()" class="flex-1 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                    <i class="fas fa-paper-plane mr-2"></i>Send
                </button>
            </div>
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

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<!-- Emoji Picker -->
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
.ql-snow .ql-picker {
    font-size: 13px;
}
</style>

<script>
let messageQuill;

document.addEventListener('DOMContentLoaded', function() {
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

    const messagesContainer = document.getElementById('messagesContainer');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // Auto-close emoji picker when clicking in editor
    messageQuill.on('selection-change', function(range) {
        if (range) {
            closeMessageEmojiPicker();
        }
    });

    // Setup emoji picker
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

    // Enter to send
    messageQuill.root.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
});

function openMessageEmojiPicker() {
    document.getElementById('emojiPickerModal').classList.remove('hidden');
}

function closeMessageEmojiPicker() {
    document.getElementById('emojiPickerModal').classList.add('hidden');
}

function sendMessage() {
    const message = messageQuill.root.innerHTML;

    if (!message || message === '<p><br></p>') {
        return;
    }

    fetch('{{ route("messages.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            receiver_id: {{ $otherUser->id }},
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
                <div class="max-w-[70%]">
                    <div class="px-3 py-2 rounded-lg bg-blue-600 text-white">
                        <div class="text-sm whitespace-pre-wrap">${data.message.message}</div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 text-right">Just now</p>
                </div>
            `;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
            messageQuill.setContents([]);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
