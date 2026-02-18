<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\BoardList;
use App\Services\AuthorizationService;
use App\Services\ProjectAuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;

class ListController extends Controller
{
    public function store(Request $request, $boardId)
    {
        $board = Board::findOrFail($boardId);

        ProjectAuthorizationService::check($board->project, 'editor');

        if (! AuthorizationService::hasBoardRole(
            $boardId,
            Auth::id(),
            ['owner', 'admin', 'member']
        )) {
            return response()->json(['message' => 'Insufficient permissions'], 403);
        }

        $data = $request->validate([
            'name'     => 'required|string',
            'position' => 'required|integer',
        ]);

        $list = BoardList::create([
            'board_id' => $board->id,
            ...$data,
        ]);

        ActivityLogger::log(
            'list.created',
            $boardId,
            ['list_id' => $list->id, 'name' => $list->name]
        );

        return response()->json($list, 201);
    }

    public function destroy(Boardlist $list) // use your actual model name
{
    $list->delete();

    return response()->json([
        'message' => 'List deleted'
    ]);
}

}
