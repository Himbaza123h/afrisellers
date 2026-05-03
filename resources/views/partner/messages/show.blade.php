@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.messages.index') }}" class="hover:text-gray-600">Messages</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">{{ $other->name }}</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">{{ $other->name }}</h1>
        <p class="text-xs text-gray-500 mt-0.5">Conversation</p>
    </div>
    <a href="{{ route('partner.messages.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-700 font-semibold">{{ session('success') }}</p>
    </div>
@endif

{{-- Message thread --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-4">
    <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 bg-gray-50">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center flex-shrink-0">
            <span class="text-xs font-black text-[#ff0808]">{{ strtoupper(substr($other->name, 0, 1)) }}</span>
        </div>
        <div>
            <p class="text-sm font-bold text-gray-900">{{ $other->name }}</p>
            <p class="text-xs text-gray-400">{{ $other->email }}</p>
        </div>
    </div>

    <div class="p-5 space-y-4 max-h-[60vh] overflow-y-auto" id="message-thread">
        @forelse($messages as $msg)
            @php $isMine = $msg->sender_id == auth()->id(); @endphp
            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[75%]">
                    <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed
                        {{ $isMine
                            ? 'bg-[#ff0808] text-white rounded-br-sm'
                            : 'bg-gray-100 text-gray-800 rounded-bl-sm' }}">
                        {{ $msg->message }}
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 {{ $isMine ? 'text-right' : 'text-left' }}">
                        {{ $msg->created_at->format('d M, H:i') }}
                        @if($isMine)
                            &middot;
                            @if($msg->is_read)
                                <i class="fas fa-check-double text-blue-400"></i>
                            @else
                                <i class="fas fa-check text-gray-400"></i>
                            @endif
                        @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="text-center py-10">
                <i class="fas fa-comment-dots text-gray-200 text-3xl mb-2"></i>
                <p class="text-xs text-gray-400">No messages yet. Say hello!</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Reply form --}}
<form action="{{ route('partner.messages.reply', $other->id) }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex gap-3 items-end">
        <textarea name="message" rows="3" required
                  class="flex-1 px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent resize-none"
                  placeholder="Type a reply...">{{ old('message') }}</textarea>
        <button type="submit"
                class="flex-shrink-0 px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-paper-plane"></i> Send
        </button>
    </div>
</form>

<script>
    // Scroll to bottom of thread on load
    const thread = document.getElementById('message-thread');
    if (thread) thread.scrollTop = thread.scrollHeight;
</script>
@endsection
