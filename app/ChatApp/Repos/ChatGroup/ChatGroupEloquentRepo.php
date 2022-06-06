<?php

namespace App\ChatApp\Repos\ChatGroup;

use App\ChatApp\Repos\ChatGroup\Contracts\ChatGroupRepo;
use App\ChatApp\General\Traits\CRUDTrait;
use App\Models\ChatGroup;

class ChatGroupEloquentRepo implements ChatGroupRepo
{
    use CRUDTrait;

    public function getModel()
    {
        return ChatGroup::class;
    }

    /**
     * Returns group with user's pivot if they exist, false otherwise
     */
    public function getGroupWithPivot($group_id, $user_id)
    {
        if(!$group_id || !$user_id) return false;

        return $this->getModel()::
            where('id', $group_id)
            ->whereExists(function ($query) use($user_id){
                $query
                    ->select()
                    ->from('group_participants')
                    ->where('user_id', $user_id);
            })
            ->with(['participants' => function ($query) use($group_id, $user_id) {
                $query
                    ->select()
                    ->where('group_id', $group_id)
                    ->where('user_id', $user_id)
                    ->first();
            }])->first();
    }

}