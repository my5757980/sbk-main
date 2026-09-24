<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\MoodRatingController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('social-login', [AuthController::class, 'SocialLogin']);
        Route::get('verification/resend/{id}', [AuthController::class, 'sendVerificationEmail']);
        Route::post('send-otp', [AuthController::class, 'sendOtp']);
        Route::post('check-otp', [AuthController::class, 'submitOtp']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('reset-password', [AuthController::class, 'reset_password_submit']);
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'user'], function () {
            route::get('info', 'UserController@info');
            route::post('changePassword', 'UserController@changePassword');
            route::post('profile-image', 'UserController@profile_image');
            route::post('update', 'UserController@update');
            route::get('update-status', 'UserController@updateStatus');
            Route::post('avator', [UserController::class, 'avator']);
            Route::post('save-relation', [UserController::class, 'saveRelation']);
            Route::post('save-situation', [UserController::class, 'saveSituation']);
            Route::post('save-habit', [UserController::class, 'saveHabit']);
            Route::post('save-mood', [UserController::class, 'saveMood']);
            Route::post('coach-avator', [UserController::class, 'uploadCoachAvatar']);
            Route::get('avator', [UserController::class, 'allAvator']);
            Route::get('coach', [UserController::class, 'allCoaches']);
            Route::post('is-completed', [UserController::class, 'isCompleted']);
            //            Route::get('/save-relation', [UserController::class, 'saveRelation']);
            //            Route::post('/mood-ratings', 'MoodRatingController@store');
            //            Route::post('save-relation', 'UserController@saveRelation');
            Route::get('/graph', 'GraphController@showPages');
            Route::get('/delete-account', 'UserController@DeleteAccount');
            Route::get('notifications', [UserController::class, 'getNotifications']);
        });
        Route::get('/category', 'CategoryController@showPages');
        Route::get('/resource', 'ResourceController@showResource');
        Route::get('/all-resource', 'AllResourceController@allResource');
        Route::get('/all-activity', 'AllResourceController@allActivity');
        Route::get('/all-feeling', 'AllResourceController@allFeeling');
        Route::get('/activity', 'ActivityController@showActivity');
        Route::get('/feeling', 'FeelingController@showFeeling');
        Route::post('/mood', 'MoodController@saveMood');
        Route::post('ratings', [MoodRatingController::class, 'store']);
        Route::get('/show-mood', 'MoodController@showMood');
        Route::group(['prefix' => 'chat', 'namespace' => 'Chat'], function () {
            route::post('store', 'ChatController@store');
            Route::get('show', 'ChatController@index');
            Route::get('unread', 'ChatController@unreadChats');
            Route::post('update', 'ChatController@updateReadStatus')->name('update');
            Route::post('deleteMessage', 'ChatController@deleteMessage')->name('delete-message');
            Route::get('showSingleChat', 'ChatController@showSingleChat');
        });

        Route::group(['prefix' => 'notification'], function () {
            route::get('show', 'NotificationController@index');
            route::get('read-all', 'NotificationController@readAll');
            route::get('read/{id}', 'NotificationController@read');
        });

        // Property Owner Apis
        Route::middleware('check.property_type')->group(function () {
            Route::group(['prefix' => 'property-listing', 'namespace' => 'Property'], function () {
                route::get('get', 'ListingController@index');
                route::get('show', 'ListingController@show');
                route::get('delete', 'ListingController@delete');
                route::post('store', 'ListingController@store');
                route::post('update', 'ListingController@update');
                route::post('delete-file', 'ListingController@deleteFile');
            });

            Route::group(['prefix' => 'bids', 'namespace' => 'Property'], function () {
                route::get('get', 'BidsController@index');
                route::get('show', 'BidsController@show');
                route::post('update-status', 'BidsController@updateBid');
            });

            Route::group(['prefix' => 'bookings', 'namespace' => 'Property'], function () {
                route::get('get', 'BookingsController@index');
                route::get('show', 'BookingsController@show');
                route::get('unavailable-properties', 'BookingsController@getUnAvailableProperties');
                route::post('update-unavailable', 'BookingsController@updatePropertyStatus');
            });
        });
    });

    Route::get('/pages', 'SiteController@showPages');
    Route::get('/site-settings', 'SiteController@index');
    Route::get('/pages/{slug?}', 'PageController@index');
    Route::get('/question', 'QuestionController@showQuestion');
    Route::get('/relation-question', 'QuestionController@allRelation');
    Route::get('/situation-question', 'QuestionController@allSituation');
    Route::get('/habit-question', 'QuestionController@allHabit');
    Route::post('/save-answer', 'SaveAnswerController@store');
    Route::post('/search-resource', 'ResourceController@searchResource');
    Route::get('landing-content', 'ContentController@landingContent');
    Route::get('faqs', 'ContentController@faqs');
    Route::get('amazing-features', 'ContentController@amazingFeatures');
    Route::post('contact-us', 'ContentController@contactUs');
});
