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

    public function memberBoards(): HasMany {
        return $this->hasMany(MemberBoard::class);
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class)
                    ->using(MemberBoard::class)
                    ->withPivot('role');
    }

    public function labels(): HasMany {
        return $this->hasMany(Label::class);
    }

    public function customFields(): HasMany {
        return $this->hasMany(Custom_Fields::class);
    }

    public function cardTemplates(): HasMany {
        return $this->hasMany(Card_Template::class);
    }

    //logs
    public function logs(): MorphMany {
        return $this->morphMany(Log::class, 'loggable');
    }
}
