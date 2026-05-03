@extends('layouts.home')
@section('page-content')

@php
    $statusColors = [
        'open'        => 'bg-red-50 text-[#ff0808] border-red-200',
        'in_progress' => 'bg-amber-50 text-amber-700 border-amber-200',
        'closed'      => 'bg-green-50 text-green-700 border-green-200',
    ];
@endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.support.tickets') }}" class="hover:text-gray-600">Tickets</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">#{{ $ticket->id }}</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">{{ $ticket->subject }}</h1>
        <div class="flex items-center gap-2 mt-1">
            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-md border {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
            </span>
            <span class="text-xs text-gray-400">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
        </div>
    </div>
    <div class="flex items-center gap-2">
        @if($ticket->status !== 'closed')
        <form action="{{ route('partner.support.ticket.close', $ticket) }}" method="POST" onsubmit="return confirm('Close this ticket?')">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
                <i class="fas fa-times-circle"></i> Close Ticket
            </button>
        </form>
        @endif
        <a href="{{ route('partner.support.tickets') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-700 font-semibold">{{ session('success') }}</p>
    </div>
@endif

{{-- Original message --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-4">
    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
        <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center">
            <span class="text-xs font-black text-[#ff0808]">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-800">{{ auth()->user()->name }} <span class="text-gray-400 font-normal">(You)</span></p>
            <p class="text-[10px] text-gray-400">{{ $ticket->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="ml-auto flex items-center gap-2 text-xs text-gray-400">
            <span class="capitalize">{{ $ticket->category }}</span>
            &middot;
            <span class="capitalize">{{ $ticket->priority }} priority</span>
        </div>
    </div>
    <p class="text-sm text-gray-700 leading-relaxed">{{ $ticket->message }}</p>
</div>

{{-- Replies --}}
@if(isset($ticket->replies) && $ticket->replies->isNotEmpty())
<div class="space-y-3 mb-4">
    @foreach($ticket->replies as $reply)
    @php $isMe = $reply->user_id == auth()->id(); @endphp
    <div class="bg-white rounded-xl border {{ $isMe ? 'border-gray-200' : 'border-blue-100 bg-blue-50' }} p-5">
        <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-100">
            <div class="w-7 h-7 rounded-full {{ $isMe ? 'bg-red-50' : 'bg-blue-100' }} flex items-center justify-center">
                <span class="text-[10px] font-black {{ $isMe ? 'text-[#ff0808]' : 'text-blue-600' }}">
                    {{ strtoupper(substr($reply->user->name ?? 'A', 0, 1)) }}
                </span>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-800">
                    {{ $reply->user->name ?? 'Support Team' }}
                    @if(!$isMe)<span class="text-[10px] px-1.5 py-0.5 bg-blue-600 text-white rounded-full ml-1">Staff</span>@endif
                </p>
                <p class="text-[10px] text-gray-400">{{ $reply->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        <p class="text-sm text-gray-700 leading-relaxed">{{ $reply->message }}</p>
    </div>
    @endforeach
</div>
@endif

{{-- Reply form --}}
@if($ticket->status !== 'closed')
<form action="{{ route('partner.support.ticket.reply', $ticket) }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Add a Reply</p>
        <textarea name="message" rows="4" required
                  class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent resize-none"
                  placeholder="Write your reply..."></textarea>
        <div class="mt-3 flex justify-end">
            <button type="submit"
                    class="px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
                <i class="fas fa-paper-plane"></i> Send Reply
            </button>
        </div>
    </div>
</form>
@else
<div class="bg-gray-50 rounded-xl border border-dashed border-gray-200 p-5 text-center">
    <i class="fas fa-lock text-gray-300 text-xl mb-2"></i>
    <p class="text-xs text-gray-400">This ticket is closed. Create a new ticket if you need further assistance.</p>
</div>
@endif
@endsection
