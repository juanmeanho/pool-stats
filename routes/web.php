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

Route::get('/getmarkets', 'ApiController@getMarkets')->name('getMarkets');


Route::prefix('auth')->group(function(){

	Route::get('init', 'AppController@init');

	Route::post('login', 'AppController@login');
	Route::any('register', 'AppController@register');
	Route::post('logout', 'AppController@logout');
	Route::get('setpasswords/{user}/{password_1}/{password_2}', 'AppController@setPasswords');
	Route::get('checkpasswords', 'AppController@checkPasswords');
	Route::get('db-blocks', 'AppController@getDbBlocks');

});

Route::prefix('api')->group(function(){

	Route::get('/getdata/{type}/{currency}/{page}', 'ApiController@getData')->name('getData');
	Route::get('/last-block', 'ApiController@getLastBlockPool')->name('getLastBlockPool');

});


