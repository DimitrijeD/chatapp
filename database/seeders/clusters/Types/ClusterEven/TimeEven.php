<?php

namespace Database\Seeders\clusters\Types\ClusterEven;

use Database\Seeders\clusters\Contracts\Cluster;

class TimeEven implements Cluster
{
    public $clusteredMessages, $timeInterval, $numMessages, $msgTimeSeparator;

    public function __construct($clusteredMessages, $timeInterval, $numMessages)
    {
        $this->clusteredMessages = $clusteredMessages;
        $this->timeInterval = $timeInterval;
        $this->numMessages = $numMessages;
        
        $this->timeClusteredMessages = [];
        $this->setEqualTimeBetweenTwoMessages();
    }

    public function build()
    {
        $dateSetter = $this->timeInterval['minTime'];

        foreach($this->clusteredMessages as $cluster){
            for($i = 0; $i < $cluster['clusterSize']; $i++){   
                // first set date, then add time separation between messages        
                $this->timeClusteredMessages[] = [
                    'updated_at' => $dateSetter->toDateTimeString(),
                    'created_at' => $dateSetter->toDateTimeString(),
                ];
                
                $dateSetter = $dateSetter->addSeconds($this->msgTimeSeparator);
            }
        }
        return $this->timeClusteredMessages;
    }

    /**
     * Sets $msgTimeSeparator in seconds
     * 
     * Since this is even cluster, for time interval [mix, max], all messages are distributed evenly for this time interval.
     * Fist message's time is 'min', last message's time is approximately 'max', while all those in between have equal time separation. 
     */
    private function setEqualTimeBetweenTwoMessages()
    {
        $diffInSeconds = $this->timeInterval['maxTime']->diffInSeconds($this->timeInterval['minTime']);
        $this->msgTimeSeparator = $diffInSeconds / $this->numMessages;
        // dd($this->msgTimeSeparator, $this->numMessages, $this->timeInterval['minTime']->toDateTimeString(), $this->timeInterval['maxTime']->toDateTimeString());

    }
}