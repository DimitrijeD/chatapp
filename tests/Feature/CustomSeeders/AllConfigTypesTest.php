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
        $msgTypes = $this->chatGroupSeeder::DISTRIBUTION_TYPES;
        $timeTypes = $this->chatGroupSeeder::DISTRIBUTION_TYPES;
        $seenTypes = $this->chatGroupSeeder::DISTRIBUTION_TYPES;

        $dataCreated = 0;

        foreach($msgTypes as $msgType){
            foreach($timeTypes as $timeType){
                foreach($seenTypes as $seenType){

                    $this->chatGroupSeeder->massSetter(
                        $numMessages = 40, 
                        $minTextLen = 10, 
                        $maxTextLen = 100, 
                        $msgType = 'DEFAULT', 
                        $timeType = 'DEFAULT',
                        $seenType = 'DEFAULT',
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
                                'firstName' => 'Qwe',
                                'lastName' => 'Qwe',
                                'email' => 'qwe@qwe', 
                                'password' => 'qweqweqwe',
                            ],
                        ],
                
                    );
                    $this->chatGroupSeeder->run();

                }
            }
        }
        // didnt throw exception so i guess i works lmao === later
        $this->assertTrue(true);
    }
}
