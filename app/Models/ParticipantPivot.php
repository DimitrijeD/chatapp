<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantPivot extends Model
{
    use HasFactory;

    const ROLE_CREATOR = 'CREATOR';         // Can do anything in protected and public groups
    const ROLE_MODERATOR = 'MODERATOR';     // can do anything except delete group
    const ROLE_PARTICIPANT = 'PARTICIPANT'; // can send messages and reactions to messages
    const ROLE_LISTENER = 'LISTENER';       // can only see and react to messages (likes emojies stuff like that)

    const ROLES = [
        self::ROLE_CREATOR, 
        self::ROLE_MODERATOR,
        self::ROLE_PARTICIPANT,
        self::ROLE_LISTENER,
    ];

    protected $fillable = [
        'user_id', 'group_id', 'last_message_seen_id', 'participant_role'
    ];

    protected $table = 'group_participants';


}
