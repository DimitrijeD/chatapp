<?php

namespace Tests\Feature\Chat\Participants\ChangeGroupName;

use App\Models\ChatGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ChatRole;
use Database\Seeders\ChatGroupClusterSeeder;
use Illuminate\Support\Facades\Auth;

class ChangeGroupNameTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chatGroupSeeder = (resolve(ChatGroupClusterSeeder::class));

        $this->withHeaders([ 'Accept' => 'application/json', ]);

        $this->changeNameEndpoint = '/api/chat/group/change-group-name';
    }

    public function test_creator_can_change_public_closed_group_name()
    {
        $this->chatGroupSeeder->massSetter([
            'model_type' => ChatGroup::TYPE_PUBLIC_CLOSED,
        ]);
        
        $this->allChatData = $this->chatGroupSeeder->run();

        Auth::login($this->allChatData['group_creator']);

        $response = $this->post($this->changeNameEndpoint, [
            'new_name' => "New Group Name",
            'group_id' => $this->allChatData['group']->id,
        ]);

        $response->assertJson(['success' => __("Group name has been changed.")]);
    }
}
