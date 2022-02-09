<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function isAuthenticated(Request $request)
    {
        if($request->user()->email_verified_at){
            return true;
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
