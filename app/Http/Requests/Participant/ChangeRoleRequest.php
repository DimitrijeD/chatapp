<?php

namespace App\Http\Requests\Participant;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ChatRole\RoleExistsRule;
use App\Models\ChatRole;

class ChangeRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to change role of targeted user.
     *
     * @return bool
     */
    public function authorize()
    {
        $requestInitiatorsRole = $this->group->participants->where('id', $this->user->id)      ->first()?->pivot->participant_role;
        $targetUserCurrentRole = $this->group->participants->where('id', $this->target_user_id)->first()?->pivot->participant_role;
        $targetUserNewRole     = $this->to_role;
        $groupType             = $this->group->model_type;

        // if at least one of them is not set
        if( !$requestInitiatorsRole || !$targetUserCurrentRole || !$targetUserNewRole || !$groupType) return false;

        return ChatRole::can([ $requestInitiatorsRole, $targetUserCurrentRole, $targetUserNewRole, $groupType ], ChatRole::ACTION_KEY_CHANGE_ROLE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'target_user_id' => ['required', 'integer'],
            'group_id' => ['required', 'integer'],
            'to_role' => ['required', 'string', new RoleExistsRule($this->to_role), ],
        ];
    }
}
