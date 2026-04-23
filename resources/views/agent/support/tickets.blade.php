@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Support Tickets</h1>
            <p class="mt-1 text-xs text-gray-500">Track and manage all your support requests</p>
        </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('agent.support.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-semibold shadow-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('agent.support.ticket.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-semibold shadow-md">
                    <i class="fas fa-plus"></i> Open New Ticket
                </a>
            </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3">
        @foreach([
            ['label'=>'Open',        'value'=>$stats['open'],        'color'=>'blue'],
            ['label'=>'In Progress', 'value'=>$stats['in_progress'], 'color'=>'amber'],
            ['label'=>'Resolved',    'value'=>$stats['resolved'],    'color'=>'green'],
        ] as $card)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-{{ $card['color'] }}-600">{{ $card['value'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('agent.support.tickets') }}"
              class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by subject or ticket #…"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <select name="status"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="open"        {{ request('status')=='open'        ?'selected':'' }}>Open</option>
                <option value="in_progress" {{ request('status')=='in_progress' ?'selected':'' }}>In Progress</option>
                <option value="resolved"    {{ request('status')=='resolved'    ?'selected':'' }}>Resolved</option>
                <option value="closed"      {{ request('status')=='closed'      ?'selected':'' }}>Closed</option>
            </select>
            <select name="category"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                @foreach(['general','technical','billing','vendor','account','other'] as $cat)
                    <option value="{{ $cat }}" {{ request('category')==$cat ?'selected':'' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
            <select name="priority"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Priorities</option>
                @foreach(['low','medium','high','urgent'] as $p)
                    <option value="{{ $p }}" {{ request('priority')==$p ?'selected':'' }}>{{ ucfirst($p) }}</option>
                @endforeach
            </select>
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('agent.support.tickets') }}"
               class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>
        </form>
    </div>

    {{-- Ticket Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-800">Tickets</h2>
            <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                {{ $tickets->total() }} {{ Str::plural('ticket', $tickets->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ticket</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Priority</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Last Update</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
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
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('agent.support.ticket.show', $ticket->id) }}"
                                   class="font-semibold text-gray-900 hover:text-blue-600 block text-sm">
                                    {{ Str::limit($ticket->subject, 50) }}
                                </a>
                                <span class="text-[10px] font-mono text-gray-400">{{ $ticket->ticket_number }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-600 capitalize">{{ $ticket->category }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $pCls }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $sCls }}">
                                    <i class="fas fa-circle text-[5px]"></i> {{ $sLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ ($ticket->last_replied_at ?? $ticket->updated_at)->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('agent.support.ticket.show', $ticket->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-ticket-alt text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No tickets found</p>
                                    <p class="text-xs text-gray-400 mt-1 mb-4">Try adjusting your filters or open a new ticket</p>
                                    <a href="{{ route('agent.support.ticket.create') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700">
                                        <i class="fas fa-plus"></i> Open Ticket
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500">
                    Showing {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} of {{ $tickets->total() }}
                </span>
                <div class="text-sm">{{ $tickets->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
