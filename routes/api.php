<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'prefix' => 'auth'

], function () {

    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');

});

Route::group(['middleware' => ['jwt.auth']], function() {
    /*AÑADE AQUI LAS RUTAS QUE QUIERAS PROTEGER CON JWT*/

    Route::post('customers/create', 'CustomerController@create');
    Route::post('customers/get', 'CustomerController@get');
    Route::post('customers/delete', 'CustomerController@delete');

});