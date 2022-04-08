<?php

namespace App\ChatApp\Repos\ParticipantPivot;

use App\ChatApp\Repos\ParticipantPivot\Contracts\ParticipantPivotRepo;
use App\ChatApp\General\Traits\CRUDTrait;
use App\Models\ParticipantPivot;
use App\Models\ChatGroup;

class ParticipantPivotEloquentRepo implements ParticipantPivotRepo
{
    use CRUDTrait;

    public function getModel()
    {
        return ParticipantPivot::class;
    }

    // order matters
    public function roleResolver($user_id, $request_initiator_id, $model_type)
    {
        if($model_type == ChatGroup::MODEL_TYPE_PRIVATE){
            return ParticipantPivot::ROLE_PARTICIPANT;
        }

        if($user_id == $request_initiator_id){
            return ParticipantPivot::ROLE_CREATOR;
        }

        if($model_type == ChatGroup::MODEL_TYPE_PUBLIC_CLOSED){
            return ParticipantPivot::ROLE_LISTENER;
        }

        if($model_type == ChatGroup::MODEL_TYPE_PUBLIC_OPEN){
            return ParticipantPivot::ROLE_PARTICIPANT;
        }

        if($model_type == ChatGroup::MODEL_TYPE_PROTECTED){
            return ParticipantPivot::ROLE_PARTICIPANT;
        }
        
        return ParticipantPivot::ROLE_PARTICIPANT;
    }
}