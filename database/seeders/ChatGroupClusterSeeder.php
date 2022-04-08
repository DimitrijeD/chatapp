<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChatGroup;
use Database\Seeders\clusters\ConfigResolvers\TimeConfigResolver;
use Database\Seeders\clusters\ConfigResolvers\MessageConfigResolver;
use Database\Seeders\clusters\ConfigResolvers\LastMessageSeenConfigResolver;

use Database\Seeders\clusters\ModelBuilders\MessagesBuilder;
use Database\Seeders\clusters\ModelBuilders\ChatGroupBuilder;
use Database\Seeders\clusters\ModelBuilders\ChatMessageTextGenerator;
use Database\Seeders\clusters\ModelBuilders\GroupParticipantsPivot;
use Database\Seeders\clusters\ModelBuilders\BuildUsers;
use Database\Seeders\clusters\ModelBuilders\TimeInterval;

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

    /**
     * Define seeder's behaviour and type
     */
    private function seederConfig()
    {
        $numMinMsg = 5;
        $numMaxMsg = 60;
        $this->numMessages = rand($numMinMsg, $numMaxMsg);

        // OR CHOOSE how many messages should be created
        // $this->numMessages = 46;

        // CHOOSE range of message text length (min must be at least 10)
        $this->minTextLen = 10;
        $this->maxTextLen = 100;

        // CHOOSE one of DISTRIBUTION_TYPES for messages
        $this->msgType = self::DISTRIBUTION_DEFAULT;

        // CHOOSE one of DISTRIBUTION_TYPES for timeline of dates messages were sent
        $this->timeType = self::DISTRIBUTION_DEFAULT;

        // CHOOSE one of DISTRIBUTION_TYPES for when and what message each user saw last 
        $this->seenType = self::DISTRIBUTION_DEFAULT;

        // CHOOSE from which
        $minTime = [
            'year'  => 2022, 
            'month' => 3, 
            'day'   => 1, 
            'hour'  => 0,
        ];

        // CHOOSE to which date messages should be distributed in 
        $maxTime = [
            'year'  => 2022, 
            'month' => 4, 
            'day'   => 1, 
            'hour'  => 1,
        ];

        // OR CHOOSE default time by setting this var to TRUE, which is last 30 days
        $defaultTimeInterval = true;

        // CHOOSE number of chat group participants
        $this->numUsers = 2;

        // If count($participants) > $this->numUsers , seeder will prioritize $participants as users to create
        $participants = [
            [
                'first_name' => 'Qwe',
                'last_name' => 'Qwe',
                'email' => 'qwe@qwe', 
                'password' => 'qweqweqwe',
            ],
            [
                'first_name' => 'Asd',
                'last_name' => 'Asd',
                'email' => 'asd@asd', 
                'password' => 'qweqweqwe',
            ],
            [
                'first_name' => 'Wer',
                'last_name' => 'Wer',
                'email' => 'wer@wer', 
                'password' => 'qweqweqwe',
            ],
            [
                'first_name' => 'Ert',
                'last_name' => 'Ert',
                'email' => 'Ert@Ert', 
                'password' => 'qweqweqwe',
            ],
        ];   

        $this->users = (new BuildUsers($participants, $this->numUsers))
            ->resolve()
            ->build(); 

        $this->timeInterval = (new TimeInterval($minTime, $maxTime, $defaultTimeInterval))->createTimeInterval();

        // CHOOSE chat group data
        $chatGroupProps = [
            'name' => "Cluster seeded | {$this->msgType} msg type | {$this->timeType} time type | {$this->seenType} seen type ",
            'model_type' => ChatGroup::MODEL_TYPE_DEFAULT,
            'updated_at' => $this->timeInterval['minTime'],
            'created_at' => $this->timeInterval['minTime'],
        ];

        $this->chatGroup = (new ChatGroupBuilder($chatGroupProps))->makeModel();
        $this->messages = new MessagesBuilder($this->chatGroup->id);
        $this->pivot = (new GroupParticipantsPivot($this->users, $this->chatGroup))->build();
    }

    public function run()
    {
        if(!isset($this->massSetterCalled)){
            $this->seederConfig();
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

        return $this->chatGroup;
    }

    public function massSetter(
        $numMessages = 40, 
        $minTextLen = 10, 
        $maxTextLen = 100, 
        $msgType = self::DISTRIBUTION_DEFAULT, 
        $timeType = self::DISTRIBUTION_DEFAULT,
        $seenType = self::DISTRIBUTION_DEFAULT,
        $minTime_ = [
            'year'  => 2022, 
            'month' => 3, 
            'day'   => 1, 
            'hour'  => 0,
        ],
        $maxTime_ = [
            'year'  => 2022, 
            'month' => 4, 
            'day'   => 1, 
            'hour'  => 1,
        ],
        $defaultTimeInterval_ = false,
        $numUsers = 3,
        $participants_ = [
            [
                'first_name' => 'Qwe',
                'last_name' => 'Qwe',
                'email' => 'qwe@qwe', 
                'password' => 'qweqweqwe',
            ],
        ],
        $creator_email = 'qwe@qwe',
    ) {
        $this->massSetterCalled = true;

        $this->numMessages = $numMessages;

        // CHOOSE range of message text length (min must be at least 10)
        $this->minTextLen = $minTextLen;
        $this->maxTextLen = $maxTextLen;

        // CHOOSE one of DISTRIBUTION_TYPES for messages
        $this->msgType = $msgType;

        // CHOOSE one of DISTRIBUTION_TYPES for timeline of dates messages were sent
        $this->timeType = $timeType;

        // CHOOSE one of DISTRIBUTION_TYPES for when and what message each user saw last 
        $this->seenType = $seenType;

        // CHOOSE from which
        $minTime = $minTime_;

        // CHOOSE to which date messages should be distributed in 
        $maxTime = $maxTime_;

        // OR CHOOSE default time by setting this var to TRUE, which is last 30 days
        $defaultTimeInterval = $defaultTimeInterval_;

        // CHOOSE number of chat group participants
        $this->numUsers = $numUsers;

        $participants = $participants_;   

        $this->users = (new BuildUsers($participants, $this->numUsers))
            ->resolve()
            ->build(); 

        $this->timeInterval = (new TimeInterval($minTime, $maxTime, $defaultTimeInterval))->createTimeInterval();

        // CHOOSE chat group data
        $chatGroupProps = [
            'name' => "Cluster seeded | {$this->msgType} msg type | {$this->timeType} time type | {$this->seenType} seen type ",
            'model_type' => ChatGroup::MODEL_TYPE_DEFAULT,
            'updated_at' => $this->timeInterval['minTime'],
            'created_at' => $this->timeInterval['minTime'],
        ];

        $this->chatGroup = (new ChatGroupBuilder($chatGroupProps))->makeModel();
        $this->messages = new MessagesBuilder($this->chatGroup->id);
        $this->pivot = (new GroupParticipantsPivot($this->users, $this->chatGroup))->build();
    }

}