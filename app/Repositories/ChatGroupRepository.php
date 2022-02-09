<?php

namespace App\Repositories;

use App\Models\ChatGroup;
use App\Models\User;

class ChatGroupRepository
{
    public function store($groupName, $users)
    {
        $newGroup = ChatGroup::create([
            'name' => $groupName,
        ]);
        foreach($users as $userFromRequest){
            $user = User::find($userFromRequest['id']);
            $this->addParticipantToGroup($newGroup, $user);
        }

        return $newGroup->fresh();
    }

    public function addParticipantToGroup(ChatGroup $group, User $user)
    {
        $group->participants()->save($user);
    }
}
