@extends('layouts.home')

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Support Tickets</h1>
            <p class="mt-1 text-xs text-gray-500">Manage and respond to user support requests</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- View Toggle --}}
            <a href="{{ route('admin.support.index') }}"
               class="px-3 py-2 rounded-lg text-sm font-semibold border transition-colors
                      {{ $viewMode === 'list' ? 'bg-[#ff0808] text-white border-[#ff0808]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                <i class="fas fa-list mr-1"></i> List
            </a>
            <a href="{{ route('admin.support.index', ['view' => 'users']) }}"
               class="px-3 py-2 rounded-lg text-sm font-semibold border transition-colors
                      {{ $viewMode === 'users' ? 'bg-[#ff0808] text-white border-[#ff0808]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                <i class="fas fa-users mr-1"></i> By User
            </a>
            <a href="{{ route('admin.support.print') }}"
               target="_blank"
               class="px-3 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-print"></i>
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-6">
        @foreach([
            ['label' => 'Total',       'value' => $stats['total'],       'color' => 'gray',   'icon' => 'fa-ticket-alt',         'filter' => []],
            ['label' => 'Open',        'value' => $stats['open'],        'color' => 'blue',   'icon' => 'fa-folder-open',        'filter' => ['status' => 'open']],
            ['label' => 'In Progress', 'value' => $stats['in_progress'], 'color' => 'amber',  'icon' => 'fa-spinner',            'filter' => ['status' => 'in_progress']],
            ['label' => 'Resolved',    'value' => $stats['resolved'],    'color' => 'green',  'icon' => 'fa-check-circle',       'filter' => ['status' => 'resolved']],
            ['label' => 'Closed',      'value' => $stats['closed'],      'color' => 'slate',  'icon' => 'fa-lock',               'filter' => ['status' => 'closed']],
            ['label' => 'Needs Attn',  'value' => $stats['attention'],   'color' => 'red',    'icon' => 'fa-exclamation-triangle','filter' => ['attention' => '1']],
        ] as $card)
        <a href="{{ route('admin.support.index', $card['filter']) }}"
           class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition-all">
            <div class="w-9 h-9 bg-{{ $card['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600 text-xs"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500">{{ $card['label'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
            </div>
        </a>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         BY USER VIEW
    ══════════════════════════════════════════════════════════════════ --}}
    @if($viewMode === 'users')

        {{-- User Search --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <form method="GET" action="{{ route('admin.support.index') }}" class="flex gap-3 items-end">
                <input type="hidden" name="view" value="users">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Search User</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Name or email…"
                               class="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.support.index', ['view' => 'users']) }}"
                       class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-800">Users with Tickets
                    <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ $users->total() }}</span>
                </h2>
            </div>

            @forelse($users as $user)
            <div class="flex items-center gap-4 px-5 py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-500 flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                        @if($user->attention_tickets_count > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full">
                                <i class="fas fa-exclamation-triangle text-[8px]"></i>
                                {{ $user->attention_tickets_count }} needs attention
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                </div>

                {{-- Ticket counts --}}
                <div class="hidden sm:flex items-center gap-4 text-center">
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ $user->support_tickets_count }}</p>
                        <p class="text-[10px] text-gray-400">Total</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-blue-600">{{ $user->open_tickets_count }}</p>
                        <p class="text-[10px] text-gray-400">Open</p>
                    </div>
                </div>

                <a href="{{ route('admin.support.index', ['user_id' => $user->id]) }}"
                   class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition-colors flex-shrink-0">
                    View Tickets <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @empty
            <div class="flex flex-col items-center py-16">
                <i class="fas fa-users text-3xl text-gray-200 mb-3"></i>
                <p class="text-sm text-gray-500">No users found</p>
            </div>
            @endforelse

            @if($users->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">{{ $users->links() }}</div>
            @endif
        </div>

    {{-- ══════════════════════════════════════════════════════════════════
         LIST VIEW
    ══════════════════════════════════════════════════════════════════ --}}
    @else

        {{-- Active user filter banner --}}
        @if(isset($filterUser) && $filterUser)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-blue-200 flex items-center justify-center font-bold text-blue-700 flex-shrink-0">
                {{ strtoupper(substr($filterUser->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-blue-900">Showing tickets for: {{ $filterUser->name }}</p>
                <p class="text-xs text-blue-600">{{ $filterUser->email }}</p>
            </div>
            <a href="{{ route('admin.support.index') }}"
               class="px-3 py-1.5 bg-white border border-blue-200 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-50">
                <i class="fas fa-times mr-1"></i> Clear Filter
            </a>
        </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <form method="GET" action="{{ route('admin.support.index') }}" class="flex flex-wrap gap-3 items-end">
                @if(request('user_id'))
                    <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                @endif
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Ticket #, subject, user…"
                               class="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                    </div>
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                        <option value="">All Statuses</option>
                        @foreach(['open'=>'Open','in_progress'=>'In Progress','resolved'=>'Resolved','closed'=>'Closed'] as $v=>$l)
                            <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[120px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Priority</label>
                    <select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                        <option value="">All</option>
                        @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $v=>$l)
                            <option value="{{ $v }}" {{ request('priority')===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Attention</label>
                    <select name="attention" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#ff0808]/20 focus:border-[#ff0808]">
                        <option value="">All Tickets</option>
                        <option value="1" {{ request('attention')==='1'?'selected':'' }}>Needs Attention</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search','status','priority','category','attention','user_id']))
                        <a href="{{ route('admin.support.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200">
                            <i class="fas fa-times mr-1"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tickets Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-800">
                    Tickets
                    <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ $tickets->total() }}</span>
                </h2>
            </div>

            @if($tickets->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ticket</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">User</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Priority</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Last Activity</th>
                            <th class="px-5 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($tickets as $ticket)
                        @php
                            $statusMap = [
                                'open'        => ['bg-blue-100 text-blue-700',   'Open'],
                                'in_progress' => ['bg-amber-100 text-amber-700', 'In Progress'],
                                'resolved'    => ['bg-green-100 text-green-700', 'Resolved'],
                                'closed'      => ['bg-gray-100 text-gray-500',   'Closed'],
                            ];
                            $prioMap = [
                                'low'    => 'bg-gray-100 text-gray-500',
                                'medium' => 'bg-blue-100 text-blue-600',
                                'high'   => 'bg-orange-100 text-orange-600',
                                'urgent' => 'bg-red-100 text-red-700 font-bold',
                            ];
                            [$sCls, $sLabel] = $statusMap[$ticket->status] ?? ['bg-gray-100 text-gray-500','Unknown'];
                            $pCls = $prioMap[$ticket->priority] ?? 'bg-gray-100 text-gray-500';
                            $rowBg = $ticket->requires_attention ? 'bg-red-50/50' : '';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $rowBg }}">
                            <td class="px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 {{ $ticket->requires_attention ? 'bg-red-100' : 'bg-[#ff0808]/10' }} rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas {{ $ticket->requires_attention ? 'fa-exclamation-triangle text-red-500' : 'fa-ticket-alt text-[#ff0808]' }} text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900 line-clamp-1 max-w-[200px]">{{ $ticket->subject }}</p>
                                            @if($ticket->requires_attention)
                                                <span class="flex-shrink-0 px-1.5 py-0.5 bg-red-100 text-red-700 text-[9px] font-bold rounded uppercase tracking-wider">
                                                    Needs Attention
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] font-mono text-gray-400 mt-0.5">{{ $ticket->ticket_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.support.index', ['user_id' => $ticket->user_id]) }}"
                                   class="flex items-center gap-2 group">
                                    <div class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-[10px] font-bold text-gray-500">{{ strtoupper(substr($ticket->user?->name ?? '?', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-800 group-hover:text-[#ff0808] transition-colors">{{ $ticket->user?->name ?? 'N/A' }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $ticket->user?->email ?? '' }}</p>
                                    </div>
                                </a>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $pCls }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $sCls }}">
                                    <i class="fas fa-circle text-[5px]"></i> {{ $sLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($ticket->latestReply)
                                    <p class="text-xs text-gray-600">{{ $ticket->latestReply->created_at->diffForHumans() }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $ticket->latestReply->is_staff_reply ? 'Staff replied' : 'User replied' }}</p>
                                @else
                                    <p class="text-xs text-gray-500">{{ $ticket->created_at->diffForHumans() }}</p>
                                    <p class="text-[10px] text-gray-400">Opened</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.support.show', $ticket) }}"
                                       class="px-3 py-1.5 bg-[#ff0808] text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    <form action="{{ route('admin.support.destroy', $ticket) }}" method="POST"
                                          onsubmit="return confirm('Delete this ticket?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">{{ $tickets->links() }}</div>
            @endif
            @else
            <div class="flex flex-col items-center py-16">
                <i class="fas fa-ticket-alt text-3xl text-gray-200 mb-3"></i>
                <p class="text-sm text-gray-500 font-semibold">No tickets found</p>
            </div>
            @endif
        </div>

    @endif {{-- end viewMode --}}

</div>
@endsection
