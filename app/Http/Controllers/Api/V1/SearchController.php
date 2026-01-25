<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Card;
use App\Models\Project;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q'    => 'required|string|min:2|max:100',
            'type' => 'required|in:projects,boards,cards',
            'board_id' => 'nullable|uuid',
            'list_id'  => 'nullable|uuid',
            'user_id'  => 'nullable|uuid',
            'from'     => 'nullable|date',
            'to'       => 'nullable|date',
        ]);

        return match ($request->type) {
            'projects' => $this->searchProjects($request),
            'boards'   => $this->searchBoards($request),
            'cards'    => $this->searchCards($request),
        };
    }

    protected function searchProjects(Request $request)
    {
        return Project::query()
            ->whereHas('members', fn ($q) =>
                $q->where('user_id', Auth::id())
            )
            ->when($request->q, fn ($q) =>
                $q->where('name', 'like', "%{$request->q}%")
            )
            ->latest()
            ->get();
    }

    protected function searchBoards(Request $request)
    {
        return Board::query()
            ->whereHas('members', fn ($q) =>
                $q->where('user_id', Auth::id())
            )
            ->when($request->q, fn ($q) =>
                $q->where('name', 'like', "%{$request->q}%")
            )
            ->when($request->board_id, fn ($q) =>
                $q->where('id', $request->board_id)
            )
            ->latest()
            ->get();
    }

    protected function searchCards(Request $request)
    {
        return Card::query()
            ->whereHas('list.board.members', fn ($q) =>
                $q->where('user_id', Auth::id())
            )
            ->when($request->q, fn ($q) =>
                $q->where('title', 'like', "%{$request->q}%")
            )
            ->when($request->board_id, fn ($q) =>
                $q->whereHas('list', fn ($l) =>
                    $l->where('board_id', $request->board_id)
                )
            )
            ->when($request->list_id, fn ($q) =>
                $q->where('list_id', $request->list_id)
            )
            ->when($request->from, fn ($q) =>
                $q->whereDate('created_at', '>=', $request->from)
            )
            ->when($request->to, fn ($q) =>
                $q->whereDate('created_at', '<=', $request->to)
            )
            ->latest()
            ->get();
    }
}

