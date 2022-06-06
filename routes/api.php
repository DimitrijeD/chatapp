<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Chat\GroupController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\Chat\ParticipantsController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\AccountVerificationController;

use App\Http\Controllers\UsersController;

//-----------------------------Auth-----------------------------//
Route::post('/register', [RegisterController::class, 'register'])->middleware(['throttle:10,1', 'guest']);
Route::post('/login', [LoginController::class, 'login'])->middleware(['throttle:10,1', 'guest']);
Route::get('/logout', [LoginController::class, 'logout'])->middleware(['auth']);

Route::post('/email-verification/create-or-update', [AccountVerificationController::class, 'createOrUpdateForEmail'])->middleware(['throttle:5,1', 'has_not_verified_email']);
Route::get('/email-verification/uid/{user_id}/c/{code}', [AuthenticationController::class, 'emailVerificationAttempt'])->middleware(['throttle:10,1']);

Route::get('/get-user', [AuthenticationController::class, 'isAuthenticated']);
//--------------------------------------------------------------//


//-----------------------------ChatMessage-----------------------------//
Route::get('/chat/group/{group_id}/before-msg/{earliest_msg_id}', [MessageController::class, 'getBeforeMessage'])->middleware(['chat_group_access', 'auth']);
Route::get('/chat/group/{group_id}/latest-messages',              [MessageController::class, 'getLatestMessages'])->middleware(['chat_group_access', 'auth']);
Route::get('/chat/group/{group_id}/from-msg/{latest_msg_id}',     [MessageController::class, 'getMissingMessages'])->middleware(['chat_group_access', 'auth']);

Route::post('/chat/message/store', [MessageController::class, 'store'])->middleware(['chat_group_access', 'auth', 'can_chat']);
Route::post('/chat/message/seen', [MessageController::class, 'messageIsSeen'])->middleware(['chat_group_access', 'auth']);
//---------------------------------------------------------------------//


//-----------------------------ChatGroup-----------------------------//
Route::post('/chat/group/store', [GroupController::class, 'store'])->middleware(['auth']);
Route::get('/chat/user/groups', [GroupController::class, 'getGroupsByUser'])->middleware(['auth']);
Route::get('/chat/group/{group_id}', [GroupController::class, 'getGroupById'])->middleware(['chat_group_access', 'auth']);
//-------------------------------------------------------------------//


//-----------------------------Participants-----------------------------// 
Route::get('/chat/users/without-self', [ParticipantsController::class, 'getAllUsersExceptSelf'])->middleware(['auth']);
Route::get('/chat/group/{group_id}/users', [ParticipantsController::class, 'getUsersByGroup'])->middleware(['auth']);
Route::post('/chat/group/{group_id}/add-users', [ParticipantsController::class, 'addUsersToGroup'])->middleware(['auth', 'chat_group_access']);
Route::get('/chat/group/{group_id}/leave', [ParticipantsController::class, 'leaveGroup'])->middleware(['auth', 'chat_group_access']);
Route::get('/chat/group/{group_id}/remove/{user_id_to_remove}', [ParticipantsController::class, 'removeUserFromGroup'])->middleware(['auth', 'chat_group_access']);
//----------------------------------------------------------------------//


//--------------------------------Users---------------------------------// 
Route::post('/users/search', [UsersController::class, 'getMissingUsers'])->middleware(['auth']);
//----------------------------------------------------------------------//
