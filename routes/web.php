<?php

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


$languages = array('ar', 'en', 'fr');
$defaultLanguage = 'ar';
if ($defaultLanguage) {
    $defaultLanguageCode = $defaultLanguage;
} else {
    $defaultLanguageCode = 'ar';
}

$currentLanguageCode = Request::segment(1, $defaultLanguageCode);
if (in_array($currentLanguageCode, $languages)) {
    Route::get('/', function () use($defaultLanguageCode) {
        return redirect()->to($defaultLanguageCode);
    });


    Route::group(['namespace' => 'Front', 'prefix' => $currentLanguageCode], function () use($currentLanguageCode) {
        app()->setLocale($currentLanguageCode);
        app()->setLocale($currentLanguageCode);
        Route::get('/', 'HomeController@index')->name('home');
       
        Auth::routes();

      


        /*         * ************************* user ************** */
        Route::group(['namespace' => 'Customer', 'prefix' => 'customer'], function () {
            Route::get('dashboard', 'DashboardController@index');
            Route::get('user/edit', 'UserController@showEditForm');
            Route::post('user/edit', 'UserController@edit');
            Route::get('user/notifications', 'UserController@notifications');
        });
    });
} else {
    Route::get('/' . $currentLanguageCode, function () use($defaultLanguageCode) {
        return redirect()->to($defaultLanguageCode);
    });
}


//Route::group(['middleware'=>'auth:admin'], function () {
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    
    Route::get('/', 'AdminController@index')->name('admin.dashboard');
    Route::get('/error', 'AdminController@error')->name('admin.error');
    Route::get('/change_lang', 'AjaxController@change_lang')->name('ajax.change_lang');

    Route::get('profile', 'ProfileController@index');
    Route::patch('profile', 'ProfileController@update');

    Route::resource('groups', 'GroupsController');
    Route::post('groups/data', 'GroupsController@data');

    Route::resource('admins', 'AdminsController');
    Route::post('admins/data', 'AdminsController@data');

    Route::resource('locations', 'LocationsController');
    Route::post('locations/data', 'LocationsController@data');

    Route::resource('packages', 'PackagesController');
    Route::post('packages/data', 'PackagesController@data');

    Route::resource('currency', 'CurrencyController');
    Route::post('currency/data', 'CurrencyController@data');

    Route::resource('categories', 'CategoriesController');
    Route::post('categories/data', 'CategoriesController@data');

    Route::resource('property_types', 'PropertyTypesController');
    Route::post('property_types/data', 'PropertyTypesController@data');

    Route::resource('rooms', 'RoomsController');
    Route::post('rooms/data', 'RoomsController@data');

    Route::resource('bathes', 'BathesController');
    Route::post('bathes/data', 'BathesController@data');

    Route::resource('engine_capacities', 'EngineCapacitiesController');
    Route::post('engine_capacities/data', 'EngineCapacitiesController@data');

    Route::resource('fuel_types', 'FuelTypesController');
    Route::post('fuel_types/data', 'FuelTypesController@data');


    Route::resource('motion_vectors', 'MotionVectorsController');
    Route::post('motion_vectors/data', 'MotionVectorsController@data');

    Route::resource('payment_methods', 'PaymentMethodsController');
    Route::post('payment_methods/data', 'PaymentMethodsController@data');
    

    Route::resource('users', 'UsersController');
    Route::post('users/data', 'UsersController@data');
    Route::get('users/status/{id}', 'UsersController@status');

    Route::resource('ads', 'AdsController');
    Route::post('ads/data', 'AdsController@data');
    Route::get('ads/special/{id}', 'AdsController@special');
    Route::get('ads/active/{id}', 'AdsController@active');


    Route::resource('orders_reports', 'OrdersReportsController');

    Route::post('settings', 'SettingsController@store');
    Route::get('notifications', 'NotificationsController@index');
    Route::post('notifications', 'NotificationsController@store');



    Route::get('settings', 'SettingsController@index');


    Route::resource('contact_messages', 'ContactMessagesController');
    Route::post('contact_messages/data', 'ContactMessagesController@data');



    Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'LoginController@login')->name('admin.login.submit');
    Route::get('logout', 'LoginController@logout')->name('admin.logout');
});
//});

