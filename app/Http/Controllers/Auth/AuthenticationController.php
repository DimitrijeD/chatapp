<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthenticationController extends Controller
{
    /**
     * Return currently logged in user if he is logged in and has email verified. 
     */
    public function isAuthenticated(Request $request)
    {
        if($request->user() && $request->user()->email_verified_at){
            return $request->user();
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

}
