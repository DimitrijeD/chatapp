<?php

namespace App\ChatApp\Repos\ParticipantPivot;

use App\ChatApp\Repos\ParticipantPivot\Contracts\ParticipantPivotRepo;
use App\ChatApp\General\Traits\CRUDTrait;
use App\Models\ParticipantPivot;
use App\Models\ChatGroup;
use App\Models\ChatRole;

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
        if($model_type == ChatGroup::TYPE_PRIVATE){
            return ChatRole::PARTICIPANT;
        }

        if($user_id == $request_initiator_id){
            return ChatRole::CREATOR;
        }

        if($model_type == ChatGroup::TYPE_PUBLIC_CLOSED){
            return ChatRole::LISTENER;
        }

        if($model_type == ChatGroup::TYPE_PUBLIC_OPEN){
            return ChatRole::PARTICIPANT;
        }

        if($model_type == ChatGroup::TYPE_PROTECTED){
            return ChatRole::PARTICIPANT;
        }
        
        return ChatRole::PARTICIPANT;
    }

    public function createMany($data)
    {
        return $this->getModel()::insert($data);
    }


}