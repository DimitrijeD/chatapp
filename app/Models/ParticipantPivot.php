<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantPivot extends Model
{
    use HasFactory;

    const CREATOR = 'CREATOR';
    const MODERATOR = 'MODERATOR';
    const PARTICIPANT = 'PARTICIPANT';
    const LISTENER = 'LISTENER';

    const ROLES = [
        self::CREATOR, 
        self::MODERATOR,
        self::PARTICIPANT,
        self::LISTENER,
    ];

    protected $fillable = [
        'user_id', 'group_id', 'last_message_seen_id', 'participant_role', 'updated_at'
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $table = 'group_participants';

}
