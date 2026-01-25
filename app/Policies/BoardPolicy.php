<?php class BoardPolicy
{
    public function view(User $user, Board $board)
    {
        return $board->members()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Board $board)
    {
        return $board->members()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'admin'])
            ->exists();
    }
}
