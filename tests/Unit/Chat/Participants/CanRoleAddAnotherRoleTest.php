<?php

namespace Tests\Unit\Chat\Participants;

use PHPUnit\Framework\TestCase;
use App\Models\ChatGroup;
use App\Models\ChatRole;

/**
 * Every test asserts that 
 *      ChatRole:: ...RULES_ constants  
 *      and
 *      ChatRole::can method
 * 
 * map each other correctly, meaning if user can execute some 'action' on 'some other user with some role' in 'some group type',
 *      ChatRole::can returns true if conditions are met, allowing request to continue. 
 *      False otherwise.
 * 
 * Tests will fail if rule structure is changed but will not
 * if rules in arrays are reordered, deleted, added ...etc  
 */
class CanRoleAddAnotherRoleTest extends TestCase
{
    use RoleTestingTraits; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = "add";
        $this->rules = ChatRole::ROLE_CAN_ADD_ROLE_IN;
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

    public function test_creator_add_role()
    {
        $this->doTest(ChatRole::CREATOR, $this->keyChecker($this->rules, ChatRole::CREATOR));
    }

    public function test_moderator_add_role()
    {
        $this->doTest(ChatRole::MODERATOR, $this->keyChecker($this->rules, ChatRole::MODERATOR));
    }

    public function test_participant_add_role()
    {
        $this->doTest(ChatRole::PARTICIPANT, $this->keyChecker($this->rules, ChatRole::PARTICIPANT));
    }

    public function test_listener_add_role()
    {
        $this->doTest(ChatRole::LISTENER, $this->keyChecker($this->rules, ChatRole::LISTENER));
    }

}
