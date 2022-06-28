<?php

namespace Tests\Feature\Chat\Participants\Add;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\ChatGroup;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;

class CreatorAddParticipantsTest extends TestCase
{
    use RefreshDatabase, InitGroup;

    protected function setUp(): void
    {
        parent::setUp();

        // role of user making request
        $this->requesterRole = ChatRole::CREATOR;

        // role 'requester' is attempting to give to user/users 
        $this->targetRole = ChatRole::PARTICIPANT;

        $this->expectedError = ["error" => __("You have no rights to add users to group.")];
    }

    public function test_creator_can_add_1_participant_to_open_group()
    {
        $this->groupSetUp(['model_type' => ChatGroup::TYPE_PUBLIC_OPEN], $this->requesterRole);

        $this->data = $this->payloadSetUp(1, $this->targetRole);

        $response = $this->post($this->addUsersEndpoint, $this->data);

        $response->assertOk();
    }

    public function test_creator_can_add_many_participants_to_open_group()
    {
        $this->groupSetUp(['model_type' => ChatGroup::TYPE_PUBLIC_OPEN], $this->requesterRole);

        $this->data = $this->payloadSetUp(2, $this->targetRole);
        
        $response = $this->post($this->addUsersEndpoint, $this->data);

        $response->assertOk();
    }

    public function test_creator_can_add_1_participant_to_protected_group()
    {
        $this->groupSetUp(['model_type' => ChatGroup::TYPE_PROTECTED], $this->requesterRole); 

        $this->data = $this->payloadSetUp(1, $this->targetRole);
        
        $response = $this->post($this->addUsersEndpoint, $this->data);
        
        $response->assertOk();
    }

    public function test_creator_can_add_many_participants_to_protected_group()
    {
        $this->groupSetUp(['model_type' => ChatGroup::TYPE_PROTECTED], $this->requesterRole);

        $this->data = $this->payloadSetUp(2, $this->targetRole);
        
        $response = $this->post($this->addUsersEndpoint, $this->data);
        
        $response->assertOk();
    }

    public function test_creator_cannot_add_1_participant_to_closed_group()
    {
        $this->groupSetUp(['model_type' => ChatGroup::TYPE_PUBLIC_CLOSED], $this->requesterRole);

        $this->data = $this->payloadSetUp(1, $this->targetRole);
        
        $response = $this->post($this->addUsersEndpoint, $this->data);

        $response->assertStatus(401)->assertJson($this->expectedError);
    }

    public function test_creator_cannot_add_many_participants_to_closed_group()
    {
        $this->groupSetUp(['model_type' => ChatGroup::TYPE_PUBLIC_CLOSED], $this->requesterRole); 

        $this->data = $this->payloadSetUp(2, $this->targetRole);
        
        $response = $this->post($this->addUsersEndpoint, $this->data);

        $response->assertStatus(401)->assertJson($this->expectedError);
    }

    public function test_creator_cannot_add_1_participant_to_private_group()
    {
        $this->groupSetUp(['model_type' => ChatGroup::TYPE_PRIVATE], $this->requesterRole);

        $this->data = $this->payloadSetUp(1, $this->targetRole);

        $response = $this->post($this->addUsersEndpoint, $this->data);

        $response->assertStatus(401)->assertJson($this->expectedError);
    }

    public function test_creator_cannot_add_many_participants_to_private_group()
    {
        $this->groupSetUp(['model_type' => ChatGroup::TYPE_PRIVATE], $this->requesterRole);

        $this->data = $this->payloadSetUp(2, $this->targetRole);
        
        $response = $this->post($this->addUsersEndpoint, $this->data);

        $response->assertStatus(401)->assertJson($this->expectedError);
    }

}
