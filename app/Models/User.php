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
        'name',
        'email',
        'password',
        'invite_id'
    ];

    protected $hidden = [
        'password',
    ];

    //Invite ID akan dibuat saat user dibuat
    protected static function booted()
    {
        parent::boot();
        static::creating(function ($user) {
            do {
                $invite = str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
            } while (User::where('invite_id', $invite)->exists());
            $user->invite_id = $invite;
        });
    }

    //Pivot User <-> Board
    public function memberBoards()
    {
        return $this->belongsToMany(Board::class, 'member_boards')
            ->using(MemberBoard::class)
            ->withPivot('role', 'isGuest')
            ->withTimestamps();
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }
}
