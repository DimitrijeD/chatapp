<?php

namespace Tests\Unit\Chat\Participants;

use PHPUnit\Framework\TestCase;
use App\Models\ChatGroup;
use App\Models\ChatRole;

class CanRoleRemoveAnotherRoleFromGroupTypeTest extends TestCase
{
    use RoleTestingTraits; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = "remove";
        $this->rules = ChatRole::ROLE_CAN_REMOVE_ROLE_FROM;
    }

    private function assertLeavesInNodeExist($roleMakingRequest, $level)
    {
        $aserted = false;

        foreach($level as $targetRole => $groupTypes){
            foreach($groupTypes as $groupType)
            {
                $aserted = true;
                $this->assertTrue(
                    ChatRole::can( [$roleMakingRequest, $targetRole, $groupType] , $this->action)
                );      
            }      
        }

        return $aserted;
    }

    public function test_creator_remove_role()
    {
        $this->doTest(ChatRole::CREATOR, $this->keyChecker($this->rules, ChatRole::CREATOR));
    }

    public function test_moderator_remove_role()
    {
        $this->doTest(ChatRole::MODERATOR, $this->keyChecker($this->rules, ChatRole::MODERATOR));
    }

    public function test_participant_remove_role()
    {
        $this->doTest(ChatRole::PARTICIPANT, $this->keyChecker($this->rules, ChatRole::PARTICIPANT));
    }

    public function test_listener_remove_role()
    {
        $this->doTest(ChatRole::LISTENER, $this->keyChecker($this->rules, ChatRole::LISTENER));
    }
}
