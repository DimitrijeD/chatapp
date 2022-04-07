<?php

namespace Database\Seeders\clusters\Types\ClusterDefault;

use Database\Seeders\clusters\Contracts\Cluster;
use Database\Seeders\clusters\Providers\MessageProvider;

class MessagesDefault extends MessageProvider implements Cluster
{
    /**
     * For 'u' users and 'nm' number of messages, create small number of messages 'snm' for each user, while iterating over same array of users.
     * Method ends when 'nm' is exhausted. If 'nm' is exhausted before some users were assigned to message, those users,
     * will not have any messages in chat group
     */
    public function build()
    {
        $this->createClusterOrder();
        $this->setUsersInCluster();
        return $this->cluster;
    }

    private function createClusterOrder()
    {
        $numMsgRemaining = $this->numMessages;

        while($numMsgRemaining > 0){
            $clusterSize = $this->getClusterSize();
            
            if($numMsgRemaining - $clusterSize <= 0){
                $clusterSize = $numMsgRemaining;
                $numMsgRemaining = 0;
            }

            $this->cluster[] = [
                'user' => null,
                'clusterSize' => $clusterSize,
            ];
            $numMsgRemaining -= $clusterSize;
        }

        // $this->testIfNumberMsgsToCreateIsExhausted();
    }

    /**
     * if $this->users collection is: [
     *      0 => userObj0,
     *      1 => userObj1,
     *      2 => userObj2,
     * ]
     * 
     * this method will treat collection as circular array. If there are 7 elements in $this->cluster, it will iterate for user indexes like:
     * 0, 1, 2, 0, 1, 2, 0 and add to lineary iterated $this->cluster, user objects from these indexes. Not sure if this explanation helps.
     * 
     */
    private function setUsersInCluster()
    {
        for($clusterIndex = 0; $clusterIndex < count($this->cluster); $clusterIndex++){
            $userIndex = $clusterIndex % count($this->users);
            $this->cluster[$clusterIndex]['user'] = $this->users[$userIndex];
        }
    }

    private function createMessage($group_id, $user_id)
    {
        $now = now();

        $chatMessages = ChatMessage::factory()->create([
            'group_id'   => $group_id,
            'user_id'    => $user_id,
            'updated_at' => $now,
            'created_at' => $now,
        ]);
    }

    private function testIfNumberMsgsToCreateIsExhausted()
    {
        $sum = 0;

        foreach($this->cluster as $clusterMixed){
            $sum += $clusterMixed['clusterSize'];
        }

        if($sum == $this->numMessages){
            dd('it works as expected');
        } else {
            dd("error - number of messages assigned to users is: {$sum}, but it should be {$this->numMessages}");
        }
    }
    
    private function getClusterSize()
    {
        return rand(1,4);
    }
}