<?php

namespace App\Services;

use App\Models\Notification as NotificationModel;
use App\Models\BoardMember;
use App\Events\NotificationBroadcasted;

class NotificationService
{
    public static function notifyBoard(
        string $boardId,
        string $type,
        array $data,
        ?string $excludeUserId = null
    ): void {
        $members = BoardMember::where('board_id', $boardId)->get();

        foreach ($members as $member) {
            if ($excludeUserId && $member->user_id === $excludeUserId) {
                continue;
            }

            $notification = NotificationModel::create([
                'user_id'  => $member->user_id,
                'board_id' => $boardId,
                'type'     => $type,
                'data'     => $data,
            ]);

            // REALTIME PUSH
            broadcast(new NotificationBroadcasted(
                $member->user_id,
                [
                    'id'         => $notification->id,
                    'type'       => $type,
                    'board_id'   => $boardId,
                    'data'       => $data,
                    'created_at'=> $notification->created_at,
                ]
            ));
        }
    }

    public static function notifyUser(
        string $userId,
        string $type,
        array $data,
        ?string $boardId = null
    ): void {
        $notification = NotificationModel::create([
            'user_id'  => $userId,
            'board_id' => $boardId,
            'type'     => $type,
            'data'     => $data,
        ]);

        broadcast(new NotificationBroadcasted(
            $userId,
            [
                'id'         => $notification->id,
                'type'       => $type,
                'board_id'   => $boardId,
                'data'       => $data,
                'created_at'=> $notification->created_at,
            ]
        ));
    }
}
