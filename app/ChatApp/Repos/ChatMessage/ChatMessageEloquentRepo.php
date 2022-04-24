<?php

namespace App\ChatApp\Repos\ChatMessage;

use App\ChatApp\Repos\ChatMessage\Contracts\ChatMessageRepo;
use App\ChatApp\General\Traits\CRUDTrait;
use App\Models\ChatMessage;

class ChatMessageEloquentRepo implements ChatMessageRepo
{
    use CRUDTrait;

    public function getModel()
    {
        return ChatMessage::class;
    }

    public function getMissingMessages($group_id, $latest_msg_id)
    {
        return ChatMessage::
              where('group_id', $group_id)
            ->where('id', '>', $latest_msg_id)
            ->with('user')
            ->get();
    }

    public function getLatestMessages($group_id)
    {
        return ChatMessage::
              where('group_id', $group_id)
            ->orderBy('id', 'desc')
            ->with('user')
            ->take(ChatMessage::INIT_NUM_MESSAGES)
            ->get();
    }
}