<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\General\IfArrayHasValueThenMustBeIntsRule;

class GetMissingUsersRequest extends FormRequest
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
            'search_str' => ['required', 'min:3', 'max:255'],
            'i_have_ids' => [new IfArrayHasValueThenMustBeIntsRule($this->i_have_ids)]
        ];
    }
}
