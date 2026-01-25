<?php

namespace App\Services;

use App\Models\Board;
use App\Models\BoardMember;
use App\Models\Project;
use App\Models\ProjectMember;

class AuthorizationService
{
    /**
     * User must be a member of the project
     */
    public static function ownsProject(string $projectId, string $userId): bool
    {
        return ProjectMember::where('project_id', $projectId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Check if user belongs to board
     */
    public static function isBoardMember(string $boardId, string $userId): bool
    {
        return \App\Models\BoardMember::where('board_id', $boardId)
            ->where('user_id', $userId)
            ->exists();
    }
    
    public static function hasBoardRole(string $boardId, string $userId, array $roles): bool
    {
        return BoardMember::where('board_id', $boardId)
            ->where('user_id', $userId)
            ->whereIn('role', $roles)
            ->exists();
    }

    public static function boardBelongsToProject(string $boardId, string $projectId): bool
    {
        return Board::where('id', $boardId)
            ->where('project_id', $projectId)
            ->exists();
    }
}
