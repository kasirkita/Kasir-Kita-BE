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

Route::get('/test', 'Api\AuthController@setUrl');

Route::get('/check', 'Api\AuthController@check');
Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');

Route::middleware(['auth:api'])->group(function(){

    Route::get('/product/list', 'Api\ProductController@list');
    Route::middleware('can:product')->group(function(){
        Route::get('/product', 'Api\ProductController@index');
        Route::post('/product', 'Api\ProductController@store');
        Route::get('/product/print', 'Api\ProductController@print');
        Route::get('/product/print-thermal', 'Api\ProductController@printThermal');
        Route::get('/product/template', 'Api\ProductController@template');
        Route::post('/product/import', 'Api\ProductController@import');
        Route::get('/product/select/{id}', 'Api\ProductController@select');
        Route::delete('/product/toggle/{id}', 'Api\ProductController@toggle');
        Route::get('/product/{id}', 'Api\ProductController@show');
        Route::post('/product/{id}', 'Api\ProductController@update');
        Route::delete('/product/{id}', 'Api\ProductController@destroy');
    });

    Route::get('/category/list', 'Api\CategoryController@list');
    Route::middleware('can:category')->group(function(){
        Route::get('/category', 'Api\CategoryController@index');
        Route::post('/category', 'Api\CategoryController@store');
        Route::delete('/category/toggle/{id}', 'Api\CategoryController@toggle');
        Route::get('/category/{id}', 'Api\CategoryController@show');
        Route::post('/category/{id}', 'Api\CategoryController@update');
        Route::delete('/category/{id}', 'Api\CategoryController@destroy');
    });

    Route::get('/unit/list', 'Api\UnitController@list');
    Route::middleware('can:unit')->group(function(){
        Route::get('/unit', 'Api\UnitController@index');
        Route::post('/unit', 'Api\UnitController@store');
        Route::delete('/unit/toggle/{id}', 'Api\UnitController@toggle');
        Route::get('/unit/{id}', 'Api\UnitController@show');
        Route::post('/unit/{id}', 'Api\UnitController@update');
        Route::delete('/unit/{id}', 'Api\UnitController@destroy');
    });
    
    Route::get('/user/list', 'Api\UserController@list');
    Route::middleware('can:user')->group(function(){
        Route::get('/user', 'Api\UserController@index');
        Route::post('/user', 'Api\UserController@store');
        Route::delete('/user/toggle/{id}', 'Api\UserController@toggle');
        Route::get('/user/{id}', 'Api\UserController@show');
        Route::post('/user/{id}', 'Api\UserController@update');
        Route::delete('/user/{id}', 'Api\UserController@destroy');
    });

    Route::middleware('can:role')->group(function(){
        Route::get('/role', 'Api\RoleController@index');
        Route::post('/role', 'Api\RoleController@store');
        Route::get('/role/list', 'Api\RoleController@list');
        Route::delete('/role/toggle/{id}', 'Api\RoleController@toggle');
        Route::get('/role/{id}', 'Api\RoleController@show');
        Route::post('/role/{id}', 'Api\RoleController@update');
        Route::delete('/role/{id}', 'Api\RoleController@destroy');
    });

    Route::get('/permission/list', 'Api\PermissionController@list');

    Route::get('/customer/list', 'Api\CustomerController@list');
    Route::middleware('can:customer')->group(function(){
        Route::get('/customer', 'Api\CustomerController@index');
        Route::post('/customer', 'Api\CustomerController@store');
        Route::delete('/customer/toggle/{id}', 'Api\CustomerController@toggle');
        Route::get('/customer/{id}', 'Api\CustomerController@show');
        Route::post('/customer/{id}', 'Api\CustomerController@update');
        Route::delete('/customer/{id}', 'Api\CustomerController@destroy');
    });

    Route::get('/supplier/list', 'Api\SupplierController@list');
    Route::middleware('can:supplier')->group(function(){
        Route::get('/supplier', 'Api\SupplierController@index');
        Route::post('/supplier', 'Api\SupplierController@store');
        Route::delete('/supplier/toggle/{id}', 'Api\SupplierController@toggle');
        Route::get('/supplier/{id}', 'Api\SupplierController@show');
        Route::post('/supplier/{id}', 'Api\SupplierController@update');
        Route::delete('/supplier/{id}', 'Api\SupplierController@destroy');
    });


    Route::middleware('can:settings')->group(function(){
        Route::get('/setting', 'Api\SettingController@show');
        Route::post('/setting', 'Api\SettingController@store');
    });

    Route::middleware('can:cashier')->group(function(){
        Route::get('/cashier/cart/{code}', 'Api\CashierController@cart');
        Route::get('/cashier/search', 'Api\CashierController@search');
        Route::post('/cashier', 'Api\SalesController@store');
    });

    Route::middleware('can:discount')->group(function(){
        Route::get('/discount', 'Api\DiscountController@index');
        Route::post('/discount', 'Api\DiscountController@store');
        Route::get('/discount/print', 'Api\DiscountController@print');
        Route::get('/discount/print-thermal', 'Api\DiscountController@printThermal');
        Route::delete('/discount/toggle/{id}', 'Api\DiscountController@toggle');
        Route::get('/discount/select/{id}', 'Api\DiscountController@select');
        Route::get('/discount/{id}', 'Api\DiscountController@show');
        Route::post('/discount/{id}', 'Api\DiscountController@update');
        Route::delete('/discount/{id}', 'Api\DiscountController@destroy');
    });

    Route::middleware('can:sales')->group(function(){
        Route::get('/sales', 'Api\SalesController@index');
        Route::get('/sales/{id}', 'Api\SalesController@show');
        Route::delete('/sales/{id}', 'Api\SalesController@destroy');
    });

    Route::middleware('can:purchase')->group(function(){
        Route::get('/purchase', 'Api\PurchaseController@index');
        Route::post('/purchase', 'Api\PurchaseController@store');
        Route::get('/purchase/{id}', 'Api\PurchaseController@show');
        Route::delete('/purchase/{id}', 'Api\PurchaseController@destroy');
    });

    Route::middleware('can:expense')->group(function(){
        Route::get('/expense', 'Api\ExpenseController@index');
        Route::post('/expense', 'Api\ExpenseController@store');
        Route::get('/expense/{id}', 'Api\ExpenseController@show');
        Route::post('/expense/{id}', 'Api\ExpenseController@update');
        Route::delete('/expense/{id}', 'Api\ExpenseController@destroy');
    });

    Route::middleware('can:stock')->group(function(){
        Route::get('/stock', 'Api\StockController@index');
        Route::post('/stock', 'Api\StockController@store');
    });

    Route::middleware('can:stock')->group(function(){
        Route::get('/report-sales', 'Api\ReportController@sales');
        Route::get('/report-sales/print/{type}', 'Api\ReportController@printSales');
        Route::get('/report-purchase', 'Api\ReportController@purchase');
        Route::get('/report-purchase/print/{type}', 'Api\ReportController@printPurchase');
        Route::get('/report-expense', 'Api\ReportController@expense');
        Route::get('/report-expense/print/{type}', 'Api\ReportController@printExpense');
        Route::get('/report-stock', 'Api\ReportController@stock');
        Route::get('/report-stock/print/{type}', 'Api\ReportController@printStock');
    });


    Route::middleware('can:dashboard')->group(function(){
        Route::get('/dashboard', 'Api\DashboardController@index');
        Route::get('/dashboard/best-seller', 'Api\DashboardController@getBestSeller');
        Route::get('/dashboard/almost-out', 'Api\DashboardController@getAlmostOutOfStock');
        Route::get('/dashboard/chart', 'Api\DashboardController@getChart');
    });

});