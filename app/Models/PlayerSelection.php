<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerSelection extends Model
{
    protected $fillable = [
        'user_id',
        'game_session_id',
        'selected_number',
    ];
}
