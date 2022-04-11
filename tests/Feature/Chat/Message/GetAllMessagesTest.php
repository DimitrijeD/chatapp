<?php

namespace Tests\Feature\Chat\Message;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

use Database\Seeders\ChatGroupClusterSeeder;
use App\Models\User;
use App\Models\ChatMessage;

class GetAllMessagesTest extends TestCase
{
    use RefreshDatabase;

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

        Auth::login($this->user);
        $this->withHeaders([ 'Accept' => 'application/json', ]);

        $this->getAllMessagesEndpoint = "/api/chat/group/{$this->group->id}/messages";
    }

    public function test_user_gets_all_messages()
    {   
        $response = $this->get($this->getAllMessagesEndpoint);

        $response->assertJsonStructure(['messages' => [
            [
                'id', 
                'group_id', 
                'user_id', 
                'text', 
                'created_at', 
                'updated_at',
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'image',
                    'thumbnail',
                    'email_verified_at',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]]);
    }

    public function test_other_user_also_gets_all_messages()
    {   
        Auth::logout($this->user);
        Auth::login($this->otherUser);

        $response = $this->get($this->getAllMessagesEndpoint);

        $response->assertJsonStructure(['messages' => [
            [
                'id', 
                'group_id', 
                'user_id', 
                'text', 
                'created_at', 
                'updated_at',
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'image',
                    'thumbnail',
                    'email_verified_at',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]]);
    }

    public function test_guest_cannot_get_messages()
    {   
        Auth::logout($this->user);

        $response = $this->get($this->getAllMessagesEndpoint);

        $response->assertStatus(401);
    }

    public function test_non_participant_cannot_get_messages()
    {   
        Auth::logout($this->user);
        Auth::login(User::factory()->create());

        $response = $this->get($this->getAllMessagesEndpoint);

        $response->assertStatus(403);
    }
}
