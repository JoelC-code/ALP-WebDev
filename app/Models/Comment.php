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

    public function Cards(): HasMany {
        return $this->hasMany(Card::class);
    }

    public function Users(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
