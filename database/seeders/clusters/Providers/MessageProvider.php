<?php

namespace Database\Seeders\clusters\Providers;

class MessageProvider
{
    public $users;
    public $group_id; 
    public $numMessages;
    public $cluster;

    public function __construct($users, $group_id, $numMessages)
    {
        $this->users = $users;
        $this->group_id = $group_id;
        $this->numMessages = $numMessages;
        
        $this->cluster = [];
    }
}