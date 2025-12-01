<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Log extends Model
{
    protected $fillable = [
        'details',
        'dates',
    ];

    public function loggable(): MorphTo {
        return $this->morphTo(__FUNCTION__, 'loggable_name', 'loggable_id');
    }
}
