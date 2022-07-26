<?php

namespace Tests\Feature\Chat\Message;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

use Database\Seeders\ChatGroupClusterSeeder;
use App\Models\User;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;
use App\Events\MeSawMessage;

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
    public function test_user_seen_doesnt_modify_others_seen_pivot_state()
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

    public function test_dispatches_seen_message_even()
    {
        Event::fake();

        $data = [
            'group_id' => $this->group->id,
            'lastMessageId' => $this->lastMessage->id,
        ];
        
        $this->post($this->seenEndpoint, $data);

        Event::assertDispatched(MeSawMessage::class, function ($e) {
            return $e->data->group_id == $this->group->id 
                && $e->data->user_id == $this->user->id
                && $e->data->last_message_seen_id == $this->lastMessage->id;
        });
    }


    public function test_cannot_seen_event_on_group_user_doesnt_belong_to()
    {
        $this->chatGroupSeeder = (resolve(ChatGroupClusterSeeder::class));
        $this->chatGroupSeeder->massSetter([
            'model_type' => ChatGroup::TYPE_PRIVATE,
            'participants' => [
                [
                    'first_name' => 'Some',
                    'last_name' => 'CompletelyDifferentUsers',
                    'email' => 'q123w321e@q321w123e',
                    'participant_role' => ChatRole::CREATOR
                ],

                [
                    'first_name' => 'Which',
                    'last_name' => 'HaveNoConnectionToThisUser',
                    'email' => 'q123w321e@q321w12sd3sde',
                    'participant_role' => ChatRole::MODERATOR
                ],
            ]
        ]);
        $allChatData = $this->chatGroupSeeder->run();

        $data = [
            'group_id' => $allChatData['group']->id, // foreign group
            'lastMessageId' => $allChatData['messages']->last()->id, // last message in foreign group
        ];
        
        $response = $this->post($this->seenEndpoint, $data);

        $response->assertJson([
            'errors' => __("You have no access rights to this chat group.")
        ]);
        
        Event::fake();
        // Assert an event was not dispatched... Middleware prevented bad request so this assert is pointless, but why not..
        Event::assertNotDispatched(MeSawMessage::class);
    }


}
