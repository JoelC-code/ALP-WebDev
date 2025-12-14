<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CustomField extends Model
{
    protected $fillable = [
        'title',
        'type',
        'board_id'
    ];

    //Pivot Cards <-> Custom Fields
    public function Cards(): BelongsToMany {
        return $this->belongsToMany(Card::class, 'field_cards')
                    ->using(FieldCard::class)
                    ->withPivot('value');
    }

    public function fieldCards(): HasMany {
        return $this->hasMany(FieldCard::class);
    }

    //Pivot CardTemplate <-> Custom Fields
    public function Card_Templates(): BelongsToMany {
        return $this->BelongsToMany(CardTemplate::class)
                    ->using(FieldTemplate::class)
                    ->withPivot('value');
    }

    public function fieldTemplate(): HasMany {
        return $this->hasMany(FieldTemplate::class);
    }

    public function board(): BelongsTo {
        return $this->belongsTo(Board::class);
    }

    //logs
    public function logs(): MorphMany {
        return $this->morphMany(Log::class, 'loggable');
    }
}
