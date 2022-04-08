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

//-----------------------------Auth-----------------------------//
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::post('/mail-verification', [EmailController::class, 'createEmailVerification']);
Route::post('/mail-verification-resend', [EmailController::class, 'resendEmailVerification']);
Route::post('/mail-verification-clicked', [EmailController::class, 'checkSlugAuthenticity']);

Route::get('/authenticated', [AuthenticationController::class, 'isAuthenticated']);
Route::get('/user-loggedin', [AuthenticationController::class, 'isLoggedIn']);
//--------------------------------------------------------------//


//-----------------------------ChatMessage-----------------------------//
Route::get('/chat/group/{group_id}/messages', [MessageController::class, 'getAllMessages'])->middleware(['chat_group_access', 'auth']);
Route::get('/chat/group/{group_id}/from-msg/{latestMsg}', [MessageController::class, 'getMissingMessages'])->middleware(['chat_group_access', 'auth']);
Route::post('/chat/message/store', [MessageController::class, 'store'])->middleware(['chat_group_access','auth', 'can_chat']);
Route::post('/chat/message/seen', [MessageController::class, 'messageIsSeen'])->middleware(['chat_group_access', 'auth']);
//---------------------------------------------------------------------//


//-----------------------------ChatGroup-----------------------------//
Route::get('/chat/user/groups', [GroupController::class, 'getGroupsByUser'])->middleware(['auth']);
Route::post('/chat/group/store', [GroupController::class, 'store'])->middleware(['auth']);

Route::get('/chat/user/groups-with-participants', [GroupController::class, 'getGroupsByUserWithParticipants'])->middleware(['auth']);
Route::get('/chat/user/groups-without-self', [GroupController::class, 'getGroupsByUserWithoutSelf'])->middleware(['auth']);
Route::get('/chat/group/{group_id}/with-participants', [GroupController::class, 'getGroupById_WithParticipants'])->middleware(['chat_group_access', 'auth']);
Route::get('/chat/group/{group_id}/without-self', [GroupController::class, 'getGroupById_WithoutSelf'])->middleware(['chat_group_access', 'auth']);
// Route::get('/chat/group/{group_id}', [GroupController::class, 'getGroupById'])->middleware(['auth']);
// Route::get('/chat/user/groups-without-self-v2', [GroupController::class, 'getGroupsByUserWithoutSelf_v2'])->middleware(['auth']);
//-------------------------------------------------------------------//


//-----------------------------Participants-----------------------------// 
Route::get('/user/unseen-states', [ParticipantsController::class, 'getAllUnseenStates'])->middleware(['auth']);
Route::get('/chat/users/without-self', [ParticipantsController::class, 'getAllUsersExceptSelf'])->middleware(['auth']);
Route::get('/chat/group/{group_id}/users', [ParticipantsController::class, 'getUsersByGroup'])->middleware(['auth']);
//----------------------------------------------------------------------//