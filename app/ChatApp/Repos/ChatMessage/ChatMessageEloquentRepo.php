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
}