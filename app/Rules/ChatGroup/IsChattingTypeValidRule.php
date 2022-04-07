<?php

namespace App\Rules\ChatGroup;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ChatGroup;

class IsChattingTypeValidRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($chatting_type)
    {
        $this->chatting_type = $chatting_type;
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
        return in_array($this->chatting_type, ChatGroup::CHATTING_TYPES);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Chatting type: '{$this->chatting_type}' is not available.";
    }
}
