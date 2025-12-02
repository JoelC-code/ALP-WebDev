<?php

//CUSTOM PIVOT TABLE

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldTemplates extends Model
{

    protected $table = 'field_templates';
    protected $fillable = [
        'custom_field_id',
        'card_id',
        'value'
    ];

    public function customFields(): BelongsTo {
        return $this->belongsTo(Custom_Fields::class);
    }

    public function cardTemplate(): BelongsTo  {
        return $this->belongsTo(Card_Template::class);
    }
}
