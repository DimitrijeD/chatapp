<?php

namespace Database\Seeders\clusters\Types\ClusterRandom;

use Database\Seeders\clusters\Contracts\Cluster;
use Faker\Factory as Faker;

class TimeRandom implements Cluster
{
    public $clusteredMessages, $timeInterval, $numMessages, $msgTimeSeparator;

    public function __construct($clusteredMessages, $timeInterval, $numMessages)
    {
        $this->clusteredMessages = $clusteredMessages;
        $this->timeInterval = $timeInterval;
        $this->numMessages = $numMessages;
        
        $this->timeClusteredMessages = [];
        $this->faker = Faker::create();
        $this->timeLine = $this->buildTimeline();
    }

    public function build()
    {
        foreach($this->timeLine as $timeLine){   
            $this->timeClusteredMessages[] = $this->makeTimeProps($timeLine);
        }

        return $this->timeClusteredMessages;
    }

    private function buildTimeline()
    {
        $disorderedTimeline = [];

        foreach($this->clusteredMessages as $cluster){
            for($i = 0; $i < $cluster['clusterSize']; $i++){   
                $disorderedTimeline[] = $this->faker->dateTimeBetween(
                    $startDate = $this->timeInterval['minTime'], 
                    $endDate = $this->timeInterval['maxTime']
                )->format('Y-m-d H:i:s');
            }
        }

        return $this->orderTimeline($disorderedTimeline);
    }

    private function orderTimeline($timeLine)
    {
        usort($timeLine, function ($a, $b) {
            return strtotime($a) - strtotime($b);
        });

        return $timeLine;
    }

    private function makeTimeProps($time)
    {
        return [
            'updated_at' => $time,
            'created_at' => $time,
        ];
    }
}