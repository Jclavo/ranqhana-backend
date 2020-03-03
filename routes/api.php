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

Route::post('register', 'API\LoginController@register');
Route::post('login', 'API\LoginController@login');

Route::get('country', 'API\CountryController@index');


// Route::middleware('auth:api')->get('getUserInformation', 'API\LoginController@getUserInformation');

Route::middleware(['auth:api'])->group(function () {

    Route::get('getUserInformation', 'API\LoginController@getUserInformation');

    Route::resource('items', 'API\ItemController');

});

