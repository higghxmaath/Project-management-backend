<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, ?string $boardId = null, array $meta = []): void
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id'       => $user?->id,
            'board_id'      => $boardId,
            'action'        => $action,
            'subject_type'  => null,
            'subject_id'    => null,
            'meta'          => $meta,
        ]);

        // Send notifications only AFTER logging
        self::dispatchNotification($action, $boardId, $meta);
    }

    protected static function dispatchNotification(
        string $action,
        ?string $boardId,
        array $meta
    ): void {
        if (! $boardId) {
            return;
        }

        if (! class_exists(\App\Services\NotificationService::class)) {
            return;
        }

        match ($action) {
            'board.member_added',
            'card.created',
            'card.moved',
            'comment.added'
                => \App\Services\NotificationService::notifyBoard(
                    $boardId,
                    $action,
                    $meta,
                    Auth::id()
                ),

            default => null,
        };
    }
}
