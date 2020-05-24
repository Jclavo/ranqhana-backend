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

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@store');
Route::get('stores/{user}', 'API\StoreController@show');
    
Route::middleware(['auth:api'])->group(function () {

    Route::get('getUserInformation', 'API\LoginController@getUserInformation');

    Route::post('items/pagination', 'API\ItemController@pagination');
    Route::resource('items', 'API\ItemController');

    // Invoice
    Route::resource('invoices', 'API\InvoiceController');
    Route::post('invoices/pagination', 'API\InvoiceController@pagination');
    Route::get('invoices/anull/{invoice}', 'API\InvoiceController@anull');
    Route::post('invoices/graphicReport', 'API\InvoiceController@graphicReport');
    // sell invoice
    // Route::post('sellInvoices', 'API\InvoiceController@createSellInvoice');
    //Route::post('invoiceDetails', 'API\InvoiceController@createInvoiceDetail');

    //Invoice details
    Route::resource('invoiceDetails', 'API\InvoiceDetailController');

    //Unit
    Route::resource('units', 'API\UnitController');
    Route::post('units/pagination', 'API\UnitController@pagination');

    //User
    Route::resource('users', 'API\UserController');
    Route::post('users/pagination', 'API\UserController@pagination');

    //Stores
    Route::resource('stores', 'API\StoreController');
    Route::post('stores/pagination', 'API\StoreController@pagination');
  
});

