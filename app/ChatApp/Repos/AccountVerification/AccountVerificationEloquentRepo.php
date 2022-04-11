<?php

namespace App\ChatApp\Repos\AccountVerification;

use App\ChatApp\Repos\AccountVerification\Contracts\AccountVerificationRepo;
use App\ChatApp\General\Traits\CRUDTrait;
use App\Models\AccountVerification;

class AccountVerificationEloquentRepo implements AccountVerificationRepo
{
    use CRUDTrait;

    public function getModel()
    {
        return AccountVerification::class;
    }

}