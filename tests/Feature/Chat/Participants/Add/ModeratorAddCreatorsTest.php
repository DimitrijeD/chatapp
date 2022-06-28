<?php

namespace Tests\Feature\Chat\Participants\Add;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\ChatGroup;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;

class ModeratorAddCreatorsTest extends TestCase
{
    use RefreshDatabase, InitGroup;

    protected function setUp(): void
    {
        parent::setUp();

        // role of user making request
        $this->requesterRole = ChatRole::MODERATOR;

        // role 'requester' is attempting to give to user/users
        $this->targetRole = ChatRole::CREATOR;

        $this->expectedError = ["error" => __("You have no rights to add users to group.")];
    }

    public function test_moderator_cannot_add_1_creator_to_any_group()
    {
        foreach(ChatGroup::TYPES as $types){
            $groupCondig = array_merge(['model_type' => $types], $this->getModeratorGroupConfig());

            $this->groupSetUp($groupCondig, $this->requesterRole);

            $this->data = $this->payloadSetUp(1, $this->targetRole);

            $response = $this->post($this->addUsersEndpoint, $this->data);

            $response->assertStatus(401)->assertJson($this->expectedError);
        }
    }

    public function test_moderator_cannot_add_many_creators_to_any_group()
    {
        foreach(ChatGroup::TYPES as $types){
            $groupCondig = array_merge(['model_type' => $types], $this->getModeratorGroupConfig());

            $this->groupSetUp(array_merge(['model_type' => $types], $this->getModeratorGroupConfig()), $this->requesterRole);

            $this->data = $this->payloadSetUp(2, $this->targetRole);

            $response = $this->post($this->addUsersEndpoint, $this->data);

            $response->assertStatus(401)->assertJson($this->expectedError);
        }
    }


}
