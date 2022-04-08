<?php

namespace Database\Factories;

use App\Models\ParticipantPivot;
use App\Models\User;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipantPivotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(), 
            'group_id' => ChatGroup::factory(), 
            'last_message_seen_id' => ChatMessage::factory(), 
            'participant_role' => ParticipantPivot::ROLE_PARTICIPANT,
        ];
    }
}
