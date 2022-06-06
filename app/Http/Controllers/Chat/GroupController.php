<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;

use App\ChatApp\Repos\User\UserEloquentRepo;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ChatMessage\ChatMessageEloquentRepo;
use App\ChatApp\Repos\ParticipantPivot\ParticipantPivotEloquentRepo;

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

        return response()->json($this->chatGroupRepo->first(['id' => $chatGroup->id], ['participants']), 201);
    }

    public function getGroupsByUser()
    {
        $groups = (auth()->user()->groups()->with('participants'))
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

}
