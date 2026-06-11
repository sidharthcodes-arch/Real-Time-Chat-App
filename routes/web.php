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
Route::post('/join', function () {
    $name = request('name');
    $users = cache()->get('online_users', []);
    $users[$name] = $name;
    cache()->put('online_users', $users, now()->addMinutes(5));
    event(new \App\Events\UsersOnlineUpdated(array_values($users)));
    return response()->noContent();
});

Route::post('/leave', function () {
    $name = request('name');
    $users = cache()->get('online_users', []);
    unset($users[$name]);
    cache()->put('online_users', $users, now()->addMinutes(5));
    event(new \App\Events\UsersOnlineUpdated(array_values($users)));
    return response()->noContent();
});