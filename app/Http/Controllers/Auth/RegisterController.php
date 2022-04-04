<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Traits\CreateValidationSlugTrait;
use App\Http\Traits\ProfilePictureTrait;
use App\Jobs\SendEmailJob;

use App\Models\AuthAttempts;
use App\Models\User;
use App\ChatApp\Repos\User\UserEloquentRepo;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterController extends Controller
{
    use ProfilePictureTrait;
    use CreateValidationSlugTrait;

    public function register(RegisterRequest $request, UserEloquentRepo $userRepo)
    {
        // Save profile pics and return their dir_name and name.ext
        $pathsToStoredProfilePics = $this->storeTrait($request);

        $user = $userRepo->create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'image'     => $pathsToStoredProfilePics['images'],
            'thumbnail' => $pathsToStoredProfilePics['thumbnails'],
        ]);

        $hash = Str::random(64);
        $dbHash = Hash::make($hash);

        AuthAttempts::create([
            'user_id' => $user->id,
            'hash' => $dbHash, 
            'type' => 'mail_validation'
        ]);

        $userData = [
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'url' => url("/mail-verification/uid/{$user->id}/h/{$hash}"),
        ];

        dispatch(new SendEmailJob($userData));

    }
}
