<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return response([
            'data' => $notifications,
            'status' => true
        ], Response::HTTP_OK);
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response([
            'data' => ['count' => $count],
            'status' => true,
        ], Response::HTTP_OK);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response([
                'message' => 'Yetkisiz erişim.',
                'status' => false
            ], Response::HTTP_FORBIDDEN);
        }

        $notification->update(['is_read' => true]);

        return response([
            'data' => $notification,
            'status' => true
        ], Response::HTTP_OK);
    }
}
