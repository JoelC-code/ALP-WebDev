<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Log extends Model
{
    protected $fillable = [
        'details',
        'dates',
        'board_id',
        'user_id',
    ];

    public function loggable(): MorphTo {
        return $this->morphTo(__FUNCTION__, 'loggable_name', 'loggable_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function board(): BelongsTo {
        return $this->belongsTo(Board::class);
    }
}
