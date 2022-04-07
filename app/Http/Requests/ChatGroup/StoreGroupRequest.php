<?php

namespace App\Http\Requests\ChatGroup;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ChatGroup\IsChattingTypeValidRule;
use App\Rules\ChatGroup\IsModelTypeValidRule;

class StoreGroupRequest extends FormRequest
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
            'name' => ['string', 'max:255', 'nullable'],
            'model_type' => ['string', 'max:255', 'nullable', new IsModelTypeValidRule($this->model_type)],
            'chatting_type' => ['string', 'max:255', 'nullable', new IsChattingTypeValidRule($this->chatting_type)],
        ];
    }
}
