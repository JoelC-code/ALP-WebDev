<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_name'
    ];

    //Pivot Board <-> Users
    public function members()
    {
        return $this->belongsToMany(User::class, 'member_boards')
            ->using(MemberBoard::class)
            ->withPivot('role', 'isGuest')
            ->withTimestamps();
    }

    public function lists()
    {
        return $this->hasMany(ListCard::class);
    }

    public function labels(): HasMany
    {
        return $this->hasMany(Label::class);
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }

    public function cardTemplates(): HasMany
    {
        return $this->hasMany(CardTemplate::class);
    }

    //Get all cards in this board
    public function cards()
    {
        return Card::whereHas('list', function ($query) {
            $query->where('board_id', $this->id);
        });
    }

    // Get cards with due dates
    public function cardsWithDueDates()
    {
        return $this->cards()
            ->whereNotNull('dates')
            ->orderBy('dates', 'asc');
    }

    // Get overdue cards
    public function overdueCards()
    {
        return $this->cardsWithDueDates()
            ->where('dates', '<', now())
            ->get();
    }

    // Get upcoming cards (next 7 days)
    public function upcomingCards()
    {
        return $this->cardsWithDueDates()
            ->where('dates', '>=', now())
            ->where('dates', '<=', now()->addDays(7))
            ->get();
    }

    //logs
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }
}
