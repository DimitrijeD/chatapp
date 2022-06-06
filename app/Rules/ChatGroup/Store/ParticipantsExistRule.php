<?php

namespace App\Rules\ChatGroup\Store;

use Illuminate\Contracts\Validation\Rule;
use App\ChatApp\Repos\User\UserEloquentRepo;

class ParticipantsExistRule implements Rule
{
    protected $arrayOfUserIds;

    public function __construct($arrayOfUserIds)
    {
        $this->arrayOfUserIds = $arrayOfUserIds; 
        $this->userRepo = new UserEloquentRepo;
    }

    public function passes($attribute, $value)
    {
        if(!$users = $this->userRepo->getMany(['id' => $this->arrayOfUserIds])) return false;

        return count($users) == count($this->arrayOfUserIds) ? true : false;
    }

    public function message()
    {
        return __("Cannot add participants which don't exist. Incorrect id.");
    }
}
