<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
                             ->latest()
                             ->paginate(20);

        $unreadCount = Notification::where('user_id', auth()->id())
                           ->unread()
                           ->count();

        return view('partner.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(int $id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();
        return back();
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
                    ->unread()
                    ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(int $id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }
}
