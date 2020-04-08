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

    Route::post('items/pagination', 'API\ItemController@pagination');
    Route::resource('items', 'API\ItemController');

    // Invoice
    Route::resource('invoices', 'API\InvoiceController');
    Route::post('invoices/pagination', 'API\InvoiceController@pagination');
    // sell invoice
    Route::post('sellInvoices', 'API\InvoiceController@createSellInvoice');
    Route::post('invoiceDetails', 'API\InvoiceController@createInvoiceDetail');

    //Unit
    Route::resource('units', 'API\UnitController');
    Route::post('units/pagination', 'API\UnitController@pagination');

   
});

