<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Dom\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'invite_id'
    ];

    protected $hidden = [
        'password',
    ];

    public function memberBoards(): HasMany {
        return $this->hasMany(MemberBoard::class);
    }

    public function boards(): BelongsToMany {
        return $this->belongsToMany(Board::class)
                    ->using(MemberBoard::class)
                    ->withPivot('role');
    }

    public function cards(): HasMany {
        return $this->hasMany(Card::class);
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }
}
