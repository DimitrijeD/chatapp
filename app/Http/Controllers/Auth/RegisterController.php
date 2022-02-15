<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Http\Traits\CreateValidationSlugTrait;
use App\Http\Traits\ProfilePictureTrait;

use App\Models\AuthAttempts;
use App\Models\User;

class RegisterController extends Controller
{
    use ProfilePictureTrait;
    use CreateValidationSlugTrait;

    public function register(Request $request)
    {
        $request->validate([
            'firstName'      => ['required', 'min:3', 'max:255'],
            'lastName'       => ['required', 'min:3', 'max:255'],
            'email'          => ['required', 'min:3', 'max:255', 'email', 'unique:users'],
            'password'       => ['required', 'min:6', 'max:255', 'confirmed'],
            'profilePicture' => ['required', 'file', 'image', 'max:5120'],
        ]);

        // Save profile pics and return their dir_name and name.ext
        $pathsToStoredProfilePics = $this->storeTrait($request);

        $user = User::create([
            'firstName' => $request->firstName,
            'lastName'  => $request->lastName,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'image'     => $pathsToStoredProfilePics['images'],
            'thumbnail' => $pathsToStoredProfilePics['thumbnails'],
        ]);

        AuthAttempts::create([
            'user_id' => $user->id,
            'hashUrl' => $this->createValidationSlug($user),
            'type' => 'mail_validation'
        ]);

        Auth::login($user);
    }
}

