<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\ChatApp\Repos\User\UserEloquentRepo;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ChatMessage\ChatMessageEloquentRepo;


class AuthenticationController extends Controller
{
    /**
     * Return currently logged in user if he is logged in and has email verified. 
     */
    public function isAuthenticated()
    {
        $user = auth()->user();
        if($user && $user->email_verified_at){
            return $user;
        }
        return false;
    }

    public function isLoggedIn()
    {
        if(auth()->user()){
            return true;
        }
        return false;
    }

    public function testEloquent(UserEloquentRepo $userRepo, ChatGroupEloquentRepo $chatGroupRepo, ChatMessageEloquentRepo $messageRepo)
    {
        // $userRepo->first(['id' => 2]);
        // $userRepo->update($userRepo->first(['id' => 2]), ['first_name' => 'name']);
        // dd($userRepo->first(['id' => 2]));

        // $chatGroup = $chatGroupRepo->first(['id' => 1]);
        // $chatGroup = $chatGroupRepo->find(1);
        // $chatGroup = $chatGroupRepo->first(['id' => 1], ['participants']);
        // $chatGroup = $chatGroupRepo->getMany([], ['participants', 'messages']);
        // dd($chatGroup);

        // $messages = $messageRepo->get(['id'=>'50'], ['user']);
        // $chatGroup = $chatGroupRepo->first(['id'=>1]);

        // dd($chatGroup->getSeenStates($chatGroup));
    }

}
