<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ListCard extends Model
{
    protected $fillable = [
        'list_name',
        'position',
        'board_id',
    ];

    public function board(): BelongsTo {
        return $this->belongsTo(Board::class);
    }

    public function cards(): HasMany {
        return $this->hasMany(Card::class, 'list_id');
    }

    //logs
    public function logs(): MorphMany {
        return $this->morphMany(Log::class, 'loggable');
    }
}
