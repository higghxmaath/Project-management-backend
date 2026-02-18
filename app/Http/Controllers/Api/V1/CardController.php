<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BoardList;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\AuthorizationService;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;

class CardController extends Controller
{
    public function store(Request $request, string $listId)
    {
        $list = BoardList::findOrFail($listId);

        if (! AuthorizationService::hasBoardRole(
            $list->board_id,
            Auth::id(),
            ['owner', 'admin', 'member']
        )) {
            return response()->json(['message' => 'Cannot add cards'], 403);
        }

        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'position' => 'required|integer|min:1',
        ]);

        $card = DB::transaction(function () use ($list, $data) {
            Card::where('list_id', $list->id)
                ->where('position', '>=', $data['position'])
                ->increment('position');

            return Card::create([
                'id'       => (string) Str::uuid(),
                'title'    => $data['title'],
                'list_id'  => $list->id,
                'position' => $data['position'],
            ]);
        });

        ActivityLogger::log(
            'card.created',
            $list->board_id,
            ['card_id' => $card->id, 'title' => $card->title]
        );

        return response()->json($card, 201);
    }

    public function move(Request $request, Card $card)
    {
        if (! AuthorizationService::hasBoardRole(
            $card->list->board_id,
            Auth::id(),
            ['owner', 'admin', 'member']
        )) {
            return response()->json(['message' => 'Cannot move cards'], 403);
        }

        $data = $request->validate([
            'list_id'  => 'required|uuid|exists:lists,id',
            'position' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($card, $data) {
            Card::where('list_id', $data['list_id'])
                ->where('position', '>=', $data['position'])
                ->increment('position');

            $card->update([
                'list_id'  => $data['list_id'],
                'position' => $data['position'],
            ]);
        });

        ActivityLogger::log(
            'card.moved',
            $card->list->board_id,
            [
                'card_id'  => $card->id,
                'to_list'  => $data['list_id'],
                'position' => $data['position'],
            ]
        );

        return response()->json($card->fresh());
    }

    public function destroy(Card $card)
{
    $card->delete();

    return response()->json([
        'message' => 'Card deleted'
    ]);
}

}
