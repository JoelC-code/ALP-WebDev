<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FieldCard extends Pivot
{
    protected $table = 'field_cards'; 
    
    public $incrementing = true;
    
    protected $fillable = [
        'custom_field_id',
        'card_id',
        'value'
    ];

    public function customFields(): BelongsTo {
        return $this->belongsTo(CustomField::class);
    }

    public function cards(): BelongsTo {
        return $this->belongsTo(Card::class);
    }
}