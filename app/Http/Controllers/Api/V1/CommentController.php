<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Services\AuthorizationService;
use App\Services\ActivityLogger;

class CommentController extends Controller
{
    public function store(Request $request, Card $card)
    {
        if (! AuthorizationService::isBoardMember(
            $card->list->board_id,
            auth()->id()
        )) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'body' => 'required|string',
        ]);

        $comment = Comment::create([
            'card_id' => $card->id,
            'user_id' => auth()->id(),
            'body'    => $data['body'],
        ]);

        ActivityLogger::log(
            'comment.added',
            $card->list->board_id,
            ['card_id' => $card->id, 'comment_id' => $comment->id]
        );

        return response()->json($comment, 201);
    }

            public function destroy(Comment $comment)
        {
            $comment->delete();

            return response()->json([
                'message' => 'Comment deleted'
            ]);
        }


}
