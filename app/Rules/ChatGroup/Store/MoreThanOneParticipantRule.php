<?php

namespace App\Rules\ChatGroup\Store;

use Illuminate\Contracts\Validation\Rule;

class MoreThanOneParticipantRule implements Rule
{
    protected $users;

    public function __construct($users_ids)
    {
        $this->users_ids = $users_ids; 
    }

    public function passes($attribute, $value)
    {
        return count($this->users_ids) >= 2 ? true : false;
    }

    public function message()
    {
        return __('Chat group must contain at least 2 users.');
    }
}
