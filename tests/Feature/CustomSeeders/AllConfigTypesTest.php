<?php

namespace Tests\Feature\CustomSeeders;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\ChatGroupClusterSeeder;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;

class AllConfigTypesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chatGroupSeeder = (resolve(ChatGroupClusterSeeder::class));
    }

    public function test_all_types_create_valid_chat()
    {
        $this->markTestSkipped('');

        foreach(ChatGroupClusterSeeder::DISTRIBUTION_TYPES as $msgType){
            foreach(ChatGroupClusterSeeder::DISTRIBUTION_TYPES as $timeType){
                foreach(ChatGroupClusterSeeder::DISTRIBUTION_TYPES as $seenType){

                    $this->chatGroupSeeder->massSetter([
                        'numUsers' => 3,
                        'maxTextLen' => 20,
                        'minTime' => [
                            'year'  => 2022, 
                            'month' => 3, 
                            'day'   => 1, 
                            'hour'  => 0,
                        ],
                        'maxTime' => [
                            'year'  => 2022, 
                            'month' => 4, 
                            'day'   => 1, 
                            'hour'  => 1,
                        ],
                        'numMessages' => 20
                    ]);

                    $this->chatGroupSeeder->run();

                }
            }
        }

        $this->markTestIncomplete('Check if created chats match given parameters');

    }
}
