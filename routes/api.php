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

// Route::post('login', 'API\UserController@login');
// Route::post('register', 'API\UserController@store');
// Route::get('stores/{user}', 'API\StoreController@show');
    
Route::middleware(['auth:api'])->group(function () {

    // Route::get('getUserInformation', 'API\LoginController@getUserInformation');

    //Item
    Route::post('items/pagination', 'API\ItemController@pagination');
    Route::resource('items', 'API\ItemController');

    // Item - Product
    Route::post('items/products', 'API\ItemController@storeProduct');
    Route::put('items/products/{item}', 'API\ItemController@updateProduct');

    // Item - Product
    Route::post('items/services', 'API\ItemController@storeService');
    Route::put('items/services/{service}', 'API\ItemController@updateService');


    // Invoice
    Route::resource('invoices', 'API\InvoiceController');
    Route::post('invoices/pagination', 'API\InvoiceController@pagination');
    Route::get('invoices/anull/{invoice}', 'API\InvoiceController@anull');
    Route::post('invoices/generate', 'API\InvoiceController@generate');

    //Invoice details
    Route::resource('invoiceDetails', 'API\InvoiceDetailController');

    //Unit
    Route::resource('units', 'API\UnitController');
    Route::post('units/pagination', 'API\UnitController@pagination');

    //User
    // Route::resource('users', 'API\UserController');
    // Route::post('users/pagination', 'API\UserController@pagination');

    //Ranqhana User 
    Route::resource('ranqhana-users', 'API\RanqhanaUserController');

    //Stores
    Route::resource('stores', 'API\StoreController');
    Route::post('stores/pagination', 'API\StoreController@pagination');

    //Country
    Route::resource('countries', 'API\CountryController');

    //Report
    Route::post('reports/invoiceMoney', 'API\ReportController@invoiceMoney');
    Route::post('reports/popularProducts', 'API\ReportController@popularProducts');

    //Stock Types
    Route::resource('stockTypes', 'API\StockTypeController');

    //Images
    Route::resource('images', 'API\ImageController');

    //Payment Method
    Route::resource('payment-methods', 'API\PaymentMethodController');

    //Payment Stage
    Route::resource('payment-stages', 'API\PaymentStageController');

    //Payments
    Route::resource('payments', 'API\PaymentController');
    Route::get('payments/invoices/{invoice_id}', 'API\PaymentController@byInvoice');
    Route::post('payments/updatePaymentDate', 'API\PaymentController@updatePaymentDate');
    
    //Payment Types
    Route::resource('payment-types', 'API\PaymentTypeController');

    //Order Stage
    Route::resource('order-stages', 'API\OrderStageController');

    //Orders
    Route::resource('orders', 'API\OrderController');
    Route::post('orders/pagination', 'API\OrderController@pagination');
    Route::post('orders/updateStage', 'API\OrderController@updateStage');
    Route::post('orders/updateDeliveryDate', 'API\OrderController@updateDeliveryDate');

    //Invoice Type
    Route::resource('invoice-types', 'API\InvoiceTypeController');

    //Invoice Stage
    Route::resource('invoice-stages', 'API\InvoiceStageController');

    //Prices
    Route::get('prices/sellPriceByItem/{item_id}', 'API\PriceController@sellPriceByItem');
    Route::get('prices/purchasePriceByItem/{item_id}', 'API\PriceController@purchasePriceByItem');
});

