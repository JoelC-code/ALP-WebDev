<?php

//CUSTOM PIVOT TABLE

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MemberBoard extends Pivot
{
    protected $table = 'member_boards';

    protected $fillable = [
        'user_id',
        'board_id',
        'role',
        'isGuest'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function board(): BelongsTo {
        return $this->belongsTo(Board::class);
    }
}
