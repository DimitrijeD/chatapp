<?php

namespace Tests\Unit\Chat\Participants;

use PHPUnit\Framework\TestCase;
use App\Models\ChatGroup;
use App\Models\ChatRole;

class CanRoleDemoteAnotherRoleInGroupTypeTest extends TestCase
{
    use RoleTestingTraits; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->rules = ChatRole::ROLE_CAN_DEMOTE_ROLE_TO_ROLE_IN;
        $this->action = "demote";
    }

    // private function assertLeavesInNodeExist($roleMakingRequest, $level)
    // {
    //     $aserted = false;

    //     foreach($level as $currentRole => $demoteRoles){
    //         foreach($demoteRoles as $demoteRole => $groupTypes){
    //             foreach($groupTypes as $groupType){
    //                 $aserted = true;
    //                 $this->assertTrue(
    //                     ChatRole::can( [$roleMakingRequest, $currentRole, $demoteRole, $groupType] , $this->action)
    //                 );      
    //             }
    //         }      
    //     }

    //     return $aserted;
    // }

    // public function test_creator_demotes_role()
    // {
    //     $this->doTest(ChatRole::CREATOR, $this->keyChecker($this->rules, ChatRole::CREATOR));
    // }

    // public function test_moderator_demotes_role()
    // {
    //     $this->doTest(ChatRole::MODERATOR, $this->keyChecker($this->rules, ChatRole::MODERATOR));
    // }

    // public function test_participant_demotes_role()
    // {
    //     $this->doTest(ChatRole::PARTICIPANT, $this->keyChecker($this->rules, ChatRole::PARTICIPANT));
    // }

    // public function test_listener_demotes_role()
    // {
    //     $this->doTest(ChatRole::LISTENER, $this->keyChecker($this->rules, ChatRole::LISTENER));
    // }
}
