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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function gameSession()
    {
        return $this->belongsTo(GameSession::class);
    }
}
