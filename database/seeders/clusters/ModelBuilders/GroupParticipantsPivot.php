<?php

namespace Database\Seeders\clusters\ModelBuilders;

use Illuminate\Support\Facades\DB;
use Database\Seeders\clusters\Contracts\Cluster;

class GroupParticipantsPivot implements Cluster
{
    public function __construct($users, $group_id)
    {
        $this->users = $users; 
        $this->group_id = $group_id;
    }
    /**
     * Each user has one 'seen state' in pivot table 'group_participants'
     * 
     * @param array $users
     */
    public function build()
    {
        $now = now();
        
        foreach($this->users as $user){
            DB::table('group_participants')->insert([
                'user_id'              => $user->id,
                'group_id'             => $this->group_id,
                'last_message_seen_id' => null,
                'updated_at'           => $now, 
                'created_at'           => $now, // @TODO make date when was group created.
            ]);
        }
    }

    /**
     * Add to 'group_participants' pivot table ID of last message user has seen.
     * 
     * 'updated_at' column is used to store time when has user seen 'that' message in chat group
     * 
     * @param array $pivotRecords - must have: [
     *      [
     *          'user_id' => int $user_id,
     *          'last_message_seen_id' => int $message_id,
     *      ],
     *      ...
     * ]
     * 
     */
    public function addLastMessageSeenId(array $pivotRecords)
    {
        foreach($pivotRecords as $pivotRecord){
            DB::table('group_participants')->where([
                'user_id'  => $pivotRecord['user_id'],
                'group_id' => $this->group_id,
            ])->update([
                'last_message_seen_id' => $pivotRecord['last_message_seen_id'],
                'updated_at' => $pivotRecord['updated_at'],
            ]);
        }
    }
}