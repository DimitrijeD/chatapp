<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        if( Auth::attempt($request->only('email', 'password')) ){
            return response()->json(Auth::user(), 200);
        }
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.']
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
 
        $request->session()->regenerateToken();
     
        return redirect('/');
    }
}
