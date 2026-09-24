<?php

use App\Http\Controllers\Admin\LandingContentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('admin', 'IndexController@index')->name('login'); // for redirection purpose
Route::name('admin.')->middleware('admin.permission')->group(
    function () {

        Route::get('/', 'IndexController@index');

        # to show login form
        Route::get('/auth/login', [
            'uses'  => 'Auth\LoginController@showLoginForm',
            'as'    => 'auth.login'
        ]);

        # login form submits to this route
        Route::post('/auth/login', [
            'uses'  => 'Auth\LoginController@login',
            'as'    => 'auth.login'
        ]);

        # logs out admin user
        # it was post method before I recieved MethodNotAllowedHttpException
        Route::any('/auth/logout', [
            'uses'  => 'Auth\LoginController@logout',
            'as'    => 'auth.logout'
        ]);

        # Password reset routes
        Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
        Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

        # shows dashboard
        Route::get('dashboard', [
            'uses'  => 'DashboardController@index',
            'as'    => 'dashboard.index'
        ]);
        Route::get('update-profile', 'AdministratorsController@editProfile')->name('update-profile');
        Route::post('update-status', 'UserController@updateStatus')->name('update-status');

        Route::resource('administrators', 'AdministratorsController');
        Route::resource('admin-roles', 'AdminRoleController');
        Route::resource('site-settings', 'SiteSettingsController');
        Route::resource('pages', 'PagesController');
        Route::resource('media-files', 'MediaFilesController');
        Route::resource('blog', 'BlogController');
        Route::resource('service', 'ServiceController');
        Route::resource('vehicle-brand', 'VehicleBrandController')->except(['show']);
        Route::resource('vehicle-model', 'VehicleModelController')->except(['show']);
        Route::resource('product', 'ProductController')->except(['show']);
        Route::resource('orders', 'CheckoutOrderController');
        Route::resource('faq', 'FaqController');
        Route::resource('testimonial', 'TestimonialController');
        Route::resource('mood', 'MoodController');
        Route::resource('contact-us', 'ContactUsController');
        Route::resource('feeling', 'FeelingController');
        Route::resource('newsletters', 'NewsLettersController');
        Route::resource('category', 'CategoryController');
        Route::resource('resource', 'ResourceController');
        Route::resource('activity', 'ActivityController');
        Route::resource('question', 'QuestionController');
        Route::resource('relation-question', 'RelationshipController');
        Route::resource('situation-question', 'SituationQuestionController');
        Route::resource('habit-question', 'HabitQuestionController');
        Route::resource('users', 'UserController');
        Route::resource('amazing-feature', 'AmazingFeatureController');
        Route::post('upload-image', [
            'uses'  => 'IndexController@uploadImage',
            'as'    => 'upload-image-from-editor'
        ]);

        Route::get('landing-content', [LandingContentController::class, 'index'])->name('landing-content.index');
        Route::post('landing-content/update', [LandingContentController::class, 'update'])->name('landing-content.update');
    }
);

