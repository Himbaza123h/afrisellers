{{-- partner/support/tickets.blade.php --}}
@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.support.index') }}" class="hover:text-gray-600">Support</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">My Tickets</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Support Tickets</h1>
    </div>
    <a href="{{ route('partner.support.ticket.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
        <i class="fas fa-plus"></i> New Ticket
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-700 font-semibold">{{ session('success') }}</p>
    </div>
@endif

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    @if($tickets->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                <i class="fas fa-ticket-alt text-gray-300 text-2xl"></i>
            </div>
            <p class="text-sm font-semibold text-gray-500">No tickets yet</p>
            <p class="text-xs text-gray-400 mt-1 mb-4">Create a ticket to get support from our team</p>
            <a href="{{ route('partner.support.ticket.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
                <i class="fas fa-plus"></i> New Ticket
            </a>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Subject</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Category</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider hidden md:table-cell">Priority</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Date</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($tickets as $ticket)
                @php
                    $statusColors = [
                        'open'        => 'bg-red-50 text-[#ff0808]',
                        'in_progress' => 'bg-amber-50 text-amber-700',
                        'closed'      => 'bg-green-50 text-green-700',
                    ];
                    $priorityColors = [
                        'low'    => 'bg-gray-100 text-gray-600',
                        'medium' => 'bg-blue-50 text-blue-600',
                        'high'   => 'bg-red-50 text-red-600',
                    ];
                @endphp
                <tr class="hover:bg-gray-50 transition-all">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-gray-800 truncate max-w-[200px]">{{ $ticket->subject }}</p>
                        <p class="text-xs text-gray-400 sm:hidden">{{ ucfirst($ticket->category) }}</p>
                    </td>
                    <td class="px-4 py-3.5 hidden sm:table-cell">
                        <span class="text-xs text-gray-500 capitalize">{{ $ticket->category }}</span>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md {{ $priorityColors[$ticket->priority] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-md {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell text-xs text-gray-400">
                        {{ $ticket->created_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <a href="{{ route('partner.support.ticket.show', $ticket) }}"
                           class="text-xs font-semibold text-[#ff0808] hover:underline">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
