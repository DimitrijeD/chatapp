<?php

namespace Database\Seeders\clusters\ConfigResolvers;

use Database\Seeders\ChatGroupClusterSeeder;
use Database\Seeders\clusters\Contracts\Resolve;
use Database\Seeders\clusters\Types\ClusterMaxActivity\TimeMaxActivity;
use Database\Seeders\clusters\Types\ClusterDefault\TimeDefault;
use Database\Seeders\clusters\Types\ClusterRandom\TimeRandom;
use Database\Seeders\clusters\Types\ClusterEven\TimeEven;

class TimeConfigResolver implements Resolve 
{
    public $clusteredMessages, $timeInterval, $type, $numMessages;

    public function __construct($clusteredMessages, $timeInterval, $type, $numMessages)
    {
        $this->clusteredMessages = $clusteredMessages;
        $this->timeInterval = $timeInterval;
        $this->type = $type;
        $this->numMessages = $numMessages;
    }

    public function resolve()
    {
        switch($this->type){
            case ChatGroupClusterSeeder::DISTRIBUTION_MAX_ACTIVITY:
                return (new TimeMaxActivity($this->clusteredMessages, $this->timeInterval, $this->numMessages));

            case ChatGroupClusterSeeder::DISTRIBUTION_DEFAULT:
                return (new TimeDefault($this->clusteredMessages, $this->timeInterval, $this->numMessages));

            case ChatGroupClusterSeeder::DISTRIBUTION_RANDOM:
                return (new TimeRandom($this->clusteredMessages, $this->timeInterval, $this->numMessages));

            case ChatGroupClusterSeeder::DISTRIBUTION_EVEN:
                return (new TimeEven($this->clusteredMessages, $this->timeInterval, $this->numMessages));
       
            default:
                dd("Invalid time type argument:'{$this->type}'!");
        }
    }

}