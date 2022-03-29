<?php

namespace Database\Seeders\clusters\ConfigResolvers;

use Database\Seeders\ChatGroupClusterSeeder;
use Database\Seeders\clusters\Contracts\Resolve;
use Database\Seeders\clusters\Types\ClusterMaxActivity\MessagesMaxActivity;
use Database\Seeders\clusters\Types\ClusterDefault\MessagesDefault;
use Database\Seeders\clusters\Types\ClusterRandom\MessagesRandom;
use Database\Seeders\clusters\Types\ClusterEven\MessagesEven;

class MessageConfigResolver implements Resolve
{
    public $users, $group_id, $type, $numMessages;

    public function __construct($users, $group_id, $type, $numMessages)
    {
        $this->users = $users;
        $this->group_id = $group_id;
        $this->type = $type;
        $this->numMessages = $numMessages;
    }

    public function resolve()
    {
        switch($this->type){
            case ChatGroupClusterSeeder::DISTRIBUTION_MAX_ACTIVITY:
                return new MessagesMaxActivity($this->users, $this->group_id, $this->numMessages);

            case ChatGroupClusterSeeder::DISTRIBUTION_DEFAULT:
                return new MessagesDefault($this->users, $this->group_id, $this->numMessages);

            case ChatGroupClusterSeeder::DISTRIBUTION_RANDOM:
                return new MessagesRandom($this->users, $this->group_id, $this->numMessages);

            case ChatGroupClusterSeeder::DISTRIBUTION_EVEN:
                return new MessagesEven($this->users, $this->group_id, $this->numMessages);

            default:
                dd("Invalid type argument:'{$this->type}'!");
        }
    }

}