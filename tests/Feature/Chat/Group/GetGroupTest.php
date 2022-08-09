<?php

namespace Tests\Feature\Chat\Group;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;

class GetGroupTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherChatParticipant = User::factory()->create();
        $this->group = ChatGroup::factory()->create([
            'model_type' => ChatGroup::TYPE_DEFAULT,
        ]);

        $this->userPivot = ParticipantPivot::factory()->create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id, 
            'last_message_seen_id' => null, 
            'participant_role' => ChatRole::PARTICIPANT,
        ]);

        $this->otherUserPivot = ParticipantPivot::factory()->create([
            'user_id' => $this->otherChatParticipant->id,
            'group_id' => $this->group->id, 
            'last_message_seen_id' => null, 
            'participant_role' => ChatRole::PARTICIPANT,
        ]);

        $this->messages = ChatMessage::factory(20)->create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'text' => "Msg txt",
        ]);

        Auth::login($this->user);

        $this->withHeaders([ 'Accept' => 'application/json' ]);

        $this->getOneGroupEndpoint = "/api/chat/group/{$this->group->id}";
    }

    public function test_response_returns_requested_data()
    {
        $response = $this->get($this->getOneGroupEndpoint);

        $response->assertJson([
            'id' => $this->group->id,
            'name' => $this->group->name,
            'model_type' => $this->group->model_type,
            'created_at' => $this->group->created_at->jsonSerialize(),
            'updated_at' => $this->group->updated_at->jsonSerialize(),
            'participants' => [
                [
                    'id' => $this->user->id,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'email' => $this->user->email,
                    'image' => $this->user->image,
                    'thumbnail' => $this->user->thumbnail,
                    'email_verified_at' => $this->user->email_verified_at->jsonSerialize(),
                    'pivot' => 
                    [
                        'group_id' => $this->userPivot->group_id,
                        'user_id' => $this->userPivot->user_id,
                        'participant_role' => $this->userPivot->participant_role, 
                        "last_message_seen_id" => null,
                        // "updated_at" => $this->userPivot->updated_at->jsonSerialize(),

                    ],
                ],

                [
                    'id' => $this->otherChatParticipant->id,
                    'first_name' => $this->otherChatParticipant->first_name,
                    'last_name' => $this->otherChatParticipant->last_name,
                    'email' => $this->otherChatParticipant->email,
                    'image' => $this->otherChatParticipant->image,
                    'thumbnail' => $this->otherChatParticipant->thumbnail,
                    'email_verified_at' => $this->otherChatParticipant->email_verified_at->jsonSerialize(),
                    'pivot' => 
                    [
                        'group_id' => $this->otherUserPivot->group_id,
                        'user_id' => $this->otherUserPivot->user_id,
                        'participant_role' => $this->otherUserPivot->participant_role,
                        "last_message_seen_id" => null,
                        // "updated_at" => $this->otherUserPivot->updated_at->jsonSerialize(),
                    ],
                ],
            ],

        ]);
    }

    public function test_not_accessible_by_guest()
    {
        Auth::logout($this->user);

        $response = $this->get($this->getOneGroupEndpoint);

        $response->assertJson(["message" => __("Unauthenticated.")]);
    }

    public function test_not_accessible_by_non_participant()
    {
        $user1 = User::factory()->create();
        Auth::logout($this->user);
        Auth::login($user1);

        $response = $this->get($this->getOneGroupEndpoint);

        $response->assertStatus(403);
    }

    public function test_accessible_by_authenticated_chat_participant()
    {
        $response = $this->get($this->getOneGroupEndpoint);

        $response->assertStatus(200);
    }

    public function test_group_doesnt_exist()
    {
        $response = $this->get("/api/chat/group/34789");

        $response->assertJson(['errors' => __("Group doesn't exist.")]);
    }

    public function test_get_refreshed_group()
    {
        $response = $this->get("/api/chat/group/refresh/{$this->group->id}");

        $response->assertOk(); // TODO
    }
}
