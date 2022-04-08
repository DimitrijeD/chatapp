<?php

namespace App\Rules\ChatGroup\Store;

use Illuminate\Contracts\Validation\Rule;

class MoreThanOneParticipantRule implements Rule
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users; 
    }

    public function passes($attribute, $value)
    {
        return count($this->users) >= 2 ? true : false;
    }

    public function message()
    {
        return __('Chat group must contain at least 2 users.');
    }
}
