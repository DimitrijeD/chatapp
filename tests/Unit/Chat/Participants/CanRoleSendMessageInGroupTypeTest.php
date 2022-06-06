<?php

namespace Tests\Unit\Chat\Participants;

use PHPUnit\Framework\TestCase;
use App\Models\ChatGroup;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;

class CanRoleSendMessageInGroupTypeTest extends TestCase
{
    use RoleTestingTraits; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = "send_message";
        $this->rules = ChatRole::ROLE_CAN_SEND_MESSAGE_IN;
    }

    private function assertLeavesInNodeExist($roleMakingRequest, $groups)
    {
        $aserted = false;

        foreach($groups as $groupType){
            $aserted = true;
            $this->assertTrue(
                ChatRole::can( [$roleMakingRequest, $groupType] , $this->action)
            );      
        }

        return $aserted;
    }

    public function test_creator_can_send_message()
    {
        $this->doTest(ChatRole::CREATOR, $this->keyChecker($this->rules, ChatRole::CREATOR));
    }

    public function test_moderator_can_send_message()
    {
        $this->doTest(ChatRole::MODERATOR, $this->keyChecker($this->rules, ChatRole::MODERATOR));
    }

    public function test_participant_can_send_message()
    {
        $this->doTest(ChatRole::PARTICIPANT, $this->keyChecker($this->rules, ChatRole::PARTICIPANT));
    }

    public function test_listener_can_send_message()
    {
        $this->doTest(ChatRole::LISTENER, $this->keyChecker($this->rules, ChatRole::LISTENER));
    }
}
