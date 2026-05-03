@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Notifications</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Notifications</h1>
        @if($unreadCount > 0)
            <p class="text-xs text-gray-500 mt-0.5">{{ $unreadCount }} unread</p>
        @endif
    </div>
    @if($unreadCount > 0)
    <form action="{{ route('partner.notifications.mark-all-read') }}" method="POST" class="inline">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
            <i class="fas fa-check-double"></i> Mark all read
        </button>
    </form>
    @endif
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-700 font-semibold">{{ session('success') }}</p>
    </div>
@endif

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    @if($notifications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                <i class="fas fa-bell text-gray-300 text-2xl"></i>
            </div>
            <p class="text-sm font-semibold text-gray-500">No notifications yet</p>
            <p class="text-xs text-gray-400 mt-1">You're all caught up!</p>
        </div>
    @else
        <ul class="divide-y divide-gray-100">
            @foreach($notifications as $notif)
            <li class="flex items-start gap-4 px-5 py-4 {{ !$notif->is_read ? 'bg-red-50/30' : '' }} hover:bg-gray-50 transition-all">
                {{-- Icon --}}
                <div class="flex-shrink-0 mt-0.5">
                    <div class="w-9 h-9 rounded-full {{ !$notif->is_read ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center">
                        <i class="fas fa-bell {{ !$notif->is_read ? 'text-[#ff0808]' : 'text-gray-400' }} text-sm"></i>
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-{{ !$notif->is_read ? 'bold' : 'semibold' }} text-gray-900">
                        {{ $notif->title }}
                    </p>
                    @if($notif->content)
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $notif->content }}</p>
                    @endif
                    <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0 mt-0.5">
                    @if(!$notif->is_read)
                    <form action="{{ route('partner.notifications.mark-read', $notif->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Mark as read"
                                class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all">
                            <i class="fas fa-check text-xs"></i>
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('partner.notifications.destroy', $notif->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" title="Delete"
                                class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </form>
                    @if($notif->hasLink())
                    <a href="{{ $notif->link_url }}"
                       class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-[#ff0808] hover:bg-red-50 rounded-lg transition-all">
                        <i class="fas fa-external-link-alt text-xs"></i>
                    </a>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
