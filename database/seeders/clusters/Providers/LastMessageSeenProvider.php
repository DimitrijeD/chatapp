<?php

namespace Database\Seeders\clusters\Providers;

class LastMessageSeenProvider
{
    public $users;
    public $group_id; 
    public $messages;

    public function __construct($users, $group_id, $messages)
    {
        $this->users = $users;
        $this->group_id = $group_id;
        $this->messages = $messages;

        $this->lastMessageSeenUpdateData = [];
    }
}