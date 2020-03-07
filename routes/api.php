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

Route::get('/check', 'Api\AuthController@check');
Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');

Route::middleware(['auth:api'])->group(function(){
    Route::get('/product', 'Api\ProductController@index');
    Route::post('/product', 'Api\ProductController@store');
    Route::post('/product/import', 'Api\ProductController@import');
    Route::delete('/product/toggle/{id}', 'Api\ProductController@toggle');
    Route::get('/product/{id}', 'Api\ProductController@show');
    Route::post('/product/{id}', 'Api\ProductController@update');
    Route::delete('/product/{id}', 'Api\ProductController@destroy');

    Route::get('/category/list', 'Api\CategoryController@list');

    Route::get('/unit/list', 'Api\UnitController@list');
});