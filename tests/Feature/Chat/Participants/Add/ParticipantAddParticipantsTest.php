<?php

namespace Tests\Feature\Chat\Participants\Add;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\ChatGroup;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;

class ParticipantAddParticipantsTest extends TestCase
{
    use RefreshDatabase, InitGroup;

    protected function setUp(): void
    {
        parent::setUp();

        // role of user making request
        $this->requesterRole = ChatRole::PARTICIPANT;

        // role 'requester' is attempting to give to user/users
        $this->targetRole = ChatRole::PARTICIPANT;

        $this->expectedError = ["error" => __("You have no rights to add users to group.")];
    }

    public function test_participant_can_add_1_participant_to_public_open_group()
    {
        $groupCondig = array_merge(['model_type' => ChatGroup::TYPE_PUBLIC_OPEN], $this->getParticipantGroupConfig());

        $this->groupSetUp($groupCondig, $this->requesterRole);

        $this->data = $this->payloadSetUp(1, $this->targetRole);

        $response = $this->post($this->addUsersEndpoint, $this->data);
        
        $response->assertOk();
    }

    public function test_participant_can_add_many_participants_to_public_open_group()
    {
        $groupCondig = array_merge(['model_type' => ChatGroup::TYPE_PUBLIC_OPEN], $this->getParticipantGroupConfig());

        $this->groupSetUp($groupCondig, $this->requesterRole);

        $this->data = $this->payloadSetUp(2, $this->targetRole);

        $response = $this->post($this->addUsersEndpoint, $this->data);
        
        $response->assertOk();
    }

    public function test_participant_can_add_1_participant_to_protected_group()
    {
        $groupCondig = array_merge(['model_type' => ChatGroup::TYPE_PROTECTED], $this->getParticipantGroupConfig());

        $this->groupSetUp($groupCondig, $this->requesterRole);

        $this->data = $this->payloadSetUp(1, $this->targetRole);

        $response = $this->post($this->addUsersEndpoint, $this->data);
        
        $response->assertOk();
    }

    public function test_participant_can_add_many_participants_to_protected_group()
    {
        $groupCondig = array_merge(['model_type' => ChatGroup::TYPE_PROTECTED], $this->getParticipantGroupConfig());

        $this->groupSetUp($groupCondig, $this->requesterRole);

        $this->data = $this->payloadSetUp(2, $this->targetRole);

        $response = $this->post($this->addUsersEndpoint, $this->data);
        
        $response->assertOk();
    }
}
