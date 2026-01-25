<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification as NotificationModel;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $perPage = min($request->get('per_page', 20), 100);

        return NotificationModel::where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    public function unreadCount()
    {
        $user = auth()->user();

        return response()->json([
            'count' => NotificationModel::where('user_id', $user->id)
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    public function markRead(string $id)
    {
        $notification = NotificationModel::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $notification) {
            return response()->json([
                'message' => 'Notification not found for this user'
            ], 404);
        }

        $notification->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Notification marked as read',
            'data' => $notification
        ]);
    }
}
