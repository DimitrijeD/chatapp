<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;
    
    const MODEL_TYPE_PRIVATE = 'PRIVATE';             // nobody else can be added
    const MODEL_TYPE_PROTECTED = 'PROTECTED';         // many users, only group_admins can invite users
    const MODEL_TYPE_PUBLIC_OPEN = 'PUBLIC_OPEN';     // everybody can join and submit messages
    const MODEL_TYPE_PUBLIC_CLOSED = 'PUBLIC_CLOSED'; // everybody can join but only creator and moderator can send messages while others can only listen

    const MODEL_TYPES = [
        self::MODEL_TYPE_PRIVATE,
        self::MODEL_TYPE_PROTECTED,
        self::MODEL_TYPE_PUBLIC_OPEN,
        self::MODEL_TYPE_PUBLIC_CLOSED,
    ];

    const MODEL_TYPE_DEFAULT = 'PUBLIC_OPEN';

    protected $fillable = [
        'name', 'model_type'
    ];

    protected $table = 'chat_groups';

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'group_id', 'id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'group_participants', 'group_id', 'user_id')
            ->withPivot(['last_message_seen_id', 'user_id', 'participant_role']);
    }

}
