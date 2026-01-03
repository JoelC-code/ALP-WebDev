<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Carbon\Carbon;

class Card extends Model
{
    protected $fillable = [
        'card_title',
        'image',
        'description',
        'dates',
        'position',
        'list_id',
        'is_completed',
    ];
    protected $casts = [
        'dates' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function isOverdue()
    {
        if (!$this->dates) return false;
        return Carbon::parse($this->dates)->isPast();
    }

    public function isDueSoon()
    {
        if (!$this->dates) return false;

        $dueDate = Carbon::parse($this->dates);

        if ($dueDate->isPast()) return false;

        $daysUntilDue = now()->diffInDays($dueDate, false);

        return $daysUntilDue >= 0 && $daysUntilDue <= 7;
    }

    //Pivot Card <-> Custom Fields
    public function customFields(): BelongsToMany
    {
        return $this->belongsToMany(CustomField::class, 'field_cards')
            ->using(FieldCard::class)
            ->withPivot('value');
    }

    public function list(): BelongsTo
    {
        return $this->belongsTo(ListCard::class, 'list_id');
    }

    public function fieldsCard(): HasMany
    {
        return $this->hasMany(FieldCard::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    //logs
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
