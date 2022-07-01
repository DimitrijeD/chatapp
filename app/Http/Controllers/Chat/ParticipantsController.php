<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\ChatRole;
use App\ChatApp\Repos\ParticipantPivot\ParticipantPivotFormatter;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ParticipantPivot\ParticipantPivotEloquentRepo;

use App\Http\Requests\Participant\AddParticipantRequest;
use App\Http\Requests\Participant\ChangeRoleRequest;
use App\Http\Requests\Participant\RemoveParticipantRequest;

use App\Events\UserRoleInGroupChangedEvent;
use App\Events\UserRemovedFromGroupEvent;
use App\Events\UserAddedToGroupEvent;
use App\Events\UserLeftGroupEvent;

class ParticipantsController extends Controller
{ 
    protected $pivotRepo;

    public function __construct(ParticipantPivotEloquentRepo $pivotRepo)
    {
        $this->pivotRepo = $pivotRepo;
    }

    public function addUsersToGroup(
        AddParticipantRequest $request, 
        ParticipantPivotFormatter $pivotFormatter, 
        ChatGroupEloquentRepo $chatGroupRepo
    ){
        $user = auth()->user();

        // get group with only request initiators pivot 
        if(!$group = $chatGroupRepo->getGroupWithPivot($request->group_id, $user->id)) 
            return response()->json(['error' => __("Group doesn't exist.")], 404);

        // check if all selected participants can be added to group on requested individual roles
        foreach($request->usersToAdd as $userToAdd){
            if(!ChatRole::can([
                    $group->participants->first()->pivot->participant_role,
                    $userToAdd['target_role'], 
                    $group->model_type,
                ],
                ChatRole::ACTION_KEY_ADD
            )) 
            return response()->json(['error' => __("You have no rights to add users to group.")], 401);
        }

        // if at least one participant selected is already in group, return error
        if($this->pivotRepo->getManyUnique($pivotFormatter->prepareManyToSelect(
            $request->usersToAdd, 
            $request->group_id
        ))) 
        return response()->json(['error' => __("Some or all selected participants are already in group. Please select only those which are not in group.")], 409);

        $msgText = count($request->usersToAdd) > 1 ? 'Participants' : 'Participant';

        if(!$this->pivotRepo->createMany($pivotFormatter->prepareManyToInsert($request->usersToAdd, $request->group_id)))
            return response()->json(['error'  => __("{$msgText} could not be added.")], 500);
            
        broadcast(new UserAddedToGroupEvent([
            'group_id' => $request->group->id,
            'participants' => $chatGroupRepo->get(
                ['id' => $request->group_id], 
                ['participants']
            )->participants,
            'addedUsers' => $request->usersToAdd
        ]));
        
        return response()->json([ 'success' => __("{$msgText} successfully added.") ]);
    }

    public function leaveGroup(Request $request)
    {
        if(!$userWithPivot = $request->group->participants->where('id', $request->user->id)->first())
            return response()->json(['error' => __('An error occured.')], 500); 

        if(!$this->pivotRepo->delete($userWithPivot->pivot))
            return response()->json(['error' => __('An error occured while disconnecting you from the group.')]); 

        broadcast(new UserLeftGroupEvent([
            'group_id' => $request->group->id,
            'user_left_id' => $request->user->id
        ]))->toOthers();

        return response()->json(['success' => __('You have left this group.')]); 
    }

    public function removeUserFromGroup(RemoveParticipantRequest $request)
    {
        $myPivot         = $request->group->participants->where('id', $request->user->id)         ->first();
        $targetForRemove = $request->group->participants->where('id', $request->user_id_to_remove)->first();

        if(!$myPivot || !$targetForRemove)
            return response()->json(['error' => __("An error occured.")], 404); 

        if(!$this->pivotRepo->delete($targetForRemove->pivot))
            return response()->json(['error' => __("An error occured while removing user from group.")], 500); 

        broadcast(new UserRemovedFromGroupEvent([
            'group_id' => $request->group->id,
            'removed_user_id' => $targetForRemove->id
        ]));

        return response()->json(['success' => __("User has been removed from group.")]);
    }

    public function chageParticipantsRole(ChangeRoleRequest $request)
    {
        $participantPivotToUpdate = $request->group->participants->where('id', $request->target_user_id)->first()?->pivot;

        if(!$participantPivotToUpdate)
            return response()->json(['error' => __("User you are trying to change role to, is not in this group.")]);

        $updatedPivot = $this->pivotRepo->update($participantPivotToUpdate, [
            'participant_role' => $request->to_role,
        ]);

        if($updatedPivot->participant_role != $request->to_role)
            return response()->json(['error' => __("We are not able to change role at this time.")], 500);

        broadcast(new UserRoleInGroupChangedEvent([
            'group_id' => $request->group->id,
            'pivot' => $updatedPivot
        ]));

        return response()->json(['success' => __("Role has been successfully changed.")]);
    }

}
