<?php

use Illuminate\Http\Request;

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

Route::get('user', function (Request $request) {
    return _api_json(false, 'user');
})->middleware('jwt.auth');
Route::group(['namespace' => 'Api'], function () {

    Route::get('token', 'BasicController@getToken');
    Route::get('settings', 'BasicController@getSettings');
    Route::post('login', 'LoginController@login');
    Route::post('register', 'RegisterController@register');
    Route::get('locations', 'BasicController@getLocations');
    Route::get('setting', 'BasicController@getSettings');
    Route::get('categories', 'BasicController@getCategories');
    Route::post('ad_raters', 'BasicController@getAdRaters');
    Route::get('basic_data', 'BasicController@getBasicData');
    Route::get('get_packages', 'BasicController@getPackages');
    Route::get('ads','AdsController@index');
    Route::get('ads/{id}','AdsController@show');
    Route::post('/password/reset', 'PasswordController@reset');
    Route::post('/password/verify', 'PasswordController@verify');
    
    

    Route::group(['middleware' => 'jwt.auth'], function () {
        
        Route::get('num_of_available_ads','BasicController@getNumOfAvailableAds');
        Route::post('user/update', 'UserController@update');
        Route::get('logout', 'UserController@logout');
        Route::get('get_user', 'UserController@getUser');
        Route::get('favourites', 'UserController@favourites');
        Route::post('rate','UserController@rate');
        Route::post('handle_favourites','UserController@handleFavourites');
        Route::post('report', 'UserController@reportAd');
        Route::post('send_contact_message', 'BasicController@sendContactMessage');
        Route::post('send_package_request', 'BasicController@sendPackageRequest');
        Route::post('ads','AdsController@store');
        Route::put('ads/{id}','AdsController@update');
        Route::delete('ads/{id}','AdsController@destroy');
        Route::delete('delete_favourites','BasicController@deleteFavourites');
        
    });
});
