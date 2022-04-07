<?php

namespace Database\Seeders\clusters\Types\ClusterEven;

use Database\Seeders\clusters\Contracts\Cluster;
use Database\Seeders\clusters\Providers\LastMessageSeenProvider;

class LastMessageSeenEven extends LastMessageSeenProvider implements Cluster
{
    public function build()
    {
        $lastIndex = count($this->messages) - 1;

        for($i = 0; $i < count($this->users); $i++){
            $arrangement = [
                'user' => $this->users[$i]->id,
                'message' => $this->messages[ $lastIndex ? $lastIndex - $i % $lastIndex : 0 ],
            ];

            $this->lastMessageSeenUpdateData[] = [
                'user_id' => $this->users[$i]->id,
                'group_id' => $this->group_id,
                'last_message_seen_id' => $arrangement['message']->id,
                'updated_at' => $this->getTimeOfLastSeen($arrangement['message']),
            ];
        }

        return $this->lastMessageSeenUpdateData;
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
