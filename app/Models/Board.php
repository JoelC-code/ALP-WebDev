<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_name'
    ];

    public function Users(): HasMany {
        return $this->hasMany(User::class);
    }

    public function Labels(): HasMany {
        return $this->hasMany(Label::class);
    }

    public function CustomFields(): HasMany {
        return $this->hasMany(Custom_Fields::class);
    }

    public function CardTemplates(): HasMany {
        return $this->hasMany(Card_Template::class);
    }

    //logs
    public function Logs(): MorphMany {
        return $this->morphMany(Log::class, 'loggable');
    }
}
