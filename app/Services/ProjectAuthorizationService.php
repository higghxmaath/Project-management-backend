<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Auth\Access\AuthorizationException;

class ProjectAuthorizationService
{
    public static function check(Project $project, string $requiredRole)
    {
        $user = auth()->user();

        $member = ProjectMember::where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $member) {
            throw new AuthorizationException('Not a project member');
        }

        $roles = ['viewer' => 1, 'editor' => 2, 'owner' => 3];

        if ($roles[$member->role] < $roles[$requiredRole]) {
            throw new AuthorizationException('Insufficient permissions');
        }
    }
}
