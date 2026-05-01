<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $this->authorize('notifications.view');
        $notifications = Notification::with('client', 'event', 'interactions')
            ->latest()
            ->paginate(30);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        $this->authorize('notifications.manage');
        $notification->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        $this->authorize('notifications.manage');
        Notification::where('is_read', false)->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /** Returns unread count — polled by the bell icon every 60 s */
    public function unreadCount()
    {
        return response()->json([
            'count' => Notification::where('is_read', false)->count(),
        ]);
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('notifications.manage');
        $notification->delete();

        return response()->json(['ok' => true]);
    }
}
