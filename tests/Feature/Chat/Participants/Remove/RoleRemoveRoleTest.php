<?php

namespace Tests\Feature\Chat\Participants\Remove;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Tests\Feature\Chat\Participants\Add\InitGroup;
use App\Models\ChatGroup;

class RoleRemoveRoleTest extends TestCase
{
    use RefreshDatabase, InitGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupSetUp(['model_type' => ChatGroup::TYPE_PUBLIC_OPEN]);

        if(!$this->userToRemove = $this->group->participants->where('id', "!=", $this->user->id)->first())
            $this->markTestIncomplete("Cannot finish this test because user to be removed, doesn't exist");

    }

    public function test_creator_remove_participant_from_open_group()
    {
        // check if participant exists 
        $this->assertDatabaseHas('group_participants', [
            'user_id' => $this->userToRemove->id,
            'group_id' => $this->group->id,
        ]);

        $response = $this->get("/api/chat/group/{$this->group->id}/remove/{$this->userToRemove->id}");

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
