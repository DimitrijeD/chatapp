<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;


Route::get('/test-get-from-response', [TestController::class, 'createResponseFromGet']);
Route::get('/test-post', [TestController::class, 'createPost']);
Route::get('/test-get-route-list', [TestController::class, 'getRouteListLoop']);

Route::get('/{any}', function () {
    return view('root');
})->where('any', '.*');