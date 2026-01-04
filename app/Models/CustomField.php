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

    // Default options for select type
    public static function getDefaultOptions(): array
    {
        return [
            ['value' => 'low', 'label' => 'Low'],
            ['value' => 'medium', 'label' => 'Medium'],
            ['value' => 'high', 'label' => 'High'],
        ];
    }

    // Get options with defaults merged
    public function getSelectOptions(): array
    {
        if ($this->type !== 'select') {
            return [];
        }

        return $this->options ?? self::getDefaultOptions();
    }

    // Pivot Cards <-> Custom Fields
    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(Card::class, 'field_cards')
                    ->using(FieldCard::class)
                    ->withPivot('value');
    }

    public function fieldCards(): HasMany
    {
        return $this->hasMany(FieldCard::class);
    }

    // Pivot CardTemplate <-> Custom Fields
    public function cardTemplates(): BelongsToMany
    {
        return $this->belongsToMany(CardTemplate::class, 'field_templates')
                    ->using(FieldTemplate::class)
                    ->withPivot('value');
    }

    public function fieldTemplate(): HasMany
    {
        return $this->hasMany(FieldTemplate::class);
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    // Logs
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }

    // Boot method to handle type changes
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($field) {
            // If type is changing, delete all field values
            if ($field->isDirty('type')) {
                $field->fieldCards()->delete();
                
                // If changing to select type, set default options
                if ($field->type === 'select' && empty($field->options)) {
                    $field->options = self::getDefaultOptions();
                }
            }
        });
    }
}