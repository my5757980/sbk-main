<?php

use App\Http\Controllers\Chat\ChatApiController;
use App\Http\Controllers\Chat\ChatCallController;
use App\Http\Controllers\Chat\ChatGroupController;
use App\Http\Controllers\Chat\ChatMediaController;
use App\Http\Controllers\Chat\ChatPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Live chat (app/Services/Chat)
|--------------------------------------------------------------------------
| The same chat as sbkautotrading.com's, on this website's own database.
| Loaded with the "web" middleware group: the session says who is here.
| chat.js is handed these addresses by resources/views/chat/app.blade.php.
*/
Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [ChatPageController::class, 'app'])->name('app');
    Route::match(['get', 'post'], 'start', [ChatPageController::class, 'start'])->name('start');
    Route::get('logout', [ChatPageController::class, 'logout'])->name('logout');
    Route::get('desk', [ChatPageController::class, 'desk'])->name('desk');
    Route::get('login', [ChatPageController::class, 'login'])->name('login');
    Route::get('media/{message}', [ChatMediaController::class, 'show'])->whereNumber('message')->name('media');

    Route::prefix('api')->name('api.')->group(function () {
        Route::get('poll', [ChatApiController::class, 'poll'])->name('poll');
        Route::post('send', [ChatApiController::class, 'send'])->name('send');
        Route::post('upload', [ChatApiController::class, 'upload'])->name('upload');
        Route::post('name', [ChatApiController::class, 'name'])->name('name');
        Route::get('ping', [ChatApiController::class, 'ping'])->name('ping');
        Route::get('ice', [ChatApiController::class, 'ice'])->name('ice');
        Route::match(['get', 'post'], 'call', [ChatCallController::class, 'handle'])->name('call');
        Route::post('group', [ChatGroupController::class, 'handle'])->name('group');
    });
});
