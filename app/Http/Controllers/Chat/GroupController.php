<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;

use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ParticipantPivot\ParticipantPivotEloquentRepo;
use App\Http\Requests\ChatGroup\ChangeGroupNameRequest;
use App\Http\Requests\ChatGroup\StoreGroupRequest;
use Illuminate\Http\Request;
use App\Events\GroupNameChangeEvent;

class GroupController extends Controller
{
    protected $chatGroupRepo, $userRepo, $chatMessageRepo;

    public function __construct(ChatGroupEloquentRepo $chatGroupRepo)
    {
        $this->chatGroupRepo = $chatGroupRepo;
    }

    public function store(StoreGroupRequest $request, ParticipantPivotEloquentRepo $participantPivotRepo)
    {
        $chatGroup = $this->chatGroupRepo->create([
            'name' => $request->name,
            'model_type' => $request->model_type,
        ]);

        $request_initiator_id = auth()->user()->id;

        foreach($request->users_ids as $user_id){
            $participantPivotRepo->create([
                'user_id' => $user_id, 
                'group_id' => $chatGroup->id, 
                'last_message_seen_id' => null, 
                'participant_role' => $participantPivotRepo->roleResolver($user_id, $request_initiator_id, $chatGroup->model_type),
            ]);
        }

        return response()->json($this->chatGroupRepo->first(['id' => $chatGroup->id], ['participants']), 201);
    }

    public function getGroupsByUser()
    {
        $groups = (auth()->user()->groups()->with(['participants', 'lastMessage.user']))
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($groups, 200);
    }

    public function getGroupById(Request $request)
    {
        // if group is collected in middleware already, no need to fetch it again
        if($request?->group?->participants)
            return response()->json($request->group);
        
        $group = $this->chatGroupRepo->get(
            ['id' => $request->group_id], 
            ['participants']
        );

        if($group)
            return response()->json($group);

        return response()->json(null, 404);
    }

    public function changeGroupName(ChangeGroupNameRequest $request)
    {
        if( !$this->chatGroupRepo->update($request->group, [ 'name' => $request->new_name ]) )
            return response()->json(['error' => __("An error occured while changing group name.")], 500);

        broadcast(new GroupNameChangeEvent([
            'group_id' => $request->group->id,
            'new_name' => $request->new_name
        ]));
        
        return response()->json(['success' => __("Group name has been changed.")]);
    }

    public function refreshGroup(Request $request)
    {
        $group = $this->chatGroupRepo->get(
            ['id' => $request->group_id], 
            ['participants', 'latestMessages.user']
        );

        return $group->participants->where('id', auth()->user()->id)->first() // if user is participant
            ? response()->json($group)
            : response()->json(['errors' => __("You have no access rights to this chat group.")], 403);
    }

}
