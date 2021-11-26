<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessageSeen extends Model
{
    use HasFactory;

    protected $table = 'last_seen_messages';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function message()
    {
        return $this->belongsTo(ChatMessage::class);
    }

    public function group()
    {
        return $this->belongsTo(ChatGroup::class);
    }

}
