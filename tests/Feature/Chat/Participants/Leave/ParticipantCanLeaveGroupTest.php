<?php

namespace Tests\Feature\Chat\Participants\Leave;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

use Database\Seeders\ChatGroupClusterSeeder;
use App\Models\User;

class ParticipantCanLeaveGroupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chatGroupSeeder = resolve(ChatGroupClusterSeeder::class);
        $this->groupData = $this->chatGroupSeeder->run();

        $this->group = $this->groupData['group'];
        $this->participants = $this->groupData['users'];
        
        $this->participant = $this->participants[1]; 

        // user leaving group
        $this->user = $this->participants->first();
        Auth::login($this->user);
        $this->withHeaders([ 'Accept' => 'application/json', ]);

        $this->endpoint = "/api/chat/group/{$this->group->id}/leave";
    }

    public function test_successfully_left_group()
    {
        $response = $this->get($this->endpoint);

        $response->assertJson(['success' => __('You have left this group.')]);
    }

    public function test_pivot_is_removed()
    {
        $response = $this->get($this->endpoint);

        $this->assertDatabaseMissing('group_participants', [
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
        ]);
    }

    public function test_user_is_not_removed()
    {
        $this->get($this->endpoint);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
        ]);
    }

    public function test_group_is_not_removed()
    {
        $this->get($this->endpoint);

        $this->assertDatabaseHas('chat_groups', [
            'id' => $this->group->id,
        ]);
    }

    public function test_non_participant_cannot_leave_group()
    {
        Auth::logout($this->user);
        $nonParticipant = User::factory()->create();  
        Auth::login($nonParticipant);

        $response = $this->get($this->endpoint);

        $response->assertJson([
            'errors' => __("You have no access rights to this chat group.")
        ]);
    }

    public function test_guest_cannot_leave_group()
    {
        Auth::logout($this->user);

        $response = $this->get($this->endpoint);

        $response->assertJson([
            'message' => __("Unauthenticated.")
        ]);
    }


}
