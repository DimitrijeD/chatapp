<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\AccountVerification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AccountVerificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code'    => Hash::make( Str::random(AccountVerification::EMAIL_HASH_LENGTH) ),
            'type'    => AccountVerification::EMAIL_TYPE,
            'user_id' => User::factory(),
            'num_of_attempts' => 1,
            'updated_at' => now(),
            'created_at' => now(),
        ];
    }
}
