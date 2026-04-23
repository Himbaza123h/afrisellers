@extends('layouts.home')

@section('page-content')
<div class="space-y-4 max-w-6xl">

    {{-- Back Button --}}
    <div>
        <a href="{{ route('notifications.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 font-medium transition-colors">
            <i class="fas fa-arrow-left text-xs"></i>
            Back to Notifications
        </a>
    </div>

    {{-- Notification Card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Card Header --}}
        <div class="flex items-start gap-4 p-6 border-b border-gray-100">
            <div class="flex-shrink-0">
                @php
                    $icon = 'fa-bell';
                    $iconBg = 'bg-gray-100';
                    $iconColor = 'text-gray-600';
                    $title = strtolower($notification->title ?? '');
                    if (str_contains($title, 'order')) { $icon = 'fa-shopping-cart'; $iconBg = 'bg-blue-50'; $iconColor = 'text-blue-600'; }
                    elseif (str_contains($title, 'product')) { $icon = 'fa-box'; $iconBg = 'bg-purple-50'; $iconColor = 'text-purple-600'; }
                    elseif (str_contains($title, 'verified') || str_contains($title, 'approved')) { $icon = 'fa-check-circle'; $iconBg = 'bg-green-50'; $iconColor = 'text-green-600'; }
                    elseif (str_contains($title, 'reject')) { $icon = 'fa-ban'; $iconBg = 'bg-red-50'; $iconColor = 'text-red-600'; }
                    elseif (str_contains($title, 'message')) { $icon = 'fa-envelope'; $iconBg = 'bg-indigo-50'; $iconColor = 'text-indigo-600'; }
                    elseif (str_contains($title, 'payment') || str_contains($title, 'plan') || str_contains($title, 'trial')) { $icon = 'fa-credit-card'; $iconBg = 'bg-yellow-50'; $iconColor = 'text-yellow-600'; }
                    elseif (str_contains($title, 'suspended') || str_contains($title, 'alert')) { $icon = 'fa-exclamation-triangle'; $iconBg = 'bg-orange-50'; $iconColor = 'text-orange-600'; }
                @endphp
                <div class="w-12 h-12 {{ $iconBg }} rounded-xl flex items-center justify-center">
                    <i class="fas {{ $icon }} {{ $iconColor }} text-lg"></i>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <h1 class="text-lg font-bold text-gray-900">{{ $notification->title }}</h1>
                    <span class="flex-shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                        {{ $notification->is_read ? 'bg-gray-100 text-gray-600' : 'bg-red-100 text-red-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $notification->is_read ? 'bg-gray-400' : 'bg-red-500' }}"></span>
                        {{ $notification->is_read ? 'Read' : 'Unread' }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $notification->created_at->format('M d, Y · h:i A') }}
                    ({{ $notification->created_at->diffForHumans() }})
                </p>
            </div>
        </div>

        {{-- Message Body --}}
        <div class="p-6">
            <p class="text-sm text-gray-700 leading-relaxed">{{ $notification->content }}</p>
        </div>

        {{-- Meta Info --}}
        <div class="px-6 pb-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
            @if($notification->user)
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">User</p>
                <p class="text-sm font-semibold text-gray-800">{{ $notification->user->name }}</p>
                <p class="text-xs text-gray-500">{{ $notification->user->email }}</p>
            </div>
            @endif
            @if($notification->vendor)
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Vendor ID</p>
                <p class="text-sm font-semibold text-gray-800">#{{ $notification->vendor->id }}</p>
                <p class="text-xs text-gray-500">{{ $notification->vendor->account_status ?? 'N/A' }}</p>
            </div>
            @endif
            @if($notification->country)
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Country</p>
                <div class="flex items-center gap-1.5">
                    @if($notification->country->flag_url)
                    <img src="{{ $notification->country->flag_url }}" class="w-4 h-3 object-cover rounded-sm" alt="">
                    @endif
                    <p class="text-sm font-semibold text-gray-800">{{ $notification->country->name }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 px-6 py-4 bg-gray-50 border-t border-gray-100">
            @if($notification->link_url)
            <a href="{{ $notification->link_url }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition font-medium text-sm">
                <i class="fas fa-external-link-alt text-xs"></i>
                Go to Link
            </a>
            @endif
            @if(!$notification->is_read)
            <button onclick="markRead({{ $notification->id }})"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                <i class="fas fa-check text-xs"></i>
                Mark as Read
            </button>
            @endif
            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}"
                onsubmit="return confirm('Delete this notification?')" class="ml-auto">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-red-50 hover:border-red-300 hover:text-red-600 transition font-medium text-sm">
                    <i class="fas fa-trash text-xs"></i>
                    Delete
                </button>
            </form>
        </div>
    </div>

</div>

<script>
function markRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) location.reload();
    });
}
</script>
@endsection
