<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('app'));

Route::get('/messages', [MessageController::class, 'index']);
Route::post('/messages', [MessageController::class, 'store']);
Route::post('/typing', function () {
    event(new \App\Events\UserTyping(request('name')));
    return response()->noContent();
});
