@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Support</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Support Center</h1>
        <p class="text-xs text-gray-500 mt-0.5">Get help from our team</p>
    </div>
    <a href="{{ route('partner.support.ticket.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
        <i class="fas fa-plus"></i> New Ticket
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach([
        ['label' => 'Open',        'value' => $stats['open'],        'color' => 'text-[#ff0808]', 'bg' => 'bg-red-50',    'icon' => 'fa-circle-exclamation'],
        ['label' => 'In Progress', 'value' => $stats['in_progress'], 'color' => 'text-amber-600', 'bg' => 'bg-amber-50',  'icon' => 'fa-spinner'],
        ['label' => 'Closed',      'value' => $stats['closed'],      'color' => 'text-green-600', 'bg' => 'bg-green-50',  'icon' => 'fa-check-circle'],
    ] as $stat)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
        <div class="w-9 h-9 {{ $stat['bg'] }} rounded-lg flex items-center justify-center mx-auto mb-2">
            <i class="fas {{ $stat['icon'] }} {{ $stat['color'] }} text-sm"></i>
        </div>
        <p class="text-2xl font-black text-gray-900">{{ $stat['value'] }}</p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Quick actions --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <a href="{{ route('partner.support.tickets') }}"
       class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:border-[#ff0808] hover:shadow-md transition-all group">
        <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 transition-all">
            <i class="fas fa-ticket-alt text-blue-600"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-gray-900">My Tickets</p>
            <p class="text-xs text-gray-400">View all support requests</p>
        </div>
    </a>
    <a href="{{ route('partner.support.faq') }}"
       class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:border-[#ff0808] hover:shadow-md transition-all group">
        <div class="w-11 h-11 bg-violet-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-violet-100 transition-all">
            <i class="fas fa-question-circle text-violet-600"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-gray-900">FAQ</p>
            <p class="text-xs text-gray-400">Common questions answered</p>
        </div>
    </a>
    <a href="{{ route('partner.support.contact') }}"
       class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4 hover:border-[#ff0808] hover:shadow-md transition-all group">
        <div class="w-11 h-11 bg-teal-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-teal-100 transition-all">
            <i class="fas fa-headset text-teal-600"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-gray-900">Contact Us</p>
            <p class="text-xs text-gray-400">Reach out directly</p>
        </div>
    </a>
</div>

{{-- Recent tickets --}}
@if($tickets->isNotEmpty())
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <p class="text-sm font-bold text-gray-900">Recent Tickets</p>
        <a href="{{ route('partner.support.tickets') }}" class="text-xs text-[#ff0808] font-semibold hover:underline">View all</a>
    </div>
    <ul class="divide-y divide-gray-100">
        @foreach($tickets as $ticket)
        <li>
            <a href="{{ route('partner.support.ticket.show', $ticket) }}"
               class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition-all group">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $ticket->subject }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $ticket->created_at->diffForHumans() }}</p>
                </div>
                @php
                    $colors = [
                        'open'        => 'bg-red-50 text-[#ff0808]',
                        'in_progress' => 'bg-amber-50 text-amber-700',
                        'closed'      => 'bg-green-50 text-green-700',
                    ];
                @endphp
                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-md flex-shrink-0 {{ $colors[$ticket->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </span>
                <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-gray-500 transition-all"></i>
            </a>
        </li>
        @endforeach
    </ul>
</div>
@endif
@endsection
