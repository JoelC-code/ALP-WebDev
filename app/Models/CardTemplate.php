<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CardTemplate extends Model
{
    protected $fillable = [
        'card_title',
        'image',
        'description',
        'dates',
        'board_id',
    ];
    protected $casts = [
        'dates' => 'datetime',
    ];

    // CustomFields <-> Card Template
    public function customFields(): BelongsToMany
    {
        return $this->belongsToMany(CustomField::class, 'field_templates', 'card_template_id', 'custom_field_id')
            ->withPivot('value');
    }

    public function fieldTemplates(): HasMany
    {
        return $this->hasMany(FieldTemplate::class);
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'label_templates', 'card_template_id', 'label_id')
            ->withTimestamps();
    }

    public function board(): BelongsTo
    {
        return $this->BelongsTo(Board::class);
    }

    //logs
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
