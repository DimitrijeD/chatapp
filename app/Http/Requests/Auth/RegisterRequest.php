<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'first_name'     => ['required', 'min:3', 'max:255'],
            'last_name'      => ['required', 'min:3', 'max:255'],
            'email'          => ['required', 'min:3', 'max:255', 'email', 'unique:users'],
            'password'       => ['required', 'min:6', 'max:255', 'confirmed'],
            'profilePicture' => ['required', 'file', 'image', 'max:5120'],
        ];
    }
}
