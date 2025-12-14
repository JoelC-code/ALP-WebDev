<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'card_id',
        'user_id',
        'comment_content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'edited_at' => 'datetime',
    ];

    public function card(): BelongsTo {
        return $this->belongsTo(Card::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}