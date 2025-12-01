<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Card extends Model
{
    protected $fillable = [
        'card_title',
        'image',
        'description',
        'dates',
        'position',
        'list_id'
    ];

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }

    public function labels(): HasMany {
        return $this->hasMany(Label::class);
    }

    public function customFields(): BelongsToMany {
        return $this->belongsToMany(Custom_Fields::class)
                    ->using(FieldsCards::class)
                    ->withPivot('value');
    }

    public function fieldsCard(): HasMany {
        return $this->hasMany(FieldsCards::class);
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    //logs
    public function logs(): MorphMany {
        return $this->morphMany(Log::class, 'loggable');
    }
}
