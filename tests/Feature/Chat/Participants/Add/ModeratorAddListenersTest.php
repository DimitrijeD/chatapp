<?php

namespace Tests\Feature\Chat\Participants\Add;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\ChatGroup;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;

class ModeratorAddListenersTest extends TestCase
{
    use RefreshDatabase, InitGroup;

    protected function setUp(): void
    {
        parent::setUp();

        // role of user making request
        $this->requesterRole = ChatRole::MODERATOR;

        // role 'requester' is attempting to give to user/users
        $this->targetRole = ChatRole::LISTENER;

        $this->expectedError = ["error" => __("You have no rights to add users to group.")];
    }

    public function test_moderator_can_add_1_listener_to_public_closed_group()
    {
        $groupCondig = array_merge(['model_type' => ChatGroup::TYPE_PUBLIC_CLOSED], $this->getModeratorGroupConfig());

        $this->groupSetUp($groupCondig, $this->requesterRole);

        $this->data = $this->payloadSetUp(1, $this->targetRole);

        $response = $this->post($this->addUsersEndpoint, $this->data);
        
        $response->assertOk();
    }

    public function test_moderator_can_add_many_listeners_to_public_closed_group()
    {
        $groupCondig = array_merge(['model_type' => ChatGroup::TYPE_PUBLIC_CLOSED], $this->getModeratorGroupConfig());

        $this->groupSetUp($groupCondig, $this->requesterRole);

        $this->data = $this->payloadSetUp(2, $this->targetRole);
        
        $response = $this->post($this->addUsersEndpoint, $this->data);

        $response->assertOk();
    }
}
