<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_group_id',
        'user_id',
        'text',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(ChatGroup::class);
    }

    public function seenState()
    {
        return $this->belongsToMany(User::class, 'group_participants', 'last_message_seen_id')
            ->withPivot(['user_id']);
    }

}
