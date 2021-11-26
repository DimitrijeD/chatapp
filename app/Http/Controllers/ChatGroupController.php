<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;

use Illuminate\Http\Request;

class ChatGroupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'groupName' => ['max:255'],
        ]);
        $newGroup = ChatGroup::create([
            'name' => $request->groupName,
        ]);
        foreach($request->users as $userFromRequest){
            $user = User::find($userFromRequest['id']);
            $this->addParticipantToGroup($newGroup, $user);
        }
        return $newGroup->fresh();
    }

    public function addParticipantToGroup(ChatGroup $group, User $user)
    {
        $group->participants()->save($user);
    }

    public function getAllUnseenStates(Request $request)
    {
        $groupsWithUnseenMessage = [];
        $user = $request->user();
        $allUserGroups = $user->groups;
        foreach($allUserGroups as $group){
            $lastMessageInGroup = $this->getLastMessageOfGroup($group->id);

            // conditions in order:
            // 1. props exist, so no errors occur when groups have no messages
            // 2. same as above
            // 3. if last message ID in group is NOT EQUAL message ID which user 'acknowledged', then he has unseen messages in that group
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

    public function getLastMessageOfGroup($groupId)
    {
        return ChatMessage::where('chat_group_id', $groupId)->latest()->first();
    }

    // return all users that belong to this group
    public function getUsersByGroup($groupId)
    {
        $group = ChatGroup::find($groupId);
        return $group->participants;
    }

    // return all groups that belong to this user
    public function getGroupsByUser(Request $request)
    {
        return $request->user()->groups;
    }

    // return all groups that belong to this user, with participants
    public function getGroupsByUserWithParticipants(Request $request)
    {
        $groupsWithUsers = [];
        $userGroups = $request->user()->groups;
        foreach($userGroups as $group){
            $groupsWithUsers[] = [
                'group' => $group,
                'participants' => $group->participants
            ];
        }
        return $groupsWithUsers;
    }

    // return all groups that belong to this user, without self as participant
    public function getGroupsByUserWithoutSelf(Request $request)
    {
        $groupsWithUsers = [];
        $user = $request->user();
        $userGroups = $user->groups->sortBy('updated_at');
        foreach($userGroups as $group){
            $groupsWithUsers[] = [
                'group' => $group,
                'participants' => $group->participants->where('id', '!=' , $user->id),
            ];
        }
        return $groupsWithUsers;
    }

    public function getGroupById($groupId)
    {
        return ChatGroup::find($groupId);
    }

    public function getGroupById_WithParticipants($groupId)
    {
        $group = ChatGroup::find($groupId);
        $participants = $group->participants;
        return [
            'group' => $group,
            'participants' => $participants
        ];
    }

    public function getGroupById_WithoutSelf(Request $request, $groupId)
    {
        $group = ChatGroup::find($groupId);
        $user = $request->user();
        return [
            'group' => $group,
            'participants' => $group->participants->where('id', '!=' , $user->id),
        ];
    }


}
