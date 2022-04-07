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
        $chatGroup = $this->chatGroupRepo->create([
            'name' => $request->name,
            'model_type' => $request->model_type,
            'chatting_type' => $request->chatting_type,
        ]);

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
        return $this->chatGroupRepo->find($request->group_id);
    }

    public function getGroupById_WithParticipants(Request $request)
    {
        $group = $this->chatGroupRepo->find($request->group_id);
        $participants = $group->participants;

        return [
            'group' => $group,
            'participants' => $participants
        ];
    }

    public function getGroupById_WithoutSelf(Request $request)
    {
        $group = $this->chatGroupRepo->find($request->group_id);
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
