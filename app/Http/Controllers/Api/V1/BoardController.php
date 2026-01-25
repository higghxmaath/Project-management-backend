<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Board;
use App\Models\BoardMember;
use App\Services\ProjectAuthorizationService;
use App\Services\AuthorizationService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    public function store(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        ProjectAuthorizationService::check($project, 'editor');

        if (! AuthorizationService::ownsProject($projectId, Auth::id())) {
            return response()->json(['message' => 'Unauthorized project access'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string',
        ]);

        $board = Board::create([
            'project_id' => $project->id,
            'name'       => $data['name'],
        ]);

        BoardMember::create([
            'board_id' => $board->id,
            'user_id'  => Auth::id(),
            'role'     => 'owner',
        ]);

        ActivityLogger::log(
            'board.created',
            $board->id,
            ['name' => $board->name]
        );

        return response()->json($board, 201);
    }

    public function show(string $boardId)
    {
        if (! AuthorizationService::isBoardMember($boardId, auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $board = Board::with([
            'lists' => function ($q) {
                $q->orderBy('position');
            },
            'lists.cards' => function ($q) {
                $q->orderBy('position');
            },
            'members.user:id,name,email',
        ])->findOrFail($boardId);

        return response()->json($board);
    }

    public function activity(Board $board)
    {
        if (! AuthorizationService::isBoardMember($board->id, Auth::id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $board->activityLogs()
            ->latest()
            ->limit(20)
            ->get();
    }
}
