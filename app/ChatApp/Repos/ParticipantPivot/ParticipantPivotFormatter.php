<?php

namespace App\ChatApp\Repos\ParticipantPivot;

use App\Models\ParticipantPivot;
use App\Models\ChatGroup;
use Illuminate\Support\Collection;

class ParticipantPivotFormatter 
{
    public function prepareManyToInsert(array $users, $group_id)
    {
        $data = [];
        
        foreach($users as $user){
            $data[] = [
                'user_id' => $user['user_id'],
                'participant_role' => $user['target_role'],
                'group_id' => $group_id
            ];
        }

        return $data;
    }

    public function prepareManyToSelect(array $users, $group_id)
    {
        $data = [];
        
        foreach($users as $user){
            $data[] = [
                'user_id' => $user['user_id'],
                'group_id' => $group_id
            ];
        }

        return $data;
    }

}