<?php

namespace Tests\Feature\Chat\Group;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\ChatGroup;
use App\Models\ParticipantPivot;

class GetGroupTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherChatParticipant = User::factory()->create();
        $this->group = ChatGroup::factory()->create([
            'model_type' => ChatGroup::MODEL_TYPE_DEFAULT,
        ]);

        $this->userPivot = ParticipantPivot::factory()->create([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id, 
            'last_message_seen_id' => null, 
            'participant_role' => ParticipantPivot::ROLE_PARTICIPANT,
        ]);

        $this->otherUserPivot = ParticipantPivot::factory()->create([
            'user_id' => $this->otherChatParticipant->id,
            'group_id' => $this->group->id, 
            'last_message_seen_id' => null, 
            'participant_role' => ParticipantPivot::ROLE_PARTICIPANT,
        ]);

        

        $this->withHeaders([
            'Accept' => 'application/json',
        ]);

        $this->getOneGroupEndpoint = "/api/chat/group/{$this->group->id}";
    }

    public function test_response_returns_requested_data()
    {
        Auth::login($this->user);

        $response = $this->get($this->getOneGroupEndpoint);

        $response->assertJson([
            'id' => $this->group->id,
            'name' => $this->group->name,
            'model_type' => $this->group->model_type,
            // 'created_at' => $this->group->created_at,
            // 'updated_at' => $this->group->updated_at,
            'participants' => [
                [
                    'id' => $this->user->id,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'email' => $this->user->email,
                    'image' => $this->user->image,
                    'thumbnail' => $this->user->thumbnail,
                    // 'email_verified_at' => $this->user->email_verified_at,
                    'pivot' => 
                    [
                        'group_id' => $this->userPivot->group_id,
                        'user_id' => $this->userPivot->user_id,
                        'participant_role' => $this->userPivot->participant_role, 
                        "last_message_seen_id" => null,
                        // "updated_at" => $this->userPivot->updated_at,

                    ],
                ],

                [
                    'id' => $this->otherChatParticipant->id,
                    'first_name' => $this->otherChatParticipant->first_name,
                    'last_name' => $this->otherChatParticipant->last_name,
                    'email' => $this->otherChatParticipant->email,
                    'image' => $this->otherChatParticipant->image,
                    'thumbnail' => $this->otherChatParticipant->thumbnail,
                    // 'email_verified_at' => $this->otherChatParticipant->email_verified_at,
                    'pivot' => 
                    [
                        'group_id' => $this->otherUserPivot->group_id,
                        'user_id' => $this->otherUserPivot->user_id,
                        'participant_role' => $this->otherUserPivot->participant_role,
                        "last_message_seen_id" => null,
                        // "updated_at" => $this->otherUserPivot->updated_at,
                    ],
                ],
            ],

        ]);
    }

    public function test_not_accessible_by_guest()
    {
        $response = $this->get($this->getOneGroupEndpoint);
        // dd($response->content());
        $response->assertStatus(401);
    }

    public function test_not_accessible_by_non_participant()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->get($this->getOneGroupEndpoint);

        $response->assertStatus(403);
    }

    public function test_accessible_by_authenticated_chat_participant()
    {
        Auth::login($this->user);

        $response = $this->get($this->getOneGroupEndpoint);

        $response->assertStatus(200);
    }
}
