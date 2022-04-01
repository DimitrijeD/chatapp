<?php

namespace App\ChatApp\Repos\ChatGroup;

use App\ChatApp\Repos\ChatGroup\Contracts\ChatGroupRepo;
use App\ChatApp\General\Traits\CRUDTrait;
use App\Models\ChatGroup;

class ChatGroupEloquentRepo implements ChatGroupRepo
{
    use CRUDTrait;

    public function getModel()
    {
        return ChatGroup::class;
    }

}