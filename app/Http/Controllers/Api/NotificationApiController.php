<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = (int) $request->input('per_page', 15);

        $notifications = PushNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
            'unread_count' => PushNotification::where('user_id', $user->id)->unread()->count(),
        ]);
    }

    public function markAsRead($id, Request $request)
    {
        $user = $request->user();

        $notification = PushNotification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'Notifikasi ditandai sebagai sudah dibaca.',
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        PushNotification::where('user_id', $user->id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Semua notifikasi ditandai sebagai sudah dibaca.',
        ]);
    }
}
