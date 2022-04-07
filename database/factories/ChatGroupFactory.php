<?php

namespace Database\Factories;

use App\Models\ChatGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Default group name',
            'model_type' => ChatGroup::MODEL_TYPE_DEFAULT,
            'chatting_type' => ChatGroup::CHATTING_TYPE_DEFAULT,
        ];
    }
}
