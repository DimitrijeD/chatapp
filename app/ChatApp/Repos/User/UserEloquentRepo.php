<?php

namespace App\ChatApp\Repos\User;

use App\ChatApp\Repos\User\Contracts\UserRepo;
use App\ChatApp\General\Traits\CRUDTrait;
use App\Models\User;

class UserEloquentRepo implements UserRepo
{
    use CRUDTrait;

    public function getModel()
    {
        return User::class;
    }
}