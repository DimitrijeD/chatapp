<?php

namespace Tests\Feature\Chat\Message;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Chat\Participants\Add\InitGroup;

use Database\Seeders\ChatGroupClusterSeeder;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;

class GetEariestMessagesTest extends TestCase
{
    use RefreshDatabase, InitGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->numMessages = 100;
        $this->groupSetUp([ 'numMessages' => $this->numMessages, ], ChatRole::CREATOR);

        $this->allMessages = $this->allChatData['messages'];

        // next line is unnesecarry, but to make sure code works, its included 
        $this->numMessages = count($this->allMessages);
        
        Auth::login($this->user);
        $this->withHeaders([ 'Accept' => 'application/json', ]);
    }

    public function test_ok()
    {
        $this->earliest_msg_id_user_has = $this->allMessages[$this->numMessages - 1]->id; 
        $this->endpoint = "/api/chat/group/{$this->group->id}/before-msg/{$this->earliest_msg_id_user_has}";

        $response = $this->get($this->endpoint);

        $response->assertOk();
    }

    /**
     * This test assumes user has initial number of messages (ChatMessage::INIT_NUM_MESSAGES)
     * He scrolled to top of chat window, triggering this request.
     */
    public function test_fetches_messages_before_earliest_user_has()
    {
        $this->indexOfEarliestMsg = $this->numMessages - ChatMessage::INIT_NUM_MESSAGES;
        $this->earliest_msg_id_user_has = $this->allMessages[$this->indexOfEarliestMsg]->id; 
        $this->endpoint = "/api/chat/group/{$this->group->id}/before-msg/{$this->earliest_msg_id_user_has}";

        $response = $this->get($this->endpoint);

        $messages = json_decode($response->content());

        $indexOfFirstExpectedMsg = $this->indexOfEarliestMsg - ChatMessage::EARLIEST_NUM_MESSAGES;
        $indexOfLastExpectedMsg  = $this->indexOfEarliestMsg; 

        $this->assertTrue( $this->hasExpectedMessages($indexOfFirstExpectedMsg, $indexOfLastExpectedMsg, $messages) );
    }

    /**
     * This test assumes user has initial number of messages (ChatMessage::INIT_NUM_MESSAGES) and scrolled up once
     * 
     * After second scroll up, user will have many 50 messages, 
     * checks if another request will return correct 'message block',
     */
    public function test_fetches_messages_before_earliest_user_has_after_second_scroll_up()
    {
        $this->indexOfEarliestMsg = $this->numMessages - ChatMessage::INIT_NUM_MESSAGES - ChatMessage::EARLIEST_NUM_MESSAGES;
        $this->earliest_msg_id_user_has = $this->allMessages[$this->indexOfEarliestMsg]->id; 
        $this->endpoint = "/api/chat/group/{$this->group->id}/before-msg/{$this->earliest_msg_id_user_has}";

        $response = $this->get($this->endpoint);

        $messages = json_decode($response->content());

        $indexOfFirstExpectedMsg = $this->indexOfEarliestMsg - ChatMessage::EARLIEST_NUM_MESSAGES;
        $indexOfLastExpectedMsg  = $this->indexOfEarliestMsg; 

        $this->assertTrue( $this->hasExpectedMessages($indexOfFirstExpectedMsg, $indexOfLastExpectedMsg, $messages) );
    }

    /**
     * Not a test method, checks if returned messages block contains all expected messages
     */
    private function hasExpectedMessages($indexOfFirstExpectedMsg, $indexOfLastExpectedMsg, $messages)
    {
        $returnedExpectedMessages = true;

        for($i = $indexOfFirstExpectedMsg; $i < $indexOfLastExpectedMsg; $i++){
            $msgFound = false;

            //compare returned messages with ones reponse should return
            foreach($messages as $msg){
                if($this->allMessages[$i]->id == $msg->id){
                    $msgFound = true;
                    break;
                }
            }

            // If at least one message was not found, test failed ;( 
            if(!$msgFound){
                $returnedExpectedMessages = false; 
                break;
            }
        }

        return $returnedExpectedMessages;
    }
}
