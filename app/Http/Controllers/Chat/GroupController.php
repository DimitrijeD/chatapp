<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\ParticipantPivot;

use App\ChatApp\Repos\User\UserEloquentRepo;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ChatMessage\ChatMessageEloquentRepo;
use App\ChatApp\Repos\ParticipantPivot\ParticipantPivotEloquentRepo;

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

    public function store(StoreGroupRequest $request, ParticipantPivotEloquentRepo $participantPivotRepo)
    {
        $chatGroup = $this->chatGroupRepo->create([
            'name' => $request->name,
            'model_type' => $request->model_type,
        ]);

        $request_initiator_id = auth()->user()->id;

        foreach($request->users as $user){
            $participantPivotRepo->create([
                'user_id' => $user['id'], 
                'group_id' => $chatGroup->id, 
                'last_message_seen_id' => null, 
                'participant_role' => $participantPivotRepo->roleResolver($user['id'], $request_initiator_id, $chatGroup->model_type),
            ]);
        }

        return response()->json($chatGroup, 201);
    }

    public function getGroupsByUser()
    {
        return response()->json(auth()->user()->groups, 200);
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
        return response()->json($groupsWithUsers, 200);
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

        return response()->json($groupsWithUsers, 200);
    }

    public function getGroupById(Request $request)
    {
        return response()->json($this->chatGroupRepo->find($request->group_id), 200);
    }

    public function getGroupById_WithParticipants(Request $request)
    {
        $group = $this->chatGroupRepo->find($request->group_id);
        $participants = $group->participants;

        return response()->json([
            'group' => $group,
            'participants' => $participants
        ], 200);
    }

    public function getGroupById_WithoutSelf(Request $request)
    {
        $group = $this->chatGroupRepo->find($request->group_id);
        $user = auth()->user();

        return response()->json([
            'group' => $group,
            'participants' => $group->participants->where('id', '!=' , $user->id),
        ], 200);
    }

    public function getGroupsByUserWithoutSelf_v2()
    {
        return response()->json(
            auth()->user()->groups()->with('participants')->orderBy('updated_at', 'desc')->get()
        , 200);
    }

}
