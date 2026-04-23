@extends('layouts.home')

@section('page-content')
<div class="space-y-4 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Notifications</h1>
            <p class="mt-1 text-xs text-gray-500">Stay up to date with your account activity</p>
        </div>
        @if($stats['unread'] > 0)
            <form action="{{ route('agent.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold shadow-md">
                    <i class="fas fa-check-double"></i> Mark All Read
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

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3">
        @foreach([
            ['label'=>'Total',  'value'=>$stats['total'],  'color'=>'gray',  'icon'=>'fa-bell'],
            ['label'=>'Unread', 'value'=>$stats['unread'], 'color'=>'blue',  'icon'=>'fa-envelope'],
            ['label'=>'Read',   'value'=>$stats['read'],   'color'=>'green', 'icon'=>'fa-envelope-open'],
        ] as $card)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-{{ $card['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600 text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex border-b border-gray-100">
            @foreach([
                [null,     'All',    $stats['total']],
                ['unread', 'Unread', $stats['unread']],
                ['read',   'Read',   $stats['read']],
            ] as [$val, $label, $count])
            <a href="{{ route('agent.notifications.index', $val ? ['filter' => $val] : []) }}"
               class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 transition-colors
                   {{ request('filter') === $val
                       ? 'border-blue-600 text-blue-600'
                       : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold
                    {{ request('filter') === $val ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $count }}
                </span>
            </a>
            @endforeach
        </div>

        {{-- Notification List --}}
        @forelse($notifications as $notification)
            <div class="flex items-start gap-4 px-5 py-4 border-b border-gray-50 last:border-0
                        {{ !$notification->is_read ? 'bg-blue-50/40' : '' }}
                        hover:bg-gray-50 transition-colors group">

                {{-- Icon --}}
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                            {{ $notification->is_read ? 'bg-gray-100' : 'bg-blue-100' }}">
                    <i class="fas fa-bell text-sm
                               {{ $notification->is_read ? 'text-gray-400' : 'text-blue-600' }}"></i>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-900 leading-snug">
                            {{ $notification->title }}
                            @if(!$notification->is_read)
                                <span class="ml-1.5 inline-block w-2 h-2 bg-blue-500 rounded-full align-middle"></span>
                            @endif
                        </p>
                        <span class="text-[10px] text-gray-400 flex-shrink-0 mt-0.5">
                            {{ $notification->time_ago }}
                        </span>
                    </div>
                    @if($notification->content)
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            {{ $notification->content }}
                        </p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                    @if(!$notification->is_read)
                        <form action="{{ route('agent.notifications.mark-read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Mark as read">
                                <i class="fas fa-check text-xs"></i>
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('agent.notifications.destroy', $notification->id) }}" method="POST"
                          onsubmit="return confirm('Delete this notification?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center py-16">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-bell-slash text-3xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 font-medium">No notifications</p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ request('filter') ? 'No ' . request('filter') . ' notifications.' : "You're all caught up!" }}
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="flex items-center justify-between px-1">
            <span class="text-xs text-gray-500">
                Showing {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }}
                of {{ $notifications->total() }}
            </span>
            <div class="text-sm">{{ $notifications->links() }}</div>
        </div>
    @endif
</div>
@endsection
