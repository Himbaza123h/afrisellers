<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'all');
        $search = $request->get('search', '');

        $query = $this->buildQuery($user)->with(['vendor', 'user', 'country']);

        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $notifications = $query->paginate(15)->withQueryString();

        $allForStats = $this->buildQuery($user)->get();
        $stats = [
            'total'   => $allForStats->count(),
            'unread'  => $allForStats->where('is_read', false)->count(),
            'read'    => $allForStats->where('is_read', true)->count(),
            'today'   => $allForStats->where('created_at', '>=', now()->startOfDay())->count(),
        ];

        return view('notifications.index', compact('notifications', 'stats', 'filter', 'search'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $notification = Notification::with(['vendor', 'user', 'country'])->findOrFail($id);

        // Mark as read when viewed
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $this->buildQuery($user)->where('is_read', false)
            ->get()
            ->each(fn($n) => $n->markAsRead());

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $user = Auth::user();
        $count = $this->buildQuery($user)->where('is_read', false)->count();

        return response()->json(['count' => $count]);
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();

        return back()->with('success', 'Notification deleted successfully.');
    }

    public function destroyAll()
    {
        $user = Auth::user();
        $this->buildQuery($user)->get()->each(fn($n) => $n->delete());

        return back()->with('success', 'All notifications cleared.');
    }

    public function allNotifications()
    {
        return $this->index(request());
    }

    private function buildQuery($user)
    {
        $query = Notification::orderBy('created_at', 'desc');

        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->country_admin && $user->country_id) {
            return $query->where(function ($q) use ($user) {
                $q->where('country_id', $user->country_id)
                  ->orWhereNull('country_id');
            });
        }

        if ($user->isVendor()) {
            return $query->where(function ($q) use ($user) {
                $q->where('vendor_id', $user->id)
                  ->orWhere('user_id', $user->id);
            });
        }

        return $query->where('user_id', $user->id);
    }
}
