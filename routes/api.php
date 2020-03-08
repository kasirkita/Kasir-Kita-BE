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
    Route::get('/product/print', 'Api\ProductController@print');
    Route::get('/product/template', 'Api\ProductController@template');
    Route::post('/product/import', 'Api\ProductController@import');
    Route::get('/product/select/{id}', 'Api\ProductController@select');
    Route::delete('/product/toggle/{id}', 'Api\ProductController@toggle');
    Route::get('/product/{id}', 'Api\ProductController@show');
    Route::post('/product/{id}', 'Api\ProductController@update');
    Route::delete('/product/{id}', 'Api\ProductController@destroy');

    Route::get('/category', 'Api\CategoryController@index');
    Route::post('/category', 'Api\CategoryController@store');
    Route::get('/category/list', 'Api\CategoryController@list');
    Route::delete('/category/toggle/{id}', 'Api\CategoryController@toggle');
    Route::get('/category/{id}', 'Api\CategoryController@show');
    Route::post('/category/{id}', 'Api\CategoryController@update');
    Route::delete('/category/{id}', 'Api\CategoryController@destroy');

    Route::get('/unit', 'Api\UnitController@index');
    Route::post('/unit', 'Api\UnitController@store');
    Route::get('/unit/list', 'Api\UnitController@list');
    Route::delete('/unit/toggle/{id}', 'Api\UnitController@toggle');
    Route::get('/unit/{id}', 'Api\UnitController@show');
    Route::post('/unit/{id}', 'Api\UnitController@update');
    Route::delete('/unit/{id}', 'Api\UnitController@destroy');

    Route::get('/unit/list', 'Api\UnitController@list');
});