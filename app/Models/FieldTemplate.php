<?php

//CUSTOM PIVOT TABLE

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldTemplate extends Model
{
    protected $fillable = [
        'custom_field_id',
        'card_id',
        'value'
    ];

    public function customFields(): BelongsTo {
        return $this->belongsTo(CustomField::class);
    }

    public function cardTemplate(): BelongsTo  {
        return $this->belongsTo(CardTemplate::class);
    }
}
