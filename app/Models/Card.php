<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Card extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'list_id',
        'title',
        'description',
        'position',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($m) => $m->id = (string) Str::uuid());
    }

    public function list()
    {
        return $this->belongsTo(BoardList::class, 'list_id');
    }
}
