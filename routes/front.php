<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\SiteController;
use App\Http\Controllers\Front\ServiceController;

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


Route::get('/', [SiteController::class, 'index'])->name('index');
Route::get('/about-us', [SiteController::class, 'aboutUs'])->name('about-us');
Route::get('/sbk-official-partners', [SiteController::class, 'officialPartners'])->name('official-partners');
Route::get('/shop', [SiteController::class, 'shop'])->name('shop');
Route::get('/search/suggestions', [SiteController::class, 'searchSuggestions'])->name('search.suggestions');
Route::get('/product/{product:slug}', [SiteController::class, 'productDetail'])->name('product.show');
Route::middleware('guest')->group(function () {
    Route::get('/login', 'AuthController@showLogin')->name('login');
    Route::post('/login', 'AuthController@login')->name('login.submit');
    Route::get('/register', 'AuthController@showRegister')->name('register');
    Route::post('/register', 'AuthController@register')->name('register.submit');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', 'AuthController@logout')->name('logout');
    Route::get('/profile', 'ProfileController@index')->name('profile.index');
    Route::put('/profile', 'ProfileController@update')->name('profile.update');
    Route::put('/profile/password', 'ProfileController@updatePassword')->name('profile.password');
});
Route::get('/cart', 'CartController@index')->name('cart.index');
Route::post('/cart/{product}', 'CartController@store')->name('cart.store');
Route::delete('/cart/{cartItem}', 'CartController@destroy')->name('cart.destroy');
Route::get('/checkout', 'CheckoutController@index')->name('checkout.index');
Route::post('/checkout', 'CheckoutController@store')->name('checkout.store');
Route::post('/checkout/paypal/create', 'CheckoutController@createPaypalOrder')->name('checkout.paypal.create');
Route::post('/checkout/paypal/capture', 'CheckoutController@capturePaypalOrder')->name('checkout.paypal.capture');
Route::get('/wishlist', 'WishlistController@index')->name('wishlist.index');
Route::post('/wishlist/{product}', 'WishlistController@store')->name('wishlist.store');
Route::delete('/wishlist/{wishlistItem}', 'WishlistController@destroy')->name('wishlist.destroy');
Route::get('/services', [SiteController::class, 'services'])->name('services');
Route::get('/blog', [SiteController::class, 'blog'])->name('blog');
Route::get('/blog/{blog:slug}', [SiteController::class, 'blogDetail'])->name('blog.show');
Route::get('/our-locations', [SiteController::class, 'ourLocations'])->name('our-locations');
Route::get('/bank', [SiteController::class, 'bank'])->name('bank');
Route::get('/contact-us', [SiteController::class, 'contactUs'])->name('contact-us');
Route::post('/contact-us', [SiteController::class, 'submitContact'])->name('contact-us.submit');
