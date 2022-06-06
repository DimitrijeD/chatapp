<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;
use App\ChatApp\Repos\ParticipantPivot\ParticipantPivotFormatter;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ChatMessage\ChatMessageEloquentRepo;
use App\ChatApp\Repos\ParticipantPivot\ParticipantPivotEloquentRepo;

use App\Http\Requests\Participant\AddParticipantRequest;

class ParticipantsController extends Controller
{ 
    protected $pivotRepo;

    public function __construct(ParticipantPivotEloquentRepo $pivotRepo)
    {
        $this->pivotRepo = $pivotRepo;
    }

    public function getAllUsersExceptSelf()
    {
        return User::all()->except(Auth::id());
    }

    public function addUsersToGroup(
        AddParticipantRequest $request, 
        ParticipantPivotFormatter $pivotFormatter, 
        ChatGroupEloquentRepo $chatGroupRepo
    ){
        $user = auth()->user();

        // get group with only request initiators pivot 
        if(!$group = $chatGroupRepo->getGroupWithPivot($request->group_id, $user->id)) 
            return response()->json(['errors' => __("Group doesn't exist.")], 404);

        // check if all selected participants can be added to group on requested individual roles
        foreach($request->usersToAdd as $userToAdd){
            if(!ChatRole::can([
                    $group->participants->first()->pivot->participant_role,
                    $userToAdd['target_role'], 
                    $group->model_type,
                ],
                'add'
            )) 
            return response()->json(['errors' => __("You have no rights to add users to group.")], 401);
        }

        // if at least one participant selected is already in group, return error
        if($this->pivotRepo->getManyUnique($pivotFormatter->prepareManyToSelect(
            $request->usersToAdd, 
            $request->group_id
        ))) 
        return response()->json(['errors' => __("Some or all selected participants are already in group. Please select only those which are not in group.")], 409);

        $msgText = count($request->usersToAdd) > 1 ? 'Participants' : 'Participant';

        if(!$createStatus = $this->pivotRepo->createMany($pivotFormatter->prepareManyToInsert($request->usersToAdd, $request->group_id)))
            return response()->json(['errors'  => __("{$msgText} could not be added.")], 500);
            
        return response()->json([
            'success' => __("{$msgText} successfully added."),
            'group' => $chatGroupRepo->get(
                ['id' => $request->group_id], 
                ['participants']
            )
        ]);
    }

    // public function getUsersByGroup($group_id, ChatGroupEloquentRepo $chatGroupRepo)
    // {
    //     return ($chatGroupRepo->find($group_id))->participants;
    // }

    /**
     * Expects midleware ChatGroupAccess to provide group with participants and user models othwerwise this code wont work
     * 
     * @todo check if $status is true after deleting model
     */
    public function leaveGroup(Request $request)
    {
        if(!$userWithPivot = $request->group->participants->where('id', $request->user->id)->first())
            return response()->json(['errors' => __('An error occured.')], 500); 

        $status = $this->pivotRepo->delete($userWithPivot->pivot);

        return response()->json(['success' => __('You have left this group.')]); 
    }

    public function removeUserFromGroup(Request $request)
    {
        $user = auth()->user();
        $myPivot = null;
        $targetForRemove = null;
        
        // get pivot of user making request
        foreach($request->group->participants as $participant){
            if($participant->id == $user->id)
                $myPivot = $participant;

            if($participant->id == $request->user_id_to_remove)
                $targetForRemove = $participant;
        }

        if(!ChatRole::can([ $myPivot->pivot->participant_role, $targetForRemove->pivot->participant_role, $request->group->model_type ], 'remove')){
            return response()->json(['errors' => __("You cannot remove this user from group.")], 401);
        }

        return $this->pivotRepo->delete($targetForRemove->pivot) 
            ? response()->json(['success' => __("User has been removed from group.")])
            : response()->json(['error'   => __("An error occured while removing user from group.")], 500);
    }

}
