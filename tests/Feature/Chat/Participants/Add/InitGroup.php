<?php

namespace Tests\Feature\Chat\Participants\Add;

use Illuminate\Support\Facades\Auth;
use Database\Seeders\ChatGroupClusterSeeder;
use App\Models\User;
use App\Models\ChatGroup;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;

/**
 * Helper trait for creating specific groups in tests such as:
 *      User which is making request has role which is defined in test,
 *      same goes for group type.
 * 
 */
trait InitGroup 
{
    private function groupSetUp($groupConfig, $requestInitiatorRole)
    {
        $this->chatGroupSeeder = (resolve(ChatGroupClusterSeeder::class));
        $this->chatGroupSeeder->massSetter($groupConfig);
        $this->allChatData = $this->chatGroupSeeder->run();

        $this->user = null;

        foreach($this->allChatData['users'] as $user){
            if($user->participant_role == $requestInitiatorRole){
                $this->user = $user;
                break;
            }
        }

        if($this->user == null)
            dd("fatal, add participants test, user with role {$requestInitiatorRole} wasn't created but is required for testing :/ .");
       
        $this->group = $this->allChatData['group'];

        Auth::login($this->user);
        $this->withHeaders([ 'Accept' => 'application/json', ]);

        $this->addUsersEndpoint = "/api/chat/group/{$this->group->id}/add-users";
    }

    private function payloadSetUp($numUsersToAdd, $forRole)
    {
        $usersToAdd = [];

        for($i = 0; $i < $numUsersToAdd; $i++){
            $usersToAdd[] = [
                'user_id' => User::factory()->create()->id,
                'target_role' => $forRole 
            ];
        }

        return [
            'group_id' => $this->group->id,
            'usersToAdd' => $usersToAdd
        ];
    }

    private function getModeratorGroupConfig()
    {
        return [
            'participants' => [
                [
                    'first_name' => 'Qwe',
                    'last_name' => 'Qwe',
                    'email' => 'qwe@qwe',
                    'participant_role' => ChatRole::CREATOR
                ],

                [
                    'first_name' => 'Asd',
                    'last_name' => 'Asd',
                    'email' => 'asd@asd',
                    'participant_role' => ChatRole::MODERATOR
                ],
            ]
        ];
    }

    private function getParticipantGroupConfig()
    {
        return [
            'participants' => [
                [
                    'first_name' => 'Qwe',
                    'last_name' => 'Qwe',
                    'email' => 'qwe@qwe',
                    'participant_role' => ChatRole::CREATOR
                ],

                [
                    'first_name' => 'Asd',
                    'last_name' => 'Asd',
                    'email' => 'asd@asd',
                    'participant_role' => ChatRole::PARTICIPANT
                ],
            ]
        ];
    }

    private function getListenerGroupConfig()
    {
        return [
            'participants' => [
                [
                    'first_name' => 'Qwe',
                    'last_name' => 'Qwe',
                    'email' => 'qwe@qwe',
                    'participant_role' => ChatRole::CREATOR
                ],

                [
                    'first_name' => 'Asd',
                    'last_name' => 'Asd',
                    'email' => 'asd@asd',
                    'participant_role' => ChatRole::LISTENER
                ],
            ]
        ];
    }

}
