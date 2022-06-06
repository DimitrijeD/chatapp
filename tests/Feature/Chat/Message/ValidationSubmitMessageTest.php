<?php

namespace Tests\Feature\Chat\Message;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\ChatRole;
use App\Models\ParticipantPivot;

class ValidationSubmitMessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->participant = User::factory()->create();
        $this->group = ChatGroup::factory()->create();
        
        $this->userPivot = ParticipantPivot::factory()->create([
            'user_id' => $this->user->id, 
            'group_id' => $this->group->id, 
            'participant_role' => ChatRole::PARTICIPANT,
            'last_message_seen_id' => (ChatMessage::factory()->create([
                'group_id' => $this->group->id,
                'user_id' => $this->user->id,
            ]))->id, 
        ]);

        $this->participantPivot = ParticipantPivot::factory()->create([
            'user_id' => $this->participant->id, 
            'group_id' => $this->group->id, 
            'participant_role' => ChatRole::PARTICIPANT,
            'last_message_seen_id' => (ChatMessage::factory()->create([
                'group_id' => $this->group->id,
                'user_id' => $this->participant->id,
            ]))->id, 
        ]);

        Auth::login($this->user);

        $this->withHeaders([
            'Accept' => 'application/json',
        ]);

        $this->userFormData = [
            'text' => $this->msg_text = Str::random(10),
            'group_id' => $this->group->id,
            'user_id' => $this->user->id
        ];

        $this->storeMessageEndpoint = '/api/chat/message/store';
    }

    public function test_message_is_saved()
    {
        $response = $this->post($this->storeMessageEndpoint, $this->userFormData);

        $this->assertDatabaseHas('chat_messages', [
            'group_id' => $this->group->id,
            'user_id' => $this->user->id,
            'text' => $this->msg_text 
        ]);
    }

    public function test_text_is_too_long()
    {
        $this->userFormData['text'] = Str::random(1001);

        $response = $this->post($this->storeMessageEndpoint, $this->userFormData);

        $response->assertStatus(422)->assertJson([
            "errors" => [
                "text" => [__("The text must not be greater than 1000 characters.")]
            ],
        ]);
    }

    public function test_text_is_required()
    {
        $this->userFormData['text'] = null;

        $response = $this->post($this->storeMessageEndpoint, $this->userFormData);

        $response->assertStatus(422)->assertJson([
            "errors" => [
                "text" => [__("The text field is required.")]
            ],
        ]);
    }

    public function test_text_must_be_string()
    {
        $this->userFormData['text'] = UploadedFile::fake()->image('avatar.jpg')->size(20);

        $response = $this->post($this->storeMessageEndpoint, $this->userFormData);

        $response->assertStatus(422)->assertJson([
            "errors" => [
                "text" => [__("The text must be a string.")]
            ],
        ]);
    }

    public function test_user_id_required()
    {
        $this->userFormData['user_id'] = null;

        $response = $this->post($this->storeMessageEndpoint, $this->userFormData);

        $response->assertStatus(422)->assertJson([
            "errors" => [
                "user_id" => [__("The user id field is required.")]
            ],
        ]);
    }

    public function test_user_id_must_be_int()
    {
        $this->userFormData['user_id'] = 'asd';

        $response = $this->post($this->storeMessageEndpoint, $this->userFormData);

        $response->assertStatus(422)->assertJson([
            "errors" => [
                "user_id" => [__("The user id must be an integer.")]
            ],
        ]);
    }

    public function test_group_id_required()
    {
        $this->userFormData['group_id'] = null;

        $response = $this->post($this->storeMessageEndpoint, $this->userFormData);

        $response->assertStatus(404)->assertJson([
            'errors' => __("Group doesn't exist.")
        ]);
    }

    public function test_group_id_must_be_int()
    {
        $this->userFormData['group_id'] = 'asd';

        $response = $this->post($this->storeMessageEndpoint, $this->userFormData);

        $response->assertStatus(404)->assertJson([
            'errors' => __("Group doesn't exist.")
        ]);
    }

    public function test_message_submit_to_group_user_doesnt_belong_to()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $diffGroup = ChatGroup::factory()->create();
        
        ParticipantPivot::factory()->create([
            'user_id' => $user1->id, 
            'group_id' => $diffGroup->id, 
            'participant_role' => ChatRole::PARTICIPANT,
            'last_message_seen_id' => (ChatMessage::factory()->create([
                'group_id' => $diffGroup->id,
                'user_id' => $user1->id,
            ]))->id, 
        ]);

        ParticipantPivot::factory()->create([
            'user_id' => $user2->id, 
            'group_id' => $diffGroup->id, 
            'participant_role' => ChatRole::PARTICIPANT,
            'last_message_seen_id' => (ChatMessage::factory()->create([
                'group_id' => $diffGroup->id,
                'user_id' => $user2->id,
            ]))->id, 
        ]);

        $this->userFormData['group_id'] = $diffGroup->id;

        $response = $this->post($this->storeMessageEndpoint, $this->userFormData);

        $response->assertStatus(403)->assertJson([
            'errors' => __("You have no access rights to this chat group.")
        ]);
    }
}
