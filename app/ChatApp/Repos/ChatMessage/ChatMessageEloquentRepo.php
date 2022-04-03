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

    public function getMissingMessages($groupId, $latestMsg)
    {
        return ChatMessage::
              where('chat_group_id', $groupId)
            ->where('id', '>', $latestMsg)
            ->with('user')
            ->get();
    }
}