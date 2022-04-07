<?php

namespace Database\Seeders\clusters\Types\ClusterRandom;

use Database\Seeders\clusters\Contracts\Cluster;
use Database\Seeders\clusters\Providers\LastMessageSeenProvider;

class LastMessageSeenRandom extends LastMessageSeenProvider implements Cluster
{
    public function build()
    {

        foreach($this->users as $user){
            $randMsg = $this->getRandomMessage();

            $this->lastMessageSeenUpdateData[] = [
                'user_id' => $user->id,
                'group_id' => $this->group_id,
                'last_message_seen_id' => $randMsg->id,
                'updated_at' => $this->getTimeOfLastSeen($randMsg),
            ];
        }

        return $this->lastMessageSeenUpdateData;
    }

    private function getRandomMessage()
    {
        return $this->messages[ rand(0, count($this->messages) - 1) ];
    }

    /**
     * @TODO this way of setting 'seen' time can produce strange behaviour 
     * because added time can, in some cases, be greater then time next message is created.
     * 
     */
    private function getTimeOfLastSeen($message)
    {
        return $message->created_at->addSeconds(rand(5, 15));
    }
}
