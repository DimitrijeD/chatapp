<?php

namespace App\ChatApp\Repos\AccountVerification;

use App\ChatApp\Repos\User\UserEloquentRepo;
use App\ChatApp\Repos\AccountVerification\AccountVerificationEloquentRepo;
use App\Models\AccountVerification;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class EmailVerification
{
    public function __construct()
    {
        $this->userRepo = new UserEloquentRepo; 
        $this->accountVerificationRepo = new AccountVerificationEloquentRepo; 
    }

    public function createOrUpdate($email, User $user = null)
    {
        if(!$user){
            if(!$user = $this->userRepo->first(['email' => $email])){
                return response()->json(['errors' => __("User doesn't exist.")]);
            }
        }

        $accoutVerification = $user->account_verification;

        if(!$accoutVerification && $user->email_verified_at){
            return response()->json(['success' => __('You are verified')]);
        }

        if($accoutVerification && $user->email_verified_at){
            $accoutVerification->delete();
            return response()->json(['success' => __('You are verified')]);
        }

        $code = Str::random(AccountVerification::EMAIL_HASH_LENGTH);

        $verification = $this->accountVerificationRepo->updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => AccountVerification::EMAIL_TYPE,
            ],
            [
                'code' => Hash::make($code),
                'num_of_attempts' => $accoutVerification ? ($accoutVerification->num_of_attempts + 1) : 1
            ]
        );

        $emailData = [
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'url' => $this->makeUrlFromCode($user->id, $code)
        ];

        dispatch(new SendEmailJob($emailData));
    }

    public function makeUrlFromCode($user_id, $code)
    {
        return url("/email-verification/uid/{$user_id}/c/{$code}");
    }

    public function attempt(User $user, $code)
    {
        $accoutVerification = $user->account_verification;

        if($accoutVerification && $user->email_verified_at){
            $accoutVerification->delete();
            return 'already_verified';
        }
        
        if(!$accoutVerification && !$user->email_verified_at)
            return 'not_verified_no_verification';

        if(!$accoutVerification && $user->email_verified_at)
            return 'already_verified';

        if( Hash::check($code, $accoutVerification->code) ){
            $accoutVerification->delete();
            $this->userRepo->update($user, ['email_verified_at' => now()]);

            return 'success';
        }

        $verification = $this->accountVerificationRepo->update($accoutVerification,['num_of_attempts' => $accoutVerification->num_of_attempts + 1]);

        return '404';
    }
}