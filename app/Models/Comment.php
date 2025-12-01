<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'comment_content',
        'date'
    ];

    public function cards(): HasMany {
        return $this->hasMany(Card::class);
    }

    public function users(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
