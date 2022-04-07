<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\ChatGroup;

class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'group_id' => ChatGroup::factory(),
            'user_id' => User::factory(),
            'text' => $this->faker->text(rand(5, 50)),
            'updated_at' => now(),
            'created_at' => now(),
        ];
    }
}
