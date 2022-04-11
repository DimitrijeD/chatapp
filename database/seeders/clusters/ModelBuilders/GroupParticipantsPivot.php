<?php

namespace Database\Seeders\clusters\ModelBuilders;

use Database\Seeders\clusters\Contracts\Cluster;
use App\Models\ChatGroup;
use App\Models\ParticipantPivot;
use App\ChatApp\Repos\ParticipantPivot\ParticipantPivotEloquentRepo;

class GroupParticipantsPivot implements Cluster
{
    public function __construct($users, $chatGroup)
    {
        $this->users = $users; 
        $this->group = $chatGroup;
        $this->participantPivotRepo = resolve(ParticipantPivotEloquentRepo::class);
    }
    /**
     * Each user has one 'seen state' in pivot table 'group_participants'
     * 
     * @param array $users
     */
    public function build()
    {
        $now = now();
        $request_initiator_id = $this->users->first()->id;

        foreach($this->users as $user){
            ParticipantPivot::create(
                [
                    'user_id'              => $user->id,
                    'group_id'             => $this->group->id,
                    'last_message_seen_id' => null,
                    'participant_role'     => $this->participantPivotRepo->roleResolver($user->id, $request_initiator_id, $this->group->model_type),
                    'updated_at'           => $this->group->created_at, 
                    'created_at'           => $this->group->created_at
                ]
            );
        }

        return $this;
    }

    /**
     * Add to 'group_participants' pivot table ID of last message user has seen.
     * 
     * 'updated_at' column is used to store time when has user seen 'that' message in chat group
     */
    public function addLastMessageSeenId(array $pivotRecords)
    {
        foreach($pivotRecords as $pivotRecord){
            ParticipantPivot::where([
                'user_id'  => $pivotRecord['user_id'],
                'group_id' => $this->group->id,
            ])->update([
                'last_message_seen_id' => $pivotRecord['last_message_seen_id'],
                'updated_at' => $pivotRecord['updated_at'],
            ]);
        }
    }

    // Returns dictionary where index is user_id.
    public function getAllGroupPivots()
    {
        return ParticipantPivot::where(['group_id' => $this->group->id])->get()->keyBy('user_id');
    }
}