<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request, string $boardId)
    {
        if (! AuthorizationService::isBoardMember($boardId, auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = min(
            $request->get('per_page', 20),
            50
        );

        $logs = ActivityLog::where('board_id', $boardId)
            ->with('user:id,name')
            ->latest()
            ->paginate($perPage);

        return response()->json($logs);
    }
}
