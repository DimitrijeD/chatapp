<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatGroup;
use App\Models\ChatMessage;

use Database\Seeders\clusters\ConfigResolvers\TimeConfigResolver;
use Database\Seeders\clusters\ConfigResolvers\MessageConfigResolver;
use Database\Seeders\clusters\ConfigResolvers\LastMessageSeenConfigResolver;

use Database\Seeders\clusters\ModelBuilders\MessagesBuilder;
use Database\Seeders\clusters\ModelBuilders\ChatGroupBuilder;
use Database\Seeders\clusters\ModelBuilders\ChatMessageTextGenerator;
use Database\Seeders\clusters\ModelBuilders\GroupParticipantsPivot;
use Database\Seeders\clusters\ModelBuilders\BuildUsers;
use Database\Seeders\clusters\ModelBuilders\TimeInterval;

use Database\Seeders\clusters\Init\GroupCreatorId;
use Database\Seeders\clusters\Init\NumOfMessages;
use Database\Seeders\clusters\Init\TextLen;

class ChatGroupClusterSeeder extends Seeder
{
    const DISTRIBUTION_MAX_ACTIVITY = 'MAX-ACTIVITY';
    const DISTRIBUTION_DEFAULT = 'DEFAULT';
    const DISTRIBUTION_RANDOM = 'RANDOM';
    const DISTRIBUTION_EVEN = 'EVEN';
    
    const DISTRIBUTION_TYPES = [
        self::DISTRIBUTION_MAX_ACTIVITY,
        self::DISTRIBUTION_DEFAULT,
        self::DISTRIBUTION_RANDOM,
        self::DISTRIBUTION_EVEN,
    ]; 

    const MIN_TEXT_LEN = 10;
    const MAX_TEXT_LEN = 200;

    const MIN_NUM_MESSAGES = 10;
    const MAX_NUM_MESSAGES = 100;

    /**
     * Define seeder's behaviour and type
     */
    private function defaultSeederConfig()
    {
        $this->numMessages = (new NumOfMessages())->get();

        $this->minTextLen = self::MIN_TEXT_LEN;
        $this->maxTextLen = self::MAX_TEXT_LEN;

        $this->msgType = self::DISTRIBUTION_DEFAULT;
        $this->timeType = self::DISTRIBUTION_DEFAULT;
        $this->seenType = self::DISTRIBUTION_DEFAULT;


        $this->users = (new BuildUsers([], null))
            ->resolve()
            ->build(); 

        $this->creator_id = (new GroupCreatorId($this->users, 'qwe@qwe'))->get();

        $this->timeInterval = (new TimeInterval(null, null, true))->createTimeInterval();

        $this->chatGroup = (new ChatGroupBuilder([
            'name' => "Cluster seeded | {$this->msgType} msg type | {$this->timeType} time type | {$this->seenType} seen type ",
            'model_type' => ChatGroup::MODEL_TYPE_DEFAULT,
            'updated_at' => $this->timeInterval['minTime'],
            'created_at' => $this->timeInterval['minTime'],
        ]))->makeModel();
        $this->messages = new MessagesBuilder($this->chatGroup->id);
        $this->pivot = (new GroupParticipantsPivot($this->users, $this->chatGroup, $this->creator_id))->build();
    }

    public function run()
    {
        if(!isset($this->massSetterCalled)){
            $this->defaultSeederConfig();
        } 
        $this->clusteredMessages = ( new MessageConfigResolver($this->users, $this->chatGroup->id, $this->msgType, $this->numMessages) )
            ->resolve()
            ->build();

        $this->messages->assembleMessageModels($this->clusteredMessages);

        $timeClusteredMessages = ( new TimeConfigResolver($this->clusteredMessages, $this->timeInterval, $this->timeType, $this->numMessages) )
            ->resolve()
            ->build();

        $this->messages->fillAssembledMessageModels($timeClusteredMessages, ['created_at', 'updated_at']);
        $this->messages->fillAssembledMessageModels( (new ChatMessageTextGenerator($this->numMessages, $this->minTextLen, $this->maxTextLen))->build(), ['text'] );
        $this->messages->bulkCreateModels();

        $this->createdMessages = $this->messages->getChatGroupMessages();

        $lastMessageSeenUpdateData = (new LastMessageSeenConfigResolver($this->users, $this->chatGroup->id, $this->seenType, $this->createdMessages))
            ->resolve()
            ->build();

        $this->pivot->addLastMessageSeenId($lastMessageSeenUpdateData);

        $this->chatGroup->last_msg_id = (ChatMessage::where('group_id', $this->chatGroup->id)->latest()->first())->id;
        $this->chatGroup->save();

        return [
            'group' => $this->chatGroup,
            'messages' => $this->createdMessages,
            'users' => $this->users,
            'pivots' => $this->pivot->getAllGroupPivots()
        ];
    }

    public function massSetter($init) 
    {
        if(!$init) return;

        $this->massSetterCalled = true;

        $this->numMessages = (new NumOfMessages( isset($init['numMessages']) ? $init['numMessages'] : 0 ))->get();

        $this->minTextLen = isset($init['minTextLen']) && $init['minTextLen'] > self::MIN_TEXT_LEN 
            ? $init['minTextLen'] 
            : self::MIN_TEXT_LEN;

        $textLen = new TextLen(
            isset($init['minTextLen']) ? $init['minTextLen'] : 0, 
            isset($init['maxTextLen']) ? $init['maxTextLen'] : 0);

        $this->minTextLen = $textLen->getMin();
        $this->maxTextLen = $textLen->getMax();

        $this->msgType  = isset($init['msgType'])  ? $init['msgType']  : self::DISTRIBUTION_DEFAULT;
        $this->timeType = isset($init['timeType']) ? $init['timeType'] : self::DISTRIBUTION_DEFAULT;
        $this->seenType = isset($init['seenType']) ? $init['seenType'] : self::DISTRIBUTION_DEFAULT;

        $this->numUsers = isset($init['numUsers']) ? $init['numUsers'] : 0;

        $this->users = (new BuildUsers(
            isset($init['participants']) ? $init['participants'] : [], 
            $this->numUsers
        ))->resolve()->build(); 

        $this->creator_id = (new GroupCreatorId( 
            $this->users, 
            isset($init['creator_email']) ? $init['creator_email'] : '' )
        )->get();

        $this->timeInterval = (new TimeInterval(
            isset($init['minTime'])             ? $init['minTime']             : false,
            isset($init['maxTime'])             ? $init['maxTime']             : false,
            isset($init['defaultTimeInterval']) ? $init['defaultTimeInterval'] : null
        ))->createTimeInterval();

        $this->chatGroup = (new ChatGroupBuilder([
            'name' => "Cluster seeded | {$this->msgType} msg type | {$this->timeType} time type | {$this->seenType} seen type ",
            'model_type' => ChatGroup::MODEL_TYPE_DEFAULT,
            'updated_at' => $this->timeInterval['minTime'],
            'created_at' => $this->timeInterval['minTime'],
        ]))->makeModel();
        $this->messages  =  new MessagesBuilder($this->chatGroup->id);
        $this->pivot     = (new GroupParticipantsPivot($this->users, $this->chatGroup, $this->creator_id))->build();
    }

}
