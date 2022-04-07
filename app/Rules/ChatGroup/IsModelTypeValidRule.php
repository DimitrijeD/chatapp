<?php

namespace App\Rules\ChatGroup;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ChatGroup;

class IsModelTypeValidRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($model_type)
    {
        $this->model_type = $model_type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($this->model_type, ChatGroup::MODEL_TYPES);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Model type: '{$this->model_type}' is not available.";
    }
}
