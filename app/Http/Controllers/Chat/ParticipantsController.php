<?php

namespace App\Http\Controllers\Chat;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ChatMessage\ChatMessageEloquentRepo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParticipantsController extends Controller
{

    public function getAllUsersExceptSelf()
    {
        return User::all()->except(Auth::id());
    }

    public function getAllUnseenStates(ChatMessageEloquentRepo $chatMessageRepo)
    {
        $groupsWithUnseenMessage = [];
        $user = auth()->user();
        $allUserGroups = $user->groups;

        foreach($allUserGroups as $group){
            $lastMessageInGroup = $chatMessageRepo->latest(['group_id' => $group->id]);

            // conditions in order:
            // 1. props exist, so no errors occur when groups have no messages
            // 2. same as above
            // 3. if last message ID in group is NOT EQUAL message ID which user 'acknowledged', then he has unseen messages in that group,
            // 4. if user is not owner of last message:
            //      to prevent strange behavior when user sends message, refreshes page, and his message is treated as last unseen to him
            if(    isset($lastMessageInGroup->id)
                && isset($group->pivot->last_message_seen_id)
                && $lastMessageInGroup->id != $group->pivot->last_message_seen_id
                && $user->id != $lastMessageInGroup->user_id)
            {
                $groupsWithUnseenMessage[] = $group;
            }
        }
        return $groupsWithUnseenMessage;
    }

    // public function getUsersByGroup($group_id, ChatGroupEloquentRepo $chatGroupRepo)
    // {
    //     return ($chatGroupRepo->find($group_id))->participants;
    // }

}
