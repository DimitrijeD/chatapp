<?php

namespace Tests\Feature\Chat\Participants\Remove;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

use Tests\Feature\Chat\Participants\Add\InitGroup;
use Database\Seeders\ChatGroupClusterSeeder;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\ChatRole;
use App\Models\ParticipantPivot;

class RoleRemoveRoleTest extends TestCase
{
    use RefreshDatabase, InitGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requesterRole = ChatRole::CREATOR;
        $this->targetRole = ChatRole::MODERATOR;

        $this->groupSetUp(['model_type' => ChatGroup::TYPE_PUBLIC_OPEN], $this->requesterRole);

        // select first user which isnt main user
        foreach($this->group->participants as $participant){
            if($participant->id != $this->user->id){
                $this->userToRemove = $participant;
                break;
            } 
        }

        $this->endpoint = "/api/chat/group/{$this->group->id}/remove/{$this->userToRemove->id}";
    }

    public function test_creator_remove_participant_from_open_group()
    {
        // check if participant exists 
        $this->assertDatabaseHas('group_participants', [
            'user_id' => $this->userToRemove->id,
            'group_id' => $this->group->id,
        ]);

        $response = $this->get($this->endpoint);

        // after request finishes, check if that participant was deleted
        $this->assertDatabaseMissing('group_participants', [
            'user_id' => $this->userToRemove->id,
            'group_id' => $this->group->id,
        ]);

        // now make sure user wasn't deleted because if this happends, There is massive issue with code... :/
        $this->assertDatabaseHas('users', [
            'id' => $this->userToRemove->id,
        ]);
    }
}
