<?php

namespace Tests\Feature\Chat\Message;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

use Database\Seeders\ChatGroupClusterSeeder;
use App\Models\User;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\ParticipantPivot;

class GetMissingMessagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupBuilderSetUp();
        
        $this->user      = $this->allChatData['users'][0];
        $this->otherUser = $this->allChatData['users'][1];
        
        $this->group = $this->allChatData['group'];

        $this->userPivot =      $this->allChatData['pivots'][$this->user     ->id];
        $this->otherUserPivot = $this->allChatData['pivots'][$this->otherUser->id];

        $this->messages = $this->allChatData['messages'];

        // ID of last message user has in his chat window
        $latest_msg_id = $this->messages[count($this->messages) - 2]->id; 

        // Making this message only one which user doesn't have in chat window
        $this->last_message_in_group = $this->messages[count($this->messages) - 1];

        Auth::login($this->user);
        $this->withHeaders([ 'Accept' => 'application/json', ]);

        $this->getMissingMessagesEndpoint = "/api/chat/group/{$this->group->id}/from-msg/{$latest_msg_id}";
    }

    private function groupBuilderSetUp()
    {
        $groupConfig = [

        ];

        $this->chatGroupSeeder = (resolve(ChatGroupClusterSeeder::class));
        $this->chatGroupSeeder->massSetter($groupConfig);
        $this->allChatData = $this->chatGroupSeeder->run();

    }

    /**
     * Request should return only one message 
     */
    public function test_get_only_one_missing_message()
    {
        $response = $this->get($this->getMissingMessagesEndpoint);

        $response->assertJson([
            [
                'id' => $this->last_message_in_group->id,
                'user_id' => $this->last_message_in_group->user_id,
                'group_id' => $this->last_message_in_group->group_id,
                'text' => $this->last_message_in_group->text, 
                'updated_at' => $this->last_message_in_group->updated_at->jsonSerialize(),
                'created_at' => $this->last_message_in_group->created_at->jsonSerialize(),
                'user' => [

                ],
            ]
        ]);
    }

    public function test_guest_cant_access()
    {
        Auth::logout($this->user);

        $response = $this->get($this->getMissingMessagesEndpoint);

        $response->assertStatus(401);
    }

    public function test_random_user_cant_access()
    {
        Auth::logout($this->user);
        Auth::login(User::factory()->create());
        
        $response = $this->get($this->getMissingMessagesEndpoint);

        $response->assertStatus(403);
    }
}
