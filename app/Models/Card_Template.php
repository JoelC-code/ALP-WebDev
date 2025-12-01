<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Card_Template extends Model
{
    protected $fillable = [
        'card_title',
        'image',
        'description',
        'dates',
    ];

    // CustomFields <-> Card Template
    public function customFields(): BelongsToMany {
        return $this->belongsToMany(Custom_Fields::class);
    }

    public function fieldTemplate(): HasMany {
        return $this->hasMany(FieldTemplates::class);
    }

    public function labels(): HasMany {
        return $this->hasMany(Label::class);
    }

    public function board(): HasMany {
        return $this->hasMany(Board::class);
    }

    //logs
    public function logs(): MorphMany {
        return $this->morphMany(Log::class, 'loggable');
    }
}
