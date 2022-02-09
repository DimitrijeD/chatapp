<?php

namespace App\Http\Controllers;

use App\Http\Traits\CreateValidationSlugTrait;
use App\Models\AuthAttempts;
use App\Models\User;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    use CreateValidationSlugTrait;

    // for initial registration email verification
    public function createEmailVerification(Request $request){
        $user = User::where('email', $request->email)->first();

        // find last/first auth attempt created after registration
        $authAttempt = AuthAttempts::
              where('type', 'mail_validation')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $userData = [
            'email' => $user->email,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'hashUrl' => $authAttempt->hashUrl,
        ];

        dispatch(new SendEmailJob($userData));
    }

    // for resending email verification
    public function resendEmailVerification(Request $request)
    {
        $user = User::find($request->user()->id);

        // Create new auth attempt
        $authAttempt = AuthAttempts::create([
            'user_id' => $user->id,
            'hashUrl' => $this->createValidationSlug($user),
            'type' => 'mail_validation'
        ]);

        $userData = [
            'email' => $user->email,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'hashUrl' => $authAttempt->hashUrl,
        ];

        dispatch(new SendEmailJob($userData));
    }

    public function checkSlugAuthenticity(Request $request){
        $arrEmailAndHash = explode('-', $request->slug);

        if(count($arrEmailAndHash) != 2){
            // There are more separators in email validation Url making it invalid.
            return 'Invalid link!';
        }

        $user = $request->user();

        // check if user exists
        if(!$user){
            // if user doesn't exist, some1 might be trying to brute force email validation
            return "User doesn't exist";
        }

        $numOfCurrentAuthAttempts = AuthAttempts::
              where('user_id', $user->id)
            ->where('type', 'mail_validation')
            ->count();

        if($numOfCurrentAuthAttempts == 0 && $user->email_verified_at){
            return "You are verified!";
        }

        $allUserAuthAttempts = AuthAttempts::
              where('user_id', $user->id)
            ->where('type', 'mail_validation')
            ->get();

        foreach($allUserAuthAttempts as $attempt){
            if($attempt->hashUrl == $request->slug && $attempt->type == 'mail_validation'){
                // user clicked on one of links sent via mail
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->save();

                // Delete all instances of mail validation attempts for this user
                AuthAttempts::
                      where('user_id', $user->id)
                    ->where('type', 'mail_validation')
                    ->delete();

                return "Successful email authentication";
            }
        }
    }
}
