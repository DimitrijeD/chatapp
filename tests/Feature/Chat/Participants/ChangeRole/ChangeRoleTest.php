<?php

namespace Tests\Feature\Chat\Participants\ChangeRole;

use App\Models\ChatGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ChatRole;
use Database\Seeders\ChatGroupClusterSeeder;
use Illuminate\Support\Facades\Auth;

class ChangeRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chatGroupSeeder = (resolve(ChatGroupClusterSeeder::class));

        $this->withHeaders([ 'Accept' => 'application/json', ]);

        $this->changeRoleEndpoint = '/api/chat/group/change-user-role';
    }

    public function test_creator_can_change_participant_to_moderator_in_public_open()
    {
        $this->chatGroupSeeder->massSetter([
            'model_type' => ChatGroup::TYPE_PUBLIC_OPEN,
            'participants' => [
                [
                    'first_name' => 'Asd',
                    'last_name' => 'Asd',
                    'email' => 'asd@asd', 
                    'participant_role' => ChatRole::PARTICIPANT
                ],
            ],
        ]);
        
        $this->allChatData = $this->chatGroupSeeder->run();

        Auth::login($this->allChatData['group_creator']);

        if(!$targetUserId = $this->allChatData['pivots']->where('participant_role', ChatRole::PARTICIPANT)->first()?->user_id)
            $this->markTestIncomplete("Cannot finish this test because target user for role change doesn't exist");

        $response = $this->post($this->changeRoleEndpoint, [
            'target_user_id' => $targetUserId,
            'group_id' => $this->allChatData['group']->id,
            'to_role' => ChatRole::MODERATOR,
        ]);

        $response->assertJson(['success' => __("Role has been successfully changed.")]);
    }
}
