<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * Disable auto-incrementing IDs
     */
    public $incrementing = false;

    /**
     * UUID primary key type
     */
    protected $keyType = 'string';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hidden fields
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Automatically generate UUID on create
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * JWT: return the identifier stored in the token
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * JWT: custom claims (none for now)
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
        public function boards()
        {
            return $this->belongsToMany(Board::class, 'board_members')
                ->withPivot('role')
                ->withTimestamps();
        }

}
   
