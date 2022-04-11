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

class SeenTest extends TestCase
{
    use RefreshDatabase;
    
    /** 
     * Creates group with at least 2 users with many messages.
     * First user is logged in. 
     * Last message in chat belongs to 'other' user.
     * First user didn't see that message.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->chatGroupSeeder = (resolve(ChatGroupClusterSeeder::class));
        
        $this->allChatData = $this->chatGroupSeeder->run();

        $this->user      = $this->allChatData['users'][0];
        $this->otherUser = $this->allChatData['users'][1];
        
        $this->group = $this->allChatData['group'];

        $this->userPivot =      $this->allChatData['pivots'][$this->user     ->id];
        $this->otherUserPivot = $this->allChatData['pivots'][$this->otherUser->id];

        $this->lastMessage = ChatMessage::factory()->create([
            'group_id' => $this->group->id,
            'user_id' => $this->otherUser->id,
        ]);

        Auth::login($this->user);
        $this->withHeaders([ 'Accept' => 'application/json', ]);

        $this->seenEndpoint = "/api/chat/message/seen";
    }

    public function test_ok()
    {
        $data = [
            'group_id' => $this->group->id,
            'lastMessageId' => $this->lastMessage->id,
        ];
        
        $response = $this->post($this->seenEndpoint, $data);

        $response->assertStatus(200);
    }

    /**
     * Assert on first user seen request, database is updated that he saw last message in chat. 
     */
    public function test_seen_event_updates_db()
    {
        $data = [
            'group_id' => $this->group->id,
            'lastMessageId' => $this->lastMessage->id,
        ];
        
        $response = $this->post($this->seenEndpoint, $data);

        $this->assertDatabaseHas('group_participants', [
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'last_message_seen_id' => $this->lastMessage->id,
            'participant_role' => $this->userPivot->participant_role,
        ]);
    }

    /**
     * Assert request doesnt modify wrong data. 
     */
    public function test_user_seen_doesnt_modify_others_seen()
    {
        $data = [
            'group_id' => $this->group->id,
            'lastMessageId' => $this->lastMessage->id,
        ];
        
        $response = $this->post($this->seenEndpoint, $data);

        $this->assertDatabaseHas('group_participants', [
            'user_id' => $this->otherUser->id,
            'group_id' => $this->group->id,
            'last_message_seen_id' => $this->otherUserPivot->last_message_seen_id,
            'participant_role' => $this->otherUserPivot->participant_role,
        ]);
    }

}
