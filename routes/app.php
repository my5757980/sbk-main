<?php

use App\Http\Controllers\Api\App\AccountController;
use App\Http\Controllers\Api\App\CatalogController;
use App\Http\Controllers\Api\App\ChatController;
use App\Http\Controllers\Api\App\OrderController;
use App\Http\Controllers\Chat\ChatApiController;
use App\Http\Controllers\Chat\ChatCallController;
use App\Http\Controllers\Chat\ChatMediaController;
use App\Http\Middleware\AppChatIdentity;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| The SBK app (mobile-app/) - /api/app/*
|--------------------------------------------------------------------------
| The website's frontend and live chat, for the phone app, on THIS website's
| database: the same vehicles, blogs, accounts, orders, contact messages and
| chat rows the website's own pages use. No admin, auction or CRM here.
|
| A customer signs in with the website's email and password and the app keeps
| a Sanctum token ("Authorization: Bearer ..."). A chat visitor without an
| account keeps the chat's visitor token ("X-Chat-Guest: ...").
*/

// What anyone can read.
Route::get('home', [CatalogController::class, 'home'])->name('home');
Route::get('filters', [CatalogController::class, 'filters'])->name('filters');
Route::get('products', [CatalogController::class, 'products'])->name('products');
Route::get('products/{slug}', [CatalogController::class, 'product'])->name('product');
Route::get('blogs', [CatalogController::class, 'blogs'])->name('blogs');
Route::get('blogs/{slug}', [CatalogController::class, 'blog'])->name('blog');

// The forms (the website's "Submit Order for Checking" and contact form).
Route::post('checkout', [OrderController::class, 'checkout'])->middleware('throttle:app-checkout')->name('checkout');
Route::post('contact', [OrderController::class, 'contact'])->middleware('throttle:app-contact')->name('contact');

// The account - one account with the website.
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', [AccountController::class, 'register'])->middleware('throttle:app-auth')->name('register');
    Route::post('login', [AccountController::class, 'login'])->middleware('throttle:app-auth')->name('login');
    Route::post('forgot', [AccountController::class, 'forgot'])->middleware('throttle:app-forgot')->name('forgot');
    Route::post('logout', [AccountController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AccountController::class, 'me'])->name('me');
    Route::post('me', [AccountController::class, 'update'])->name('me.update');
    Route::put('me/password', [AccountController::class, 'password'])->name('me.password');
    Route::get('orders', [AccountController::class, 'orders'])->name('orders');
});

// The live chat: the website chat's own endpoints (routes/chat.php), reached with the app's token.
Route::prefix('chat')->name('chat.')->middleware(AppChatIdentity::class)->group(function () {
    Route::match(['get', 'post'], 'start', [ChatController::class, 'start'])->middleware('throttle:app-chat-start')->name('start');
    Route::get('poll', [ChatApiController::class, 'poll'])->name('poll');
    Route::post('send', [ChatApiController::class, 'send'])->name('send');
    Route::post('upload', [ChatApiController::class, 'upload'])->name('upload');
    Route::post('name', [ChatApiController::class, 'name'])->name('name');
    Route::get('ping', [ChatApiController::class, 'ping'])->name('ping');
    Route::get('ice', [ChatApiController::class, 'ice'])->name('ice');
    Route::match(['get', 'post'], 'call', [ChatCallController::class, 'handle'])->name('call');
    Route::get('media/{message}', [ChatMediaController::class, 'show'])->whereNumber('message')->name('media');
});
