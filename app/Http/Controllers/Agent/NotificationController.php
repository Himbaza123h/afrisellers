<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Notification::forUser(auth()->id())
            ->when($request->filter === 'unread', fn($q) => $q->unread())
            ->when($request->filter === 'read',   fn($q) => $q->read())
            ->latest();

        $notifications = $query->paginate(20)->withQueryString();

        $stats = [
            'total'  => Notification::forUser(auth()->id())->count(),
            'unread' => Notification::forUser(auth()->id())->unread()->count(),
            'read'   => Notification::forUser(auth()->id())->read()->count(),
        ];

        return view('agent.notifications.index', compact('notifications', 'stats'));
    }

    // ─── MARK ONE AS READ ─────────────────────────────────────────────
    public function markAsRead($id)
    {
        $notification = Notification::forUser(auth()->id())->findOrFail($id);
        $notification->markAsRead();

        if ($notification->hasLink()) {
            return redirect($notification->link_url);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    // ─── MARK ALL AS READ ─────────────────────────────────────────────
    public function markAllAsRead()
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    // ─── DESTROY ──────────────────────────────────────────────────────
    public function destroy($id)
    {
        $notification = Notification::forUser(auth()->id())->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }
}
