<?php

namespace App\Http\Requests\ChatMessage;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatMessageRequest extends FormRequest
{
    /**
     * If user belongs to chat group
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
            'text' => ['required', 'string', 'max:1000',],
            'user_id' => ['required', 'integer'],
            'group_id' => ['required', 'integer'],
        ];
    }
}
