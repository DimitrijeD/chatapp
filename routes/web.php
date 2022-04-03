<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticationController;

Route::get('/test', [AuthenticationController::class, 'testEloquent']);
Route::get('/mail-verification/uid/{user_id}/h/{hash}', [AuthenticationController::class, 'checkSlugAuthenticity']);

Route::get('/{any}', function () {
    return view('root');
})->where('any', '.*');