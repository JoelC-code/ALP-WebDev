<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldsCards extends Model
{
    protected $fillable = [
        'custom_fields_id',
        'card_id',
        'value'
    ];

    public function customFields(): BelongsTo {
        return $this->belongsTo(Custom_Fields::class);
    }

    public function cards(): BelongsTo  {
        return $this->belongsTo(Card::class);
    }
}
