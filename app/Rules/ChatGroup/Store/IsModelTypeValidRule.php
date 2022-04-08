<?php

namespace App\Rules\ChatGroup\Store;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ChatGroup;

class IsModelTypeValidRule implements Rule
{
    public function __construct($model_type)
    {
        $this->model_type = $model_type;
    }

    public function passes($attribute, $value)
    {
        return in_array($this->model_type, ChatGroup::MODEL_TYPES);
    }

    public function message()
    {
        return "Model type is not available.";
    }
}
