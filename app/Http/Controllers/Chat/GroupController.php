<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;

use App\ChatApp\Repos\User\UserEloquentRepo;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ChatMessage\ChatMessageEloquentRepo;

use App\Http\Requests\ChatGroup\GetGroupRequest;
use App\Http\Requests\ChatGroup\StoreGroupRequest;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $chatGroupRepo, $userRepo, $chatMessageRepo;

    public function __construct(ChatGroupEloquentRepo $chatGroupRepo, UserEloquentRepo $userRepo, ChatMessageEloquentRepo $chatMessageRepo)
    {
        $this->chatGroupRepo = $chatGroupRepo;
        $this->userRepo = $userRepo;
        $this->chatMessageRepo = $chatMessageRepo;
    }

    public function store(StoreGroupRequest $request)
    {
        $chatGroup = $this->chatGroupRepo->create(['name' => $request->name]);

        foreach($request->users as $user){
            $user = $this->userRepo->first(['id' => $user['id']]);
            $this->addParticipantToGroup($chatGroup, $user);
        }

        return $chatGroup;
    }

    // EXTRACT INTO CLASS
    private function addParticipantToGroup(ChatGroup $chatGroup, User $user)
    {
        $chatGroup->participants()->save($user);
    }

    public function getAllUnseenStates()
    {
        $groupsWithUnseenMessage = [];
        $user = auth()->user();
        $allUserGroups = $user->groups;
        foreach($allUserGroups as $group){
            $lastMessageInGroup = $this->getLastMessageOfGroup($group->id);

            // conditions in order:
            // 1. props exist, so no errors occur when groups have no messages
            // 2. same as above
            // 3. if last message ID in group is NOT EQUAL message ID which user 'acknowledged', then he has unseen messages in that group,
            //      @bug theres a problem, for new groups receivers do not pass this check (should be true but isnt) because $group->pivot->last_message_seen_id=null for new groups/new users added to group and 3. if comparison doesnt return true for some reason, or bug is somewhere else :/
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

    // EXTRACT THIS
    private function getLastMessageOfGroup($id)
    {
        return $this->chatMessageRepo->latest(['chat_group_id' => $id]);
    }

    // public function getUsersByGroup($id)
    // {
    //     return ($this->chatGroupRepo->find($id))->participants;
    // }

    public function getGroupsByUser()
    {
        return auth()->user()->groups;
    }

    // return all groups that belong to this user, with participants
    public function getGroupsByUserWithParticipants()
    {
        $groupsWithUsers = [];
        $userGroups = auth()->user()->groups;
        foreach($userGroups as $group){
            $groupsWithUsers[] = [
                'group' => $group,
                'participants' => $group->participants
            ];
        }
        return $groupsWithUsers;
    }

    // return all groups that belong to this user, without self as participant
    public function getGroupsByUserWithoutSelf()
    {
        $groupsWithUsers = [];
        $user = auth()->user();
        $userGroups = $user->groups->sortBy('updated_at');
        foreach($userGroups as $group){
            $groupsWithUsers[] = [
                'group' => $group,
                'participants' => $group->participants->where('id', '!=' , $user->id),
            ];
        }

        return $groupsWithUsers;
    }

    public function getGroupById(Request $request)
    {
        return $this->chatGroupRepo->find($request->id);
    }

    public function getGroupById_WithParticipants(Request $request)
    {
        $group = $this->chatGroupRepo->find($request->id);
        $participants = $group->participants;

        return [
            'group' => $group,
            'participants' => $participants
        ];
    }

    public function getGroupById_WithoutSelf(Request $request)
    {
        $group = $this->chatGroupRepo->find($request->id);
        $user = auth()->user();

        return [
            'group' => $group,
            'participants' => $group->participants->where('id', '!=' , $user->id),
        ];
    }

    public function getGroupsByUserWithoutSelf_v2()
    {
        return auth()->user()
            ->groups()
            ->with('participants')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

}
