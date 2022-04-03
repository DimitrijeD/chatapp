<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\ChatApp\Repos\User\UserEloquentRepo;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ChatMessage\ChatMessageEloquentRepo;
use Illuminate\Http\Request;
use App\Models\AuthAttempts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function testEloquent(UserEloquentRepo $userRepo, ChatGroupEloquentRepo $chatGroupRepo, ChatMessageEloquentRepo $chatMessageRepo)
    {
        $this->chatGroupRepo = $userRepo;
        // $userRepo->first(['id' => 2]);
        // $userRepo->update($userRepo->first(['id' => 2]), ['first_name' => 'name']);
        // dd($userRepo->first(['id' => 2]));

        // $chatGroup = $chatGroupRepo->first(['id' => 1]);
        // $chatGroup = $chatGroupRepo->find(1);
        // $chatGroup = $chatGroupRepo->first(['id' => 1], ['participants']);
        // $chatGroup = $chatGroupRepo->getMany([], ['participants', 'messages']);
        // dd($chatGroup);

        // $messages = $chatMessageRepo->get(['id'=>'50'], ['user']);
        // $chatGroup = $chatGroupRepo->first(['id'=>1]);

        // dd($chatGroup->getSeenStates($chatGroup));
        // dd( ($chatGroupRepo->find(1))->participants);
        // $t = auth()->user()
        //     ->groups()
        //     ->with('participants')
        //     ->orderBy('updated_at', 'desc')
        //     ->get();
        // dd($t);

        // dd($messageRepo->getMany(['chat_group_id' => 1], ['user'], null));

        // dd($chatMessageRepo->getMissingMessages(1, 65));

        // dd($this->chatGroupRepo->update($this->chatGroupRepo->find(1), ['updated_at' => date('Y-m-d H:i:s')]));


    }

    public function checkSlugAuthenticity($user_id, $hash, UserEloquentRepo $userRepo)
    {
        $user = $userRepo->find($user_id);

        if(!$user){
            return response()->json([
                'error' => __("Account doesn't exist."),
            ]);
        }

        $authAttempts = AuthAttempts::
              where('user_id', $user->id)
            ->where('type', 'mail_validation')
            ->get();

        if(count($authAttempts) == 0 && $user->email_verified_at){
            return response()->json([
                'success' => __("You are verified."),
            ]);
        }

        foreach($authAttempts as $attempt){
            if( Hash::check($hash, $attempt->hashUrl)){

                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->save();

                $authAttempts->delete();
                
                Auth::login($user);

                return response()->json([
                    'success' => __("Successful authentication."),
                    'user' => $user,
                ]);
            }
        }

        // incorrect $attempt->hashUrl received or brute force attacks.
    }

}
