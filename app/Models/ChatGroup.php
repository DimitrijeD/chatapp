<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $table = 'chat_groups';

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'group_participants')
            ->withPivot(['last_message_seen_id']);;
    }

}