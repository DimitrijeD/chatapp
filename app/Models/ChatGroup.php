<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;
    
    const TYPE_PRIVATE = 'PRIVATE';             // nobody else can be added, DM-s
    const TYPE_PROTECTED = 'PROTECTED';         // invitation only, few users, everybody can chat
    const TYPE_PUBLIC_OPEN = 'PUBLIC_OPEN';     // everybody can join and submit messages
    const TYPE_PUBLIC_CLOSED = 'PUBLIC_CLOSED'; // invitation only, only creator and moderator can send messages while others can only listen

    const TYPES = [
        self::TYPE_PRIVATE,
        self::TYPE_PROTECTED,
        self::TYPE_PUBLIC_OPEN,
        self::TYPE_PUBLIC_CLOSED,
    ];

    const TYPE_DEFAULT = 'PUBLIC_OPEN';

    protected $fillable = [
        'name', 'model_type', 'updated_at'
    ];

    protected $table = 'chat_groups';
    
    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'group_id', 'id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, ParticipantPivot::class, 'group_id', 'user_id')
            ->withPivot(['last_message_seen_id', 'user_id', 'participant_role', 'updated_at']);
    }

    public function pivots()
    {
        return $this->hasMany(ParticipantPivot::class, 'group_id', 'id');
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'group_id', 'id')->latestOfMany();
    }

    public function latestMessages()
    {
        return $this->hasMany(ChatMessage::class, 'group_id', 'id')->limit(ChatMessage::INIT_NUM_MESSAGES);
    }
}
