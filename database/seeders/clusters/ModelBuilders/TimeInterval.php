<?php

namespace Database\Seeders\clusters\ModelBuilders;

use Carbon\Carbon;

class TimeInterval
{
    public $minTime, $maxTime;

    public function __construct($minTime, $maxTime, $defaultTimeInterval)
    {
        $this->minTime = $minTime; 
        $this->maxTime = $maxTime; 
        $this->defaultTimeInterval = $defaultTimeInterval;
    }

    /**
     * All messages must have 'created_at' and 'updated_at' in [min, max] time interval
     * 
     * In case user defined time interval is not valid, it will use default time interval
     * 
     */
    public function createTimeInterval()
    {   
        $this->timeInterval = [];

        if($this->defaultTimeInterval){
            return $this->createDefaultTime();;
        }

        if($this->minTime && $this->maxTime){
            $timeInterval['maxTime'] = Carbon::create($this->maxTime['year'], $this->maxTime['month'], $this->maxTime['day'], $this->maxTime['hour']);    
            $timeInterval['minTime'] = Carbon::create($this->minTime['year'], $this->minTime['month'], $this->minTime['day'], $this->minTime['hour']);
        }  

        if( (!$this->minTime && !$this->maxTime) || !$this->validateTimeInterval($timeInterval) ){
            $defaultInterval = $this->createDefaultTime();
            $timeInterval['minTime'] = $defaultInterval['minTime'];
            $timeInterval['maxTime'] = $defaultInterval['maxTime'];    
        }
        
        return $timeInterval;
    }

    /**
     * $minTime must be older then $maxTime.
     */
    private function validateTimeInterval($timeInterval)
    {
        if(!$timeInterval){
            return false;
        }

        // if min time is greater or equal to max time, false
        if( $timeInterval['minTime']->gte($timeInterval['maxTime']) ){
            return false;
        }

        return true;
    }

    /**
     * 30 day difference between min and max time interval
     */
    private function createDefaultTime()
    {
        return [
            'minTime' => Carbon::now()->subDays(30),
            'maxTime' => Carbon::now(),
        ];
    }
}