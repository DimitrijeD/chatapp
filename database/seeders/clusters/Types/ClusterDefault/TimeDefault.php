<?php

namespace Database\Seeders\clusters\Types\ClusterDefault;

use Database\Seeders\clusters\Contracts\Cluster;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TimeDefault implements Cluster
{
    public $clusteredMessages, $timeInterval, $numMessages, $msgTimeSeparator;

    public function __construct($clusteredMessages, $timeInterval, $numMessages)
    {
        $this->clusteredMessages = $clusteredMessages;
        $this->timeInterval = $timeInterval;
        $this->numMessages = $numMessages;
        
        $this->timeClusteredMessages = [];
        $this->timeLine = [];

        $this->setEqualTimeSeparation();
        $this->faker = Faker::create();
    }

    /**
     * Returns reversed order or array timeClusteredMessages, populated by this method.
     */
    public function build()
    {
        $this->timeLine = $this->buildTimeline();
        foreach($this->timeLine as $time){
            $this->timeClusteredMessages[] = $this->makeTimeProps($time);
        }

        // dd($this->testAmountExhaust());
        return $this->timeClusteredMessages;
    }

    private function makeTimeProps($time)
    {
        return [
            'updated_at' => $time,
            'created_at' => $time,
        ];
    }

    private function buildTimeline()
    {
        $timelineOfBlocks = [];
        $blockSetter = $this->timeInterval['minTime'];

        for($i = 0; $i < count($this->clusteredMessages); $i++){
            $timelineOfBlocks[$i] = $blockSetter; // ->toDateTimeString() 
            $blockSetter = Carbon::parse( $blockSetter->addSeconds($this->blockSeparator)->toDateTimeString() );
        }


        return $this->normalizeBlocks($timelineOfBlocks);
    }

    private function normalizeBlocks($blocks)
    {
        for($i = 0; $i < count($blocks); $i++){
            if($i + 1 < count($blocks)){
                $min = $blocks[$i];
                $max = $blocks[$i + 1];
                $blocks[$i] = $this->moveTimeInRange( $min, $this->getLimitTimeOfBlock($min, $max) ); 
            } else {
                // last one must be in range with accual max
                $min = $blocks[$i];
                $blocks[$i] = $this->moveTimeInRange( $min, $this->getLimitTimeOfBlock($min, $this->timeInterval['maxTime']) ); 
            }
        }
        // $this->testIsNumOfBlocksEqualToNumOfClusters($blocks, $this->clusteredMessages);

        return $this->extractBlocksIntoMessages($blocks);
    }

    private function extractBlocksIntoMessages($blocks)
    {
        $msg_times = [];

        for($i = 0; $i < count($blocks); $i++){
            $blockSetter = Carbon::parse( $blocks[$i]->toDateTimeString() );
            // dd($blockSetter );
            for($j = 0; $j < $this->clusteredMessages[$i]['clusterSize']; $j++){
                $msg_times[] = $blockSetter->addSeconds($this->getTimeWithinCluster());
            }
        }
        return $msg_times;
    }

    private function moveTimeInRange($min, $max)
    {
        return $min->addSeconds($this->getRandSecondsAdditionWithinBlocks($min, $max));
    }

    private function getRandSecondsAdditionWithinBlocks($min, $max)
    {
        return rand(1, $max->diffInSeconds($min));
    }



    // (maxTime-minTime) / numberOfBlocks
    private function setEqualTimeSeparation()
    {
        $this->blockSeparator = $this->timeInterval['maxTime']->diffInSeconds($this->timeInterval['minTime']) / count($this->clusteredMessages);
    }

    /**
     * Reduces max for 5%  
     * create temporary max value for block time to prevent message block overflow
     */   
    private function getLimitTimeOfBlock($min, $max)
    {
        $timeToReduce = (int) ($max->diffInSeconds($min) * 0.05); 

        return $max->subSeconds($timeToReduce);
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

    private function testAmountExhaust(){
        if(count($this->timeClusteredMessages) == $this->numMessages){
            return 'works as expected';
        } 
        return 'Doesnt work as expected, target number of messages is: ' . $this->numMessages . ', while number of dates is: ' . count($this->timeClusteredMessages) . '. They must be equal';
    }

    private function testnumMessages()
    {
        $sum = 0;

        foreach($this->clusteredMessages as $couster){
            $sum += $couster['clusterSize'];

        }
        if($sum == $this->numMessages){
            dd('works as expected');
        } else {
            dd('doesnt work:' . 'number of messages to create is: {$this->numMessages}, while sum in clusters is {$sum}');
        }
    }

    private function testIsNumOfBlocksEqualToNumOfClusters($blocks, $clusters){
        $b = count($blocks);
        $c = count($clusters);
        if( $b == $c ){
            dd('works');
        } else {
            dd(' doesnt work. Number of blocks is {$b}, while number of clusters is {$c}');
        }
    }


}