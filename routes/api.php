<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmailController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AuthenticationController;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);


Route::post('/mail-verification', [EmailController::class, 'createEmailVerification']);
Route::post('/mail-verification-resend', [EmailController::class, 'resendEmailVerification']);
Route::post('/mail-verification-clicked', [EmailController::class, 'checkSlugAuthenticity']);

Route::get('/authenticated', [AuthenticationController::class, 'isAuthenticated']);
Route::get('/user-loggedin', [AuthenticationController::class, 'isLoggedIn']);


