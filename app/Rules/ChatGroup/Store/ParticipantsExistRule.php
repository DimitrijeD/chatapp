<?php

namespace App\Rules\ChatGroup\Store;

use Illuminate\Contracts\Validation\Rule;
use App\ChatApp\Repos\User\UserEloquentRepo;

class ParticipantsExistRule implements Rule
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users; 
        $this->userRepo = new UserEloquentRepo;
    }

    public function passes($attribute, $value)
    {
        $arrayOfUsersIds = [];
        foreach($this->users as $user){
            $arrayOfUsersIds[] = $user['id'];
        }

        return $this->userRepo->getMany(['id' => $arrayOfUsersIds])->count() == count($arrayOfUsersIds) ? true : false;
    }

    public function message()
    {
        return __("Cannot create chat group with users which don't exist");
    }
}
