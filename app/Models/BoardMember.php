<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BoardMember extends Model
{
    protected $table = 'board_members';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'board_id',
        'user_id',
        'role',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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

    // ✅ PREVENT DUPLICATE
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

}
