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
    

    Route::post('login', 'LoginController@login');
    Route::post('register', 'RegisterController@register');

    Route::get('setting', 'BasicController@getSettings');
    

    Route::group(['middleware' => 'jwt.auth'], function () {

        Route::post('user/update', 'UserController@update');
        Route::get('logout', 'UserController@logout');
        Route::get('get_categories', 'BasicController@getCategories');
        
        Route::get('get_user', 'UserController@getUser');

        Route::get('favourites', 'UserController@favourites');

        Route::post('handle_favourites','UserController@handleFavourites');
        Route::post('send_contact_message', 'BasicController@sendContactMessage');
       
        
    
        
        Route::resource('ads', 'AdsController');
        
    });
});
