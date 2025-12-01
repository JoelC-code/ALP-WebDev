<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Custom_Fields extends Model
{
    protected $fillable = [
        'title',
        'type',
        'board_id'
    ];

    public function Cards(): HasMany {
        return $this->hasMany(Card::class);
    }

    public function Card_Templates(): HasMany {
        return $this->hasMany(Card_Template::class);
    }

    public function Board(): HasMany {
        return $this->hasMany(Board::class);
    }

    //logs
    public function Logs(): MorphMany {
        return $this->morphMany(Log::class, 'loggable');
    }
}
