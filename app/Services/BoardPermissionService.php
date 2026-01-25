<?php

namespace App\Services;

use App\Models\BoardMember;

class BoardPermissionService
{
    public static function hasRole(string $boardId, string $userId, array $roles): bool
    {
        return BoardMember::where('board_id', $boardId)
            ->where('user_id', $userId)
            ->whereIn('role', $roles)
            ->exists();
    }
}
