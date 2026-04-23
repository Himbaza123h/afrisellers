@php
    use App\Models\Notification;
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $notifications = collect();
    $unreadCount = 0;

    if ($user) {
        if ($user->hasRole('admin')) {
            $notifications = Notification::with(['vendor', 'user', 'country'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        } elseif ($user->country_admin && $user->country_id) {
            $notifications = Notification::with(['vendor', 'user', 'country'])
                ->where(function ($q) use ($user) {
                    $q->where('country_id', $user->country_id)
                      ->orWhereNull('country_id');
                })
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        } elseif ($user->isVendor()) {
            $notifications = Notification::with(['vendor', 'user', 'country'])
                ->where(function ($q) use ($user) {
                    $q->where('vendor_id', $user->id)
                      ->orWhere('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        } else {
            $notifications = Notification::with(['vendor', 'user', 'country'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        $unreadCount = $notifications->where('is_read', false)->count();
    }

    function getNotificationStyle($title) {
        $titleLower = strtolower($title);
        if (str_contains($titleLower, 'order'))                                    return ['icon' => 'fa-shopping-cart',      'color' => 'blue'];
        elseif (str_contains($titleLower, 'review') || str_contains($titleLower, 'rating')) return ['icon' => 'fa-star',           'color' => 'green'];
        elseif (str_contains($titleLower, 'payment'))                              return ['icon' => 'fa-money-bill-wave',     'color' => 'purple'];
        elseif (str_contains($titleLower, 'stock') || str_contains($titleLower, 'alert'))   return ['icon' => 'fa-exclamation-triangle', 'color' => 'orange'];
        elseif (str_contains($titleLower, 'approved') || str_contains($titleLower, 'verified')) return ['icon' => 'fa-check-circle',  'color' => 'teal'];
        elseif (str_contains($titleLower, 'ship') || str_contains($titleLower, 'delivery'))  return ['icon' => 'fa-truck',          'color' => 'indigo'];
        elseif (str_contains($titleLower, 'refund') || str_contains($titleLower, 'return'))  return ['icon' => 'fa-undo',           'color' => 'red'];
        elseif (str_contains($titleLower, 'follow') || str_contains($titleLower, 'subscriber')) return ['icon' => 'fa-user-plus',   'color' => 'pink'];
        else                                                                        return ['icon' => 'fa-bell',               'color' => 'gray'];
    }
@endphp

<div class="relative">
    <button id="notifications-btn" class="relative p-1.5 sm:p-2 hover:bg-gray-100 rounded-lg transition-colors"
        title="Notifications">
        <i class="far fa-bell text-base sm:text-lg lg:text-xl text-gray-600"></i>
        @if($unreadCount > 0)
            <span id="notification-badge"
                class="absolute top-0 right-0 bg-[#ff0808] text-white text-[9px] sm:text-xs w-4 h-4 sm:w-5 sm:h-5 rounded-full flex items-center justify-center font-bold">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notifications Dropdown -->
    <div id="notifications-dropdown"
        class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-[32rem] overflow-hidden">

        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
            @if($unreadCount > 0)
                <button onclick="markAllAsRead()" class="text-xs text-[#ff0808] hover:text-red-700 font-medium">
                    Mark all as read
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div id="notifications-list" class="overflow-y-auto max-h-96">
            @forelse($notifications as $notification)
                @php
                    $style   = getNotificationStyle($notification->title);
                    $isUnread = !$notification->is_read;
                @endphp

                <a href="{{ $notification->link_url ?? '#' }}"
                   onclick="markAsRead({{ $notification->id }})"
                   data-notification-id="{{ $notification->id }}"
                   data-unread="{{ $isUnread ? 'true' : 'false' }}"
class="notification-item flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-all border-b border-gray-100
                          {{ $isUnread ? 'bg-red-50' : 'bg-white' }}">

                    <!-- Icon -->
                    <div class="flex-shrink-0 w-10 h-10 bg-{{ $style['color'] }}-500 rounded-full flex items-center justify-center">
                        <i class="fas {{ $style['icon'] }} text-white text-sm"></i>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium {{ $isUnread ? 'text-gray-900' : 'text-gray-500' }}">
                            {{ $notification->title }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $notification->content }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>

                        @if($user->hasRole('admin') || $user->country_admin)
                            <div class="flex gap-2 mt-1">
                                @if($notification->country)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                        {{ $notification->country->name }}
                                    </span>
                                @endif
                                @if($notification->vendor)
                                    <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded">
                                        {{ $notification->vendor->name }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Unread dot -->
                    @if($isUnread)
                        <span class="unread-dot flex-shrink-0 w-2 h-2 bg-[#ff0808] rounded-full mt-2"></span>
                    @endif
                </a>
            @empty
                <div class="px-4 py-12 text-center">
                    <i class="far fa-bell-slash text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500 font-medium">No notifications yet</p>
                    <p class="text-xs text-gray-400 mt-1">We'll notify you when something new arrives</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if($notifications->count() > 0)
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <a href="{{ route('notifications.index') }}" class="text-xs text-[#ff0808] hover:text-red-700 font-medium text-center block">
                    View All Notifications
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notificationsBtn      = document.getElementById('notifications-btn');
        const notificationsDropdown = document.getElementById('notifications-dropdown');

        if (notificationsBtn && notificationsDropdown) {
            notificationsBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                notificationsDropdown.classList.toggle('hidden');
                const alertsDropdown = document.getElementById('alerts-dropdown');
                if (alertsDropdown) alertsDropdown.classList.add('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!notificationsBtn.contains(e.target) && !notificationsDropdown.contains(e.target)) {
                    notificationsDropdown.classList.add('hidden');
                }
            });
        }
    });

    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (item) {
                    // Swap unread styles → read styles
item.classList.remove('bg-red-50');
item.classList.add('bg-white');
                    item.setAttribute('data-unread', 'false');

                    // Bold title → muted
                    const title = item.querySelector('p.font-medium');
                    if (title) {
                        title.classList.remove('text-gray-900');
                        title.classList.add('text-gray-500');
                    }

                    // Remove red dot
                    const dot = item.querySelector('.unread-dot');
                    if (dot) dot.remove();
                }
                updateBadgeCount();
            }
        })
        .catch(err => console.error('Error:', err));
    }

    function markAllAsRead() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-item').forEach(item => {
item.classList.remove('bg-red-50');
item.classList.add('bg-white');
                    item.setAttribute('data-unread', 'false');

                    const title = item.querySelector('p.font-medium');
                    if (title) {
                        title.classList.remove('text-gray-900');
                        title.classList.add('text-gray-500');
                    }

                    const dot = item.querySelector('.unread-dot');
                    if (dot) dot.remove();
                });

                const badge = document.getElementById('notification-badge');
                if (badge) badge.remove();
            }
        })
        .catch(err => console.error('Error:', err));
    }

    function updateBadgeCount() {
        fetch('/notifications/unread-count')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('notification-badge');
                if (data.count > 0) {
                    if (badge) badge.textContent = data.count > 99 ? '99+' : data.count;
                } else {
                    if (badge) badge.remove();
                }
            })
            .catch(err => console.error('Error:', err));
    }

    setInterval(updateBadgeCount, 30000);
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
