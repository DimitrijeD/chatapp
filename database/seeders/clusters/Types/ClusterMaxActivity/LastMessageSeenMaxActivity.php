<?php

namespace Database\Seeders\clusters\Types\ClusterMaxActivity;

use Database\Seeders\clusters\Contracts\Cluster;
use Database\Seeders\clusters\Providers\LastMessageSeenProvider;

class LastMessageSeenMaxActivity extends LastMessageSeenProvider implements Cluster
{
    public function build()
    {
        $this->lastMessage = $this->getLastMessage();

        foreach($this->users as $user){
            $this->lastMessageSeenUpdateData[] = $this->createPivotUpdateData(
                $user->id, 
                $this->lastMessage->id, 
                $this->getTimeOfLastSeen($this->lastMessage)
            );
        }

        return $this->lastMessageSeenUpdateData;
    }

    private function getLastMessage()
    {
        $temp_id = 0;
        $lastMessage = null;

        foreach($this->messages as $message){
            if($message->id > $temp_id){
                $id = $message->id; 
                $temp_id = $message->id;
                $lastMessage = $message;
            }
        }

        return $lastMessage;
    }

    private function createPivotUpdateData($user_id, $lastMessageID, $updated_at)
    {
        return [
            'user_id' => $user_id,
            'group_id' => $this->group_id,
            'last_message_seen_id' => $lastMessageID,
            'updated_at' => $updated_at,
        ];
    }

    /**
     * For 'max activity' , time of 'last message seen' is set to few seconds after 'last message' is 'created'. 
     */
    private function getTimeOfLastSeen($message)
    {
        return $message->created_at->addSeconds(rand(5, 15));
    }
}
