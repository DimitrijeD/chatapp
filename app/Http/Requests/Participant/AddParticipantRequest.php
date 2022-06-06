<?php

namespace App\Http\Requests\Participant;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ChatGroup\Store\ParticipantsExistRule;

class AddParticipantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'group_id' => ['required', 'integer'],
            'usersToAdd' => ['required', 'array',  new ParticipantsExistRule($this->formatUserIds($this->usersToAdd))],
        ];
    }

    private function formatUserIds($usersToAdd){
        $arrayOfUserIds = [];
        foreach($usersToAdd as $user){
            $arrayOfUserIds[] = $user['user_id'];
        }
        return $arrayOfUserIds;
    }
}
