<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['user_id', 'message', 'is_user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
