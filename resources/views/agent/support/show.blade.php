@extends('layouts.home')

@push('styles')
<style>
    .reply-bubble { border-radius: 12px 12px 12px 4px; }
    .reply-bubble-mine { border-radius: 12px 12px 4px 12px; }
</style>
@endpush

@section('page-content')
<div class="space-y-5 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-start gap-3">
            <a href="{{ route('agent.support.tickets') }}"
               class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors mt-0.5 flex-shrink-0">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold text-gray-900 leading-snug">{{ $ticket->subject }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <span class="text-[10px] font-mono font-semibold text-gray-400">{{ $ticket->ticket_number }}</span>
                    @php
                        $statusMap = [
                            'open'        => ['bg-blue-100 text-blue-700',   'Open'],
                            'in_progress' => ['bg-amber-100 text-amber-700', 'In Progress'],
                            'resolved'    => ['bg-green-100 text-green-700', 'Resolved'],
                            'closed'      => ['bg-gray-100 text-gray-600',   'Closed'],
                        ];
                        $prioMap = [
                            'low'    => 'bg-gray-100 text-gray-500',
                            'medium' => 'bg-blue-100 text-blue-600',
                            'high'   => 'bg-orange-100 text-orange-600',
                            'urgent' => 'bg-red-100 text-red-600',
                        ];
                        [$sCls, $sLabel] = $statusMap[$ticket->status] ?? ['bg-gray-100 text-gray-600','Unknown'];
                        $pCls = $prioMap[$ticket->priority] ?? 'bg-gray-100 text-gray-500';
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $sCls }}">
                        <i class="fas fa-circle text-[5px]"></i> {{ $sLabel }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $pCls }}">
                        {{ ucfirst($ticket->priority) }} Priority
                    </span>
                    <span class="text-[10px] text-gray-400 capitalize">{{ $ticket->category }}</span>
                </div>
            </div>
        </div>

        @if(!$ticket->isClosed())
            <form action="{{ route('agent.support.ticket.close', $ticket->id) }}" method="POST"
                  onsubmit="return confirm('Close this ticket?')" class="flex-shrink-0">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                    <i class="fas fa-times-circle text-red-400"></i> Close Ticket
                </button>
            </form>
        @endif
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm text-red-900 font-medium flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Ticket Thread --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Original Message --}}
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-500 to-blue-700 flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}
                            <span class="ml-2 px-1.5 py-0.5 bg-blue-100 text-blue-600 text-[9px] font-bold rounded uppercase">You</span>
                        </p>
                        <span class="text-xs text-gray-400">{{ $ticket->created_at->format('M d, Y · H:i') }}</span>
                    </div>
                    <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $ticket->description }}</div>
                </div>
            </div>
        </div>

        {{-- Replies --}}
        <div class="divide-y divide-gray-50">
            @foreach($ticket->replies as $reply)
                @php $isStaff = $reply->is_staff_reply; @endphp
                <div class="p-5 {{ $isStaff ? 'bg-blue-50/40' : '' }}">
                    <div class="flex items-start gap-3 {{ $isStaff ? '' : 'flex-row-reverse' }}">
                        {{-- Avatar --}}
                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                            {{ $isStaff
                                ? 'bg-violet-500 to-violet-700'
                                : 'bg-blue-500 to-blue-700' }}">
                            <span class="text-white text-sm font-bold">
                                {{ strtoupper(substr($reply->user?->name ?? 'S', 0, 1)) }}
                            </span>
                        </div>

                        {{-- Bubble --}}
                        <div class="flex-1 {{ $isStaff ? '' : 'flex flex-col items-end' }}">
                            <div class="flex items-center gap-2 mb-1.5 {{ $isStaff ? '' : 'flex-row-reverse' }}">
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $reply->user?->name ?? 'Unknown' }}
                                </p>
                                @if($isStaff)
                                    <span class="px-1.5 py-0.5 bg-violet-100 text-violet-700 text-[9px] font-bold rounded uppercase">
                                        Support
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-600 text-[9px] font-bold rounded uppercase">
                                        You
                                    </span>
                                @endif
                                <span class="text-xs text-gray-400">
                                    {{ $reply->created_at->format('M d · H:i') }}
                                </span>
                            </div>
                            <div class="inline-block px-4 py-3 max-w-[85%]
                                {{ $isStaff
                                    ? 'reply-bubble bg-white border border-gray-200 text-gray-800'
                                    : 'reply-bubble-mine bg-blue-600 text-white' }}
                                text-sm leading-relaxed shadow-sm whitespace-pre-wrap">
                                {{ $reply->message }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Closed banner --}}
        @if($ticket->isClosed())
            <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center gap-3">
                <i class="fas fa-lock text-gray-400"></i>
                <div>
                    <p class="text-sm font-semibold text-gray-600">This ticket is {{ $ticket->status }}</p>
                    <p class="text-xs text-gray-400">
                        Closed on {{ ($ticket->closed_at ?? $ticket->resolved_at)?->format('M d, Y') }}.
                        Need more help?
                        <a href="{{ route('agent.support.ticket.create') }}" class="text-blue-600 underline font-medium">
                            Open a new ticket
                        </a>
                    </p>
                </div>
            </div>
        @else
        {{-- Reply Box --}}
        <div class="p-5 border-t border-gray-200 bg-gray-50">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Add Reply</p>

            @if($errors->has('message'))
                <p class="text-xs text-red-600 mb-2">{{ $errors->first('message') }}</p>
            @endif

            <form action="{{ route('agent.support.ticket.reply', $ticket->id) }}" method="POST">
                @csrf
                <textarea name="message" rows="4" required
                    placeholder="Type your reply here…"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none leading-relaxed">{{ old('message') }}</textarea>
                <div class="flex items-center justify-between mt-3">
                    <p class="text-xs text-gray-400">
                        <i class="fas fa-clock mr-1"></i>
                        Typical response within 24 hours on business days
                    </p>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>

    {{-- Ticket Meta --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Ticket Details</h3>
        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Ticket #</dt>
                <dd class="text-sm font-mono font-bold text-gray-700">{{ $ticket->ticket_number }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Created</dt>
                <dd class="text-sm font-medium text-gray-700">{{ $ticket->created_at->format('M d, Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Category</dt>
                <dd class="text-sm font-medium text-gray-700 capitalize">{{ $ticket->category }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Replies</dt>
                <dd class="text-sm font-medium text-gray-700">{{ $ticket->replies->count() }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
