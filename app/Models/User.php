<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Dom\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function Boards(): HasMany {
        return $this->hasMany(Board::class);
    }

    public function Cards(): HasMany {
        return $this->hasMany(Card::class);
    }

    public function Comments(): HasMany {
        return $this->hasMany(Comment::class);
    }
}
