<?php

namespace Database\Seeders\clusters\Types\ClusterDefault;

use Database\Seeders\clusters\Contracts\Cluster;
use Database\Seeders\clusters\Providers\LastMessageSeenProvider;

class LastMessageSeenDefault extends LastMessageSeenProvider implements Cluster
{
    public function build()
    {
        foreach($this->users as $user){
            $message = $this->messages[ $this->chooseLastMessageSeen() ];

            $this->lastMessageSeenUpdateData[] = [
                'user_id' => $user->id,
                'group_id' => $this->group_id,
                'last_message_seen_id' => $message->id,
                'updated_at' => $this->getTimeOfLastSeen($message)
            ];
        }

        return $this->lastMessageSeenUpdateData;
    }

    /**
     * Choose one of top 10 percentile range of messages for one user seen last.
     */
    private function chooseLastMessageSeen()
    {
        $lastIndex = count($this->messages) - 1;
        $minIndex = (int) ($lastIndex - round($lastIndex * 10/100));

        return rand($minIndex, $lastIndex);
    }

    /**
     * For 'max activity' , time of 'last message seen' is set to few seconds after 'last message' is 'created'. 
     * 
     * @TODO this way of setting 'seen' time can produce strange behaviour 
     * because added time can, in some cases, be greater then time next message is created.
     * 
     */
    private function getTimeOfLastSeen($message)
    {
        return $message->created_at->addSeconds(rand(5, 15));
    }
}
