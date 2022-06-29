<?php

namespace App\Http\Requests\ChatGroup;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ChatRole;

class ChangeGroupNameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $requestInitiatorsRole = $this->group->participants->where('id', $this->user->id)->first()?->pivot->participant_role;
        $groupType = $this->group->model_type;

        return ChatRole::can([ $requestInitiatorsRole, $groupType ], ChatRole::ACTION_KEY_CHANGE_GROUP_NAME);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'new_name' => ['string', 'max:255', 'nullable'],
            'group_id' => ['required', 'integer'],
        ];
    }
}
