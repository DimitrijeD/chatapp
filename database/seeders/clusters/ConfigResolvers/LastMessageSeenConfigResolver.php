<?php

namespace Database\Seeders\clusters\ConfigResolvers;

use Database\Seeders\ChatGroupClusterSeeder;
use Database\Seeders\clusters\Contracts\Resolve;
use Database\Seeders\clusters\Types\ClusterMaxActivity\LastMessageSeenMaxActivity;
use Database\Seeders\clusters\Types\ClusterDefault\LastMessageSeenDefault;
use Database\Seeders\clusters\Types\ClusterRandom\LastMessageSeenRandom;
use Database\Seeders\clusters\Types\ClusterEven\LastMessageSeenEven;

class LastMessageSeenConfigResolver implements Resolve
{
    public $users, $group_id, $type, $messages;

    public function __construct($users, $group_id, $type, $messages)
    {
        $this->users = $users;
        $this->group_id = $group_id;
        $this->type = $type;
        $this->messages = $messages;
    }

    public function resolve()
    {
        switch($this->type){
            case ChatGroupClusterSeeder::DISTRIBUTION_MAX_ACTIVITY:
                return new LastMessageSeenMaxActivity($this->users, $this->group_id, $this->messages);

            case ChatGroupClusterSeeder::DISTRIBUTION_DEFAULT:
                return new LastMessageSeenDefault($this->users, $this->group_id, $this->messages);

            case ChatGroupClusterSeeder::DISTRIBUTION_RANDOM:
                return new LastMessageSeenRandom($this->users, $this->group_id, $this->messages);

            case ChatGroupClusterSeeder::DISTRIBUTION_EVEN:
                return new LastMessageSeenEven($this->users, $this->group_id, $this->messages);

            default:
                dd("Invalid last message seen type argument:'{$this->type}'!");
        }
    }

}