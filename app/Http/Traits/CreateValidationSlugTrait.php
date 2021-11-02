<?php

namespace App\Http\Traits;
use App\Models\User;

trait CreateValidationSlugTrait {
    public function createValidationSlug(User $user) {
        $firstPartHash  = md5(rand(1, 100) . $user->email     . time());
        $secondPartHash = md5(rand(1, 100) . $user->firstName . time());

        $hashUrl = $user->email . '-' . $firstPartHash . $secondPartHash;
        return $hashUrl;
    }
}
