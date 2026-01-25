<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BoardMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;

class BoardMemberController extends Controller
{
    public function store(Request $request, string $boardId)
{
    $data = $request->validate([
        'user_id' => 'required|uuid|exists:users,id',
        'role'    => 'required|in:admin,member,viewer',
    ]);

    $isOwner = BoardMember::where('board_id', $boardId)
        ->where('user_id', Auth::id())
        ->where('role', 'owner')
        ->exists();

    if (! $isOwner) {
        return response()->json(['message' => 'Only board owner can add members'], 403);
    }

    // 🔒 PREVENT DUPLICATE
    $already = BoardMember::where('board_id', $boardId)
        ->where('user_id', $data['user_id'])
        ->exists();

    if ($already) {
        return response()->json([
            'message' => 'User is already a member of this board'
        ], 409);
    }

    $member = BoardMember::create([
        'board_id' => $boardId,
        'user_id'  => $data['user_id'],
        'role'     => $data['role'],
    ]);

    ActivityLogger::log(
        'board.member_added',
        $boardId,
        ['user_id' => $data['user_id'], 'role' => $data['role']]
    );

    return response()->json($member, 201);
}

    public function destroy(string $boardId, string $userId)
    {
        BoardMember::where('board_id', $boardId)
            ->where('user_id', $userId)
            ->delete();

        ActivityLogger::log(
            'board.member_removed',
            $boardId,
            ['user_id' => $userId]
        );

        return response()->json(['message' => 'Member removed']);
    }
}
