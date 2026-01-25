<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BoardList extends Model
{
    protected $table = 'lists';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'board_id',
        'name',
        'position',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($m) => $m->id = (string) Str::uuid());
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'list_id')
            ->orderBy('position');
    }
}
