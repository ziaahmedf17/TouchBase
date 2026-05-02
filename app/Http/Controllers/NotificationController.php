<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private function tenantQuery(): Builder
    {
        $q = Notification::query();
        $tenantId = auth()->user()->tenantId();
        if ($tenantId) {
            $q->whereHas('client', fn($c) => $c->where('tenant_id', $tenantId));
        }
        return $q;
    }

    public function index()
    {
        $this->authorize('notifications.view');

        $notifications = $this->tenantQuery()
            ->with('client', 'event', 'interactions')
            ->latest()
            ->paginate(30);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        $this->authorize('notifications.manage');
        $this->authorizeTenant($notification);
        $notification->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        $this->authorize('notifications.manage');
        $this->tenantQuery()->where('is_read', false)->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function unreadCount()
    {
        $count = $this->tenantQuery()->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('notifications.manage');
        $this->authorizeTenant($notification);
        $notification->delete();

        return response()->json(['ok' => true]);
    }

    private function authorizeTenant(Notification $notification): void
    {
        $tenantId = auth()->user()->tenantId();
        if ($tenantId && $notification->client?->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
