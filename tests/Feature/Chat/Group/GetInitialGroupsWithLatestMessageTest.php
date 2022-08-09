<?php

namespace Tests\Feature\Chat\Group;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Database\Seeders\ChatGroupClusterSeeder;

use App\Models\User;
use App\Models\ChatGroup;
use App\Models\ParticipantPivot;

class GetInitialGroupsWithLatestMessageTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();

        $chatGroupSeeder = resolve(ChatGroupClusterSeeder::class);
        $this->groups = [];

        for($i = 0; $i < 3; $i++){
            $this->groups[] = $chatGroupSeeder->run();
        }

        if(!$this->user = User::where(['email' => 'qwe@qwe'])->first())
            $this->markTestIncomplete("Cannot finish this test because target user doesn't exist");

        $this->withHeaders([ 'Accept' => 'application/json', ]);
        Auth::login($this->user);

        $this->getManyGroupsEndpoint = "/api/chat/user/groups";
    }

    public function test_gets_groups_with_latest_message()
    {
        $response = $this->get($this->getManyGroupsEndpoint);

        $response->assertJsonStructure([[
            'id', 
            'name', 
            'created_at', 
            'updated_at',

            'participants' => [[
                    'id', 
                    'first_name', 
                    'last_name', 
                    'email',
                    'image',
                    'thumbnail',
                    'pivot' => [
                        'group_id', 
                        'user_id', 
                        'last_message_seen_id', 
                        'participant_role', 
                        'updated_at'
                    ]
                ],
                // ...
            ],

            'last_message' => [
                'id',
                'group_id',
                'user_id',
                'text',
                'created_at',
                'updated_at',
                'user' => [
                    'id', 
                    'first_name', 
                    'last_name', 
                    'email',
                    'image',
                    'thumbnail',
                ]
            ]
        ]]);
    }
}
