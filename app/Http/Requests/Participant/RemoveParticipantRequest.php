<?php

namespace App\Http\Requests\Participant;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ChatRole;

class RemoveParticipantRequest extends FormRequest
{

    public function authorize()
    {
        $initiatorRole = $this->group->participants->where('id', $this->user->id)         ->first()?->pivot->participant_role;
        $targetRole    = $this->group->participants->where('id', $this->user_id_to_remove)->first()?->pivot->participant_role;
        $groupType     = $this->group->model_type;
        
        return ChatRole::can([ $initiatorRole, $targetRole, $groupType ], ChatRole::ACTION_KEY_REMOVE);
    }

    public function rules()
    {
        return [
            // ...
        ];
    }
}
