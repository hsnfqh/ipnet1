<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->route('notifications.index');
    }

    public function latest()
    {
        $userId = auth()->id();
        $notifications = Notification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        $notifications->each(function($notif) {
            $notif->time_ago = $notif->created_at->diffForHumans();
        });

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount
        ]);
    }

    public function ajaxMarkAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Hapus satu notifikasi milik user yang sedang login.
     */
    public function destroy(Notification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Notifikasi dihapus.');
    }

    /**
     * Hapus semua notifikasi milik user yang sedang login.
     */
    public function destroyAll()
    {
        Notification::where('user_id', auth()->id())->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Semua notifikasi dihapus.');
    }
}