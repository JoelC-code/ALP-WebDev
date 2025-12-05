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

    protected static function boot() {
        parent::boot();
        static::creating(function ($user) {
            do {
                $invite = str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
            } while (User::where('invite_id', $invite)->exists());
            $user->invite_id = $invite;
        });
    }

    //Pivot User <-> Board
    public function memberBoards(): HasMany {
        return $this->hasMany(MemberBoard::class);
    }

    public function boards(): BelongsToMany {
        return $this->belongsToMany(Board::class, 'member_board')
                    ->using(MemberBoard::class)
                    ->withPivot('role', 'isGuest');
    }

    public function cards(): HasMany {
        return $this->hasMany(Card::class);
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    public function logs(): HasMany {
        return $this->hasMany(Log::class);
    }
}
