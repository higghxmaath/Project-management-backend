<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Board extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['project_id', 'name'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($m) => $m->id = (string) Str::uuid());
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function lists()
    {
        return $this->hasMany(BoardList::class)->orderBy('position');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')
            ->where('subject_type', self::class);
    }    

     public function members()
    {
        return $this->hasMany(BoardMember::class);
    }

}
   
