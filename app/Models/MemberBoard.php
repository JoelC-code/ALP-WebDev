<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberBoard extends Model
{
    protected $fillable = [
        'user_id',
        'board_id',
        'role'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function board(): BelongsTo {
        return $this->belongsTo(Board::class);
    }
}
