<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Label extends Model
{
    protected $fillable = [
        'title',
        'color',
        'board_id'
    ];

    public function cards(): BelongsToMany {
        return $this->belongsToMany(Card::class);
    }

    public function cardTemplates(): HasMany {
        return $this->hasMany(CardTemplate::class);
    }

    public function board(): BelongsTo {
        return $this->belongsTo(Board::class);
    }

    //logs
    public function logs(): MorphMany {
        return $this->morphMany(Log::class, 'loggable');
    }
}
