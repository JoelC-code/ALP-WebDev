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
        'board_id',
        'options'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public static function getDefaultOptions(): array
    {
        return [
            ['value' => 'low', 'label' => 'Low'],
            ['value' => 'medium', 'label' => 'Medium'],
            ['value' => 'high', 'label' => 'High'],
        ];
    }

    public function getSelectOptions(): array
    {
        if ($this->type !== 'select') {
            return [];
        }

        return $this->options ?? self::getDefaultOptions();
    }

    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(Card::class, 'field_cards')
            ->using(FieldCard::class)
            ->withPivot('value');
    }

    public function fieldCards(): HasMany
    {
        return $this->hasMany(FieldCard::class, 'custom_field_id');
    }

    public function cardTemplates(): BelongsToMany
    {
        return $this->belongsToMany(CardTemplate::class, 'field_templates')
            ->using(FieldTemplate::class)
            ->withPivot('value');
    }

    // FIX: Specify the correct table name
    public function fieldTemplates(): HasMany
    {
        return $this->hasMany(FieldTemplate::class, 'custom_field_id');
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($field) {
            if ($field->isDirty('type')) {
                // These will now use the correct table names
                $field->fieldCards()->delete();
                $field->fieldTemplates()->delete();

                if ($field->type === 'select' && empty($field->options)) {
                    $field->options = self::getDefaultOptions();
                }
            }
        });

        static::deleting(function ($field) {
            // These will now use the correct table names
            $field->fieldCards()->delete();
            $field->fieldTemplates()->delete();
        });
    }
}
