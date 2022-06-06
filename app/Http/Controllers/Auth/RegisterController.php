<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Http\Traits\ProfilePictureTrait;
use App\Jobs\SendEmailJob;

use App\Models\User;
use App\ChatApp\Repos\User\UserEloquentRepo;
use App\Http\Requests\Auth\RegisterRequest;
use App\ChatApp\Repos\AccountVerification\EmailVerification;

class RegisterController extends Controller
{
    use ProfilePictureTrait;

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

        Auth::login($user);

        (new EmailVerification)->createOrUpdate($user->email, $user);
        
        return response()->json([
            'success' => __('Your account has been created and verification email has been sent. Check your inbox.'),
            'user' => $user
        ], 201);
    }
}
