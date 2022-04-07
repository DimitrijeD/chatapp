<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;
    
    const MODEL_TYPE_PRIVATE = 'PRIVATE';     // nobody else can be added
    const MODEL_TYPE_PROTECTED = 'PROTECTED'; // many users, only group_admins can invite users
    const MODEL_TYPE_PUBLIC = 'PUBLIC';       // everybody can join

    const CHATTING_TYPE_PROTECTED = 'PROTECTED'; // many users, only group_admins can send msges
    const CHATTING_TYPE_PUBLIC = 'PUBLIC';       // everybody can send msges

    const MODEL_TYPES = [
        self::MODEL_TYPE_PRIVATE,
        self::MODEL_TYPE_PROTECTED,
        self::MODEL_TYPE_PUBLIC,
    ];

    const CHATTING_TYPES = [
        self::CHATTING_TYPE_PROTECTED,
        self::CHATTING_TYPE_PUBLIC,
    ];

    const MODEL_TYPE_DEFAULT = 'PUBLIC';
    const CHATTING_TYPE_DEFAULT = 'PUBLIC';

    protected $fillable = [
        'name', 'model_type', 'chatting_type'
    ];

    protected $table = 'chat_groups';

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'group_id', 'id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'group_participants', 'group_id', 'user_id')
            ->withPivot(['last_message_seen_id', 'user_id']);
    }

}
