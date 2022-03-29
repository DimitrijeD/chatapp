<?php

namespace Database\Seeders\clusters\Types\ClusterMaxActivity;

use Database\Seeders\clusters\Contracts\Cluster;

class TimeMaxActivity implements Cluster
{
    public $clusteredMessages, $timeInterval, $numMessages, $msgTimeSeparator;

    public function __construct($clusteredMessages, $timeInterval, $numMessages)
    {
        $this->clusteredMessages = $clusteredMessages;
        $this->timeInterval = $timeInterval;
        $this->numMessages = $numMessages;
        
        $this->timeClusteredMessages = [];
    }

    /**
     * Returns reversed order or array timeClusteredMessages, populated by this method.
     */
    public function build()
    {
        $dateSetter = $this->timeInterval['maxTime'];
        $reversedTCM = array_reverse($this->clusteredMessages);
        
        foreach($reversedTCM as $cluster){
            for($i = 0; $i < $cluster['clusterSize']; $i++){   
                $this->timeClusteredMessages[] = $this->makeTimeProps($dateSetter->toDateTimeString());
                $dateSetter = $dateSetter->subSeconds($this->getTimeWithinCluster());
            }
            $dateSetter = $dateSetter->subSeconds($this->getTimeOutsideCluster());
        }
        
        return array_reverse($this->timeClusteredMessages);
    }

    private function makeTimeProps($time)
    {
        return [
            'updated_at' => $time,
            'created_at' => $time,
        ];
    }

    private function getTimeWithinCluster()
    {
        return rand(2, 14);
    }

    private function getTimeOutsideCluster()
    {
        return rand(2, 14);
    }

    /**
     * Sets $msgTimeSeparator in seconds
     *  
     */
    private function setEqualTimeBetweenTwoMessages()
    {
        $diffInSeconds = $this->timeInterval['maxTime']->diffInSeconds($this->timeInterval['minTime']);
        $this->msgTimeSeparator = $diffInSeconds / $this->numMessages;
        // dd($this->msgTimeSeparator, $this->numMessages, $this->timeInterval['minTime']->toDateTimeString(), $this->timeInterval['maxTime']->toDateTimeString());

    }
}