<?php

namespace Tests\Feature\Chat\Participants\Add;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\ChatGroup;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;

class ListenerCanNeverAddAnybodyTest extends TestCase
{
    use RefreshDatabase, InitGroup;

    protected function setUp(): void
    {
        parent::setUp();

        // role of user making request
        $this->requesterRole = ChatRole::LISTENER;

        $this->expectedError = ["error" => __("You have no rights to add users to group.")];
    }

    public function test_listener_cannot_add_anybody_to_any_group()
    {
        foreach(ChatGroup::TYPES as $type)
        {
            foreach(ChatRole::ROLES as $targetRole){
                $groupCondig = array_merge(['model_type' => $type], $this->getListenerGroupConfig());

                $this->groupSetUp($groupCondig, $this->requesterRole);

                $this->data = $this->payloadSetUp(1, $targetRole);

                $response = $this->post($this->addUsersEndpoint, $this->data);
                
                $response->assertStatus(401)->assertJson($this->expectedError);
            }
        }
    }

    public function test_listener_cannot_add_many_users_of_diff_roles_to_any_group()
    {
        foreach(ChatGroup::TYPES as $type)
        {
            foreach(ChatRole::ROLES as $targetRole){
                $groupCondig = array_merge(['model_type' => $type], $this->getListenerGroupConfig());

                $this->groupSetUp($groupCondig, $this->requesterRole);

                $this->data = $this->payloadSetUp(2, $targetRole);

                $response = $this->post($this->addUsersEndpoint, $this->data);
                
                $response->assertStatus(401)->assertJson($this->expectedError);
            }
        }
    }
}
