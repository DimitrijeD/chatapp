<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    const INIT_NUM_MESSAGES = 10;
    const EARLIEST_NUM_MESSAGES = 20;

    protected $fillable = [
        'group_id', 'user_id', 'text', 'updated_at'
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id', 'id');
    }

}
