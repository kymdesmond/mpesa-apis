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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/access-token', 'PaymentController@generateSandBoxToken');

Route::get('/b2c', 'HomeController@b2c');
Route::post('/b2c','PaymentController@b2c');

//Route::post('/callback', 'PaymentController@processB2CRequestCallback');

Auth::routes();

