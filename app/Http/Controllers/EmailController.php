<?php

namespace App\Http\Controllers;

use App\Http\Traits\CreateValidationSlugTrait;
use App\Models\AuthAttempts;
use App\Models\User;
use App\ChatApp\Repos\User\UserEloquentRepo;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    
    use CreateValidationSlugTrait;

    // for initial registration email verification
    public function createEmailVerification(Request $request, UserEloquentRepo $userRepo)
    {
        $user = $userRepo->first(['email' => $request->email]);

        // find last/first auth attempt created after registration
        $authAttempt = AuthAttempts::
              where('type', 'mail_validation')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $userData = [
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'hashUrl' => $authAttempt->hashUrl,
        ];

        dispatch(new SendEmailJob($userData));
    }

    // for resending email verification
    public function resendEmailVerification(Request $request)
    {
        $user = $request->user();

        // Create new auth attempt
        $authAttempt = AuthAttempts::create([
            'user_id' => $user->id,
            'hashUrl' => $this->createValidationSlug($user),
            'type' => 'mail_validation'
        ]);

        $userData = [
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'hashUrl' => $authAttempt->hashUrl,
        ];

        dispatch(new SendEmailJob($userData));
    }
    
}
