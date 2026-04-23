@extends('layouts.home')

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Support Center</h1>
            <p class="mt-1 text-xs text-gray-500">Get help, track your tickets, and find answers</p>
        </div>
        <a href="{{ route('agent.support.ticket.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-semibold shadow-md">
            <i class="fas fa-plus"></i> Open New Ticket
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['label'=>'Open',        'value'=>$stats['open'],        'color'=>'blue',   'icon'=>'fa-folder-open'],
            ['label'=>'In Progress', 'value'=>$stats['in_progress'], 'color'=>'amber',  'icon'=>'fa-spinner'],
            ['label'=>'Resolved',    'value'=>$stats['resolved'],    'color'=>'green',  'icon'=>'fa-check-circle'],
            ['label'=>'Total',       'value'=>$stats['total'],       'color'=>'purple', 'icon'=>'fa-ticket-alt'],
        ] as $card)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-{{ $card['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Recent Tickets --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-800">Recent Tickets</h2>
                    <a href="{{ route('agent.support.tickets') }}"
                       class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                @forelse($recentTickets as $ticket)
                @php
    // unread = latest reply is from staff (admin answered, agent hasn't replied back)
    $hasUnread = $ticket->latestReply && $ticket->latestReply->is_staff_reply;

    $statusMap = [
        'open'        => ['bg-blue-100 text-blue-700 border-blue-200',   'fa-folder-open',  'Open'],
        'in_progress' => ['bg-amber-100 text-amber-700 border-amber-200','fa-spinner',       'In Progress'],
        'resolved'    => ['bg-green-100 text-green-700 border-green-200','fa-check-circle',  'Resolved'],
        'closed'      => ['bg-gray-100 text-gray-500 border-gray-200',   'fa-lock',          'Closed'],
    ];
    $prioMap = [
        'low'    => ['bg-gray-100 text-gray-500',       'Low'],
        'medium' => ['bg-blue-100 text-blue-600',       'Medium'],
        'high'   => ['bg-orange-100 text-orange-600',   'High'],
        'urgent' => ['bg-red-100 text-red-700 font-bold','Urgent'],
    ];
    [$sCls, $sIcon, $sLabel] = $statusMap[$ticket->status] ?? ['bg-gray-100 text-gray-500 border-gray-200', 'fa-question', 'Unknown'];
    [$pCls, $pLabel] = $prioMap[$ticket->priority] ?? ['bg-gray-100 text-gray-500', ucfirst($ticket->priority)];

    // Row left-border accent per status
    $rowAccent = match($ticket->status) {
        'open'        => 'border-l-4 border-l-blue-400',
        'in_progress' => 'border-l-4 border-l-amber-400',
        'resolved'    => 'border-l-4 border-l-green-400',
        'closed'      => 'border-l-4 border-l-gray-300',
        default       => '',
    };
@endphp
<a href="{{ route('agent.support.ticket.show', $ticket->id) }}"
   class="flex items-start gap-4 px-5 py-4 border-b border-gray-50 hover:bg-gray-50 transition-colors last:border-0 {{ $rowAccent }} {{ $hasUnread ? 'bg-red-50/40' : '' }}">
    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5
        {{ $ticket->status === 'open'        ? 'bg-blue-100'  : '' }}
        {{ $ticket->status === 'in_progress' ? 'bg-amber-100' : '' }}
        {{ $ticket->status === 'resolved'    ? 'bg-green-100' : '' }}
        {{ $ticket->status === 'closed'      ? 'bg-gray-100'  : '' }}">
        <i class="fas {{ $sIcon }} text-sm
            {{ $ticket->status === 'open'        ? 'text-blue-600'  : '' }}
            {{ $ticket->status === 'in_progress' ? 'text-amber-600' : '' }}
            {{ $ticket->status === 'resolved'    ? 'text-green-600' : '' }}
            {{ $ticket->status === 'closed'      ? 'text-gray-400'  : '' }}"></i>
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-start gap-2 mb-1">
            <p class="text-sm font-semibold text-gray-900 truncate flex-1">
                {{ $ticket->subject }}
            </p>
            {{-- Unread badge --}}
            @if($hasUnread)
                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#ff0808] text-white animate-pulse">
                    <i class="fas fa-circle text-[5px]"></i> New Reply
                </span>
            @else
                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $sCls }}">
                    <i class="fas fa-circle text-[5px]"></i> {{ $sLabel }}
                </span>
            @endif
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-[10px] font-mono text-gray-400">{{ $ticket->ticket_number }}</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $pCls }}">
                {{ $pLabel }}
            </span>
            @if($ticket->latestReply)
                <span class="text-[10px] {{ $hasUnread ? 'text-[#ff0808] font-semibold' : 'text-gray-400' }}">
                    {{ $hasUnread ? '⚡ Staff replied ' : 'Last reply ' }}{{ $ticket->latestReply->created_at->diffForHumans() }}
                </span>
            @else
                <span class="text-[10px] text-gray-400">
                    Opened {{ $ticket->created_at->diffForHumans() }}
                </span>
            @endif
        </div>
    </div>
    <i class="fas fa-chevron-right text-gray-300 text-xs mt-1 flex-shrink-0"></i>
</a>
                @empty
                    <div class="flex flex-col items-center py-12">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-ticket-alt text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">No tickets yet</p>
                        <p class="text-xs text-gray-400 mt-1 mb-4">Open a ticket and we'll help you out</p>
                        <a href="{{ route('agent.support.ticket.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700">
                            <i class="fas fa-plus"></i> Open Ticket
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Links + FAQ Preview --}}
        <div class="space-y-4">

            {{-- Quick Links --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Quick Links</h3>
                <div class="space-y-2">
                    @foreach([
                        ['route'=>'agent.support.ticket.create', 'icon'=>'fa-plus-circle',  'label'=>'Open New Ticket',   'color'=>'text-blue-500'],
                        ['route'=>'agent.support.tickets',       'icon'=>'fa-list',          'label'=>'My Tickets',        'color'=>'text-purple-500'],
                        ['route'=>'agent.support.faq',           'icon'=>'fa-question-circle','label'=>'Browse FAQ',       'color'=>'text-green-500'],
                        ['route'=>'agent.support.contact',       'icon'=>'fa-envelope',      'label'=>'Contact Us',        'color'=>'text-amber-500'],
                    ] as $link)
                    <a href="{{ route($link['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors group">
                        <i class="fas {{ $link['icon'] }} {{ $link['color'] }} w-4 text-center"></i>
                        <span class="text-sm text-gray-700 font-medium group-hover:text-gray-900">{{ $link['label'] }}</span>
                        <i class="fas fa-chevron-right text-gray-300 text-xs ml-auto"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- FAQ Preview --}}
            @if($faqs->count())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Popular FAQs</h3>
                <div class="space-y-3">
                    @foreach($faqs as $faq)
                        <div class="border-b border-gray-50 last:border-0 pb-3 last:pb-0">
                            <p class="text-xs font-semibold text-gray-700 leading-snug">{{ $faq->question }}</p>
                            <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ Str::limit($faq->answer, 80) }}</p>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('agent.support.faq') }}"
                   class="mt-4 text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    View All FAQs <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
