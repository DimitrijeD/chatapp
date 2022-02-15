<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmailController;
use App\Http\Controllers\ChatGroupController;
use App\Http\Controllers\ChatController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AuthenticationController;


Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login',  [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::post('/mail-verification',         [EmailController::class, 'createEmailVerification']);
Route::post('/mail-verification-resend',  [EmailController::class, 'resendEmailVerification']);
Route::post('/mail-verification-clicked', [EmailController::class, 'checkSlugAuthenticity']);

Route::get('/authenticated', [AuthenticationController::class, 'isAuthenticated']);
Route::get('/user-loggedin', [AuthenticationController::class, 'isLoggedIn']);

Route::get('/chat/group/{groupId}/get-all-messages',   [ChatController::class, 'getAllMessages']);
Route::post('/chat/group/{groupId}/message',           [ChatController::class, 'saveNewMessage']);
Route::post('/chat/getAllUsersExceptSelf',             [ChatController::class, 'getAllUsersExceptSelf']);
Route::post('/chat/messages-seen',                     [ChatController::class, 'messageIsSeen']);

Route::get('/chat/groups-by-user',                     [ChatGroupController::class, 'getGroupsByUser']);
Route::get('/chat/users-by-groups/{groupId}',          [ChatGroupController::class, 'getUsersByGroup']);
Route::post('/chat/group/new',                         [ChatGroupController::class, 'store']);

Route::get('/chat/groups-by-user-with-participants',   [ChatGroupController::class, 'getGroupsByUserWithParticipants']);

// ----------------------------------------------------------------------------------------------------------------------
Route::get('/chat/groups-by-user-without-self',        [ChatGroupController::class, 'getGroupsByUserWithoutSelf']);
Route::get('/chat/groups-by-user-without-self-v2',     [ChatGroupController::class, 'getGroupsByUserWithoutSelf_v2']);
// ----------------------------------------------------------------------------------------------------------------------

Route::post('/chat/group/{groupId}',                   [ChatGroupController::class, 'getGroupById']);
Route::post('/chat/group-with-participants/{groupId}', [ChatGroupController::class, 'getGroupById_WithParticipants']);
Route::post('/chat/group-without-self/{groupId}',      [ChatGroupController::class, 'getGroupById_WithoutSelf']);
Route::get('/all-unseen-states',                       [ChatGroupController::class, 'getAllUnseenStates']);


