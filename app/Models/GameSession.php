<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'winning_number',
        'is_active'
    ];
}
