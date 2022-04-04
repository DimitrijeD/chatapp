<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmailController;
use App\Http\Controllers\Chat\GroupController;
use App\Http\Controllers\Chat\MessageController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AuthenticationController;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login',  [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::post('/mail-verification',         [EmailController::class, 'createEmailVerification']);
Route::post('/mail-verification-resend',  [EmailController::class, 'resendEmailVerification']);
Route::post('/mail-verification-clicked', [EmailController::class, 'checkSlugAuthenticity']);

Route::get('/authenticated', [AuthenticationController::class, 'isAuthenticated']);
Route::get('/user-loggedin', [AuthenticationController::class, 'isLoggedIn']);

Route::get('/chat/group/{groupId}/messages', [MessageController::class, 'getAllMessages']);
Route::get('/chat/group/{groupId}/from-msg/{latestMsg}', [MessageController::class, 'getMissingMessages']);

Route::post('/chat/message/store', [MessageController::class, 'store']);
Route::post('/chat/messages-seen', [MessageController::class, 'messageIsSeen']);
Route::get('/chat/getAllUsersExceptSelf', [MessageController::class, 'getAllUsersExceptSelf']);

Route::get('/chat/groups-by-user', [GroupController::class, 'getGroupsByUser']);
Route::get('/chat/users-by-groups/{groupId}', [GroupController::class, 'getUsersByGroup']);
Route::post('/chat/group/new', [GroupController::class, 'store']);

Route::get('/chat/groups-by-user-with-participants', [GroupController::class, 'getGroupsByUserWithParticipants']);
Route::get('/chat/groups-by-user-without-self', [GroupController::class, 'getGroupsByUserWithoutSelf']);
Route::get('/chat/groups-by-user-without-self-v2', [GroupController::class, 'getGroupsByUserWithoutSelf_v2']);
Route::get('/chat/group/with-participants/{id}', [GroupController::class, 'getGroupById_WithParticipants']);
Route::get('/chat/group/without-self/{id}', [GroupController::class, 'getGroupById_WithoutSelf']);

Route::get('/all-unseen-states', [GroupController::class, 'getAllUnseenStates']);

// Route::get('/chat/group/{id}', [GroupController::class, 'getGroupById']);
