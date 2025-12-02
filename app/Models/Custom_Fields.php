<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Custom_Fields extends Model
{
    protected $fillable = [
        'title',
        'type',
        'board_id'
    ];

    //Pivot Cards <-> Custom Fields
    public function Cards(): BelongsToMany {
        return $this->belongsToMany(Card::class, 'field_card')
                    ->using(FieldsCards::class)
                    ->withPivot('value');
    }

    public function fieldCards(): HasMany {
        return $this->hasMany(FieldsCards::class);
    }

    //Pivot CardTemplate <-> Custom Fields
    public function Card_Templates(): BelongsToMany {
        return $this->BelongsToMany(Card_Template::class, 'field_templates')
                    ->using(FieldTemplates::class)
                    ->withPivot('value');
    }

    public function fieldTemplate(): HasMany {
        return $this->hasMany(FieldTemplates::class);
    }

    public function board(): HasMany {
        return $this->hasMany(Board::class);
    }

    //logs
    public function logs(): MorphMany {
        return $this->morphMany(Log::class, 'loggable');
    }
}
