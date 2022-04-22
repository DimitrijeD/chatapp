<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmailController;

use App\Http\Controllers\Chat\GroupController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\Chat\ParticipantsController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\AccountVerificationController;

//-----------------------------Auth-----------------------------//
Route::post('/register', [RegisterController::class, 'register'])->middleware(['throttle:10,1', 'guest']);
Route::post('/login', [LoginController::class, 'login'])->middleware(['throttle:10,1', 'guest']);
Route::get('/logout', [LoginController::class, 'logout'])->middleware(['auth']);

Route::post('/email-verification/create-or-update', [AccountVerificationController::class, 'createOrUpdateForEmail'])->middleware(['throttle:30,1', 'has_not_verified_email']); //, 'guest'
Route::get('/email-verification/uid/{user_id}/c/{code}', [AuthenticationController::class, 'emailVerificationAttempt'])->middleware(['throttle:50,1']);

Route::get('/authenticated', [AuthenticationController::class, 'isAuthenticated']);
Route::get('/user-loggedin', [AuthenticationController::class, 'isLoggedIn']);
//--------------------------------------------------------------//


//-----------------------------ChatMessage-----------------------------//
Route::get('/chat/group/{group_id}/messages', [MessageController::class, 'getAllMessages'])->middleware(['chat_group_access', 'auth']);
Route::get('/chat/group/{group_id}/from-msg/{latest_msg_id}', [MessageController::class, 'getMissingMessages'])->middleware(['chat_group_access', 'auth']);
Route::post('/chat/message/store', [MessageController::class, 'store'])->middleware(['chat_group_access','auth', 'can_chat']);
Route::post('/chat/message/seen', [MessageController::class, 'messageIsSeen'])->middleware(['chat_group_access', 'auth']);
//---------------------------------------------------------------------//


//-----------------------------ChatGroup-----------------------------//
Route::post('/chat/group/store', [GroupController::class, 'store'])->middleware(['auth']);
Route::get('/chat/user/groups', [GroupController::class, 'getGroupsByUser'])->middleware(['auth']);

Route::get('/chat/user/groups-with-participants', [GroupController::class, 'getGroupsByUserWithParticipants'])->middleware(['auth']);
// Route::get('/chat/user/groups-without-self', [GroupController::class, 'getGroupsByUserWithoutSelf'])->middleware(['auth']);
Route::get('/chat/group/{group_id}/with-participants', [GroupController::class, 'getGroupById_WithParticipants'])->middleware(['chat_group_access', 'auth']);
Route::get('/chat/group/{group_id}/without-self', [GroupController::class, 'getGroupById_WithoutSelf'])->middleware(['chat_group_access', 'auth']);
Route::get('/chat/group/{group_id}', [GroupController::class, 'getGroupById'])->middleware(['chat_group_access', 'auth']);
// Route::get('/chat/user/groups-without-self-v2', [GroupController::class, 'getGroupsByUserWithoutSelf_v2'])->middleware(['chat_group_access','auth']);
//-------------------------------------------------------------------//


//-----------------------------Participants-----------------------------// 
Route::get('/user/unseen-states', [ParticipantsController::class, 'getAllUnseenStates'])->middleware(['auth']);
Route::get('/chat/users/without-self', [ParticipantsController::class, 'getAllUsersExceptSelf'])->middleware(['auth']);
Route::get('/chat/group/{group_id}/users', [ParticipantsController::class, 'getUsersByGroup'])->middleware(['auth']);
//----------------------------------------------------------------------//